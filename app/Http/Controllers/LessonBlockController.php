<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ElearningLesson;
use App\Models\LessonBlock;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class LessonBlockController extends Controller
{
    // ── Store a new block ─────────────────────────────────

    public function store(Request $request, Course $course, ElearningLesson $lesson): RedirectResponse
    {
        $request->validate([
            'block_type' => ['required', 'string', 'in:' . implode(',', array_keys(LessonBlock::TYPES))],
            'title'      => ['nullable', 'string', 'max:255'],
        ]);

        $maxOrder = $lesson->allBlocks()->max('sort_order') ?? -1;

        $block = new LessonBlock([
            'lesson_id'  => $lesson->id,
            'block_type' => $request->block_type,
            'title'      => $request->title,
            'status'     => 'active',   // always active on creation — no user input needed
            'sort_order' => $maxOrder + 1,
        ]);

        $block->content      = $this->processContent($request);
        $block->media_path   = $request->media_path;
        $block->settings_json= $this->processSettings($request);
        $block->save();

        return redirect()
            ->route('elearning.lessons.edit', [$course, $lesson])
            ->with('success', '✅ Block "' . $block->getTypeLabel() . '" added successfully.');
    }

    // ── Update an existing block ──────────────────────────

    public function update(Request $request, Course $course, ElearningLesson $lesson, LessonBlock $block): RedirectResponse
    {
        $request->validate([
            'title'  => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $block->title         = $request->title;
        $block->status        = $request->input('status', $block->status);
        $block->content       = $this->processContent($request);
        $block->media_path    = $request->media_path ?? $block->media_path;
        $block->settings_json = $this->processSettings($request);
        $block->save();

        return redirect()
            ->route('elearning.lessons.edit', [$course, $lesson])
            ->with('success', '✅ Block updated.');
    }

    // ── Delete a block ────────────────────────────────────

    public function destroy(Course $course, ElearningLesson $lesson, LessonBlock $block): RedirectResponse
    {
        $block->delete();
        $this->renormalizeOrder($lesson);

        return redirect()
            ->route('elearning.lessons.edit', [$course, $lesson])
            ->with('success', 'Block deleted.');
    }

    // ── Move up (lower sort_order) ────────────────────────

    public function moveUp(Course $course, ElearningLesson $lesson, LessonBlock $block): RedirectResponse
    {
        $prev = $lesson->allBlocks()
            ->where('sort_order', '<', $block->sort_order)
            ->orderBy('sort_order', 'desc')
            ->first();

        if ($prev) {
            [$block->sort_order, $prev->sort_order] = [$prev->sort_order, $block->sort_order];
            $block->save();
            $prev->save();
        }

        return redirect()->route('elearning.lessons.edit', [$course, $lesson]);
    }

    // ── Move down (higher sort_order) ─────────────────────

    public function moveDown(Course $course, ElearningLesson $lesson, LessonBlock $block): RedirectResponse
    {
        $next = $lesson->allBlocks()
            ->where('sort_order', '>', $block->sort_order)
            ->orderBy('sort_order')
            ->first();

        if ($next) {
            [$block->sort_order, $next->sort_order] = [$next->sort_order, $block->sort_order];
            $block->save();
            $next->save();
        }

        return redirect()->route('elearning.lessons.edit', [$course, $lesson]);
    }

    // ── Helpers ───────────────────────────────────────────

    /**
     * Build the content value from request based on block_type.
     * Simple types: content field directly.
     * JSON types: build array from repeater fields and encode.
     */
    private function processContent(Request $request): ?string
    {
        $type = $request->block_type ?? $request->route('block')?->block_type;

        return match ($type) {

            // JSON array types ─────────────────────────────
            'accordion'         => $this->buildAccordionJson($request),
            'gallery'           => $this->buildGalleryJson($request),
            'slides'            => $this->buildSlidesJson($request),
            'download'          => $this->buildDownloadJson($request),
            'knowledge_check'   => $this->buildKnowledgeCheckJson($request),
            'scenario'          => $this->buildScenarioJson($request),
            'matching'          => $this->buildMatchingJson($request),
            'fun_fact'          => $this->buildFunFactJson($request),
            'reflection'        => $this->buildReflectionJson($request),
            'click_reveal'      => $this->buildClickRevealJson($request),
            'myth_fact'         => $this->buildMythFactJson($request),
            'workplace_example' => $this->buildWorkplaceExampleJson($request),
            'case_study'        => $this->buildCaseStudyJson($request),

            // Flat content ─────────────────────────────────
            default     => $request->content,
        };
    }

    private function processSettings(Request $request): ?array
    {
        $settings = [];

        if ($request->filled('caption'))          $settings['caption']           = $request->caption;
        if ($request->filled('allow_download'))   $settings['allow_download']    = (bool) $request->allow_download;
        if ($request->filled('autoplay'))         $settings['autoplay']          = (bool) $request->autoplay;

        return empty($settings) ? null : $settings;
    }

    // ── JSON builders for complex block types ─────────────

    private function buildAccordionJson(Request $request): string
    {
        $titles   = $request->input('accordion_title', []);
        $contents = $request->input('accordion_content', []);
        $items = [];
        foreach ($titles as $i => $title) {
            if (trim($title) !== '') {
                $items[] = ['title' => $title, 'body' => $contents[$i] ?? ''];
            }
        }
        return json_encode($items);
    }

    private function buildGalleryJson(Request $request): string
    {
        $urls     = $request->input('gallery_url', []);
        $captions = $request->input('gallery_caption', []);
        $items = [];
        foreach ($urls as $i => $url) {
            if (trim($url) !== '') {
                $items[] = ['url' => $url, 'caption' => $captions[$i] ?? ''];
            }
        }
        return json_encode($items);
    }

    private function buildSlidesJson(Request $request): string
    {
        $titles    = $request->input('slide_title', []);
        $texts     = $request->input('slide_text', []);
        $imageUrls = $request->input('slide_image_url', []);
        $items = [];
        foreach ($titles as $i => $title) {
            if (trim($title) !== '' || trim($texts[$i] ?? '') !== '' || trim($imageUrls[$i] ?? '') !== '') {
                $items[] = [
                    'title'     => $title,
                    'text'      => $texts[$i] ?? '',
                    'image_url' => $imageUrls[$i] ?? '',
                ];
            }
        }
        return json_encode($items);
    }

    private function buildDownloadJson(Request $request): string
    {
        $titles = $request->input('dl_title', []);
        $urls   = $request->input('dl_url', []);
        $types  = $request->input('dl_type', []);
        $items = [];
        foreach ($titles as $i => $title) {
            if (trim($title) !== '') {
                $items[] = ['title' => $title, 'url' => $urls[$i] ?? '', 'type' => $types[$i] ?? 'file'];
            }
        }
        return json_encode($items);
    }

    private function buildKnowledgeCheckJson(Request $request): string
    {
        $optionTexts   = $request->input('kc_option_text', []);
        $correctFlags  = $request->input('kc_correct', []);
        $options = [];
        foreach ($optionTexts as $i => $text) {
            if (trim($text) !== '') {
                $options[] = ['text' => $text, 'correct' => in_array((string)$i, (array)$correctFlags)];
            }
        }
        return json_encode([
            'question'    => $request->kc_question ?? '',
            'type'        => $request->kc_type ?? 'single',
            'options'     => $options,
            'explanation' => $request->kc_explanation ?? '',
        ]);
    }

    private function buildScenarioJson(Request $request): string
    {
        $texts         = $request->input('sc_option_text', []);
        $explanations  = $request->input('sc_option_explanation', []);
        $correctFlags  = $request->input('sc_correct', []);
        $options = [];
        foreach ($texts as $i => $text) {
            if (trim($text) !== '') {
                $options[] = [
                    'text'        => $text,
                    'explanation' => $explanations[$i] ?? '',
                    'correct'     => in_array((string)$i, (array)$correctFlags),
                ];
            }
        }
        return json_encode([
            'text'    => $request->sc_text ?? '',
            'options' => $options,
        ]);
    }

    private function buildMatchingJson(Request $request): string
    {
        $lefts  = $request->input('match_left', []);
        $rights = $request->input('match_right', []);
        $pairs = [];
        foreach ($lefts as $i => $left) {
            if (trim($left) !== '') {
                $pairs[] = ['left' => $left, 'right' => $rights[$i] ?? ''];
            }
        }
        return json_encode(['pairs' => $pairs]);
    }

    private function buildFunFactJson(Request $request): string
    {
        return json_encode([
            'icon'    => $request->input('ff_icon', '💡'),
            'title'   => $request->input('ff_title', 'Did You Know?'),
            'content' => $request->input('ff_content', ''),
        ]);
    }

    private function buildReflectionJson(Request $request): string
    {
        $questions = array_filter(array_map('trim', $request->input('ref_questions', [])));
        return json_encode([
            'prompt'    => $request->input('ref_prompt', ''),
            'questions' => array_values($questions),
        ]);
    }

    private function buildClickRevealJson(Request $request): string
    {
        return json_encode([
            'question'    => $request->input('cr_question', ''),
            'answer'      => $request->input('cr_answer', ''),
            'explanation' => $request->input('cr_explanation', ''),
        ]);
    }

    private function buildMythFactJson(Request $request): string
    {
        return json_encode([
            'myth' => $request->input('mf_myth', ''),
            'fact' => $request->input('mf_fact', ''),
        ]);
    }

    private function buildWorkplaceExampleJson(Request $request): string
    {
        $contexts  = $request->input('we_context', []);
        $descs     = $request->input('we_description', []);
        $examples  = [];
        foreach ($contexts as $i => $ctx) {
            if (trim($ctx) !== '') {
                $examples[] = ['context' => $ctx, 'description' => $descs[$i] ?? ''];
            }
        }
        return json_encode(['examples' => $examples]);
    }

    private function buildCaseStudyJson(Request $request): string
    {
        $questions = array_filter(array_map('trim', $request->input('cs_questions', [])));
        return json_encode([
            'case_description'  => $request->input('cs_case', ''),
            'questions'         => array_values($questions),
            'expected_response' => $request->input('cs_response', ''),
        ]);
    }

    private function renormalizeOrder(ElearningLesson $lesson): void
    {
        $lesson->allBlocks()->orderBy('sort_order')->get()
            ->each(function ($block, $i) {
                $block->update(['sort_order' => $i]);
            });
    }
}
