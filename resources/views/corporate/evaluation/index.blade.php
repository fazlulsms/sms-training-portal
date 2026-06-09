@extends('layouts.app')
@section('title', 'Training Evaluation')
@section('content')

<div class="page-header">
    <div>
        <div style="font-size:13px;color:#9ca3af;margin-bottom:4px;">
            <a href="{{ route('corporate.sessions.show', $session) }}" style="color:#6b7280;text-decoration:none;">{{ $session->course_name }}</a>
            / Evaluation
        </div>
        <h1 class="page-title">Training Evaluation</h1>
        <p class="page-subtitle">{{ $session->project->company_name }}</p>
    </div>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

<style>@media(max-width:768px){.eval-two-col{grid-template-columns:1fr!important;}}</style>
<div style="display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start;" class="eval-two-col">
<div>
    {{-- Add evaluation --}}
    <div class="card" style="margin-bottom:20px;">
        <div class="card-header"><h3 class="card-title">Add Feedback</h3></div>
        <div class="card-body">
            <form method="POST" action="{{ route('corporate.sessions.evaluation.store', $session) }}">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Evaluator Name</label>
                        <input type="text" name="evaluator_name" class="form-control" value="{{ old('evaluator_name') }}" placeholder="Participant or observer name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Overall Score (1–5) *</label>
                        <div style="display:flex;gap:6px;margin-top:4px;" id="starPicker">
                            @for($s = 1; $s <= 5; $s++)
                            <label style="cursor:pointer;">
                                <input type="radio" name="feedback_score" value="{{ $s }}" {{ old('feedback_score')==$s?'checked':'' }} style="display:none;">
                                <span class="star" data-val="{{ $s }}"
                                      style="font-size:28px;color:#d1d5db;transition:.1s;user-select:none;">★</span>
                            </label>
                            @endfor
                        </div>
                    </div>
                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label">Feedback / Comments</label>
                        <textarea name="comments" class="form-control" rows="3" placeholder="General feedback about the training…">{{ old('comments') }}</textarea>
                    </div>
                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label">Effectiveness Notes</label>
                        <textarea name="effectiveness_notes" class="form-control" rows="2" placeholder="What was effective? What could be improved?">{{ old('effectiveness_notes') }}</textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top:4px;">Add Feedback</button>
            </form>
        </div>
    </div>

    {{-- Evaluation list --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title">Responses ({{ $evaluations->count() }})</h3></div>
        <div class="card-body" style="padding:0;">
            @forelse($evaluations as $ev)
            <div style="padding:16px 20px;border-bottom:1px solid #f0f2f5;">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:8px;">
                    <div>
                        @if($ev->evaluator_name)
                        <div style="font-weight:700;font-size:14px;color:#111827;">{{ $ev->evaluator_name }}</div>
                        @endif
                        <div style="color:#f59e0b;font-size:18px;margin-top:2px;">
                            {{ str_repeat('★', $ev->feedback_score) }}<span style="color:#d1d5db;">{{ str_repeat('★', 5 - $ev->feedback_score) }}</span>
                            <span style="font-size:13px;color:#6b7280;margin-left:6px;">{{ $ev->feedback_score }}/5</span>
                        </div>
                    </div>
                    <div style="display:flex;gap:8px;align-items:center;flex-shrink:0;">
                        <span style="font-size:12px;color:#9ca3af;">{{ $ev->created_at->format('d M Y') }}</span>
                        <form method="POST" action="{{ route('corporate.sessions.evaluation.destroy', [$session, $ev]) }}" onsubmit="return confirm('Remove?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="background:none;border:none;color:#ef4444;font-size:16px;cursor:pointer;">✕</button>
                        </form>
                    </div>
                </div>
                @if($ev->comments)
                <div style="font-size:13.5px;color:#374151;line-height:1.6;margin-bottom:6px;">{{ $ev->comments }}</div>
                @endif
                @if($ev->effectiveness_notes)
                <div style="background:#f8f9fa;border-radius:8px;padding:8px 12px;font-size:13px;color:#6b7280;font-style:italic;">
                    💡 {{ $ev->effectiveness_notes }}
                </div>
                @endif
            </div>
            @empty
            <div style="text-align:center;padding:40px;color:#9ca3af;">No feedback yet.</div>
            @endforelse
        </div>
    </div>
</div>

<aside>
    {{-- Score summary --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title">Summary</h3></div>
        <div class="card-body" style="text-align:center;padding:24px;">
            @if($avgScore)
            <div style="font-size:48px;font-weight:900;color:#1e3a8a;line-height:1;">{{ number_format($avgScore,1) }}</div>
            <div style="font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">Average Score</div>
            <div style="color:#f59e0b;font-size:28px;margin-bottom:6px;">
                {{ str_repeat('★', round($avgScore)) }}<span style="color:#d1d5db;">{{ str_repeat('★', 5 - round($avgScore)) }}</span>
            </div>
            <div style="font-size:13px;color:#6b7280;">Based on {{ $evaluations->count() }} response(s)</div>

            {{-- Score distribution --}}
            <div style="margin-top:20px;text-align:left;">
                @for($s = 5; $s >= 1; $s--)
                @php $cnt = $evaluations->where('feedback_score', $s)->count(); $pct = $evaluations->count() > 0 ? ($cnt/$evaluations->count()*100) : 0; @endphp
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                    <span style="font-size:12px;color:#f59e0b;width:16px;">{{ $s }}★</span>
                    <div style="flex:1;background:#f0f2f5;border-radius:4px;height:8px;overflow:hidden;">
                        <div style="width:{{ $pct }}%;background:#f59e0b;height:100%;border-radius:4px;transition:.3s;"></div>
                    </div>
                    <span style="font-size:12px;color:#6b7280;width:20px;text-align:right;">{{ $cnt }}</span>
                </div>
                @endfor
            </div>
            @else
            <div style="color:#9ca3af;font-size:14px;">No evaluations yet.</div>
            @endif
        </div>
    </div>
</aside>
</div>

<script>
const stars = document.querySelectorAll('#starPicker .star');
stars.forEach(star => {
    star.parentElement.querySelector('input').addEventListener('change', function() {
        const val = parseInt(this.value);
        stars.forEach(s => {
            s.style.color = parseInt(s.dataset.val) <= val ? '#f59e0b' : '#d1d5db';
        });
    });
    star.addEventListener('mouseover', function() {
        const val = parseInt(this.dataset.val);
        stars.forEach(s => { s.style.color = parseInt(s.dataset.val) <= val ? '#f59e0b' : '#d1d5db'; });
    });
    star.addEventListener('mouseout', function() {
        const checked = document.querySelector('#starPicker input:checked');
        const val = checked ? parseInt(checked.value) : 0;
        stars.forEach(s => { s.style.color = parseInt(s.dataset.val) <= val ? '#f59e0b' : '#d1d5db'; });
    });
});
// Restore selected on load
const checked = document.querySelector('#starPicker input:checked');
if (checked) {
    const val = parseInt(checked.value);
    stars.forEach(s => { s.style.color = parseInt(s.dataset.val) <= val ? '#f59e0b' : '#d1d5db'; });
}
</script>
@endsection
