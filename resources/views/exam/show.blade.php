<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $questionSet->title }} â€“ SMS Training Academy</title>
<style>
*,*::before,*::after{box-sizing:border-box;}
body{font-family:'Segoe UI',Arial,sans-serif;background:#f0f4f8;color:#1e293b;margin:0;padding:0;}
.top-bar{background:linear-gradient(135deg,#0f1e45,#1e3a8a);color:#fff;padding:18px 28px;display:flex;align-items:center;justify-content:space-between;}
.top-bar h1{font-size:16px;font-weight:800;margin:0;}
.top-bar p{font-size:12px;opacity:.7;margin:2px 0 0;}
.timer{background:rgba(255,255,255,.12);padding:6px 14px;border-radius:8px;font-size:14px;font-weight:800;font-family:monospace;color:#fff;}
.container{max-width:760px;margin:28px auto;padding:0 16px;}
.exam-info{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:20px;margin-bottom:20px;}
.exam-info h2{font-size:18px;font-weight:800;color:#1e293b;margin:0 0 8px;}
.exam-info p{font-size:13px;color:#64748b;margin:0;}
.info-chips{display:flex;gap:10px;flex-wrap:wrap;margin-top:12px;}
.chip{background:#f1f5f9;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:700;color:#475569;}
.q-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:22px;margin-bottom:16px;}
.q-header{display:flex;align-items:flex-start;gap:10px;margin-bottom:14px;}
.q-num{width:28px;height:28px;background:#1e3a8a;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;flex-shrink:0;margin-top:1px;}
.q-text{font-size:15px;font-weight:600;color:#1e293b;line-height:1.5;}
.q-sub{font-size:12px;color:#64748b;margin-top:4px;}
.option-list{display:flex;flex-direction:column;gap:8px;}
.option-label{display:flex;align-items:center;gap:10px;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:8px;cursor:pointer;transition:border-color .15s,background .15s;}
.option-label:hover{border-color:#bfdbfe;background:#f0f9ff;}
.option-label input:checked + .opt-text{font-weight:700;color:#1e3a8a;}
.option-label:has(input:checked){border-color:#bfdbfe;background:#eff6ff;}
.opt-text{font-size:14px;color:#374151;flex:1;}
textarea.ans-input{width:100%;border:1.5px solid #e2e8f0;border-radius:8px;padding:12px;font-size:14px;color:#374151;resize:vertical;font-family:inherit;}
textarea.ans-input:focus{border-color:#1e3a8a;outline:none;}
input.ans-input{width:100%;border:1.5px solid #e2e8f0;border-radius:8px;padding:11px 14px;font-size:14px;color:#374151;}
input.ans-input:focus{border-color:#1e3a8a;outline:none;}
.decl-box{display:flex;align-items:flex-start;gap:12px;padding:14px;background:#fffbeb;border:1px solid #fde68a;border-radius:8px;}
.decl-box input{width:18px;height:18px;flex-shrink:0;margin-top:2px;}
.submit-bar{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:40px;}
.submit-btn{background:#1e3a8a;color:#fff;border:none;border-radius:10px;padding:13px 36px;font-size:15px;font-weight:800;cursor:pointer;}
.submit-btn:hover{background:#1e40af;}
.req-star{color:#ef4444;margin-left:3px;}
.marks-tag{background:#f0fdf4;color:#166534;font-size:11px;font-weight:700;padding:2px 8px;border-radius:10px;margin-left:8px;}
</style>
</head>
<body>

<div class="top-bar">
    <div>
        <h1>ðŸ“‹ {{ $questionSet->title }}</h1>
        <p>{{ $attempt->enrollment->trainingSchedule?->course?->name ?? 'Training Programme' }} â€” Knowledge Test</p>
    </div>
    @if($questionSet->time_limit_minutes)
    <div class="timer" id="examTimer">â± <span id="timerDisplay">{{ $questionSet->time_limit_minutes }}:00</span></div>
    @endif
</div>

<div class="container">

    <div class="exam-info">
        <h2>{{ $questionSet->title }}</h2>
        @if($questionSet->description)
        <p>{{ $questionSet->description }}</p>
        @endif
        <div class="info-chips">
            <span class="chip">ðŸ“ {{ $questions->count() }} Questions</span>
            <span class="chip">ðŸ† {{ $questionSet->total_marks }} Total Marks</span>
            <span class="chip">âœ… Pass: {{ $questionSet->effectivePassMark() }} marks</span>
            @if($questionSet->time_limit_minutes)
            <span class="chip">â± {{ $questionSet->time_limit_minutes }} min</span>
            @endif
            <span class="chip">ðŸŽ¯ Attempt #{{ $attempt->attempt_number }}</span>
        </div>
    </div>

    <form method="POST" action="/exam/{{ $attempt->exam_token }}/submit" id="examForm" enctype="multipart/form-data"
          onsubmit="return confirmSubmit()">
        @csrf

        @foreach($questions as $idx => $question)
        @php $existing = $existingAnswers->get($question->id); @endphp
        <div class="q-card" id="q{{ $question->id }}">
            <div class="q-header">
                <div class="q-num">{{ $idx + 1 }}</div>
                <div>
                    <div class="q-text">
                        {{ $question->question_text }}
                        @if($question->is_required)<span class="req-star">*</span>@endif
                        @if($question->marks > 0)<span class="marks-tag">{{ $question->marks }} marks</span>@endif
                    </div>
                    @php $typeLabel = \App\Models\Question::TYPES[$question->question_type] ?? $question->question_type; @endphp
                    <div class="q-sub">{{ $typeLabel }}</div>
                </div>
            </div>

            @if(in_array($question->question_type, ['mcq_single','true_false']))
                @php $selectedIds = array_map('intval', (array)($existing?->answer_options ?? [])); @endphp
                <div class="option-list">
                    @foreach($question->options as $opt)
                    <label class="option-label">
                        <input type="radio" name="q_{{ $question->id }}" value="{{ $opt->id }}"
                               {{ in_array((int)$opt->id, $selectedIds) ? 'checked' : '' }}
                               {{ $question->is_required ? 'required' : '' }}>
                        <span class="opt-text">{{ $opt->option_text }}</span>
                    </label>
                    @endforeach
                </div>

            @elseif($question->question_type === 'mcq_multiple')
                @php $selectedIds = array_map('intval', (array)($existing?->answer_options ?? [])); @endphp
                <div class="option-list">
                    @foreach($question->options as $opt)
                    <label class="option-label">
                        <input type="checkbox" name="q_{{ $question->id }}[]" value="{{ $opt->id }}"
                               {{ in_array((int)$opt->id, $selectedIds) ? 'checked' : '' }}>
                        <span class="opt-text">{{ $opt->option_text }}</span>
                    </label>
                    @endforeach
                </div>

            @elseif($question->question_type === 'paragraph')
                <textarea name="q_{{ $question->id }}" class="ans-input" rows="5"
                          placeholder="Write your answer hereâ€¦"
                          {{ $question->is_required ? 'required' : '' }}>{{ $existing?->answer_text }}</textarea>

            @elseif($question->question_type === 'short_answer')
                <input type="text" name="q_{{ $question->id }}" class="ans-input"
                       value="{{ $existing?->answer_text }}"
                       placeholder="Your answerâ€¦"
                       {{ $question->is_required ? 'required' : '' }}>

            @elseif($question->question_type === 'date')
                <input type="date" name="q_{{ $question->id }}" class="ans-input"
                       value="{{ $existing?->answer_text }}"
                       {{ $question->is_required ? 'required' : '' }}>

            @elseif($question->question_type === 'file_upload')
                <input type="file" name="q_{{ $question->id }}" class="ans-input"
                       {{ $question->is_required && !$existing?->file_path ? 'required' : '' }}>
                @if($existing?->file_path)
                <div style="font-size:12px;color:#16a34a;margin-top:6px;">ðŸ“Ž File already uploaded â€” upload a new one to replace.</div>
                @endif

            @elseif($question->question_type === 'declaration')
                <div class="decl-box">
                    <input type="checkbox" name="q_{{ $question->id }}" value="1"
                           id="decl_{{ $question->id }}"
                           {{ $existing?->answer_text === 'Yes' ? 'checked' : '' }}
                           {{ $question->is_required ? 'required' : '' }}>
                    <label for="decl_{{ $question->id }}" style="font-size:14px;color:#374151;cursor:pointer;">
                        I confirm and agree to the above declaration.
                    </label>
                </div>
            @endif
        </div>
        @endforeach

        <div class="submit-bar">
            <div style="font-size:13px;color:#64748b;">
                <strong>{{ $questions->count() }}</strong> questions total Â·
                Fields marked <span style="color:#ef4444;">*</span> are required
            </div>
            <button type="submit" class="submit-btn">ðŸ“¤ Submit Exam</button>
        </div>
    </form>
</div>

<script>
function confirmSubmit() {
    return confirm('Are you sure you want to submit your exam? You cannot change your answers after submission.');
}

@if($questionSet->time_limit_minutes)
// Countdown timer
let totalSeconds = {{ $questionSet->time_limit_minutes * 60 }};
const timerEl = document.getElementById('timerDisplay');
const interval = setInterval(() => {
    totalSeconds--;
    const m = Math.floor(totalSeconds / 60);
    const s = totalSeconds % 60;
    timerEl.textContent = m + ':' + String(s).padStart(2,'0');
    if (totalSeconds <= 60) timerEl.style.color = '#fbbf24';
    if (totalSeconds <= 0) {
        clearInterval(interval);
        timerEl.textContent = 'Time\'s up!';
        document.getElementById('examForm').submit();
    }
}, 1000);
@endif
</script>

</body>
</html>
