<?php

namespace App\Services;

use App\Models\Course;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class CourseImageService
{
    // Target output dimensions
    const COVER_W = 1200;
    const COVER_H = 675;
    const THUMB_W = 600;
    const THUMB_H = 338;
    const WEBP_QUALITY = 75;

    // DALL-E 3 cost per image (standard 1792×1024)
    const DALLE_COST_USD = 0.080;

    // Category → theme + color palette presets
    private static array $categoryPresets = [
        'Health & Wellbeing' => [
            'theme'  => 'workplace wellbeing, calm professional environment, healthy work culture, mindfulness at work',
            'colors' => 'calming blue and soft purple',
        ],
        'Wellbeing' => [
            'theme'  => 'wellness, workplace wellbeing, calm professional environment',
            'colors' => 'soft blue and purple',
        ],
        'HR'  => [
            'theme'  => 'professional HR workplace discussion, employee engagement, team meeting, workforce management',
            'colors' => 'professional blue and teal',
        ],
        'Compliance' => [
            'theme'  => 'corporate compliance, professional document review, policy adherence',
            'colors' => 'trustworthy blue and teal',
        ],
        'Environmental' => [
            'theme'  => 'environmental sustainability, green workplace, eco-conscious management, nature and industry',
            'colors' => 'fresh green and blue',
        ],
        'Auditor' => [
            'theme'  => 'professional auditor conducting systematic review, clipboard and checklist, quality assurance inspection',
            'colors' => 'deep navy and gold',
        ],
        'Lead Auditor' => [
            'theme'  => 'lead auditor directing an audit team, senior professional reviewing systems, authority and expertise',
            'colors' => 'deep navy and gold',
        ],
        'Social Compliance' => [
            'theme'  => 'ethical workplace, fair labor practices, worker wellbeing, management and worker engagement',
            'colors' => 'trustworthy blue and warm teal',
        ],
        'Food Safety' => [
            'theme'  => 'food production quality control, hygienic food processing environment, clean food safety standards',
            'colors' => 'fresh green and warm orange',
        ],
        'Food' => [
            'theme'  => 'food quality assurance, safe food production, hygiene in food industry',
            'colors' => 'green and orange',
        ],
        'Security' => [
            'theme'  => 'supply chain security, secure logistics, customs inspection, cargo protection',
            'colors' => 'dark navy blue and bold orange',
        ],
        'Supply Chain' => [
            'theme'  => 'global supply chain management, logistics coordination, international trade flow',
            'colors' => 'dark navy blue and orange',
        ],
        'Safety' => [
            'theme'  => 'workplace safety, occupational health, protective work environment',
            'colors' => 'safety yellow and dark navy',
        ],
        'Quality' => [
            'theme'  => 'quality management systems, process improvement, certification and standards',
            'colors' => 'corporate blue and silver',
        ],
        'Management' => [
            'theme'  => 'business management, professional leadership, strategic planning',
            'colors' => 'deep navy and gold',
        ],
        'Leadership' => [
            'theme'  => 'professional leadership development, executive coaching, team direction',
            'colors' => 'deep navy and warm gold',
        ],
    ];

    // ── Public API ──────────────────────────────────────────────────

    public static function buildPrompt(Course $course, string $style = 'modern', string $complexity = 'standard'): string
    {
        $courseName  = $course->name;
        $courseType  = $course->delivery_type ?? $course->course_type ?? 'professional training';
        $categoryName = $course->category ?? $course->categoryRelation?->name ?? '';
        $description = strip_tags($course->short_description ?? $course->description ?? '');
        $description = substr($description, 0, 200);

        [$theme, $colors] = self::resolvePreset($categoryName, $description);

        $styleNote = match ($style) {
            'corporate' => 'Corporate flat illustration. Business-first environment. Formal professional tones.',
            'premium'   => 'Premium modern illustration. Sophisticated colour use. High-end eLearning platform quality.',
            default     => 'Modern flat vector illustration. Clean lines. Contemporary professional look.',
        };

        $complexityNote = match ($complexity) {
            'simple'  => 'Very simple composition. Single person OR one key symbolic object. Maximum white space. Minimal elements.',
            'premium' => 'Rich layered composition. Subtle environment context. Professional visual depth. Still clean and uncluttered.',
            default   => 'Clean balanced composition. One or two people. Clear central subject. Generous breathing space.',
        };

        return <<<PROMPT
Create a premium corporate training course illustration for a website course card.

STYLE: {$styleNote}

STRICT FORMAT REQUIREMENTS:
- 16:9 landscape orientation
- NO text, labels, numbers, or watermarks anywhere in the image
- NO logos or brand marks
- NO realistic or detailed faces — use simple stylised flat character illustrations only
- NO stock-photo appearance
- Professional international business environment

THUMBNAIL-FIRST DESIGN — this image will display at 450px wide on a website card:
- One immediately recognisable focal subject
- Maximum 2 people if people are shown
- Large, bold visual elements that read clearly at small sizes
- Generous white space and breathing room
- Clean, uncluttered composition

STRICTLY AVOID:
- Infographics, charts, graphs, or data visualisations
- Computer dashboards, multiple screens, or interface mockups
- Many small icons scattered across the image
- Crowded scenes with many elements
- Presentation slides or PowerPoint-style layouts
- Text blocks or speech bubbles

COURSE CONTEXT:
Course Name: {$courseName}
Training Type: {$courseType}
Theme: {$theme}

COLOUR PALETTE: {$colors}

COMPOSITION GUIDANCE: {$complexityNote}

OUTPUT: Single professional website course cover illustration only. No collages, no multiple panels.
PROMPT;
    }

    // ── Image Generation via DALL-E 3 ───────────────────────────────

    public static function callDalle3(string $prompt): array
    {
        $response = Http::withToken(config('ai.openai_key'))
            ->timeout(90)
            ->post('https://api.openai.com/v1/images/generations', [
                'model'           => 'dall-e-3',
                'prompt'          => $prompt,
                'n'               => 1,
                'size'            => '1792x1024',
                'quality'         => 'standard',
                'response_format' => 'url',
            ]);

        if (!$response->successful()) {
            $errMsg = $response->json('error.message') ?? 'DALL-E 3 API error';
            throw new \RuntimeException("Image generation failed: {$errMsg}");
        }

        $imageUrl      = $response->json('data.0.url');
        $revisedPrompt = $response->json('data.0.revised_prompt') ?? $prompt;

        return [$imageUrl, $revisedPrompt];
    }

    // ── Image Processing (download → resize → WebP) ─────────────────

    public static function downloadAndProcess(string $imageUrl, int $courseId): array
    {
        $rawData = Http::timeout(60)->get($imageUrl)->body();

        if (empty($rawData)) {
            throw new \RuntimeException('Failed to download generated image');
        }

        $source = @imagecreatefromstring($rawData);
        if (!$source) {
            throw new \RuntimeException('Could not decode downloaded image');
        }

        $srcW = imagesx($source);
        $srcH = imagesy($source);

        // Generate cover (1200×675)
        $cover = imagecreatetruecolor(self::COVER_W, self::COVER_H);
        imagecopyresampled($cover, $source, 0, 0, 0, 0, self::COVER_W, self::COVER_H, $srcW, $srcH);

        // Generate thumbnail (600×338)
        $thumb = imagecreatetruecolor(self::THUMB_W, self::THUMB_H);
        imagecopyresampled($thumb, $source, 0, 0, 0, 0, self::THUMB_W, self::THUMB_H, $srcW, $srcH);

        imagedestroy($source);

        // Save as WebP
        $coverPath = "course-covers/course_{$courseId}_cover.webp";
        $thumbPath = "course-covers/course_{$courseId}_thumb.webp";

        ob_start();
        imagewebp($cover, null, self::WEBP_QUALITY);
        $coverData = ob_get_clean();
        imagedestroy($cover);

        ob_start();
        imagewebp($thumb, null, self::WEBP_QUALITY);
        $thumbData = ob_get_clean();
        imagedestroy($thumb);

        Storage::disk('public')->put($coverPath, $coverData);
        Storage::disk('public')->put($thumbPath, $thumbData);

        return [$coverPath, $thumbPath];
    }

    // ── Upload + Process a custom file ──────────────────────────────

    public static function processUpload(string $tmpPath, int $courseId): array
    {
        $rawData = file_get_contents($tmpPath);
        $source  = @imagecreatefromstring($rawData);
        if (!$source) {
            throw new \RuntimeException('Could not decode uploaded image');
        }

        $srcW = imagesx($source);
        $srcH = imagesy($source);

        $cover = imagecreatetruecolor(self::COVER_W, self::COVER_H);
        imagecopyresampled($cover, $source, 0, 0, 0, 0, self::COVER_W, self::COVER_H, $srcW, $srcH);

        $thumb = imagecreatetruecolor(self::THUMB_W, self::THUMB_H);
        imagecopyresampled($thumb, $source, 0, 0, 0, 0, self::THUMB_W, self::THUMB_H, $srcW, $srcH);

        imagedestroy($source);

        $coverPath = "course-covers/course_{$courseId}_cover.webp";
        $thumbPath = "course-covers/course_{$courseId}_thumb.webp";

        ob_start(); imagewebp($cover, null, self::WEBP_QUALITY); $coverData = ob_get_clean();
        ob_start(); imagewebp($thumb, null, self::WEBP_QUALITY); $thumbData = ob_get_clean();
        imagedestroy($cover);
        imagedestroy($thumb);

        Storage::disk('public')->put($coverPath, $coverData);
        Storage::disk('public')->put($thumbPath, $thumbData);

        return [$coverPath, $thumbPath];
    }

    // ── Delete stored files ─────────────────────────────────────────

    public static function deleteFiles(Course $course): void
    {
        if ($course->cover_image)     Storage::disk('public')->delete($course->cover_image);
        if ($course->cover_thumbnail) Storage::disk('public')->delete($course->cover_thumbnail);
    }

    // ── Helpers ────────────────────────────────────────────────────

    private static function resolvePreset(string $categoryName, string $description): array
    {
        foreach (self::$categoryPresets as $key => $preset) {
            if (stripos($categoryName, $key) !== false || stripos($key, $categoryName) !== false) {
                return [$preset['theme'], $preset['colors']];
            }
        }
        // Fallback: derive from description keywords
        $lower = strtolower($categoryName . ' ' . $description);
        if (str_contains($lower, 'food'))        return [self::$categoryPresets['Food Safety']['theme'],   self::$categoryPresets['Food Safety']['colors']];
        if (str_contains($lower, 'audit'))       return [self::$categoryPresets['Auditor']['theme'],       self::$categoryPresets['Auditor']['colors']];
        if (str_contains($lower, 'safety'))      return [self::$categoryPresets['Safety']['theme'],        self::$categoryPresets['Safety']['colors']];
        if (str_contains($lower, 'environment')) return [self::$categoryPresets['Environmental']['theme'], self::$categoryPresets['Environmental']['colors']];
        if (str_contains($lower, 'quality'))     return [self::$categoryPresets['Quality']['theme'],       self::$categoryPresets['Quality']['colors']];

        return [
            'professional corporate training, workplace skill development, business learning environment',
            'professional blue and grey',
        ];
    }
}
