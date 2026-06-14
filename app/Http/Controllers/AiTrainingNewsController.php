<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Enrollment;
use App\Models\TrainingSchedule;
use App\Services\OpenAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiTrainingNewsController extends Controller
{
    public function __construct(private OpenAIService $ai) {}

    private function guardAdmin(): void
    {
        $role = auth()->user()?->role ?? '';
        if (!in_array($role, ['admin', 'super_admin'])) {
            abort(403);
        }
    }

    // ── Index: all training news articles ────────────────────
    public function index(Request $request)
    {
        $this->guardAdmin();

        $query = BlogPost::with(['trainingSchedule.course', 'category', 'approvedBy'])
            ->whereIn('article_type', [
                BlogPost::TYPE_TRAINING_NEWS,
                BlogPost::TYPE_SUCCESS_STORY,
                BlogPost::TYPE_ANNOUNCEMENT,
            ])
            ->latest('updated_at');

        if ($request->filled('q')) {
            $query->where('title', 'like', '%' . $request->q . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('article_type', $request->type);
        }

        $articles = $query->paginate(20)->withQueryString();

        // Stats for header tiles
        $stats = [
            'total'     => BlogPost::whereIn('article_type', [BlogPost::TYPE_TRAINING_NEWS, BlogPost::TYPE_SUCCESS_STORY, BlogPost::TYPE_ANNOUNCEMENT])->count(),
            'published' => BlogPost::whereIn('article_type', [BlogPost::TYPE_TRAINING_NEWS, BlogPost::TYPE_SUCCESS_STORY, BlogPost::TYPE_ANNOUNCEMENT])->where('status', BlogPost::STATUS_PUBLISHED)->count(),
            'draft'     => BlogPost::whereIn('article_type', [BlogPost::TYPE_TRAINING_NEWS, BlogPost::TYPE_SUCCESS_STORY, BlogPost::TYPE_ANNOUNCEMENT])->where('status', BlogPost::STATUS_DRAFT)->count(),
            'review'    => BlogPost::whereIn('article_type', [BlogPost::TYPE_TRAINING_NEWS, BlogPost::TYPE_SUCCESS_STORY, BlogPost::TYPE_ANNOUNCEMENT])->where('status', BlogPost::STATUS_UNDER_REVIEW)->count(),
            'views'     => BlogPost::whereIn('article_type', [BlogPost::TYPE_TRAINING_NEWS, BlogPost::TYPE_SUCCESS_STORY, BlogPost::TYPE_ANNOUNCEMENT])->sum('view_count'),
        ];

        // Completed schedules without a news article yet
        $pending = TrainingSchedule::with('course')
            ->where('schedule_status', 'Completed')
            ->whereDoesntHave('newsArticles', fn($q) => $q->whereIn('article_type', [BlogPost::TYPE_TRAINING_NEWS, BlogPost::TYPE_SUCCESS_STORY, BlogPost::TYPE_ANNOUNCEMENT]))
            ->orderByDesc('end_date')
            ->limit(5)
            ->get();

        return view('training-news.index', compact('articles', 'stats', 'pending'));
    }

    // ── Analytics dashboard ───────────────────────────────────
    public function analytics()
    {
        $this->guardAdmin();

        $byStatus = BlogPost::whereIn('article_type', [BlogPost::TYPE_TRAINING_NEWS, BlogPost::TYPE_SUCCESS_STORY, BlogPost::TYPE_ANNOUNCEMENT])
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $byType = BlogPost::whereIn('article_type', [BlogPost::TYPE_TRAINING_NEWS, BlogPost::TYPE_SUCCESS_STORY, BlogPost::TYPE_ANNOUNCEMENT])
            ->select('article_type', DB::raw('count(*) as total'))
            ->groupBy('article_type')
            ->pluck('total', 'article_type');

        $topArticles = BlogPost::whereIn('article_type', [BlogPost::TYPE_TRAINING_NEWS, BlogPost::TYPE_SUCCESS_STORY, BlogPost::TYPE_ANNOUNCEMENT])
            ->where('status', BlogPost::STATUS_PUBLISHED)
            ->orderByDesc('view_count')
            ->limit(10)
            ->get();

        $recentlyPublished = BlogPost::whereIn('article_type', [BlogPost::TYPE_TRAINING_NEWS, BlogPost::TYPE_SUCCESS_STORY, BlogPost::TYPE_ANNOUNCEMENT])
            ->where('status', BlogPost::STATUS_PUBLISHED)
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        $totalViews = BlogPost::whereIn('article_type', [BlogPost::TYPE_TRAINING_NEWS, BlogPost::TYPE_SUCCESS_STORY, BlogPost::TYPE_ANNOUNCEMENT])->sum('view_count');

        $byCategory = BlogPost::whereIn('article_type', [BlogPost::TYPE_TRAINING_NEWS, BlogPost::TYPE_SUCCESS_STORY, BlogPost::TYPE_ANNOUNCEMENT])
            ->with('category')
            ->select('blog_category_id', DB::raw('count(*) as total'), DB::raw('sum(view_count) as views'))
            ->groupBy('blog_category_id')
            ->orderByDesc('views')
            ->limit(8)
            ->get();

        $aiGenerated = BlogPost::whereIn('article_type', [BlogPost::TYPE_TRAINING_NEWS, BlogPost::TYPE_SUCCESS_STORY, BlogPost::TYPE_ANNOUNCEMENT])
            ->where('ai_generated', true)->count();

        $monthlyPublished = BlogPost::whereIn('article_type', [BlogPost::TYPE_TRAINING_NEWS, BlogPost::TYPE_SUCCESS_STORY, BlogPost::TYPE_ANNOUNCEMENT])
            ->where('status', BlogPost::STATUS_PUBLISHED)
            ->where('published_at', '>=', now()->subMonths(6))
            ->select(DB::raw("DATE_FORMAT(published_at, '%Y-%m') as month"), DB::raw('count(*) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        return view('training-news.analytics', compact(
            'byStatus', 'byType', 'topArticles', 'recentlyPublished',
            'totalViews', 'byCategory', 'aiGenerated', 'monthlyPublished'
        ));
    }

    // ── Create: AI generation page for a schedule ─────────────
    public function create(TrainingSchedule $schedule)
    {
        $this->guardAdmin();

        if ($schedule->schedule_status !== 'Completed') {
            return back()->with('error', 'News articles can only be generated for Completed training schedules.');
        }

        $data        = $this->collectScheduleData($schedule);
        $categories  = BlogCategory::orderBy('name')->get();
        $existingArticles = BlogPost::where('training_schedule_id', $schedule->id)
            ->whereIn('article_type', [BlogPost::TYPE_TRAINING_NEWS, BlogPost::TYPE_SUCCESS_STORY, BlogPost::TYPE_ANNOUNCEMENT])
            ->get();

        return view('training-news.create', compact('schedule', 'data', 'categories', 'existingArticles'));
    }

    // ── AJAX: Generate article ────────────────────────────────
    public function generateArticle(Request $request, TrainingSchedule $schedule)
    {
        $this->guardAdmin();

        $articleType = $request->input('article_type', BlogPost::TYPE_TRAINING_NEWS);
        $data        = $this->collectScheduleData($schedule);

        $prompt = $this->buildArticlePrompt($data, $articleType, (string) ($request->input('instructions') ?? ''));

        $result = $this->ai->generateText($prompt, 'training_news_article', auth()->id(), 3000);

        if (!$result['success']) {
            return response()->json(['success' => false, 'error' => $result['error']]);
        }

        $raw     = preg_replace(['/^```json\s*/i', '/```\s*$/'], '', trim($result['text'] ?? ''));
        $decoded = json_decode($raw, true);

        if (!$decoded || empty($decoded['title'])) {
            return response()->json(['success' => false, 'error' => 'AI returned invalid content. Please try again.']);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'title'        => $decoded['title'] ?? '',
                'excerpt'      => $decoded['excerpt'] ?? '',
                'content'      => $decoded['content'] ?? '',
                'suggested_slug' => BlogPost::generateSlug($decoded['title'] ?? 'training-news'),
            ],
        ]);
    }

    // ── AJAX: Generate SEO ────────────────────────────────────
    public function generateSeo(Request $request)
    {
        $this->guardAdmin();

        $title   = $request->input('title', '');
        $content = $request->input('content', '');
        $data    = $request->input('schedule_data', []);

        $prompt = <<<PROMPT
You are an SEO specialist for a professional training company. Output ONLY valid JSON.

Generate complete SEO metadata for this training news article:
Title: {$title}
Content excerpt: {$this->truncate($content, 600)}
Training context: {$this->formatScheduleDataForPrompt($data)}

Return ONLY a JSON object:
{
  "seo_title": "...",
  "seo_description": "160 chars max meta description",
  "og_title": "...",
  "og_description": "...",
  "focus_keywords": "keyword1, keyword2, keyword3, keyword4, keyword5",
  "tags": ["tag1", "tag2", "tag3", "tag4", "tag5"],
  "slug": "url-friendly-slug"
}

Rules:
- seo_title: 55-65 chars, include main keyword
- seo_description: 150-160 chars, compelling, include keyword
- focus_keywords: ISO standards, audit terms, location, training type
- tags: 5-8 relevant tags (ISO 9001, Internal Auditor, Dhaka, etc.)
- slug: lowercase, hyphens only
PROMPT;

        $result = $this->ai->generateText($prompt, 'training_news_seo', auth()->id(), 800);

        if (!$result['success']) {
            return response()->json(['success' => false, 'error' => $result['error']]);
        }

        $raw     = preg_replace(['/^```json\s*/i', '/```\s*$/'], '', trim($result['text'] ?? ''));
        $decoded = json_decode($raw, true);

        return response()->json(['success' => true, 'data' => $decoded ?? []]);
    }

    // ── AJAX: Generate Social Media ───────────────────────────
    public function generateSocial(Request $request)
    {
        $this->guardAdmin();

        $title   = $request->input('title', '');
        $excerpt = $request->input('excerpt', '');
        $data    = $request->input('schedule_data', []);

        $prompt = <<<PROMPT
You are a professional social media manager for a training company. Output ONLY valid JSON.

Create social media posts for this training news:
Title: {$title}
Summary: {$excerpt}
Training: {$this->formatScheduleDataForPrompt($data)}

Return ONLY this JSON:
{
  "linkedin": "Professional LinkedIn post (150-300 words). Formal tone. Include key achievements, participant count, trainer highlight, and company positioning. End with CTA.",
  "facebook": "Engaging Facebook post (100-200 words). Warm, celebratory tone. Include emojis. Highlight participants and achievements.",
  "twitter": "Twitter/X post (max 270 chars). Punchy and professional. Include 2-3 hashtags.",
  "instagram": "Instagram caption (100-150 words). Visual storytelling tone. Include emojis. Strong CTA. End with hashtags.",
  "hashtags": "#Training #ISO9001 #ProfessionalDevelopment #Audit #SMS (10-15 relevant hashtags as a single string)"
}
PROMPT;

        $result = $this->ai->generateText($prompt, 'training_news_social', auth()->id(), 1500);

        if (!$result['success']) {
            return response()->json(['success' => false, 'error' => $result['error']]);
        }

        $raw     = preg_replace(['/^```json\s*/i', '/```\s*$/'], '', trim($result['text'] ?? ''));
        $decoded = json_decode($raw, true);

        return response()->json(['success' => true, 'data' => $decoded ?? []]);
    }

    // ── Store: save article ───────────────────────────────────
    public function store(Request $request, TrainingSchedule $schedule)
    {
        $this->guardAdmin();

        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'excerpt'        => 'required|string|max:500',
            'content'        => 'required|string',
            'article_type'   => 'required|in:training_news,success_story,course_announcement',
            'blog_category_id' => 'nullable|exists:blog_categories,id',
            'seo_title'      => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'og_title'       => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'focus_keywords' => 'nullable|string',
            'tags'           => 'nullable|string',
            'social_linkedin'  => 'nullable|string',
            'social_facebook'  => 'nullable|string',
            'social_twitter'   => 'nullable|string',
            'social_instagram' => 'nullable|string',
            'hashtags'       => 'nullable|string',
            'slug'           => 'nullable|string|max:255',
            'featured_image' => 'nullable|image|max:4096',
            'ai_generated'   => 'nullable|boolean',
        ]);

        // Handle featured image upload
        $imagePath = null;
        if ($request->hasFile('featured_image')) {
            $imagePath = $request->file('featured_image')->store('blog', 'public');
        } else {
            // Use cover photo from training media as default
            $cover = $schedule->media()->where('media_type', 'cover')->first()
                  ?? $schedule->media()->where('is_featured', true)->first()
                  ?? $schedule->media()->first();
            if ($cover) {
                $imagePath = $cover->file_path;
            }
        }

        // Auto-create "Training News" category if none selected
        if (empty($validated['blog_category_id'])) {
            $cat = BlogCategory::firstOrCreate(
                ['slug' => 'training-news'],
                ['name' => 'Training News', 'color' => '#1e3a8a']
            );
            $validated['blog_category_id'] = $cat->id;
        }

        $slug = $validated['slug']
            ? Str::slug($validated['slug'])
            : BlogPost::generateSlug($validated['title']);

        // Ensure slug unique
        $base = $slug;
        $n    = 1;
        while (BlogPost::where('slug', $slug)->exists()) {
            $slug = "$base-$n";
            $n++;
        }

        // Parse tags JSON string
        $tags = null;
        if (!empty($validated['tags'])) {
            $decoded = json_decode($validated['tags'], true);
            $tags    = is_array($decoded) ? $decoded : array_map('trim', explode(',', $validated['tags']));
        }

        $article = BlogPost::create([
            'title'              => $validated['title'],
            'slug'               => $slug,
            'excerpt'            => $validated['excerpt'],
            'content'            => $validated['content'],
            'article_type'       => $validated['article_type'],
            'blog_category_id'   => $validated['blog_category_id'],
            'training_schedule_id' => $schedule->id,
            'course_id'          => $schedule->course_id,
            'featured_image'     => $imagePath,
            'seo_title'          => $validated['seo_title'] ?? $validated['title'],
            'seo_description'    => $validated['seo_description'] ?? $validated['excerpt'],
            'og_title'           => $validated['og_title'] ?? $validated['title'],
            'og_description'     => $validated['og_description'] ?? $validated['excerpt'],
            'focus_keywords'     => $validated['focus_keywords'],
            'tags'               => $tags,
            'social_linkedin'    => $validated['social_linkedin'],
            'social_facebook'    => $validated['social_facebook'],
            'social_twitter'     => $validated['social_twitter'],
            'social_instagram'   => $validated['social_instagram'],
            'hashtags'           => $validated['hashtags'],
            'author'             => auth()->user()->name,
            'ai_generated'       => $request->boolean('ai_generated'),
            'ai_generated_at'    => $request->boolean('ai_generated') ? now() : null,
            'status'             => BlogPost::STATUS_DRAFT,
            'view_count'         => 0,
            'change_log'         => [[
                'action'    => 'created',
                'user_id'   => auth()->id(),
                'timestamp' => now()->toIso8601String(),
            ]],
        ]);

        return redirect()->route('training-news.edit', $article->id)
            ->with('success', 'Article saved as draft. Review and publish when ready.');
    }

    // ── Edit: manual editing ──────────────────────────────────
    public function edit(BlogPost $article)
    {
        $this->guardAdmin();
        $categories = BlogCategory::orderBy('name')->get();
        $article->load('trainingSchedule.course', 'category');
        return view('training-news.edit', compact('article', 'categories'));
    }

    // ── Update ────────────────────────────────────────────────
    public function update(Request $request, BlogPost $article)
    {
        $this->guardAdmin();

        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'excerpt'         => 'required|string|max:500',
            'content'         => 'required|string',
            'article_type'    => 'required|in:training_news,success_story,course_announcement,blog_post',
            'blog_category_id' => 'nullable|exists:blog_categories,id',
            'seo_title'       => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'og_title'        => 'nullable|string|max:255',
            'og_description'  => 'nullable|string|max:500',
            'focus_keywords'  => 'nullable|string',
            'tags'            => 'nullable|string',
            'social_linkedin'  => 'nullable|string',
            'social_facebook'  => 'nullable|string',
            'social_twitter'   => 'nullable|string',
            'social_instagram' => 'nullable|string',
            'hashtags'        => 'nullable|string',
            'featured_image'  => 'nullable|image|max:4096',
        ]);

        $imagePath = $article->featured_image;
        if ($request->hasFile('featured_image')) {
            $imagePath = $request->file('featured_image')->store('blog', 'public');
        }

        $tags = null;
        if (!empty($validated['tags'])) {
            $decoded = json_decode($validated['tags'], true);
            $tags    = is_array($decoded) ? $decoded : array_map('trim', explode(',', $validated['tags']));
        }

        $log   = $article->change_log ?? [];
        $log[] = ['action' => 'updated', 'user_id' => auth()->id(), 'timestamp' => now()->toIso8601String()];

        $article->update(array_merge($validated, [
            'featured_image' => $imagePath,
            'tags'           => $tags,
            'change_log'     => $log,
        ]));

        return back()->with('success', 'Article updated.');
    }

    // ── Workflow actions ──────────────────────────────────────
    public function submitForReview(BlogPost $article)
    {
        $this->guardAdmin();
        $article->appendChangeLog('submitted_for_review');
        $article->update(['status' => BlogPost::STATUS_UNDER_REVIEW, 'change_log' => $article->change_log]);
        return back()->with('success', 'Article submitted for review.');
    }

    public function approve(BlogPost $article)
    {
        $this->guardAdmin();
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can approve articles.');
        }
        $article->appendChangeLog('approved');
        $article->update([
            'status'      => BlogPost::STATUS_APPROVED,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'change_log'  => $article->change_log,
        ]);
        return back()->with('success', 'Article approved. Ready to publish.');
    }

    public function publish(BlogPost $article)
    {
        $this->guardAdmin();
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can publish articles.');
        }
        $article->appendChangeLog('published');
        $article->update([
            'status'       => BlogPost::STATUS_PUBLISHED,
            'published_at' => $article->published_at ?? now(),
            'change_log'   => $article->change_log,
        ]);
        return back()->with('success', 'Article is now live on the website.');
    }

    public function unpublish(BlogPost $article)
    {
        $this->guardAdmin();
        $article->appendChangeLog('unpublished');
        $article->update(['status' => BlogPost::STATUS_DRAFT, 'change_log' => $article->change_log]);
        return back()->with('success', 'Article moved back to draft.');
    }

    public function archive(BlogPost $article)
    {
        $this->guardAdmin();
        $article->appendChangeLog('archived');
        $article->update(['status' => BlogPost::STATUS_ARCHIVED, 'change_log' => $article->change_log]);
        return back()->with('success', 'Article archived.');
    }

    public function destroy(BlogPost $article)
    {
        $this->guardAdmin();
        $article->delete();
        return redirect()->route('training-news.index')->with('success', 'Article deleted.');
    }

    // ── Data collection ───────────────────────────────────────
    public function collectScheduleData(TrainingSchedule $schedule): array
    {
        $schedule->load(['course', 'trainer', 'enrollments', 'media']);

        $enrollments    = $schedule->enrollments;
        $totalEnrolled  = $enrollments->count();
        $attended       = $enrollments->where('attendance_status', 'present')->count();
        $completed      = $enrollments->where('completion_status', 'completed')->count();
        $organizations  = $enrollments->pluck('company')->filter()->unique()->count();
        $countries      = $enrollments->pluck('country')->filter()->unique()->count();
        $attendanceRate = $totalEnrolled > 0 ? round(($attended / $totalEnrolled) * 100) : 0;
        $completionRate = $totalEnrolled > 0 ? round(($completed / $totalEnrolled) * 100) : 0;

        $course  = $schedule->course;
        $trainer = $schedule->trainer;

        return [
            // Course
            'course_name'        => $course->name ?? $schedule->training_title ?? 'Training Program',
            'course_code'        => $course->code ?? '',
            'course_category'    => $course->category ?? '',
            'course_description' => $course->short_description ?? $course->description ?? '',
            'learning_objectives' => $course->learning_objectives ?? '',
            'duration'           => $schedule->duration ?? $course->duration ?? '',
            'delivery_method'    => $course->delivery_type ?? 'Instructor-Led',
            // Schedule
            'batch_code'         => $schedule->batch_code ?? '',
            'start_date'         => $schedule->start_date ? \Carbon\Carbon::parse($schedule->start_date)->format('d F Y') : '',
            'end_date'           => $schedule->end_date   ? \Carbon\Carbon::parse($schedule->end_date)->format('d F Y')   : '',
            'venue'              => $schedule->venue ?? '',
            'city'               => $schedule->city ?? '',
            'country'            => $schedule->country ?? '',
            'training_mode'      => $schedule->training_mode ?? 'Physical',
            // Trainer
            'trainer_name'       => $trainer->name ?? '',
            'trainer_designation' => $trainer->designation ?? '',
            'trainer_bio'        => $trainer->short_bio ?? '',
            // Participants
            'total_participants' => $totalEnrolled,
            'organizations'      => $organizations,
            'countries_count'    => $countries,
            'attendance_rate'    => $attendanceRate,
            'completion_rate'    => $completionRate,
        ];
    }

    // ── Prompt builders ───────────────────────────────────────
    private function buildArticlePrompt(array $data, string $type, string $extraInstructions): string
    {
        $typeDesc = match($type) {
            BlogPost::TYPE_SUCCESS_STORY  => 'success story highlighting participant achievements and transformational outcomes',
            BlogPost::TYPE_ANNOUNCEMENT   => 'course announcement promoting an upcoming batch with professional marketing tone',
            default                        => 'professional training completion news article for the company website',
        };

        $statsText = "Participants: {$data['total_participants']}, Organizations: {$data['organizations']}, Countries: {$data['countries_count']}, Attendance Rate: {$data['attendance_rate']}%, Completion Rate: {$data['completion_rate']}%";
        $extra = $extraInstructions ? "Additional instructions: {$extraInstructions}" : '';

        return <<<PROMPT
You are a professional content writer for SMS Training Services, a leading professional training and certification company in Bangladesh serving the international market.

Write a {$typeDesc}. Output ONLY valid JSON.

TRAINING DATA:
Course: {$data['course_name']} {$data['course_code']}
Category: {$data['course_category']}
Description: {$data['course_description']}
Learning Objectives: {$data['learning_objectives']}
Duration: {$data['duration']}
Dates: {$data['start_date']} to {$data['end_date']}
Venue: {$data['venue']}, {$data['city']}, {$data['country']}
Mode: {$data['training_mode']}
Batch: {$data['batch_code']}
Trainer: {$data['trainer_name']}, {$data['trainer_designation']}
Trainer Bio: {$data['trainer_bio']}
Statistics: {$statsText}
{$extra}

ARTICLE STRUCTURE (write full HTML content for each section):
1. Professional SEO-friendly headline
2. Compelling excerpt (2-3 sentences, max 300 chars)
3. Full article (500-1200 words) as clean HTML using <p>, <h2>, <h3>, <ul>, <li>, <strong> tags including:
   - Strong introduction paragraph summarizing the event
   - Training overview (purpose, scope, significance)
   - Key topics covered
   - Trainer highlight section
   - Participant engagement summary (use numbers, do NOT name individual participants)
   - SMS organizational commitment statement
   - Professional closing and call to action

RULES:
- Professional, factual, human-readable tone
- SEO-friendly but not keyword-stuffed
- Do NOT use participant names
- Use actual numbers from statistics
- Include SMS Training Services brand positioning
- Mention ISO standards, compliance, or industry context where relevant

Return ONLY this JSON:
{
  "title": "Professional headline here",
  "excerpt": "Short compelling summary...",
  "content": "<p>Full HTML article...</p>"
}
PROMPT;
    }

    // ── Helpers ───────────────────────────────────────────────
    private function truncate(string $text, int $len): string
    {
        $plain = strip_tags($text);
        return strlen($plain) > $len ? substr($plain, 0, $len) . '...' : $plain;
    }

    private function formatScheduleDataForPrompt(array $data): string
    {
        if (empty($data)) return '';
        $parts = [];
        if (!empty($data['course_name'])) $parts[] = "Course: {$data['course_name']}";
        if (!empty($data['city']))        $parts[] = "City: {$data['city']}, {$data['country']}";
        if (!empty($data['trainer_name'])) $parts[] = "Trainer: {$data['trainer_name']}";
        return implode(' | ', $parts);
    }
}
