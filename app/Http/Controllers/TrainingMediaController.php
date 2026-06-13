<?php

namespace App\Http\Controllers;

use App\Models\TrainingMedia;
use App\Models\TrainingSchedule;
use App\Services\OpenAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrainingMediaController extends Controller
{
    public function __construct(private OpenAIService $ai) {}

    private function guardAdmin(): void
    {
        $role = auth()->user()?->role ?? '';
        if (!in_array($role, ['admin', 'super_admin'])) abort(403);
    }

    public function index(TrainingSchedule $schedule)
    {
        $this->guardAdmin();
        $schedule->load('course');
        $media = $schedule->media()->orderBy('sort_order')->orderBy('created_at')->get()->groupBy('media_type');
        return view('training-media.index', compact('schedule', 'media'));
    }

    public function store(Request $request, TrainingSchedule $schedule)
    {
        $this->guardAdmin();

        $request->validate([
            'files'      => 'required|array|min:1|max:20',
            'files.*'    => 'required|image|max:8192|mimes:jpg,jpeg,png,webp',
            'media_type' => 'required|in:cover,gallery,group,trainer,venue,activity',
        ]);

        $type    = $request->input('media_type');
        $maxSort = $schedule->media()->where('media_type', $type)->max('sort_order') ?? 0;
        $saved   = [];

        foreach ($request->file('files') as $file) {
            $path = $file->store("training-media/{$schedule->id}", 'public');

            $media = TrainingMedia::create([
                'training_schedule_id' => $schedule->id,
                'media_type'           => $type,
                'file_path'            => $path,
                'file_name'            => $file->getClientOriginalName(),
                'file_size'            => $file->getSize(),
                'mime_type'            => $file->getMimeType(),
                'is_featured'          => false,
                'sort_order'           => ++$maxSort,
                'uploaded_by'          => auth()->id(),
            ]);

            $saved[] = [
                'id'       => $media->id,
                'url'      => asset('storage/' . $path),
                'type'     => $type,
                'name'     => $file->getClientOriginalName(),
                'size'     => $media->file_size_human,
            ];
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'media' => $saved]);
        }

        return back()->with('success', count($saved) . ' photo(s) uploaded successfully.');
    }

    public function update(Request $request, TrainingMedia $media)
    {
        $this->guardAdmin();

        $request->validate([
            'caption'         => 'nullable|string|max:500',
            'alt_text'        => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
        ]);

        $media->update($request->only(['caption', 'alt_text', 'seo_description']));

        return response()->json(['success' => true]);
    }

    public function setFeatured(TrainingMedia $media)
    {
        $this->guardAdmin();

        // Unset featured for same type on same schedule
        TrainingMedia::where('training_schedule_id', $media->training_schedule_id)
            ->where('media_type', $media->media_type)
            ->update(['is_featured' => false]);

        $media->update(['is_featured' => true]);

        return response()->json(['success' => true]);
    }

    public function reorder(Request $request, TrainingSchedule $schedule)
    {
        $this->guardAdmin();

        $request->validate(['order' => 'required|array', 'order.*' => 'integer']);

        foreach ($request->input('order') as $sort => $mediaId) {
            TrainingMedia::where('id', $mediaId)
                ->where('training_schedule_id', $schedule->id)
                ->update(['sort_order' => $sort]);
        }

        return response()->json(['success' => true]);
    }

    public function generateCaptions(Request $request, TrainingSchedule $schedule)
    {
        $this->guardAdmin();
        $schedule->load('course');

        $media = $schedule->media()->whereNull('caption')->orWhere('caption', '')->get();

        if ($media->isEmpty()) {
            return response()->json(['success' => false, 'error' => 'All photos already have captions.']);
        }

        $courseName = $schedule->course->name ?? $schedule->training_title ?? 'Training Program';
        $venue      = trim(($schedule->city ?? '') . ', ' . ($schedule->country ?? ''), ', ');
        $dates      = trim(($schedule->start_date ? \Carbon\Carbon::parse($schedule->start_date)->format('d M Y') : '') . ' – ' . ($schedule->end_date ? \Carbon\Carbon::parse($schedule->end_date)->format('d M Y') : ''), ' –');

        $typeLabels = TrainingMedia::$types;
        $updated    = 0;

        foreach ($media as $item) {
            $typeLabel = $typeLabels[$item->media_type] ?? $item->media_type;

            $prompt = <<<PROMPT
Write professional image metadata for a training event photo. Output ONLY valid JSON.

Photo type: {$typeLabel}
Training: {$courseName}
Venue: {$venue}
Dates: {$dates}

Return:
{
  "caption": "One sentence descriptive caption (max 150 chars)",
  "alt_text": "SEO alt text (max 100 chars)",
  "seo_description": "Detailed description for search engines (max 200 chars)"
}
PROMPT;

            $result = $this->ai->generateText($prompt, 'training_media_caption', auth()->id(), 200);

            if ($result['success']) {
                $raw     = preg_replace(['/^```json\s*/i', '/```\s*$/'], '', trim($result['text'] ?? ''));
                $decoded = json_decode($raw, true);
                if ($decoded) {
                    $item->update([
                        'caption'              => $decoded['caption'] ?? null,
                        'alt_text'             => $decoded['alt_text'] ?? null,
                        'seo_description'      => $decoded['seo_description'] ?? null,
                        'ai_captions_generated' => true,
                    ]);
                    $updated++;
                }
            }
        }

        return response()->json(['success' => true, 'updated' => $updated]);
    }

    public function destroy(TrainingMedia $media)
    {
        $this->guardAdmin();
        Storage::disk('public')->delete($media->file_path);
        $media->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Photo deleted.');
    }
}
