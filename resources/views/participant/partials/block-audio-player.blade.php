@php
    $bapIsReady = $block->audio_enabled && $blockAudio && $blockAudio->isReady();
    $audioId    = 'block_' . $block->id;
@endphp

@if($bapIsReady)

@once
<style>
/* ── Section Audio Player (SAP) — minimal: play/pause + mute only ── */
.sap {
    display:flex; align-items:center; gap:10px;
    padding:8px 14px; margin-bottom:14px;
    background:linear-gradient(135deg,#1e1b4b 0%,#312e81 100%);
    border-radius:10px; border:1px solid rgba(139,92,246,.25);
}
.sap-play {
    width:34px; height:34px; border-radius:50%; border:none;
    background:#7c3aed; cursor:pointer; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    transition:.15s; box-shadow:0 2px 8px rgba(124,58,237,.4);
}
.sap-play:hover { background:#6d28d9; transform:scale(1.06); }
.sap-info { flex:1; display:flex; align-items:center; gap:8px; min-width:0; }
.sap-lbl  { font-size:11px; font-weight:700; color:rgba(255,255,255,.45); text-transform:uppercase; letter-spacing:.06em; white-space:nowrap; }
.sap-wave { display:flex; align-items:center; gap:2px; flex-shrink:0; }
.sap-wave span { width:3px; height:13px; border-radius:2px; background:#a78bfa; display:block; animation:sapWv .8s ease-in-out infinite; }
.sap-wave span:nth-child(2) { animation-delay:.16s; }
.sap-wave span:nth-child(3) { animation-delay:.32s; }
.sap-wave span:nth-child(4) { animation-delay:.48s; }
.sap-wave.paused span { animation:none; opacity:.25; transform:scaleY(.35); }
@keyframes sapWv { 0%,100%{transform:scaleY(.3)} 50%{transform:scaleY(1)} }
.sap-mute {
    background:none; border:none; cursor:pointer; padding:5px;
    color:rgba(255,255,255,.5); display:flex; align-items:center;
    justify-content:center; flex-shrink:0; transition:color .15s; border-radius:6px;
}
.sap-mute:hover { color:#fff; background:rgba(255,255,255,.1); }
</style>
@endonce

<div class="sap" data-sap-id="{{ $audioId }}">
    <button class="sap-play" id="sap-play-{{ $audioId }}"
            onclick="sapToggle('{{ $audioId }}')" type="button" title="Play / Pause">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="white" stroke="none"><polygon points="5 3 19 12 5 21 5 3"/></svg>
    </button>
    <div class="sap-info">
        <span class="sap-wave paused" id="sap-wave-{{ $audioId }}">
            <span></span><span></span><span></span><span></span>
        </span>
        <span class="sap-lbl">Section Audio</span>
    </div>
    <button class="sap-mute" id="sap-mute-{{ $audioId }}"
            onclick="sapMute('{{ $audioId }}')" type="button" title="Mute / Unmute">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="sap-mute-icon-{{ $audioId }}"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>
    </button>
    <audio id="sap-audio-{{ $audioId }}" src="{{ $blockAudio->publicUrl() }}" preload="none"></audio>
</div>

@endif
