<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $template->name }} — SMS Training</title>
<style>
*{box-sizing:border-box;margin:0;padding:0;}
body{background:#f0f4fa;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;color:#111827;min-height:100vh;}

/* ── Page shell ── */
.page{max-width:680px;margin:0 auto;padding:32px 16px 72px;}

/* ── Logo area ── */
.brand-area{text-align:center;margin-bottom:32px;}
.brand-area img{height:52px;filter:drop-shadow(0 2px 6px rgba(0,0,0,.12));}
.brand-label{
    display:inline-flex;align-items:center;gap:6px;
    margin-top:10px;font-size:12px;font-weight:600;color:#6b7280;
    letter-spacing:.3px;
}
.brand-label::before,.brand-label::after{content:'';display:block;width:28px;height:1px;background:#d1d5db;}

/* ── Main card ── */
.form-card{background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 4px 24px rgba(15,23,42,.10),0 1px 4px rgba(15,23,42,.06);}

/* ── Header ── */
.form-header{
    background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 60%,#3b82f6 100%);
    padding:32px 32px 28px;
    position:relative;overflow:hidden;
}
.form-header::before{
    content:'';position:absolute;top:-60px;right:-60px;
    width:220px;height:220px;
    background:radial-gradient(circle,rgba(255,255,255,.1) 0%,transparent 70%);
    border-radius:50%;
}
.form-header::after{
    content:'';position:absolute;bottom:-40px;left:-40px;
    width:160px;height:160px;
    background:radial-gradient(circle,rgba(255,255,255,.06) 0%,transparent 70%);
    border-radius:50%;
}
.fh-badge{
    display:inline-flex;align-items:center;gap:6px;
    background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);
    color:rgba(255,255,255,.9);font-size:11px;font-weight:700;
    padding:4px 12px;border-radius:99px;margin-bottom:14px;
    position:relative;z-index:1;
}
.fh-title{font-size:22px;font-weight:900;color:#fff;line-height:1.25;margin-bottom:8px;position:relative;z-index:1;}
.fh-desc{font-size:13.5px;color:rgba(255,255,255,.75);line-height:1.6;position:relative;z-index:1;}
.fh-meta{
    display:flex;align-items:center;gap:16px;margin-top:20px;padding-top:16px;
    border-top:1px solid rgba(255,255,255,.15);position:relative;z-index:1;flex-wrap:wrap;
}
.fh-meta-item{font-size:11.5px;color:rgba(255,255,255,.7);display:flex;align-items:center;gap:5px;}
.fh-meta-dot{width:4px;height:4px;border-radius:50%;background:rgba(255,255,255,.4);}

/* ── Progress bar ── */
.progress-track{height:3px;background:#e5e7eb;}
.progress-fill{height:3px;background:linear-gradient(90deg,#1e3a8a,#3b82f6);transition:width .4s;}

/* ── Form body ── */
.form-body{padding:28px 32px 32px;}

/* ── Error block ── */
.err-block{background:#fff5f5;border:1.5px solid #fca5a5;border-radius:12px;padding:14px 18px;margin-bottom:20px;}
.err-block p{color:#b91c1c;font-size:13px;margin:2px 0;display:flex;align-items:flex-start;gap:6px;}

/* ── Respondent info ── */
.respondent-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:24px;}
.fb-label{display:block;font-size:12.5px;font-weight:700;color:#374151;margin-bottom:6px;}
.fb-input{
    width:100%;padding:11px 13px;
    border:1.5px solid #e5e7eb;border-radius:10px;
    font-size:14px;font-family:inherit;color:#111827;
    background:#fff;outline:none;transition:border-color .15s,box-shadow .15s;
}
.fb-input:focus{border-color:#1e3a8a;box-shadow:0 0 0 3px rgba(30,58,138,.08);}
.fb-divider{border:none;border-top:1.5px solid #f3f4f6;margin:24px 0;}

/* ── Question card ── */
.q-card{
    background:#fff;border:1.5px solid #e5e7eb;
    border-radius:14px;padding:22px 22px 20px;
    margin-bottom:16px;
    transition:border-color .2s,box-shadow .2s;
    position:relative;overflow:hidden;
}
.q-card::before{
    content:'';position:absolute;left:0;top:0;bottom:0;width:4px;
    background:linear-gradient(180deg,#1e3a8a,#3b82f6);
    border-radius:4px 0 0 4px;
}
.q-card:focus-within{border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,.08);}
.q-header{display:flex;align-items:flex-start;gap:12px;margin-bottom:16px;}
.q-num-badge{
    width:28px;height:28px;border-radius:8px;
    background:linear-gradient(135deg,#1e3a8a,#3b82f6);
    color:#fff;font-size:12px;font-weight:800;
    display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;
}
.q-meta{flex:1;}
.q-text{font-size:14.5px;font-weight:700;color:#111827;line-height:1.45;margin-bottom:6px;}
.q-tags{display:flex;align-items:center;gap:6px;flex-wrap:wrap;}
.q-tag{display:inline-flex;align-items:center;gap:4px;padding:2px 9px;border-radius:99px;font-size:10.5px;font-weight:700;}
.qc-overall  {background:#f5f3ff;color:#6d28d9;}
.qc-content  {background:#eff6ff;color:#1d4ed8;}
.qc-trainer  {background:#f0fdf4;color:#166534;}
.qc-platform {background:#fff7ed;color:#c2410c;}
.qc-elearning{background:#e0f2fe;color:#0369a1;}
.qc-open     {background:#f9fafb;color:#374151;}
.q-required-badge{color:#dc2626;font-size:10px;font-weight:800;background:#fee2e2;padding:2px 7px;border-radius:99px;}

/* ── Stars ── */
.star-field{margin-top:4px;}
.stars-ltr{display:inline-flex;flex-direction:row-reverse;gap:3px;}
.stars-ltr input{display:none;}
.stars-ltr label{font-size:34px;color:#d1d5db;cursor:pointer;line-height:1;transition:color .1s,transform .1s;}
.stars-ltr label:hover{transform:scale(1.15);}
.stars-ltr label:hover,.stars-ltr label:hover~label,.stars-ltr input:checked~label{color:#f59e0b;}
.star-labels{display:flex;justify-content:space-between;margin-top:6px;font-size:10.5px;font-weight:600;color:#9ca3af;}

/* ── Yes / No ── */
.yn-group{display:flex;gap:10px;flex-wrap:wrap;}
.yn-option{
    display:flex;align-items:center;gap:8px;
    padding:11px 22px;border:2px solid #e5e7eb;border-radius:10px;
    cursor:pointer;font-size:14px;font-weight:700;color:#374151;
    background:#fafafa;transition:all .15s;flex:1;min-width:100px;
}
.yn-option:has(input:checked){border-color:#1e3a8a;background:#eff6ff;color:#1e3a8a;}
.yn-option:hover{border-color:#3b82f6;background:#f0f9ff;}
.yn-option input{accent-color:#1e3a8a;width:16px;height:16px;}

/* ── Textarea / Select ── */
.fb-textarea{
    width:100%;padding:12px 14px;
    border:1.5px solid #e5e7eb;border-radius:10px;
    font-size:14px;font-family:inherit;
    resize:vertical;outline:none;min-height:90px;
    background:#fafafa;color:#374151;
    transition:border-color .15s,box-shadow .15s;
}
.fb-textarea:focus{border-color:#1e3a8a;box-shadow:0 0 0 3px rgba(30,58,138,.08);background:#fff;}
.fb-select{
    width:100%;padding:11px 14px;
    border:1.5px solid #e5e7eb;border-radius:10px;
    font-size:14px;font-family:inherit;outline:none;
    background:#fafafa;cursor:pointer;transition:border-color .15s;
}
.fb-select:focus{border-color:#1e3a8a;box-shadow:0 0 0 3px rgba(30,58,138,.08);background:#fff;}

/* ── Testimonial ── */
.testimonial-card{
    background:linear-gradient(135deg,#fffbeb,#fef3c7);
    border:1.5px solid #fde68a;border-radius:14px;
    padding:20px;margin-bottom:24px;
}
.tc-head{display:flex;align-items:center;gap:10px;margin-bottom:6px;}
.tc-icon{width:32px;height:32px;background:#f59e0b;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.tc-title{font-size:14px;font-weight:800;color:#92400e;}
.tc-desc{font-size:12.5px;color:#b45309;line-height:1.5;margin-top:2px;}
.tc-checkbox-row{display:flex;align-items:flex-start;gap:10px;margin-top:12px;padding-top:12px;border-top:1px solid #fcd34d;cursor:pointer;}
.tc-checkbox-row input{accent-color:#d97706;width:16px;height:16px;flex-shrink:0;margin-top:2px;cursor:pointer;}
.tc-checkbox-row span{font-size:13px;font-weight:600;color:#78350f;line-height:1.5;}
.tc-text-wrap{margin-top:12px;display:none;}
.tc-text-wrap.show{display:block;}
.tc-textarea{
    width:100%;padding:11px 13px;
    border:1.5px solid #fcd34d;border-radius:10px;
    font-size:13.5px;font-family:inherit;resize:vertical;
    min-height:80px;background:#fff;outline:none;
    transition:border-color .15s;
}
.tc-textarea:focus{border-color:#d97706;box-shadow:0 0 0 3px rgba(217,119,6,.1);}

/* ── Submit button ── */
.btn-submit{
    width:100%;padding:15px;border:none;border-radius:12px;
    font-size:15.5px;font-weight:800;cursor:pointer;
    background:linear-gradient(135deg,#1e3a8a,#2563eb);
    color:#fff;
    display:flex;align-items:center;justify-content:center;gap:10px;
    box-shadow:0 4px 16px rgba(30,58,138,.3);
    transition:opacity .15s,transform .15s,box-shadow .15s;
}
.btn-submit:hover{opacity:.93;transform:translateY(-1px);box-shadow:0 6px 20px rgba(30,58,138,.35);}
.btn-submit:active{transform:translateY(0);}

/* ── Footer ── */
.fb-footer{text-align:center;margin-top:24px;font-size:12px;color:#9ca3af;}
.fb-footer a{color:#6b7280;text-decoration:none;}

@media(max-width:600px){
    .form-header{padding:24px 20px 20px;}
    .form-body{padding:20px 16px 24px;}
    .q-card{padding:16px 16px 14px;}
    .respondent-grid{grid-template-columns:1fr;}
}
</style>
</head>
<body>

<div class="page">

    {{-- Brand --}}
    <div class="brand-area">
        <img src="{{ asset('sms-logo.png') }}" alt="SMS Training Academy">
        <div class="brand-label">Training Evaluation Form</div>
    </div>

    <div class="form-card">

        {{-- Header --}}
        <div class="form-header">
            @php
                $typeLabels = ['ilt'=>'Instructor-Led Training','elearning'=>'eLearning Course','webinar'=>'Webinar','workshop'=>'Workshop','trainer'=>'Trainer Evaluation'];
                $questions  = $response->assignment?->template?->questions ?? collect();
                $questions  = $questions->sortBy('sort_order');
            @endphp
            <div class="fh-badge">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                {{ $typeLabels[$template->type ?? ''] ?? 'Course Feedback' }}
            </div>
            <h1 class="fh-title">{{ $template->name }}</h1>
            <p class="fh-desc">{{ $template->description ?? 'Your feedback is valuable to us. All responses are confidential and used to improve our training programs.' }}</p>
            <div class="fh-meta">
                <span class="fh-meta-item">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Est. {{ ceil($questions->count() * 0.5) }} min
                </span>
                <span class="fh-meta-dot"></span>
                <span class="fh-meta-item">{{ $questions->count() }} questions</span>
                <span class="fh-meta-dot"></span>
                <span class="fh-meta-item">{{ $questions->where('is_required',true)->count() }} required</span>
            </div>
        </div>

        {{-- Progress track --}}
        <div class="progress-track">
            <div class="progress-fill" id="progressFill" style="width:0%;"></div>
        </div>

        <div class="form-body">

            @if($errors->any())
            <div class="err-block">
                @foreach($errors->all() as $e)
                <p>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ $e }}
                </p>
                @endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('feedback.submit', $response->token) }}" id="feedbackForm">
                @csrf

                {{-- Anonymous respondent info --}}
                @if(!$response->user_id)
                <div class="respondent-grid">
                    <div>
                        <label class="fb-label">Your Name</label>
                        <input type="text" name="respondent_name" class="fb-input"
                               value="{{ old('respondent_name', $response->respondent_name) }}"
                               placeholder="Full Name">
                    </div>
                    <div>
                        <label class="fb-label">Email <span style="color:#9ca3af;font-weight:500;">(optional)</span></label>
                        <input type="email" name="respondent_email" class="fb-input"
                               value="{{ old('respondent_email', $response->respondent_email) }}"
                               placeholder="you@example.com">
                    </div>
                </div>
                <hr class="fb-divider">
                @endif

                @php $existingAnswers = $response->answers->keyBy('question_id'); @endphp

                @foreach($questions as $i => $q)
                @php
                    $qcClass = 'qc-' . match($q->category) {
                        'overall' => 'overall', 'content' => 'content', 'trainer' => 'trainer',
                        'platform' => 'platform', 'elearning' => 'elearning', default => 'open',
                    };
                    $qcLabel = \App\Models\FeedbackQuestion::$CATEGORIES[$q->category] ?? $q->category;
                @endphp
                <div class="q-card">
                    <div class="q-header">
                        <div class="q-num-badge">{{ $i + 1 }}</div>
                        <div class="q-meta">
                            <div class="q-text">
                                {{ $q->question_text }}
                                @if($q->is_required)<span style="color:#dc2626;margin-left:2px;">*</span>@endif
                            </div>
                            <div class="q-tags">
                                <span class="q-tag {{ $qcClass }}">{{ $qcLabel }}</span>
                                @if($q->is_required)
                                <span class="q-required-badge">Required</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($q->question_type === 'rating_5')
                    @php $prev = (int) old("answers.{$q->id}", $existingAnswers->get($q->id)?->answer_rating ?? 0); @endphp
                    <div class="star-field">
                        <div class="stars-ltr">
                            @for($star = 5; $star >= 1; $star--)
                            <input type="radio" name="answers[{{ $q->id }}]"
                                   id="star_{{ $q->id }}_{{ $star }}" value="{{ $star }}"
                                   {{ $prev === $star ? 'checked' : '' }}>
                            <label for="star_{{ $q->id }}_{{ $star }}" title="{{ $star }} star{{ $star > 1 ? 's' : '' }}">★</label>
                            @endfor
                        </div>
                        <div class="star-labels">
                            <span>1 — Poor</span>
                            <span>3 — Good</span>
                            <span>5 — Excellent</span>
                        </div>
                    </div>

                    @elseif($q->question_type === 'yes_no')
                    @php $prev = old("answers.{$q->id}"); $prevAns = $existingAnswers->get($q->id); @endphp
                    <div class="yn-group">
                        <label class="yn-option">
                            <input type="radio" name="answers[{{ $q->id }}]" value="1"
                                   {{ ($prev === '1' || ($prevAns && $prevAns->answer_bool)) ? 'checked' : '' }}>
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Yes
                        </label>
                        <label class="yn-option">
                            <input type="radio" name="answers[{{ $q->id }}]" value="0"
                                   {{ ($prev === '0' || ($prevAns && !$prevAns->answer_bool && $prevAns->answer_bool !== null)) ? 'checked' : '' }}>
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            No
                        </label>
                    </div>

                    @elseif($q->question_type === 'select' && $q->options)
                    @php $opts = is_array($q->options) ? $q->options : json_decode($q->options, true); @endphp
                    <select name="answers[{{ $q->id }}]" class="fb-select">
                        <option value="">— Choose an option —</option>
                        @foreach(($opts ?? []) as $opt)
                        <option value="{{ $opt }}" {{ old("answers.{$q->id}", $existingAnswers->get($q->id)?->answer_text) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>

                    @else
                    <textarea name="answers[{{ $q->id }}]" class="fb-textarea"
                              placeholder="Share your thoughts here…">{{ old("answers.{$q->id}", $existingAnswers->get($q->id)?->answer_text) }}</textarea>
                    @endif
                </div>
                @endforeach

                <hr class="fb-divider">

                {{-- Testimonial --}}
                <div class="testimonial-card">
                    <div class="tc-head">
                        <div class="tc-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        </div>
                        <div>
                            <div class="tc-title">Share Your Story (Optional)</div>
                            <div class="tc-desc">Help others discover SMS Training Academy</div>
                        </div>
                    </div>
                    <label class="tc-checkbox-row">
                        <input type="checkbox" name="testimonial_consent" value="1"
                               id="testimonialCheck" {{ old('testimonial_consent') ? 'checked' : '' }}>
                        <span>I consent to my feedback being used as a testimonial on the SMS Training Academy website</span>
                    </label>
                    <div class="tc-text-wrap {{ old('testimonial_consent') ? 'show' : '' }}" id="tcTextWrap">
                        <textarea name="testimonial_text" class="tc-textarea"
                                  placeholder="Write a short testimonial (1–3 sentences) for public display…">{{ old('testimonial_text') }}</textarea>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    Submit Feedback
                </button>

            </form>
        </div>
    </div>

    <div class="fb-footer">
        &copy; {{ date('Y') }} SMS Training Academy &nbsp;·&nbsp; <a href="{{ route('public.home') }}">smscert.org</a>
    </div>

</div>

<script>
// Testimonial toggle
document.getElementById('testimonialCheck').addEventListener('change', function() {
    document.getElementById('tcTextWrap').classList.toggle('show', this.checked);
});

// Live progress bar — counts answered questions
(function(){
    var form     = document.getElementById('feedbackForm');
    var fill     = document.getElementById('progressFill');
    var total    = {{ $questions->count() }};
    if(!fill || !total) return;

    function update(){
        var answered = 0;
        var seen = {};
        form.querySelectorAll('input,select,textarea').forEach(function(el){
            var name = el.name;
            if(!name || name==='testimonial_consent' || name==='testimonial_text' || !name.startsWith('answers[')) return;
            if(seen[name]) return;
            seen[name] = true;
            var val = '';
            if(el.type==='radio'){ val = form.querySelector('input[name="'+name+'"]:checked')?.value||''; }
            else { val = el.value.trim(); }
            if(val !== '') answered++;
        });
        fill.style.width = Math.min(100, Math.round((answered/total)*100)) + '%';
    }
    form.addEventListener('change', update);
    form.addEventListener('input', update);
    update();
})();
</script>
</body>
</html>
