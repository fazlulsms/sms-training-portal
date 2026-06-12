<?php

namespace App\Http\Controllers;

use App\Models\AiPromptTemplate;
use App\Models\Trainer;
use App\Services\OpenAIService;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser as PdfParser;
use ZipArchive;

class AiTrainerProfileController extends Controller
{
    private const TEMPLATE_CODE = 'trainer_profile_generator_v1';
    private const SESSION_KEY   = 'ai_trainer_draft';

    public function __construct(private OpenAIService $ai) {}

    private function guardSuperAdmin(): void
    {
        if (! auth()->user()?->isSuperAdmin()) {
            abort(403, 'AI Trainer Profile Generator is restricted to Super Admins.');
        }
    }

    // ── Step 1: Upload form ──────────────────────────────────────
    // GET /admin/ai/trainer-profile

    public function index()
    {
        $this->guardSuperAdmin();
        session()->forget(self::SESSION_KEY);

        $trainers = Trainer::orderBy('name')->get(['id', 'name', 'designation', 'ai_generated']);
        return view('ai.trainer-profile.upload', compact('trainers'));
    }

    // ── Step 2: Process upload + generate ───────────────────────
    // POST /admin/ai/trainer-profile/generate

    public function generate(Request $request)
    {
        $this->guardSuperAdmin();

        $request->validate([
            'trainer_id'  => 'nullable|exists:trainers,id',
            'trainer_name'=> 'required_without:trainer_id|nullable|string|max:255',
            'documents'   => 'required|array|min:1|max:5',
            'documents.*' => 'required|file|mimes:pdf,docx,doc,txt|max:10240',
            'extra_notes' => 'nullable|string|max:2000',
        ]);

        $template = AiPromptTemplate::where('template_code', self::TEMPLATE_CODE)
            ->where('is_active', true)
            ->first();

        if (! $template) {
            return back()->with('error', 'AI template "' . self::TEMPLATE_CODE . '" not found or inactive. Go to AI → Prompt Templates to create it.');
        }

        // ── Extract text from uploaded files ─────────────────────
        $filesAnalyzed = [];
        $allText       = '';

        foreach ($request->file('documents') as $file) {
            $ext      = strtolower($file->getClientOriginalExtension());
            $origName = $file->getClientOriginalName();
            $text     = '';

            try {
                $text = match ($ext) {
                    'pdf'  => $this->extractPdf($file->getRealPath()),
                    'docx' => $this->extractDocx($file->getRealPath()),
                    'doc'  => $this->extractDoc($file->getRealPath()),
                    'txt'  => file_get_contents($file->getRealPath()),
                    default => '',
                };
            } catch (\Throwable $e) {
                // Partial failure: skip file but continue
            }

            $text = trim($text);
            if ($text !== '') {
                $filesAnalyzed[] = ['name' => $origName, 'type' => strtoupper($ext), 'chars' => strlen($text)];
                $allText .= "\n\n--- Document: {$origName} ---\n" . $text;
            }
        }

        $allText = $this->toUtf8(trim($allText));
        if (empty($allText)) {
            return back()->with('error', 'Could not extract readable text from the uploaded file(s). Please check the file format or try a TXT export.');
        }

        // ── Determine trainer name ────────────────────────────────
        $trainer     = null;
        $trainerName = null;

        if ($request->filled('trainer_id')) {
            $trainer     = Trainer::find($request->trainer_id);
            $trainerName = $trainer?->name;
        }
        if (empty($trainerName)) {
            $trainerName = $request->input('trainer_name', 'Unknown Trainer');
        }

        // ── Build prompt input ────────────────────────────────────
        $input  = "Trainer Name: {$trainerName}\n";
        if (! empty($request->extra_notes)) {
            $input .= "Additional Context: " . $request->extra_notes . "\n";
        }
        $input .= "\n=== DOCUMENT TEXT ===\n" . mb_substr($allText, 0, 12000);

        // ── Call AI ───────────────────────────────────────────────
        $startTime = microtime(true);
        $result    = $this->ai->generateFromTemplate($template, $input, auth()->id());
        $duration  = round(microtime(true) - $startTime, 1);

        if (! $result['success']) {
            return back()->with('error', 'AI generation failed: ' . ($result['error'] ?? 'Unknown error. Please try again.'));
        }

        $raw    = trim($result['text'] ?? '');
        $raw    = preg_replace('/^```(?:json)?\s*/i', '', $raw);
        $raw    = preg_replace('/\s*```$/', '', $raw);
        $raw    = trim($raw);
        $parsed = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($parsed)) {
            return back()->with('error', 'AI returned an unexpected format. Please try again. (Parse error: ' . json_last_error_msg() . ')');
        }

