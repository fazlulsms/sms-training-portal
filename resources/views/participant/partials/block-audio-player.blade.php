@php
    $bapIsReady = $block->audio_enabled && $blockAudio && $blockAudio->isReady();
    $audioId    = 'block_' . $block->id;
    $sapDur     = $blockAudio->duration_seconds ?? 0;
@endphp

@if($bapIsReady)

@once
<style>
/* ── Section Audio Player (SAP) ─────────────────────────── */
.sap {
    display:flex; align-items:center; gap:12px;
    padding:10px 16px; margin-bottom:14px;
    background:linear-gradient(135deg,#1e1b4b 0%,#312e81 100%);
    border-radius:12px; border:1px solid rgba(139,92,246,.2);
    box-shadow:0 4px 16px rgba(30,27,75,.25);
}
.sap-play {
    width:36px; height:36px; border-radius:50%; border:none;
    background:#7c3aed; cursor:pointer; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    transition:.15s; box-shadow:0 2px 10px rgba(124,58,237,.45);
}
.sap-play:hover { background:#6d28d9; transform:scale(1.08); }
.sap-play:active { transform:scale(.96); }
.sap-center { flex:1; display:flex; flex-direction:column; gap:5px; min-width:0; }
.sap-top { display:flex; align-items:center; gap:8px; }
.sap-lbl  { font-size:10.5px; font-weight:700; color:rgba(255,255,255,.45); text-transform:uppercase; letter-spacing:.07em; white-space:nowrap; }
.sap-wave { display:flex; align-items:center; gap:2px; }
.sap-wave span { width:3px; height:14px; border-radius:2px; background:#a78bfa; display:block; animation:sapWv .8s ease-in-out infinite; }
.sap-wave span:nth-child(2) { animation-delay:.13s; }
.sap-wave span:nth-child(3) { animation-delay:.26s; }
.sap-wave span:nth-child(4) { animation-delay:.39s; }
.sap-wave.paused span { animation:none; opacity:.3; transform:scaleY(.35); }
@keyframes sapWv { 0%,100%{transform:scaleY(.3)} 50%{transform:scaleY(1)} }
/* Progress bar */
.sap-prog-row { display:flex; align-items:center; gap:8px; }
.sap-bar-wrap { flex:1; height:3px; background:rgba(255,255,255,.12); border-radius:4px; overflow:hidden; cursor:pointer; position:relative; }
.sap-bar-fill { height:100%; background:linear-gradient(90deg,#7c3aed,#a78bfa); border-radius:4px; width:0; transition:width .3s linear; pointer-events:none; }
.sap-time { font-size:10px; font-weight:700; color:rgba(255,255,255,.4); white-space:nowrap; font-variant-numeric:tabular-nums; }
/* Mute */
.sap-mute {
    background:none; border:none; cursor:pointer; padding:6px;
    color:rgba(255,255,255,.45); display:flex; align-items:center;
    justify-content:center; flex-shrink:0; transition:color .15s; border-radius:8px;
}
.sap-mute:hover { color:#fff; background:rgba(255,255,255,.08); }
</style>
@endonce

<div class="sap" data-sap-id="{{ $audioId }}">
    <button class="sap-play" id="sap-play-{{ $audioId }}"
            onclick="sapToggle('{{ $audioId }}')" type="button" title="Play / Pause">
        <svg id="sap-play-icon-{{ $audioId }}" width="13" height="13" viewBox="0 0 24 24" fill="white" stroke="none"><polygon points="5 3 19 12 5 21 5 3"/></svg>
        <svg id="sap-pause-icon-{{ $audioId }}" width="13" height="13" viewBox="0 0 24 24" fill="white" stroke="none" style="display:none"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>
    </button>
    <div class="sap-center">
        <div class="sap-top">
            <span class="sap-wave paused" id="sap-wave-{{ $audioId }}">
                <span></span><span></span><span></span><span></span>
            </span>
            <span class="sap-lbl">Section Audio</span>
        </div>
        <div class="sap-prog-row">
            <div class="sap-bar-wrap" onclick="sapSeek('{{ $audioId }}', event)" title="Seek">
                <div class="sap-bar-fill" id="sap-bar-{{ $audioId }}"></div>
            </div>
            <span class="sap-time" id="sap-time-{{ $audioId }}">{{ $sapDur > 0 ? '0:00 / ' . gmdate('G:i', $sapDur) : '0:00' }}</span>
        </div>
    </div>
    <button class="sap-mute" id="sap-mute-{{ $audioId }}"
            onclick="sapMute('{{ $audioId }}')" type="button" title="Mute / Unmute">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="sap-mute-icon-{{ $audioId }}"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>
    </button>
    <audio id="sap-audio-{{ $audioId }}" src="{{ $blockAudio->publicUrl() }}" preload="none"></audio>
</div>

@endif
