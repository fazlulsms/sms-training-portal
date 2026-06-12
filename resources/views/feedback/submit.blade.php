<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Feedback – {{ config('app.name') }}</title>
<style>
*{box-sizing:border-box;}
body{margin:0;background:#f8fafc;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;color:#111827;}
.wrap{max-width:640px;margin:0 auto;padding:28px 16px 60px;}
.logo{text-align:center;margin-bottom:28px;}
.logo img{height:48px;}
.logo span{display:block;font-size:14px;color:#6b7280;margin-top:4px;}
.card{background:#fff;border-radius:14px;box-shadow:0 2px 12px rgba(0,0,0,.07);overflow:hidden;}
.card-header{background:linear-gradient(135deg,#1e3a8a,#2563eb);color:#fff;padding:22px 28px;}
.card-header h1{margin:0;font-size:20px;font-weight:800;}
.card-header p{margin:6px 0 0;font-size:13px;opacity:.8;}
.card-body{padding:28px;}
.fg{margin-bottom:22px;}
.fg label{display:block;font-weight:600;font-size:13px;color:#374151;margin-bottom:6px;}
.fg .hint{font-size:12px;color:#9ca3af;margin-top:3px;}
.star-row{display:flex;gap:6px;flex-wrap:wrap;}
.star-btn{cursor:pointer;font-size:28px;color:#d1d5db;line-height:1;transition:color .1s;background:none;border:none;padding:2px 3px;}
.star-btn.active,.star-btn:hover~.star-btn{color:#d1d5db;}
.star-btn.active,.star-row:hover .star-btn:hover,.star-row:hover .star-btn:hover~.star-btn+.star-btn{color:#d1d5db;}
.stars{display:flex;gap:4px;direction:rtl;}
.stars input{display:none;}
.stars label{font-size:30px;color:#d1d5db;cursor:pointer;transition:color .15s;}
.stars input:checked~label,.stars label:hover,.stars label:hover~label{color:#f59e0b;}
.radio-group{display:flex;gap:12px;flex-wrap:wrap;}
.radio-option{display:flex;align-items:center;gap:6px;padding:8px 16px;border:2px solid #e5e7eb;border-radius:8px;cursor:pointer;font-size:13.5px;font-weight:600;transition:border-color .15s;}
.radio-option:hover{border-color:#2563eb;}
.radio-option input{accent-color:#1e3a8a;}
textarea{width:100%;padding:10px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;font-family:inherit;resize:vertical;outline:none;min-height:80px;transition:border-color .15s;}
textarea:focus{border-color:#1e3a8a;}
.req{color:#dc2626;margin-left:2px;}
.divider{border:none;border-top:1px solid #f3f4f6;margin:10px 0 22px;}
.testimonial-box{background:#fffbeb;border:1.5px solid #fde68a;border-radius:10px;padding:16px 18px;margin-bottom:22px;}
.testimonial-box label{font-weight:700;font-size:13px;color:#92400e;cursor:pointer;}
.btn-submit{width:100%;background:linear-gradient(135deg,#1e3a8a,#2563eb);color:#fff;border:none;border-radius:10px;padding:14px;font-size:15px;font-weight:800;cursor:pointer;margin-top:4px;transition:opacity .15s;}
.btn-submit:hover{opacity:.9;}
.err{background:#fee2e2;border-radius:8px;padding:12px 16px;margin-bottom:18px;}
.err p{margin:3px 0;font-size:13px;color:#b91c1c;}
</style>
</head>
<body>
<div class="wrap">
    <div class="logo">
        <span>Training Feedback Form</span>
    </div>

    <div class="card">
        <div class="card-header">
            <h1>{{ $response->assignment?->template?->name ?? 'Course Feedback' }}</h1>
            <p>Your feedback helps us improve. All responses are confidential.</p>
        </div>
        <div class="card-body">

            @if($errors->any())
            <div class="err">
                @foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('feedback.submit', $response->token) }}">
                @csrf

                {{-- Respondent info if anonymous --}}
                @if(!$response->user_id)
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:22px;">
                    <div class="fg" style="margin-bottom:0;">
                        <label>Your Name</label>
                        <input type="text" name="respondent_name" value="{{ old('respondent_name', $response->respondent_name) }}"
                               placeholder="Full Name" style="width:100%;padding:9px 12px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;font-family:inherit;outline:none;">
                    </div>
                    <div class="fg" style="margin-bottom:0;">
                        <label>Email (optional)</label>
                        <input type="email" name="respondent_email" value="{{ old('respondent_email', $response->respondent_email) }}"
                               placeholder="you@example.com" style="width:100%;padding:9px 12px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;font-family:inherit;outline:none;">
                    </div>
                </div>
                <hr class="divider">
                @endif

                @php
                $questions = $response->assignment?->template?->questions ?? collect();
                $existingAnswers = $response->answers->keyBy('question_id');
                @endphp

                @foreach($questions->sortBy('sort_order') as $i => $q)
                <div class="fg">
                    <label>
                        {{ $i + 1 }}. {{ $q->question_text }}
                        @if($q->is_required)<span class="req">*</span>@endif
                    </label>

                    @if($q->question_type === 'rating_5')
                    @php $prev = (int) old("answers.{$q->id}", $existingAnswers->get($q->id)?->answer_rating ?? 0); @endphp
                    <div class="stars">
                        @for($star = 5; $star >= 1; $star--)
                        <input type="radio" name="answers[{{ $q->id }}]" id="star_{{ $q->id }}_{{ $star }}" value="{{ $star }}"
                               {{ $prev === $star ? 'checked' : '' }}>
                        <label for="star_{{ $q->id }}_{{ $star }}" title="{{ $star }} stars">★</label>
                        @endfor
                    </div>
                    <div class="hint">Click to rate (1 = Poor, 5 = Excellent)</div>

                    @elseif($q->question_type === 'yes_no')
                    @php $prev = old("answers.{$q->id}"); $prevAns = $existingAnswers->get($q->id); @endphp
                    <div class="radio-group">
                        <label class="radio-option">
                            <input type="radio" name="answers[{{ $q->id }}]" value="1"
                                   {{ ($prev === '1' || ($prevAns && $prevAns->answer_bool)) ? 'checked' : '' }}>
                            Yes
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="answers[{{ $q->id }}]" value="0"
                                   {{ ($prev === '0' || ($prevAns && !$prevAns->answer_bool && $prevAns->answer_bool !== null)) ? 'checked' : '' }}>
                            No
                        </label>
                    </div>

                    @elseif($q->question_type === 'select' && $q->options)
                    @php $opts = is_array($q->options) ? $q->options : json_decode($q->options, true); @endphp
                    <select name="answers[{{ $q->id }}]" style="width:100%;padding:10px 12px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;font-family:inherit;outline:none;">
                        <option value="">— Select —</option>
                        @foreach($opts as $opt)
                        <option value="{{ $opt }}" {{ old("answers.{$q->id}", $existingAnswers->get($q->id)?->answer_text) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>

                    @else
                    <textarea name="answers[{{ $q->id }}]" placeholder="Your answer…">{{ old("answers.{$q->id}", $existingAnswers->get($q->id)?->answer_text) }}</textarea>
                    @endif
                </div>
                @endforeach

                <hr class="divider">

                {{-- Testimonial --}}
                <div class="testimonial-box">
                    <label style="display:flex;gap:8px;align-items:flex-start;">
                        <input type="checkbox" name="testimonial_consent" value="1" {{ old('testimonial_consent') ? 'checked' : '' }}
                               style="margin-top:2px;accent-color:#92400e;" id="testimonialCheck">
                        <span>I consent to my feedback being used as a testimonial on the training website (optional)</span>
                    </label>
                    <div id="testimonialTextBox" style="margin-top:12px;display:{{ old('testimonial_consent') ? 'block' : 'none' }};">
                        <textarea name="testimonial_text" placeholder="Write a short testimonial for public display…">{{ old('testimonial_text') }}</textarea>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Submit Feedback</button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('testimonialCheck').addEventListener('change', function() {
    document.getElementById('testimonialTextBox').style.display = this.checked ? 'block' : 'none';
});
</script>
</body>
</html>
