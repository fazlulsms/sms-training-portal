<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Preview: {{ $template->name }} — Admin</title>
<style>
*{box-sizing:border-box;}
body{margin:0;background:#f8fafc;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;color:#111827;}

/* ── Admin preview banner ── */
.preview-bar{
    position:sticky; top:0; z-index:100;
    background:#1e3a8a;
    color:#fff;
    padding:10px 24px;
    display:flex; align-items:center; justify-content:space-between; gap:14px; flex-wrap:wrap;
    box-shadow:0 2px 8px rgba(0,0,0,.2);
    font-size:13px;
}
.preview-bar-left { display:flex; align-items:center; gap:10px; }
.preview-badge {
    background:#fff; color:#1e3a8a;
    font-size:10px; font-weight:900; letter-spacing:.6px; text-transform:uppercase;
    padding:3px 10px; border-radius:99px;
}
.preview-bar-title { font-weight:700; font-size:14px; }
.preview-bar-sub   { font-size:11.5px; opacity:.7; margin-top:1px; }
.preview-bar a {
    display:inline-flex; align-items:center; gap:5px;
    background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.3);
    color:#fff; text-decoration:none;
    padding:7px 14px; border-radius:8px; font-size:12.5px; font-weight:700;
    transition:background .15s;
}
.preview-bar a:hover { background:rgba(255,255,255,.25); }

