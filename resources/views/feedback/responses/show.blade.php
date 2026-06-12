@extends('layouts.app')
@section('page-title', 'Feedback Response')
@section('content')
<div style="max-width:820px; margin:auto;">
    <div style="margin-bottom:16px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
        <a href="{{ route('feedback.responses.index') }}" style="color:#6b7280; font-size:13px; text-decoration:none;">← Back to Responses</a>
        <form method="POST" action="{{ route('feedback.responses.destroy', $response) }}" onsubmit="return confirm('Delete this response?')">
            @csrf @method('DELETE')
            <button type="submit" style="background:#fee2e2; color:#b91c1c; padding:7px 16px; border-radius:7px; font-size:12.5px; font-weight:700; border:none; cursor:pointer;">Delete Response</button>
        </form>
    </div>

    {{-- Respondent info --}}
    <div style="background:#fff; border-radius:12px; padding:20px 24px; box-shadow:0 2px 8px rgba(0,0,0,.06); margin-bottom:16px;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:10px;">
            <div>
                <div style="font-size:17px; font-weight:800; color:#111827;">{{ $response->respondent_name ?: ($response->user?->name ?? 'Anonymous') }}</div>
                <div style="font-size:13px; color:#6b7280; margin-top:2px;">{{ $response->respondent_email ?: $response->user?->email }}</div>
                <div style="font-size:12px; color:#9ca3af; margin-top:4px;">
                    Submitted: {{ $response->submitted_at?->format('d M Y, h:i A') }}
                    @if($response->is_demo) &nbsp;
                    <span style="background:#fef3c7; color:#92400e; font-size:10px; font-weight:700; padding:1px 7px; border-radius:99px;">DEMO FEEDBACK</span>
                    @endif
                </div>
            </div>
            <div style="text-align:right;">
                @if($response->overall_rating)
                <div style="font-size:28px; font-weight:800; color:#d97706;">{{ number_format($response->overall_rating, 1) }} <span style="font-size:20px;">★</span></div>
                <div style="font-size:11px; color:#9ca3af;">Overall Rating</div>
                @endif
            </div>
        </div>
        <div style="margin-top:14px; padding-top:14px; border-top:1px solid #f3f4f6; font-size:13px; color:#6b7280;">
            Template: <strong style="color:#374151;">{{ $response->assignment?->template?->name }}</strong>
            &nbsp;·&nbsp;
            Type: <strong style="color:#374151;">{{ $response->assignment?->template?->type_label }}</strong>
        </div>
    </div>

    {{-- Testimonial --}}
    @if($response->testimonial_consent && $response->testimonial_text)
    <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:10px; padding:16px 20px; margin-bottom:16px;">
        <div style="font-size:12px; font-weight:700; color:#92400e; margin-bottom:6px;">
            💬 PUBLIC TESTIMONIAL
            @if($response->testimonial_approved)
            <span style="background:#d1fae5; color:#065f46; padding:1px 8px; border-radius:99px; font-size:10px;">Approved</span>
            @else
            <span style="background:#fee2e2; color:#b91c1c; padding:1px 8px; border-radius:99px; font-size:10px;">Pending Approval</span>
            @endif
        </div>
        <p style="font-size:13.5px; color:#374151; margin:0 0 10px; font-style:italic;">"{{ $response->testimonial_text }}"</p>
        @if(!$response->testimonial_approved)
        <form method="POST" action="{{ route('feedback.responses.approve-testimonial', $response) }}" style="display:inline;">
            @csrf
            <button type="submit" style="background:#059669; color:#fff; padding:6px 14px; border-radius:6px; font-size:12px; font-weight:700; border:none; cursor:pointer;">Approve for Public Display</button>
        </form>
        @endif
    </div>
    @endif

    {{-- Answers --}}
    <div style="background:#fff; border-radius:12px; padding:22px 24px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
        <div style="font-size:15px; font-weight:700; color:#111827; margin-bottom:16px;">Answers</div>

        @php
        $answersKeyed = $response->answers->keyBy('question_id');
        $questions    = $response->assignment?->template?->questions ?? collect();
        @endphp

        @foreach($questions as $i => $q)
        @php $ans = $answersKeyed->get($q->id); @endphp
        <div style="padding:12px 0; {{ !$loop->last ? 'border-bottom:1px solid #f3f4f6;' : '' }}">
            <div style="font-size:12px; color:#9ca3af; margin-bottom:3px;">
                Q{{ $i + 1 }} · {{ \App\Models\FeedbackQuestion::$CATEGORIES[$q->category] ?? $q->category }}
            </div>
            <div style="font-size:13.5px; font-weight:600; color:#374151; margin-bottom:6px;">{{ $q->question_text }}</div>
            <div style="font-size:14px; color:#111827;">
                @if(!$ans)
                <span style="color:#9ca3af; font-style:italic;">Not answered</span>
                @elseif($q->question_type === 'rating_5' && $ans->answer_rating)
                <span style="color:#d97706; font-size:18px;">{{ str_repeat('★', $ans->answer_rating) }}<span style="color:#e5e7eb;">{{ str_repeat('★', 5 - $ans->answer_rating) }}</span></span>
                <span style="font-size:13px; color:#6b7280; margin-left:6px;">{{ $ans->answer_rating }} / 5</span>
                @elseif($q->question_type === 'yes_no')
                <span style="{{ $ans->answer_bool ? 'color:#059669;' : 'color:#dc2626;' }} font-weight:700;">
                    {{ $ans->answer_bool ? '✅ Yes' : '❌ No' }}
                </span>
                @else
                <p style="margin:0; color:#374151; line-height:1.6;">{{ $ans->answer_text ?? '—' }}</p>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
