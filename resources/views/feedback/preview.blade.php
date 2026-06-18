<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Preview: {{ $template->name }}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0;}
body{background:#f0f4fa;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;color:#111827;min-height:100vh;}

/* ── Admin banner ── */
.admin-bar{
    position:sticky;top:0;z-index:200;
    background:linear-gradient(135deg,#0f172a,#1e3a8a);
    padding:0 24px;
    display:flex;align-items:center;justify-content:space-between;gap:16px;
    height:52px;
    box-shadow:0 2px 12px rgba(0,0,0,.3);
}
.admin-bar-left{display:flex;align-items:center;gap:12px;}
.admin-pill{
    background:#f59e0b;color:#fff;
    font-size:9.5px;font-weight:900;letter-spacing:.8px;text-transform:uppercase;
    padding:3px 10px;border-radius:99px;flex-shrink:0;
}
.admin-title{font-weight:700;font-size:13.5px;color:#fff;}
.admin-sub{font-size:11px;color:rgba(255,255,255,.55);margin-top:1px;}
.admin-actions{display:flex;gap:8px;flex-shrink:0;}
.admin-btn{
    display:inline-flex;align-items:center;gap:5px;
    padding:6px 14px;border-radius:7px;font-size:12px;font-weight:700;
    text-decoration:none;transition:all .15s;cursor:pointer;border:none;
}
.admin-btn-ghost{background:rgba(255,255,255,.12);color:#fff;border:1px solid rgba(255,255,255,.2);}
.admin-btn-ghost:hover{background:rgba(255,255,255,.22);}
.admin-btn-outline{background:transparent;color:rgba(255,255,255,.7);border:1px solid rgba(255,255,255,.2);}
.admin-btn-outline:hover{background:rgba(255,255,255,.1);color:#fff;}

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

/* ── Questions body ── */
.form-body{padding:28px 32px 32px;}

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
.q-tag{
    display:inline-flex;align-items:center;gap:4px;
    padding:2px 9px;border-radius:99px;font-size:10.5px;font-weight:700;
}
.qt-rating  {background:#ede9fe;color:#5b21b6;}
.qt-yesno   {background:#d1fae5;color:#065f46;}
.qt-text    {background:#f0f9ff;color:#0369a1;}
.qt-select  {background:#fef3c7;color:#92400e;}
.qc-overall  {background:#f5f3ff;color:#6d28d9;}
.qc-content  {background:#eff6ff;color:#1d4ed8;}
.qc-trainer  {background:#f0fdf4;color:#166534;}
.qc-platform {background:#fff7ed;color:#c2410c;}
.qc-elearning{background:#e0f2fe;color:#0369a1;}
.qc-open     {background:#f9fafb;color:#374151;}
.q-required{color:#dc2626;font-size:10px;font-weight:800;background:#fee2e2;padding:2px 7px;border-radius:99px;}

/* ── Star rating ── */
.star-field{margin-top:4px;}
.star-wrap{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
.stars-ltr{display:inline-flex;flex-direction:row-reverse;gap:3px;}
.stars-ltr input{display:none;}
.stars-ltr label{font-size:34px;color:#d1d5db;cursor:default;line-height:1;transition:color .1s,transform .1s;}
.stars-ltr label:hover,.stars-ltr label:hover~label,.stars-ltr input:checked~label{color:#f59e0b;}
.star-labels{display:flex;justify-content:space-between;margin-top:6px;font-size:10.5px;font-weight:600;color:#9ca3af;}

/* ── Yes/No ── */
.yn-group{display:flex;gap:10px;flex-wrap:wrap;}
.yn-option{
    display:flex;align-items:center;gap:8px;
    padding:10px 20px;border:2px solid #e5e7eb;border-radius:10px;
    cursor:default;font-size:14px;font-weight:700;color:#374151;
    background:#fafafa;transition:all .15s;
    flex:1;min-width:100px;
}
.yn-option input{accent-color:#1e3a8a;width:16px;height:16px;}

/* ── Textarea / Select ── */
.fb-textarea{
    width:100%;padding:12px 14px;
    border:1.5px solid #e5e7eb;border-radius:10px;
    font-size:14px;font-family:inherit;
    resize:vertical;outline:none;min-height:90px;
    background:#fafafa;color:#374151;
    transition:border-color .15s;
}
.fb-select{
    width:100%;padding:11px 14px;
    border:1.5px solid #e5e7eb;border-radius:10px;
    font-size:14px;font-family:inherit;outline:none;
    background:#fafafa;cursor:default;
}

/* ── Divider ── */
.fb-divider{border:none;border-top:1.5px solid #f3f4f6;margin:24px 0;}

/* ── Testimonial ── */
.testimonial-card{
    background:linear-gradient(135deg,#fffbeb,#fef3c7);
    border:1.5px solid #fde68a;border-radius:14px;
    padding:20px;margin-bottom:24px;
}
.testimonial-card .tc-head{
    display:flex;align-items:center;gap:10px;margin-bottom:6px;
}
.tc-icon{width:32px;height:32px;background:#f59e0b;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.tc-title{font-size:14px;font-weight:800;color:#92400e;}
.tc-desc{font-size:12.5px;color:#b45309;line-height:1.5;margin-top:2px;}
.tc-checkbox-row{display:flex;align-items:flex-start;gap:10px;margin-top:12px;padding-top:12px;border-top:1px solid #fcd34d;}
.tc-checkbox-row input{accent-color:#d97706;width:16px;height:16px;flex-shrink:0;margin-top:2px;}
.tc-checkbox-row span{font-size:13px;font-weight:600;color:#78350f;cursor:default;}

/* ── Submit button ── */
.btn-submit{
    width:100%;padding:15px;border:none;border-radius:12px;
    font-size:15.5px;font-weight:800;cursor:not-allowed;
    background:linear-gradient(135deg,#94a3b8,#64748b);
    color:#fff;
    display:flex;align-items:center;justify-content:center;gap:10px;
    box-shadow:0 2px 8px rgba(100,116,139,.3);
    opacity:.8;
}
.btn-preview-notice{
    display:flex;align-items:center;justify-content:center;gap:6px;
    margin-top:12px;font-size:12px;color:#9ca3af;font-weight:600;
}

/* ── Footer meta card ── */
.meta-card{
    background:#fff;border-radius:14px;padding:20px 24px;
    box-shadow:0 2px 8px rgba(15,23,42,.06);margin-top:20px;
    border:1px solid #e5e7eb;
}
.meta-card-title{font-size:12px;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.7px;margin-bottom:14px;}
.meta-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:12px;}
.meta-item{background:#f8fafc;border-radius:8px;padding:10px 12px;}
.meta-item-label{font-size:10.5px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.5px;margin-bottom:3px;}
.meta-item-val{font-size:13px;font-weight:800;color:#111827;}

@media(max-width:600px){
    .form-header{padding:24px 20px 20px;}
    .form-body{padding:20px 16px 24px;}
    .q-card{padding:16px 16px 14px;}
    .admin-bar{padding:0 14px;height:48px;}
    .admin-sub{display:none;}
    .admin-title{font-size:12.5px;}
}
</style>
</head>
<body>

{{-- Admin sticky bar --}}
<div class="admin-bar">
    <div class="admin-bar-left">
        <span class="admin-pill">Preview</span>
        <div>
            <div class="admin-title">{{ $template->name }}</div>
            <div class="admin-sub">Read-only · participant view</div>
        </div>
    </div>
    <div class="admin-actions">
        <a href="{{ route('feedback.templates.edit', $template) }}" class="admin-btn admin-btn-ghost">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Edit
        </a>
        <a href="{{ route('feedback.templates.show', $template) }}" class="admin-btn admin-btn-outline">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            Back
        </a>
    </div>
</div>

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
            @endphp
            <div class="fh-badge">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                {{ $typeLabels[$template->type] ?? ucfirst($template->type) }}
            </div>
            <h1 class="fh-title">{{ $template->name }}</h1>
            <p class="fh-desc">{{ $template->description ?? 'Your feedback is valuable to us. All responses are confidential and used to improve our training programs.' }}</p>
            <div class="fh-meta">
                @php $questions = $template->questions->sortBy('sort_order'); @endphp
                <span class="fh-meta-item">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Est. {{ ceil($questions->count() * 0.5) }} min
                </span>
                <span class="fh-meta-dot"></span>
                <span class="fh-meta-item">{{ $questions->count() }} questions</span>
                <span class="fh-meta-dot"></span>
                <span class="fh-meta-item">{{ $questions->where('is_required',true)->count() }} required</span>
                @if($template->require_for_certificate)
                <span class="fh-meta-dot"></span>
                <span class="fh-meta-item" style="color:#fcd34d;">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
                    Required for certificate
                </span>
                @endif
            </div>
        </div>

        {{-- Progress track (visual only) --}}
        <div class="progress-track">
            <div class="progress-fill" style="width:0%;"></div>
        </div>

        {{-- Questions --}}
        <div class="form-body">

            @foreach($questions as $i => $q)
            @php
                $qtClass = match($q->question_type) {
                    'rating_5' => 'qt-rating', 'yes_no' => 'qt-yesno',
                    'text' => 'qt-text', 'select' => 'qt-select', default => 'qt-text',
                };
                $qtLabel = match($q->question_type) {
                    'rating_5' => '★ Rating', 'yes_no' => 'Yes / No',
                    'text' => 'Open Text', 'select' => 'Select', default => 'Text',
                };
                $qcClass = 'qc-' . ($q->category === 'content' ? 'content' : ($q->category === 'overall' ? 'overall' : ($q->category === 'trainer' ? 'trainer' : ($q->category === 'platform' ? 'platform' : ($q->category === 'elearning' ? 'elearning' : 'open')))));
                $qcLabel = \App\Models\FeedbackQuestion::$CATEGORIES[$q->category] ?? $q->category;
            @endphp
            <div class="q-card">
                <div class="q-header">
                    <div class="q-num-badge">{{ $i + 1 }}</div>
                    <div class="q-meta">
                        <div class="q-text">
                            {{ $q->question_text }}
                            @if($q->is_required)<span style="color:#dc2626;margin-left:3px;">*</span>@endif
                        </div>
                        <div class="q-tags">
                            <span class="q-tag {{ $qtClass }}">{{ $qtLabel }}</span>
                            <span class="q-tag {{ $qcClass }}">{{ $qcLabel }}</span>
                            @if($q->is_required)
                            <span class="q-required">Required</span>
                            @endif
                        </div>
                    </div>
                </div>

                @if($q->question_type === 'rating_5')
                <div class="star-field">
                    <div class="star-wrap">
                        <div class="stars-ltr">
                            @for($star = 5; $star >= 1; $star--)
                            <input type="radio" id="pv_{{ $q->id }}_{{ $star }}" name="pv[{{ $q->id }}]" value="{{ $star }}" disabled>
                            <label for="pv_{{ $q->id }}_{{ $star }}" title="{{ $star }} star{{ $star > 1 ? 's' : '' }}">★</label>
                            @endfor
                        </div>
                    </div>
                    <div class="star-labels">
                        <span>1 — Poor</span>
                        <span>3 — Good</span>
                        <span>5 — Excellent</span>
                    </div>
                </div>

                @elseif($q->question_type === 'yes_no')
                <div class="yn-group">
                    <label class="yn-option">
                        <input type="radio" name="pv[{{ $q->id }}]" value="1" disabled>
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        Yes
                    </label>
                    <label class="yn-option">
                        <input type="radio" name="pv[{{ $q->id }}]" value="0" disabled>
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        No
                    </label>
                </div>

                @elseif($q->question_type === 'select' && $q->options)
                @php $opts = is_array($q->options) ? $q->options : json_decode($q->options, true); @endphp
                <select class="fb-select" disabled>
                    <option>— Choose an option —</option>
                    @foreach(($opts ?? []) as $opt)
                    <option>{{ $opt }}</option>
                    @endforeach
                </select>

                @else
                <textarea class="fb-textarea" placeholder="Share your thoughts here…" disabled></textarea>
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
                        <div class="tc-desc">Consent to having your feedback featured as a testimonial</div>
                    </div>
                </div>
                <div class="tc-checkbox-row">
                    <input type="checkbox" disabled>
                    <span>I consent to my feedback being used as a testimonial on the SMS Training Academy website</span>
                </div>
            </div>

            {{-- Submit (disabled in preview) --}}
            <button class="btn-submit" disabled>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                Submit Feedback
            </button>
            <div class="btn-preview-notice">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Preview only — form cannot be submitted from here
            </div>

        </div>
    </div>

    {{-- Metadata footer --}}
    <div class="meta-card">
        <div class="meta-card-title">Template Info</div>
        <div class="meta-grid">
            <div class="meta-item">
                <div class="meta-item-label">Type</div>
                <div class="meta-item-val">{{ $typeLabels[$template->type] ?? ucfirst($template->type) }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-item-label">Questions</div>
                <div class="meta-item-val">{{ $questions->count() }} total</div>
            </div>
            <div class="meta-item">
                <div class="meta-item-label">Required</div>
                <div class="meta-item-val">{{ $questions->where('is_required',true)->count() }} of {{ $questions->count() }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-item-label">Certificate Gate</div>
                <div class="meta-item-val" style="color:{{ $template->require_for_certificate ? '#d97706' : '#6b7280' }};">
                    {{ $template->require_for_certificate ? 'Yes — Required' : 'No' }}
                </div>
            </div>
            <div class="meta-item">
                <div class="meta-item-label">Allow Multiple</div>
                <div class="meta-item-val">{{ $template->allow_multiple ? 'Yes' : 'No' }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-item-label">Status</div>
                <div class="meta-item-val" style="color:{{ $template->is_active ? '#16a34a' : '#dc2626' }};">
                    {{ $template->is_active ? 'Active' : 'Inactive' }}
                </div>
            </div>
        </div>
    </div>

</div>
</body>
</html>