/* ── Form layout (mirrors participant view) ── */
.wrap{max-width:640px;margin:0 auto;padding:28px 16px 60px;}
.logo{text-align:center;margin-bottom:28px;}
.logo img{height:48px;}
.logo-org{display:block;font-size:13px;color:#6b7280;margin-top:6px;font-weight:600;}
.card{background:#fff;border-radius:14px;box-shadow:0 2px 12px rgba(0,0,0,.07);overflow:hidden;}
.card-header{background:linear-gradient(135deg,#1e3a8a,#2563eb);color:#fff;padding:22px 28px;}
.card-header h1{margin:0;font-size:20px;font-weight:800;}
.card-header p{margin:6px 0 0;font-size:13px;opacity:.8;}
.card-body{padding:28px;}
.fg{margin-bottom:22px;}
.fg label{display:block;font-weight:600;font-size:13px;color:#374151;margin-bottom:6px;}
.fg .hint{font-size:12px;color:#9ca3af;margin-top:3px;}
.stars{display:flex;gap:4px;direction:rtl;}
.stars input{display:none;}
.stars label{font-size:30px;color:#d1d5db;cursor:default;transition:color .15s;}
.stars input:checked~label,.stars label:hover,.stars label:hover~label{color:#f59e0b;}
.radio-group{display:flex;gap:12px;flex-wrap:wrap;}
.radio-option{display:flex;align-items:center;gap:6px;padding:8px 16px;border:2px solid #e5e7eb;border-radius:8px;cursor:default;font-size:13.5px;font-weight:600;}
.radio-option input{accent-color:#1e3a8a;}
textarea{width:100%;padding:10px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;font-family:inherit;resize:vertical;outline:none;min-height:80px;background:#fafafa;}
select.prev-select{width:100%;padding:10px 12px;border:1.5px solid #d1d5db;border-radius:8px;font-size:14px;font-family:inherit;outline:none;background:#fafafa;}
.req{color:#dc2626;margin-left:2px;}
.divider{border:none;border-top:1px solid #f3f4f6;margin:10px 0 22px;}
.testimonial-box{background:#fffbeb;border:1.5px solid #fde68a;border-radius:10px;padding:16px 18px;margin-bottom:22px;}
.testimonial-box label{font-weight:700;font-size:13px;color:#92400e;cursor:default;}

/* Submit button — disabled in preview */
.btn-preview-submit{
    width:100%; background:linear-gradient(135deg,#9ca3af,#6b7280);
    color:#fff; border:none; border-radius:10px; padding:14px;
    font-size:15px; font-weight:800; cursor:not-allowed;
    margin-top:4px; opacity:.85;
    display:flex; align-items:center; justify-content:center; gap:8px;
}

/* Category chip */
.q-chip{display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:99px;font-size:10.5px;font-weight:700;margin-bottom:8px;}
.chip-overall  {background:#ede9fe;color:#6d28d9;}
.chip-content  {background:#dbeafe;color:#1d4ed8;}
.chip-trainer  {background:#d1fae5;color:#065f46;}
.chip-platform {background:#fef3c7;color:#92400e;}
.chip-elearning{background:#e0f2fe;color:#0369a1;}
.chip-open     {background:#f3f4f6;color:#374151;}

/* Question counter */
.q-num{
    display:inline-flex;align-items:center;justify-content:center;
    width:22px;height:22px;border-radius:50%;
    background:#e0e7ff;color:#3730a3;
    font-size:11px;font-weight:800;flex-shrink:0;margin-right:6px;
    vertical-align:middle;
}
</style>
</head>
<body>

{{-- Admin preview bar --}}
<div class="preview-bar">
    <div class="preview-bar-left">
        <span class="preview-badge">Admin Preview</span>
        <div>
            <div class="preview-bar-title">{{ $template->name }}</div>
            <div class="preview-bar-sub">This is a read-only preview of the participant feedback form</div>
        </div>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('feedback.templates.edit', $template) }}">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Edit Template
        </a>
        <a href="{{ route('feedback.templates.show', $template) }}">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            Back
        </a>
    </div>
</div>

{{-- Participant view (mirrors feedback.submit exactly) --}}
<div class="wrap">
    <div class="logo">
        @if(file_exists(public_path('sms-logo.png')))
        <img src="{{ asset('sms-logo.png') }}" alt="SMS Training Academy">
        @endif
        <span class="logo-org">Training Feedback Form</span>
    </div>

    <div class="card">
        <div class="card-header">
            <h1>{{ $template->name }}</h1>
            <p>{{ $template->description ?? 'Your feedback helps us improve. All responses are confidential.' }}</p>
        </div>
        <div class="card-body">

            @php $questions = $template->questions->sortBy('sort_order'); @endphp

            @foreach($questions as $i => $q)
            <div class="fg">
                <label>
                    <span class="q-num">{{ $i + 1 }}</span>
                    {{ $q->question_text }}
                    @if($q->is_required)<span class="req">*</span>@endif
                </label>

                @php
                    $chipClass = match($q->category) {
                        'overall'   => 'chip-overall',
                        'content'   => 'chip-content',
                        'trainer'   => 'chip-trainer',
                        'platform'  => 'chip-platform',
                        'elearning' => 'chip-elearning',
                        default     => 'chip-open',
                    };
                    $chipLabel = \App\Models\FeedbackQuestion::$CATEGORIES[$q->category] ?? $q->category;
                @endphp
                <div>
                    <span class="q-chip {{ $chipClass }}">
                        {{ $chipLabel }}
                    </span>
                </div>

                @if($q->question_type === 'rating_5')
                <div class="stars">
                    @for($star = 5; $star >= 1; $star--)
                    <input type="radio" id="prev_star_{{ $q->id }}_{{ $star }}" name="prev_answers[{{ $q->id }}]" value="{{ $star }}" disabled>
                    <label for="prev_star_{{ $q->id }}_{{ $star }}" title="{{ $star }} star{{ $star > 1 ? 's' : '' }}">★</label>
                    @endfor
                </div>
                <div class="hint">Click to rate (1 = Poor, 5 = Excellent)</div>

                @elseif($q->question_type === 'yes_no')
                <div class="radio-group">
                    <label class="radio-option">
                        <input type="radio" name="prev_answers[{{ $q->id }}]" value="1" disabled> Yes
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="prev_answers[{{ $q->id }}]" value="0" disabled> No
                    </label>
                </div>

                @elseif($q->question_type === 'select' && $q->options)
                @php $opts = is_array($q->options) ? $q->options : json_decode($q->options, true); @endphp
                <select class="prev-select" disabled>
                    <option>— Select —</option>
                    @foreach(($opts ?? []) as $opt)
                    <option>{{ $opt }}</option>
                    @endforeach
                </select>

                @else
                <textarea placeholder="Your answer…" disabled style="cursor:default;"></textarea>
                @endif
            </div>
            @endforeach

            <hr class="divider">

            {{-- Testimonial box --}}
            <div class="testimonial-box">
                <label style="display:flex;gap:8px;align-items:flex-start;">
                    <input type="checkbox" disabled style="margin-top:2px;accent-color:#92400e;">
                    <span>I consent to my feedback being used as a testimonial on the training website (optional)</span>
                </label>
            </div>

            <button class="btn-preview-submit" disabled>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Submit Feedback &nbsp;—&nbsp; <span style="font-weight:400;font-size:13px;opacity:.8;">Preview mode, not submittable</span>
            </button>

        </div>
    </div>

    {{-- Template metadata footer --}}
    <div style="margin-top:24px;background:#fff;border-radius:12px;padding:16px 20px;box-shadow:0 1px 6px rgba(0,0,0,.06);font-size:12.5px;color:#6b7280;">
        <div style="font-weight:700;color:#374151;font-size:13px;margin-bottom:10px;">Template Details</div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:10px;">
            <div><span style="font-weight:600;color:#111827;">Type:</span> {{ \App\Models\FeedbackTemplate::$TYPES[$template->type] ?? $template->type }}</div>
            <div><span style="font-weight:600;color:#111827;">Questions:</span> {{ $questions->count() }}</div>
            <div><span style="font-weight:600;color:#111827;">Required Qs:</span> {{ $questions->where('is_required', true)->count() }}</div>
            <div><span style="font-weight:600;color:#111827;">For Certificate:</span> {{ $template->require_for_certificate ? 'Yes' : 'No' }}</div>
            <div><span style="font-weight:600;color:#111827;">Allow Multiple:</span> {{ $template->allow_multiple ? 'Yes' : 'No' }}</div>
            <div><span style="font-weight:600;color:#111827;">Status:</span> {{ $template->is_active ? 'Active' : 'Inactive' }}</div>
        </div>
    </div>
</div>

</body>
</html>
