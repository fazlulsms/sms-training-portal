@extends('layouts.public')

@section('page-title', 'Reviews & Testimonials')
@section('seo-title', 'Participant Reviews — SMS Training Academy')
@section('seo-desc', 'Read verified reviews and testimonials from professionals who completed training at SMS Training Academy.')

@section('content')
<style>
.testi-hero { background:linear-gradient(135deg,#0f172a,#1e3a8a); padding:56px 0; color:#fff; text-align:center; }
.testi-hero h1 { font-size:38px; font-weight:900; margin:0 0 10px; }
.testi-hero p  { font-size:16px; opacity:.75; margin:0; }

.testi-filters { background:#fff; border-bottom:1px solid #e9ecf0; padding:16px 0; }
.testi-filter-row { display:flex; gap:12px; align-items:center; flex-wrap:wrap; }
.testi-select { padding:9px 14px; border:1.5px solid #e5e7eb; border-radius:10px; font-size:14px; font-family:inherit; color:#374151; }
.testi-filter-btn { padding:9px 18px; background:#1e3a8a; color:#fff; border:none; border-radius:10px; font-weight:700; font-size:14px; cursor:pointer; font-family:inherit; }

.testi-body { padding:48px 0 60px; }
.testi-main-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:20px; }

/* big stats strip */
.testi-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:20px; margin-bottom:40px; }
@media(max-width:700px){ .testi-stats{grid-template-columns:repeat(2,1fr);} }
.testi-stat { background:#fff; border:1px solid #e9ecf0; border-radius:14px; padding:22px; text-align:center; }
.testi-stat-num { font-size:36px; font-weight:900; color:#1e3a8a; line-height:1; margin-bottom:6px; }
.testi-stat-label { font-size:13px; color:#6b7280; font-weight:600; }

/* Submit form */
.testi-form-section { background:linear-gradient(135deg,#f0f4ff,#dbeafe); border:1px solid #bfdbfe; border-radius:20px; padding:40px; margin-top:56px; }
.testi-form-title { font-size:26px; font-weight:900; color:#111827; margin:0 0 6px; }
.testi-form-sub { font-size:14.5px; color:#6b7280; margin:0 0 28px; }
.tf-grid { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
@media(max-width:640px){ .tf-grid{grid-template-columns:1fr;} }
.tf-label { font-size:13.5px; font-weight:700; color:#374151; margin-bottom:6px; display:block; }
.tf-input {
    width:100%; padding:11px 14px; border:1.5px solid #e5e7eb; border-radius:10px;
    font-size:14.5px; font-family:inherit; color:#111827; background:#fff; box-sizing:border-box;
}
.tf-input:focus { outline:none; border-color:#1e3a8a; box-shadow:0 0 0 3px rgba(30,58,138,.1); }
.tf-stars { display:flex; gap:8px; margin-top:4px; }
.tf-star { font-size:28px; cursor:pointer; color:#d1d5db; transition:color .1s; }
.tf-star.active { color:#f59e0b; }
.tf-submit {
    background:linear-gradient(135deg,#1e3a8a,#2563eb); color:#fff; border:none;
    padding:14px 32px; border-radius:12px; font-size:15px; font-weight:800;
    cursor:pointer; font-family:inherit; margin-top:6px;
}
</style>

<div class="testi-hero">
    <div class="pub-container">
        <div style="font-size:48px;margin-bottom:12px;">⭐</div>
        <h1>What Our Participants Say</h1>
        <p>Verified reviews from professionals who completed training at SMS Training Academy</p>
    </div>
</div>

{{-- Filters --}}
<div class="testi-filters">
    <div class="pub-container">
        <form method="GET" action="{{ route('public.testimonials') }}">
            <div class="testi-filter-row">
                <select name="course" class="testi-select">
                    <option value="">All Courses</option>
                    @foreach($courses as $c)
                    <option value="{{ $c->id }}" {{ request('course') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
                <select name="rating" class="testi-select">
                    <option value="">All Ratings</option>
                    @foreach([5,4,3] as $r)
                    <option value="{{ $r }}" {{ request('rating') == $r ? 'selected' : '' }}>{{ str_repeat('★',$r) }} & above</option>
                    @endforeach
                </select>
                <button type="submit" class="testi-filter-btn">Filter</button>
                @if(request()->hasAny(['course','rating']))
                <a href="{{ route('public.testimonials') }}" style="font-size:13px;color:#6b7280;text-decoration:none;">✕ Clear</a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="pub-container">
<div class="testi-body">

    {{-- Stats --}}
    @php
        $avgRating = \App\Models\Testimonial::approved()->avg('rating');
        $totalReviews = \App\Models\Testimonial::approved()->count();
        $fiveStars = \App\Models\Testimonial::approved()->where('rating',5)->count();
    @endphp
    <div class="testi-stats">
        <div class="testi-stat">
            <div class="testi-stat-num">{{ number_format($avgRating, 1) }}</div>
            <div style="color:#f59e0b;font-size:18px;margin-bottom:4px;">{{ str_repeat('★', round($avgRating)) }}</div>
            <div class="testi-stat-label">Average Rating</div>
        </div>
        <div class="testi-stat">
            <div class="testi-stat-num">{{ $totalReviews }}</div>
            <div class="testi-stat-label">Total Reviews</div>
        </div>
        <div class="testi-stat">
            <div class="testi-stat-num">{{ $fiveStars }}</div>
            <div class="testi-stat-label">5-Star Reviews</div>
        </div>
        <div class="testi-stat">
            <div class="testi-stat-num">{{ $totalReviews > 0 ? round(($fiveStars / $totalReviews) * 100) : 0 }}%</div>
            <div class="testi-stat-label">Would Recommend</div>
        </div>
    </div>

    {{-- Success flash --}}
    @if(session('success'))
    <div style="background:#dcfce7;border:1px solid #86efac;color:#166534;padding:14px 18px;border-radius:12px;font-weight:700;margin-bottom:24px;font-size:14.5px;">
        ✅ {{ session('success') }}
    </div>
    @endif

    @if($testimonials->isEmpty())
    <div style="text-align:center;padding:64px 24px;background:#fff;border:1px solid #e9ecf0;border-radius:16px;margin-bottom:32px;">
        <div style="width:72px;height:72px;background:linear-gradient(135deg,#f0f4ff,#dbeafe);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="1.8"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        </div>
        <h3 style="font-size:20px;font-weight:900;color:#111827;margin:0 0 8px;">No participant reviews available yet</h3>
        <p style="color:#6b7280;font-size:15px;margin:0;line-height:1.7;">Reviews will appear automatically after course completion.<br>Be the first to share your experience below!</p>
    </div>
    @else
    <div class="testi-main-grid">
        @foreach($testimonials as $t)
        <div class="testi-card">
            <div class="testi-stars">{{ str_repeat('★', $t->rating) }}{{ str_repeat('☆', 5 - $t->rating) }}</div>
            <p class="testi-text">"{{ $t->feedback }}"</p>
            @if($t->course_name || $t->course)
            <div style="font-size:11.5px;color:#1e3a8a;font-weight:700;margin-bottom:10px;background:#f0f4ff;display:inline-block;padding:3px 10px;border-radius:20px;">
                📚 {{ $t->course_name ?? $t->course?->name }}
            </div>
            @endif
            @if($t->training_date)
            <div style="font-size:11.5px;color:#9ca3af;margin-bottom:10px;">{{ $t->training_date }}</div>
            @endif
            <div class="testi-author">
                <div class="testi-avatar">
                    @if($t->photo)
                    <img src="{{ asset('storage/'.$t->photo) }}" alt="{{ $t->name }}" style="width:44px;height:44px;border-radius:50%;object-fit:cover;">
                    @else
                    {{ strtoupper(substr($t->name,0,1)) }}
                    @endif
                </div>
                <div>
                    <div class="testi-name">{{ $t->name }}</div>
                    <div class="testi-role">{{ $t->designation }}{{ $t->company ? ' · '.$t->company : '' }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div style="margin-top:32px;">{{ $testimonials->links() }}</div>
    @endif

    {{-- Submit testimonial form --}}
    <div class="testi-form-section" id="submit-review">
        <h2 class="testi-form-title">Share Your Experience</h2>
        <p class="testi-form-sub">Completed a training with us? We'd love to hear your feedback.</p>

        <form method="POST" action="{{ route('public.testimonials.submit') }}" enctype="multipart/form-data">
            @csrf
            @if($errors->any())
            <div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:12px 16px;border-radius:10px;font-size:13.5px;margin-bottom:20px;">
                Please fix the errors below.
            </div>
            @endif

            <div class="tf-grid">
                <div>
                    <label class="tf-label">Your Name *</label>
                    <input type="text" name="name" class="tf-input" value="{{ old('name') }}" required>
                    @error('name')<p style="color:#ef4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="tf-label">Email Address</label>
                    <input type="email" name="email" class="tf-input" value="{{ old('email') }}">
                </div>
                <div>
                    <label class="tf-label">Phone</label>
                    <input type="text" name="phone" class="tf-input" value="{{ old('phone') }}">
                </div>
                <div>
                    <label class="tf-label">Designation</label>
                    <input type="text" name="designation" class="tf-input" value="{{ old('designation') }}" placeholder="e.g. Senior Engineer">
                </div>
                <div>
                    <label class="tf-label">Company / Organization</label>
                    <input type="text" name="company" class="tf-input" value="{{ old('company') }}">
                </div>
                <div>
                    <label class="tf-label">Course Name</label>
                    <input type="text" name="course_name" class="tf-input" value="{{ old('course_name') }}" placeholder="Which course did you attend?">
                </div>
                <div>
                    <label class="tf-label">Training Date / Batch</label>
                    <input type="text" name="training_date" class="tf-input" value="{{ old('training_date') }}" placeholder="e.g. March 2025">
                </div>
                <div>
                    <label class="tf-label">Your Photo (optional)</label>
                    <input type="file" name="photo" class="tf-input" accept="image/*" style="padding:8px;">
                    @error('photo')<p style="color:#ef4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>@enderror
                </div>
            </div>

            <div style="margin-top:20px;">
                <label class="tf-label">Your Rating *</label>
                <div class="tf-stars" id="starRow">
                    @for($i = 1; $i <= 5; $i++)
                    <span class="tf-star {{ old('rating', 0) >= $i ? 'active' : '' }}" data-val="{{ $i }}" onclick="setRating({{ $i }})">★</span>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="ratingInput" value="{{ old('rating', '') }}">
                @error('rating')<p style="color:#ef4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>@enderror
            </div>

            <div style="margin-top:20px;">
                <label class="tf-label">Your Feedback * <span style="color:#9ca3af;font-weight:500;">(min 20 characters)</span></label>
                <textarea name="feedback" class="tf-input" rows="5" required minlength="20" placeholder="Share what you learned, how the trainer was, and how this training helped your career…">{{ old('feedback') }}</textarea>
                @error('feedback')<p style="color:#ef4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>@enderror
            </div>

            <div style="margin-top:16px;">
                <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;font-size:14px;color:#374151;">
                    <input type="checkbox" name="consent" value="1" {{ old('consent') ? 'checked' : '' }} required style="margin-top:2px;accent-color:#1e3a8a;">
                    I consent to SMS Training Academy (Sustainable Management System Inc.) publishing my review on their website and marketing materials.
                </label>
                @error('consent')<p style="color:#ef4444;font-size:12px;margin:4px 0 0;">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="tf-submit">⭐ Submit Review</button>
        </form>
    </div>

</div>
</div>

<script>
function setRating(val) {
    document.getElementById('ratingInput').value = val;
    document.querySelectorAll('.tf-star').forEach((s, i) => {
        s.classList.toggle('active', i < val);
    });
}
</script>
@endsection
