@php
    $rcpIsPreview = $previewMode ?? false;
    $rcpIsReady   = $lessonRecapAudio && $lessonRecapAudio->isReady();
    $rcpAudioId   = 'lesson_recap';

    $rcpGenerateUrl = (!$rcpIsPreview && isset($enrollment) && $enrollment)
        ? route('participant.lesson.recap.generate', [$enrollment->id, $lesson->id])
        : null;
@endphp

<div class="lb" style="background:linear-gradient(135deg,#f5f3ff 0%,#ede9fe 100%);border:1px solid rgba(124,58,237,.2);margin-bottom:16px;" id="rcp-card">
    <div class="lb-head" style="background:transparent;border-bottom:1px solid rgba(124,58,237,.15);">
        <div class="lb-head-icon" style="background:linear-gradient(135deg,#7c3aed,#6d28d9);color:white;font-size:16px;">🎓</div>
        <span class="lb-head-label" style="color:#7c3aed;">AI Coach</span>
        <span class="lb-head-title" style="color:#1e1b4b;">Lesson Recap</span>
    </div>
    <div class="lb-body" style="padding-top:14px;">

        @if($rcpIsReady)
            <p style="font-size:13px;color:#4b5563;margin:0 0 12px;">
                A personalised recap covering what you've learned, key concepts, common mistakes, and practical workplace tips.
            </p>
            <div class="bap-player-wrap">
                @include('participant.partials.audio-player-controls', [
                    'audioId' => $rcpAudioId,
                    'label'   => 'AI Lesson Recap &mdash; ' . $lesson->title,
                ])
                <audio id="lfAudio_{{ $rcpAudioId }}" preload="none" src="{{ $lessonRecapAudio->publicUrl() }}"></audio>
            </div>

        @elseif($rcpGenerateUrl)
            <p style="font-size:13px;color:#4b5563;margin:0 0 14px;">
                Get a personalised 2-minute audio recap — key concepts, common mistakes, and practical tips for the workplace.
            </p>
            <div class="bap-trigger" id="rcp-trigger">
                <button class="bap-btn" style="font-size:13px;padding:8px 16px;border-radius:8px;" onclick="rcpGenerate('{{ $rcpGenerateUrl }}', '{{ csrf_token() }}')">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2a3 3 0 0 1 3 3v7a3 3 0 0 1-6 0V5a3 3 0 0 1 3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/><line x1="8" y1="23" x2="16" y2="23"/></svg>
                    Get AI Lesson Recap
                </button>
                <span style="font-size:11.5px;color:#6b7280;">&approx; 2 min</span>
            </div>
            <div class="bap-loading" id="rcp-loading">
                <div class="bap-spinner" style="border-color:rgba(124,58,237,.25);border-top-color:#7c3aed;"></div>
                Generating your lesson recap&hellip; this takes about 20 seconds.
            </div>
            <div id="rcp-player"></div>

        @else
            <p style="font-size:13px;color:#6b7280;margin:0;font-style:italic;">
                AI Lesson Recap is available to enrolled learners after completing the lesson content.
            </p>
        @endif

    </div>
</div>
