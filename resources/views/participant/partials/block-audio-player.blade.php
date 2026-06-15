@php
    // Only render this partial when block has audio enabled AND audio is ready.
    // Administrators generate and review audio before publishing — learners never trigger generation.
    $bapIsReady = $block->audio_enabled && $blockAudio && $blockAudio->isReady();
    $audioId    = 'block_' . $block->id;
@endphp

@if($bapIsReady)

@once
<style>
/* ── Shared audio player controls ── */
.lf-aw-label {
    font-size:11px; font-weight:700; color:rgba(255,255,255,.5);
    text-transform:uppercase; letter-spacing:.05em; margin-bottom:12px;
    display:flex; align-items:center; gap:6px;
}
.lf-aw-label-dot {
    width:6px; height:6px; border-radius:50%; background:#a78bfa; flex-shrink:0;
    animation: lf-pulse 1.8s ease-in-out infinite;
}
.lf-aw-label-dot.paused { animation:none; background:rgba(167,139,250,.4); }
@keyframes lf-pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(.75)} }
.lf-aw-controls { display:flex; align-items:center; gap:10px; }
.lf-aw-play-btn {
    width:42px; height:42px; border-radius:50%; background:#7c3aed; border:none;
    display:flex; align-items:center; justify-content:center; cursor:pointer;
    flex-shrink:0; transition:.15s; box-shadow:0 2px 8px rgba(124,58,237,.5);
}
.lf-aw-play-btn:hover { background:#6d28d9; transform:scale(1.05); }
.lf-aw-timeline { flex:1; display:flex; flex-direction:column; gap:5px; }
.lf-aw-seek {
    -webkit-appearance:none; appearance:none; width:100%; height:4px;
    border-radius:2px; background:rgba(255,255,255,.2); outline:none; cursor:pointer;
}
.lf-aw-seek::-webkit-slider-thumb {
    -webkit-appearance:none; appearance:none; width:14px; height:14px;
    border-radius:50%; background:#a78bfa; cursor:pointer;
    box-shadow:0 0 0 2px rgba(167,139,250,.4);
}
.lf-aw-time {
    display:flex; justify-content:space-between; font-size:11px;
    color:rgba(255,255,255,.45); font-variant-numeric:tabular-nums;
}
.lf-aw-stop-btn {
    display:inline-flex; align-items:center; gap:5px;
    padding:6px 11px; border-radius:7px; flex-shrink:0;
    background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.18);
    cursor:pointer; font-size:11.5px; font-weight:700; color:rgba(255,255,255,.75);
    transition:.15s;
}
.lf-aw-stop-btn:hover { background:rgba(255,255,255,.2); color:white; }
.lf-aw-speed-btn {
    font-size:11.5px; font-weight:800; color:#a78bfa;
    background:none; border:1px solid rgba(167,139,250,.4);
    border-radius:5px; padding:3px 7px; cursor:pointer;
    flex-shrink:0; transition:.15s; min-width:40px; text-align:center;
}
.lf-aw-speed-btn:hover { background:rgba(167,139,250,.15); }

/* ── Block player wrapper ── */
.bap {
    margin-top: 16px;
    padding-top: 14px;
    border-top: 1px solid rgba(124,58,237,.12);
}
.bap-player-wrap {
    background: linear-gradient(135deg,#1e1b4b 0%,#312e81 100%);
    border-radius: 12px;
    padding: 14px 16px;
    border: 1px solid rgba(139,92,246,.3);
    box-shadow: 0 3px 14px rgba(124,58,237,.18);
}
</style>
@endonce

<div class="bap" id="bap_{{ $block->id }}">
    <div class="bap-player-wrap">
        @include('participant.partials.audio-player-controls', [
            'audioId' => $audioId,
            'label'   => '&#9654; Play Audio',
        ])
        <audio id="lfAudio_{{ $audioId }}" preload="none" src="{{ $blockAudio->publicUrl() }}"></audio>
    </div>
</div>

@endif