        session()->put(self::SESSION_KEY, [
            'trainer_id'    => $trainer?->id,
            'trainer_name'  => $trainerName,
            'ai_output'     => $parsed,
            'ai_usage'      => $result['usage'] ?? [],
            'template_id'   => $template->id,
            'files_analyzed'=> $filesAnalyzed,
            'duration'      => $duration,
        ]);

        return redirect()->route('ai.trainer-profile.preview');
    }

    // ── Step 3: Preview ──────────────────────────────────────────
    // GET /admin/ai/trainer-profile/preview

    public function preview()
    {
        $this->guardSuperAdmin();

        $draft = session(self::SESSION_KEY);
        if (! $draft) {
            return redirect()->route('ai.trainer-profile.index')
                ->with('warning', 'No AI draft found. Please upload documents first.');
        }

        $trainer = $draft['trainer_id'] ? Trainer::find($draft['trainer_id']) : null;

        return view('ai.trainer-profile.preview', [
            'draft'    => $draft,
            'aiOutput' => $draft['ai_output'],
            'aiUsage'  => $draft['ai_usage'] ?? [],
            'trainer'  => $trainer,
        ]);
    }

    // ── Step 4: Save ─────────────────────────────────────────────
    // POST /admin/ai/trainer-profile/save

    public function save(Request $request)
    {
        $this->guardSuperAdmin();

        $draft = session(self::SESSION_KEY);
        if (! $draft) {
            return redirect()->route('ai.trainer-profile.index')
                ->with('error', 'Session expired. Please upload documents again.');
        }

        $ai = $draft['ai_output'];

        // ── Collect editable fields from form ─────────────────────
        $fields = [
            'name'                    => $request->input('name',                   $draft['trainer_name']),
            'designation'             => $request->input('designation',            $ai['designation'] ?? null),
            'organization'            => $request->input('organization',           $ai['organization'] ?? null),
            'qualification'           => $request->input('qualification',          $ai['qualification'] ?? null),
            'short_bio'               => $request->input('short_bio',              $ai['professional_bio'] ?? null),
            'expertise_areas'         => $request->input('expertise_areas',        $this->arrayToText($ai['expertise_areas'] ?? [])),
            'certifications'          => $request->input('certifications',         $this->arrayToText($ai['certifications'] ?? [])),
            'experience'              => $request->input('experience',             $ai['years_experience'] ?? null),
            'professional_highlights' => $request->input('professional_highlights',$this->arrayToText($ai['professional_highlights'] ?? [])),
            'industries_served'       => $request->input('industries_served',      $this->arrayToText($ai['industries_served'] ?? [])),
            'countries_covered'       => $request->input('countries_covered',      $this->arrayToText($ai['countries_covered'] ?? [])),
            'languages_spoken'        => $request->input('languages_spoken',       $this->arrayToText($ai['languages_spoken'] ?? [])),
            'training_specializations'=> $request->input('training_specializations',$this->arrayToText($ai['training_specializations'] ?? [])),
            'audit_specializations'   => $request->input('audit_specializations',  $this->arrayToText($ai['audit_specializations'] ?? [])),
            'seo_title'               => $request->input('seo_title',              $ai['seo_title'] ?? null),
            'seo_description'         => $request->input('seo_description',        $ai['seo_description'] ?? null),
            'seo_keywords'            => $request->input('seo_keywords',           $ai['seo_keywords'] ?? null),
            'ai_generated'            => true,
            'ai_profile_data'         => $ai,
        ];

        // Strip empty strings to null
        foreach ($fields as $k => $v) {
            if ($v === '') {
                $fields[$k] = null;
            }
        }

        $trainerId = $draft['trainer_id'];

        if ($trainerId && ($trainer = Trainer::find($trainerId))) {
            // Update existing trainer
            $trainer->update($fields);
            session()->forget(self::SESSION_KEY);

            return redirect('/admin/trainers/edit/' . $trainer->id)
                ->with('success', '✨ AI profile for "' . $trainer->name . '" saved. Review and update as needed.');
        }

        // Create new trainer
        $fields['status']     = 1;
        $fields['is_public']  = false;
        $fields['display_order'] = 0;
        $trainer = Trainer::create($fields);

        session()->forget(self::SESSION_KEY);

        return redirect('/admin/trainers/edit/' . $trainer->id)
            ->with('success', '✨ New trainer profile for "' . $trainer->name . '" created. Review and publish when ready.');
    }

    // ── Cancel ───────────────────────────────────────────────────
    // POST /admin/ai/trainer-profile/cancel

    public function cancel()
    {
        $this->guardSuperAdmin();
        session()->forget(self::SESSION_KEY);
        return redirect('/admin/trainers');
    }

    // ── File text extraction helpers ─────────────────────────────

    private function extractPdf(string $path): string
    {
        $parser = new PdfParser();
        $pdf    = $parser->parseFile($path);
        return $this->toUtf8($pdf->getText());
    }

    private function extractDocx(string $path): string
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            return '';
        }

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if ($xml === false) {
            return '';
        }

        $doc = simplexml_load_string($xml);
        if ($doc === false) {
            return $this->toUtf8(strip_tags($xml));
        }

        $doc->registerXPathNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $texts = $doc->xpath('//w:t');
        return $this->toUtf8(implode(' ', array_map(fn($t) => (string) $t, $texts)));
    }

    private function extractDoc(string $path): string
    {
        $content = file_get_contents($path);
        // Keep only printable ASCII and whitespace; strip binary content
        $content = preg_replace('/[\x00-\x08\x0b\x0c\x0e-\x1f\x7f]/', ' ', $content);
        $content = preg_replace('/[\x80-\xff]+/', ' ', $content);
        $content = preg_replace('/\s{3,}/', "\n", $content);
        return trim($content);
    }

    /**
     * Ensure text is clean, valid UTF-8 so json_encode never fails.
     * Detects common encodings (ISO-8859-1, Windows-1252) and converts,
     * then strips any remaining invalid byte sequences.
     */
    private function toUtf8(string $text): string
    {
        if ($text === '') {
            return '';
        }

        // If not valid UTF-8, try to detect encoding and convert
        if (! mb_check_encoding($text, 'UTF-8')) {
            $detected = mb_detect_encoding($text, ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'ASCII'], true);
            if ($detected && $detected !== 'UTF-8') {
                $text = mb_convert_encoding($text, 'UTF-8', $detected);
            }
        }

        // Final pass: strip any remaining invalid UTF-8 byte sequences
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');

        // Collapse excessive whitespace from binary noise
        $text = preg_replace('/[^\S\n]{4,}/', ' ', $text);
        $text = preg_replace('/\n{4,}/', "\n\n", $text);

        return trim($text);
    }

    private function arrayToText(mixed $value): string
    {
        if (is_array($value)) {
            return implode("\n", array_map('trim', array_filter($value)));
        }
        return (string) ($value ?? '');
    }
}
