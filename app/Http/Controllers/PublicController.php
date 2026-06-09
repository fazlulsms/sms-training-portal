<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\TrainingSchedule;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PublicController extends Controller
{
    // ── Homepage ─────────────────────────────────────────────
    public function home()
    {
        $featuredCourses = Course::where('is_public', true)
            ->where('is_featured', true)
            ->withCount(['trainingSchedules as open_schedules_count' => fn($q) => $q->where('is_public', true)])
            ->latest()
            ->take(6)
            ->get();

        $elearningCourses = Course::where('is_public', true)
            ->where('delivery_type', 'eLearning')
            ->latest()->take(4)->get();

        $upcomingSchedules = TrainingSchedule::with('course', 'trainer')
            ->where('is_public', true)
            ->whereIn('schedule_status', ['Upcoming', 'Running'])
            ->where(function ($q) {
                $q->whereNull('registration_deadline')
                  ->orWhere('registration_deadline', '>=', now()->toDateString());
            })
            ->orderBy('start_date')
            ->take(6)
            ->get();

        $featuredTestimonials = Testimonial::featured()->latest()->take(6)->get();
        $latestBlogs = BlogPost::published()->with('category')->latest('published_at')->take(3)->get();

        $categories = Course::where('is_public', true)
            ->select('category')
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        $stats = [
            'courses'      => Course::where('is_public', true)->count(),
            'schedules'    => TrainingSchedule::where('is_public', true)->count(),
            'testimonials' => Testimonial::approved()->count(),
            'blogs'        => BlogPost::published()->count(),
        ];

        return view('public.home', compact(
            'featuredCourses', 'elearningCourses', 'upcomingSchedules',
            'featuredTestimonials', 'latestBlogs', 'categories', 'stats'
        ));
    }

    // ── Course Catalog ────────────────────────────────────────
    public function courses(Request $request)
    {
        $query = Course::where('is_public', true)
            ->withCount(['trainingSchedules as open_schedules_count' => fn($q) =>
                $q->where('is_public', true)->whereIn('schedule_status', ['Upcoming','Running'])
            ]);

        // Filters
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($sq) =>
                $sq->where('name', 'like', "%$q%")
                   ->orWhere('short_description', 'like', "%$q%")
                   ->orWhere('category', 'like', "%$q%")
            );
        }
        if ($request->filled('category'))   $query->where('category', $request->category);
        if ($request->filled('type'))       $query->where('delivery_type', $request->type);
        if ($request->filled('has_schedule')) $query->having('open_schedules_count', '>', 0);
        if ($request->filled('max_fee')) {
            $query->where(fn($sq) =>
                $sq->where('public_price', '<=', $request->max_fee)
                   ->orWhere('course_fee', '<=', $request->max_fee)
            );
        }

        $courses = $query->latest()->paginate(12)->withQueryString();

        $categories = Course::where('is_public', true)
            ->select('category')->whereNotNull('category')->distinct()->pluck('category');

        $deliveryTypes = ['eLearning', 'Instructor-Led', 'Hybrid'];

        return view('public.courses', compact('courses', 'categories', 'deliveryTypes'));
    }

    // ── Course Details ────────────────────────────────────────
    public function courseDetail(string $slug)
    {
        $course = Course::where('slug', $slug)
            ->where('is_public', true)
            ->with([
                'publicSchedules.trainer',
                'testimonials',
                'blogPosts' => fn($q) => $q->published()->latest('published_at')->take(3),
            ])
            ->firstOrFail();

        $relatedCourses = Course::where('is_public', true)
            ->where('id', '!=', $course->id)
            ->where(fn($q) =>
                $q->where('category', $course->category)
                  ->orWhere('delivery_type', $course->delivery_type)
            )
            ->take(4)->get();

        return view('public.course-detail', compact('course', 'relatedCourses'));
    }

    // ── Training Calendar ─────────────────────────────────────
    public function calendar(Request $request)
    {
        $query = TrainingSchedule::with('course', 'trainer')
            ->where('is_public', true)
            ->whereIn('schedule_status', ['Upcoming', 'Running'])
            ->where(fn($q) =>
                $q->whereNull('registration_deadline')
                  ->orWhere('registration_deadline', '>=', now()->toDateString())
            );

        if ($request->filled('month')) {
            $query->whereMonth('start_date', Carbon::parse($request->month)->month)
                  ->whereYear('start_date',  Carbon::parse($request->month)->year);
        }
        if ($request->filled('mode'))   $query->where('training_mode', $request->mode);
        if ($request->filled('course')) $query->where('course_id', $request->course);

        $schedules = $query->orderBy('start_date')->paginate(15)->withQueryString();
        $courses   = Course::where('is_public', true)->select('id','name')->get();

        return view('public.calendar', compact('schedules', 'courses'));
    }

    // ── Blog Listing ──────────────────────────────────────────
    public function blog(Request $request)
    {
        $query = BlogPost::published()->with('category', 'course');

        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($sq) =>
                $sq->where('title', 'like', "%$q%")->orWhere('excerpt', 'like', "%$q%")
            );
        }

        $posts      = $query->latest('published_at')->paginate(9)->withQueryString();
        $categories = BlogCategory::withCount(['publishedPosts'])->having('published_posts_count','>',0)->get();
        $featured   = BlogPost::published()->latest('published_at')->take(3)->get();

        return view('public.blog', compact('posts', 'categories', 'featured'));
    }

    // ── Blog Detail ───────────────────────────────────────────
    public function blogDetail(string $slug)
    {
        $post = BlogPost::published()
            ->where('slug', $slug)
            ->with('category', 'course')
            ->firstOrFail();

        $post->increment('view_count');

        $related = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->where(fn($q) =>
                $q->where('blog_category_id', $post->blog_category_id)
                  ->orWhere('course_id', $post->course_id)
            )
            ->latest('published_at')->take(3)->get();

        return view('public.blog-detail', compact('post', 'related'));
    }

    // ── Testimonials ──────────────────────────────────────────
    public function testimonials(Request $request)
    {
        $query = Testimonial::approved()->with('course');

        if ($request->filled('course')) $query->where('course_id', $request->course);
        if ($request->filled('rating')) $query->where('rating', $request->rating);

        $testimonials = $query->latest()->paginate(12)->withQueryString();
        $courses      = Course::where('is_public', true)->select('id','name')->get();

        return view('public.testimonials', compact('testimonials', 'courses'));
    }

    // ── Submit Testimonial ────────────────────────────────────
    public function testimonialSubmit(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:120',
            'email'         => 'nullable|email|max:120',
            'phone'         => 'nullable|string|max:30',
            'designation'   => 'nullable|string|max:100',
            'company'       => 'nullable|string|max:120',
            'course_id'     => 'nullable|exists:courses,id',
            'course_name'   => 'nullable|string|max:200',
            'training_date' => 'nullable|string|max:50',
            'rating'        => 'required|integer|min:1|max:5',
            'feedback'      => 'required|string|min:20|max:2000',
            'photo'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'consent'       => 'required|accepted',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('testimonials', 'public');
        }

        $validated['status'] = 'pending';

        Testimonial::create($validated);

        return redirect()->route('public.testimonials')
            ->with('success', 'Thank you for your feedback! It will be visible after admin review.');
    }

    // ── Certificate Verify ────────────────────────────────────
    public function verifyCertificate(\Illuminate\Http\Request $request)
    {
        $result = null;

        if ($request->filled('cert') && $request->filled('name')) {
            $certCode  = trim($request->input('cert'));
            $inputName = trim($request->input('name'));

            // 1) ILT enrollment (SMS-TC-*)
            $iltEnroll = \App\Models\Enrollment::where('certificate_number', $certCode)
                ->whereNotNull('certificate_number')
                ->first();
            if ($iltEnroll) {
                $actualName = $iltEnroll->full_name ?? '';
                similar_text(strtolower($inputName), strtolower($actualName), $pct);
                if ($pct >= 50) {
                    $result = [
                        'found'       => true,
                        'type'        => 'ILT',
                        'cert_number' => $certCode,
                        'name'        => $iltEnroll->full_name,
                        'company'     => $iltEnroll->company ?? '—',
                        'course'      => $iltEnroll->trainingSchedule?->course?->name ?? '—',
                        'batch'       => $iltEnroll->trainingSchedule?->batch_code ?? '—',
                        'issue_date'  => $iltEnroll->certificate_issue_date,
                    ];
                }
            }

            // 2) eLearning enrollment
            if (!$result) {
                $eEnroll = \App\Models\ElearningEnrollment::where('certificate_number', $certCode)
                    ->whereNotNull('certificate_number')
                    ->with('user', 'course')
                    ->first();
                if ($eEnroll) {
                    $actualName = $eEnroll->participant_name ?? $eEnroll->user?->name ?? '';
                    similar_text(strtolower($inputName), strtolower($actualName), $pct);
                    if ($pct >= 50) {
                        $result = [
                            'found'       => true,
                            'type'        => 'eLearning',
                            'cert_number' => $certCode,
                            'name'        => $actualName,
                            'company'     => $eEnroll->company ?? '—',
                            'course'      => $eEnroll->course?->name ?? '—',
                            'batch'       => '—',
                            'issue_date'  => $eEnroll->certificate_issued_at ?? $eEnroll->updated_at,
                        ];
                    }
                }
            }

            // 3) Corporate certificate (SMS-TR-*)
            if (!$result) {
                $corpCert = \App\Models\CorporateCertificate::where('certificate_number', $certCode)
                    ->with(['participant', 'project'])
                    ->first();
                if ($corpCert) {
                    $actualName = $corpCert->participant?->participant_name ?? '';
                    similar_text(strtolower($inputName), strtolower($actualName), $pct);
                    if ($pct >= 50) {
                        $result = [
                            'found'       => true,
                            'type'        => 'Corporate',
                            'cert_number' => $certCode,
                            'name'        => $actualName,
                            'company'     => $corpCert->participant?->company ?? '—',
                            'course'      => $corpCert->project?->title ?? '—',
                            'batch'       => '—',
                            'issue_date'  => $corpCert->issue_date,
                        ];
                    }
                }
            }

            // Certificate number exists but name doesn't match (or not found)
            if (!$result) {
                // Check if cert number exists at all (so we give a more helpful message)
                $certExists = \App\Models\Enrollment::where('certificate_number', $certCode)->exists()
                           || \App\Models\ElearningEnrollment::where('certificate_number', $certCode)->exists()
                           || \App\Models\CorporateCertificate::where('certificate_number', $certCode)->exists();

                $result = [
                    'found'        => false,
                    'cert_number'  => $certCode,
                    'name_mismatch'=> $certExists, // cert exists but name < 50% match
                ];
            }
        }

        return view('public.verify-certificate', compact('result'));
    }
}
