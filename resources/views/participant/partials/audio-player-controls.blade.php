<div class="lf-aw-label">{{ $label }}</div>
<div class="lf-aw-controls">
    <button class="lf-aw-play-btn" id="lfPlay_{{ $audioId }}" onclick="lfAudioPlay('{{ $audioId }}')">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="white" stroke="none"><polygon points="5 3 19 12 5 21 5 3"/></svg>
    </button>
    <div class="lf-aw-timeline">
        <input type="range" class="lf-aw-seek" id="lfSeek_{{ $audioId }}" min="0" max="100" value="0"
               oninput="lfAudioSeek('{{ $audioId }}', this)">
        <div class="lf-aw-time">
            <span id="lfCur_{{ $audioId }}">0:00</span>
            <span id="lfDur_{{ $audioId }}">0:00</span>
        </div>
    </div>
    <button class="lf-aw-stop-btn" onclick="lfAudioStop('{{ $audioId }}')" title="Stop">
        <svg width="10" height="10" viewBox="0 0 24 24" fill="rgba(255,255,255,.7)" stroke="none"><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
    </button>
    <button class="lf-aw-speed-btn" id="lfSpeed_{{ $audioId }}" onclick="lfAudioSpeed('{{ $audioId }}', this)" title="Playback speed">1x</button>
</div>
