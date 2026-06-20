<?php

namespace App\Services;

use App\Models\PptCourse;
use App\Models\PptSlide;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class PptExtractionService
{
    // Namespaces used in OOXML / PPTX
    private const NS_A = 'http://schemas.openxmlformats.org/drawingml/2006/main';
    private const NS_P = 'http://schemas.openxmlformats.org/presentationml/2006/main';
    private const NS_R = 'http://schemas.openxmlformats.org/package/2006/relationships';

    public function extract(PptCourse $pptCourse): void
    {
        $filePath = Storage::disk('local')->path($pptCourse->file_path);

        if (!file_exists($filePath)) {
            $pptCourse->update(['status' => 'draft', 'processing_error' => 'Uploaded file not found on disk.']);
            return;
        }

        $zip = new ZipArchive;
        $result = $zip->open($filePath);

        if ($result !== true) {
            $pptCourse->update([
                'status'           => 'draft',
                'processing_error' => "Cannot open file as ZIP/PPTX (error code: {$result}).",
            ]);
            return;
        }

        try {
            $this->doExtract($zip, $pptCourse);
        } catch (\Throwable $e) {
            Log::error('PptExtractionService: extraction failed', [
                'ppt_course_id' => $pptCourse->id,
                'error'         => $e->getMessage(),
            ]);
            $pptCourse->update([
                'status'           => 'draft',
                'processing_error' => 'Extraction error: ' . $e->getMessage(),
            ]);
        } finally {
            $zip->close();
        }
    }

    private function doExtract(ZipArchive $zip, PptCourse $pptCourse): void
    {
        // Discover all slide XML files
        $slideEntries = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (preg_match('#^ppt/slides/(slide(\d+))\.xml$#', $name, $m)) {
                $slideEntries[] = [
                    'zip_path'   => $name,
                    'slide_name' => $m[1],       // e.g. "slide3"
                    'num'        => (int) $m[2],  // e.g. 3
                ];
            }
        }

        if (empty($slideEntries)) {
            $pptCourse->update([
                'status'           => 'draft',
                'processing_error' => 'No slides found in the uploaded file. Ensure it is a valid PPTX.',
            ]);
            return;
        }

        // Sort by the numeric slide number from the filename
        usort($slideEntries, fn($a, $b) => $a['num'] <=> $b['num']);

        $mediaDir  = "ppt-builder/{$pptCourse->id}/slides";
        $extracted = 0;

        foreach ($slideEntries as $order => $entry) {
            ['zip_path' => $zipPath, 'slide_name' => $slideName, 'num' => $slideNum] = $entry;

            $slideXml = $zip->getFromName($zipPath);
            if ($slideXml === false) continue;

            [$title, $content] = $this->parseSlideText($slideXml);

            // Speaker notes live in ppt/notesSlides/notesSlide{N}.xml
            $notesXml    = $zip->getFromName("ppt/notesSlides/notesSlide{$slideNum}.xml");
            $speakerNotes = $notesXml ? $this->parseNotesText($notesXml) : null;

            // Extract first image referenced by this slide
            $imagePath = $this->extractSlideImage($zip, $slideName, $slideNum, $mediaDir, $pptCourse->id);

            PptSlide::create([
                'ppt_course_id' => $pptCourse->id,
                'slide_number'  => $slideNum,
                'slide_order'   => $order + 1,
                'title'         => $title ?: "Slide {$slideNum}",
                'content_text'  => $content,
                'speaker_notes' => $speakerNotes,
                'image_path'    => $imagePath,
            ]);

            $extracted++;
        }

        $pptCourse->update([
            'status'       => 'ready',
            'total_slides' => $extracted,
        ]);
    }

    // ──────────────────────────────────────────────────────────
    // Parse slide XML → [title, content]
    // ──────────────────────────────────────────────────────────

    private function parseSlideText(string $xmlContent): array
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlContent);
        if ($xml === false) return ['', ''];

        $xml->registerXPathNamespace('a', self::NS_A);
        $xml->registerXPathNamespace('p', self::NS_P);

        $titleParts   = [];
        $contentParts = [];

        $shapes = $xml->xpath('//p:sp') ?: [];

        foreach ($shapes as $shape) {
            $phNodes = $shape->xpath('.//p:ph') ?: [];
            $isTitle = false;
            foreach ($phNodes as $ph) {
                $type = (string) ($ph->attributes()['type'] ?? '');
                if (in_array($type, ['title', 'ctrTitle'], true)) {
                    $isTitle = true;
                    break;
                }
            }

            $textNodes = $shape->xpath('.//a:t') ?: [];
            $shapeText = trim(implode('', array_map(fn($t) => (string) $t, $textNodes)));

            if ($shapeText === '') continue;

            if ($isTitle) {
                $titleParts[] = $shapeText;
            } else {
                $contentParts[] = $shapeText;
            }
        }

        return [
            implode(' ', $titleParts),
            implode("\n", $contentParts),
        ];
    }

    // ──────────────────────────────────────────────────────────
    // Parse notes XML → speaker notes string
    // ──────────────────────────────────────────────────────────

    private function parseNotesText(string $xmlContent): ?string
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlContent);
        if ($xml === false) return null;

        $xml->registerXPathNamespace('a', self::NS_A);
        $xml->registerXPathNamespace('p', self::NS_P);

        $textNodes = $xml->xpath('//a:t') ?: [];
        $text      = trim(implode(' ', array_map(fn($t) => (string) $t, $textNodes)));

        // Remove "Click to edit Master text styles" boilerplate that appears from slide master
        $text = preg_replace('/Click to edit (?:Master )?(?:title|text)\s*styles?/i', '', $text);
        $text = trim($text);

        return $text !== '' ? $text : null;
    }

    // ──────────────────────────────────────────────────────────
    // Extract first image for a slide into public storage
    // ──────────────────────────────────────────────────────────

    private function extractSlideImage(
        ZipArchive $zip,
        string     $slideName,
        int        $slideNum,
        string     $mediaDir,
        int        $courseId
    ): ?string {
        // Slide rels file: ppt/slides/_rels/slide{N}.xml.rels
        $relsPath = "ppt/slides/_rels/{$slideName}.xml.rels";
        $relsXml  = $zip->getFromName($relsPath);

        if ($relsXml === false) return null;

        libxml_use_internal_errors(true);
        $rels = simplexml_load_string($relsXml);
        if ($rels === false) return null;

        $rels->registerXPathNamespace('r', self::NS_R);

        // Find first image relationship
        foreach ($rels->children() as $rel) {
            $type   = (string) ($rel->attributes()['Type'] ?? '');
            $target = (string) ($rel->attributes()['Target'] ?? '');

            if (!str_contains($type, '/image')) continue;

            // Target is relative to ppt/slides/ → resolve to ppt/...
            $resolvedPath = 'ppt/slides/' . $target;
            // Normalise /../ segments
            $resolvedPath = $this->normalisePath($resolvedPath);

            $imageData = $zip->getFromName($resolvedPath);
            if ($imageData === false) continue;

            $ext = strtolower(pathinfo($resolvedPath, PATHINFO_EXTENSION));
            if (!in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp'], true)) {
                $ext = 'png';
            }

            $storagePath = "{$mediaDir}/slide-{$slideNum}.{$ext}";
            Storage::disk('public')->put($storagePath, $imageData);

            return $storagePath;
        }

        return null;
    }

    // ──────────────────────────────────────────────────────────
    // Resolve path with ../ segments
    // ──────────────────────────────────────────────────────────

    private function normalisePath(string $path): string
    {
        $parts  = explode('/', $path);
        $result = [];

        foreach ($parts as $part) {
            if ($part === '' || $part === '.') continue;
            if ($part === '..') {
                array_pop($result);
            } else {
                $result[] = $part;
            }
        }

        return implode('/', $result);
    }
}
