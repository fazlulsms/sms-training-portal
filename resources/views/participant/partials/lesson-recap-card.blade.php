@php
    // Show only when audio is ready. Learners never generate audio — it is prepared by administrators.
    $rcpIsReady = $lessonRecapAudio && $lessonRecapAudio->isReady();
    $rcpAudioId = 'lesson_recap';
    $showCard   = $rcpIsReady || ($previewMode ?? false);
@endphp

@if($showCard)
<div class="lb" style="background:linear-gradient(135deg,#f5f3ff 0%,#ede9fe 100%);border:1px solid rgba(124,58,237,.2);margin-bottom:16px;" id="rcp-card">
    <div class="lb-head" style="background:transparent;border-bottom:1px solid rgba(124,58,237,.15);">
        <div class="lb-head-icon" style="background:linear-gradient(135deg,#7c3aed,#6d28d9);color:white;font-size:16px;">🎧</div>
        <span class="lb-head-title" style="color:#1e1b4b;">Lesson Audio Summary</span>
    </div>
    <div class="lb-body" style="padding-top:14px;">

        @if($rcpIsReady)
            <p style="font-size:13px;color:#4b5563;margin:0 0 12px;">
                Listen to a summary of what you've learned — key concepts, common mistakes, and practical workplace tips.
            </p>
            <div class="bap-player-wrap">
                @include('participant.partials.audio-player-controls', [
                    'audioId' => $rcpAudioId,
                    'label'   => '&#9654; Lesson Audio Summary',
                ])
                <audio id="lfAudio_{{ $rcpAudioId }}"
                       preload="none"
                       src="{{ $lessonRecapAudio->publicUrl() }}"
                       data-audio-db-id="{{ $lessonRecapAudio->id }}"
                       data-audio-duration="{{ $lessonRecapAudio->duration_seconds ?? 0 }}"></audio>
            </div>

        @else
            {{-- Preview mode placeholder --}}
            <p style="font-size:13px;color:#6b7280;margin:0;font-style:italic;">
                An audio summary is available for enrolled learners after the lesson audio is prepared.
            </p>
        @endif

    </div>
</div>
@endif
