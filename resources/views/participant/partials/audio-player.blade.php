@php
    $hasNarration   = $narrationAudio && $narrationAudio->isReady();
    $hasExplanation = $aiExplanationAudio && $aiExplanationAudio->isReady();
    if (!$hasNarration && !$hasExplanation) return;
@endphp

<div class="lf-audio-widget" id="lfAudioWidget">
    <style>
    .lf-audio-widget {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
        border-radius: 14px;
        padding: 18px 20px;
        margin-bottom: 20px;
        border: 1px solid rgba(139,92,246,.35);
        box-shadow: 0 4px 20px rgba(124,58,237,.2);
    }
    .lf-aw-tabs { display:flex; gap:6px; margin-bottom:16px; }
    .lf-aw-tab {
        flex:1; padding:8px 12px; border-radius:8px; border:none;
        font-size:12.5px; font-weight:700; cursor:pointer; transition:.15s;
        display:flex; align-items:center; justify-content:center; gap:5px;
        background:rgba(255,255,255,.1); color:rgba(255,255,255,.65);
    }
    .lf-aw-tab:hover  { background:rgba(255,255,255,.18); }
    .lf-aw-tab.active { background:rgba(255,255,255,.22); color:white; }
    .lf-aw-player-area { display:none; }
    .lf-aw-player-area.active { display:block; }
    .lf-aw-label {
        font-size:11px; font-weight:700; color:rgba(255,255,255,.45);
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
    </style>

    @if($hasNarration && $hasExplanation)
    <div class="lf-aw-tabs">
        <button class="lf-aw-tab active" id="lf-tab-narration" onclick="lfAudioSwitch('narration')">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5"/><path d="M17.5 2.5a2.121 2.121 0 0 1 3 3L12 14l-4 1 1-4 9.5-9.5z"/></svg>
            Listen to Lesson
        </button>
        <button class="lf-aw-tab" id="lf-tab-ai_explanation" onclick="lfAudioSwitch('ai_explanation')">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/><path d="M12 12h.01"/><path d="M17 12h.01"/><path d="M7 12h.01"/></svg>
            AI Teacher
        </button>
    </div>
    @else
    <div style="font-size:12.5px;font-weight:700;color:rgba(255,255,255,.8);margin-bottom:14px;display:flex;align-items:center;gap:7px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
        {{ $hasNarration ? 'Listen to Lesson' : 'AI Teacher' }}
    </div>
    @endif

    @if($hasNarration)
    <div class="lf-aw-player-area active" id="lf-player-narration">
        @include('participant.partials.audio-player-controls', [
            'audioId' => 'narration',
            'label'   => 'Lesson Narration &mdash; Overview',
        ])
    </div>
    @endif

    @if($hasExplanation)
    <div class="lf-aw-player-area @if(!$hasNarration) active @endif" id="lf-player-ai_explanation">
        @include('participant.partials.audio-player-controls', [
            'audioId' => 'ai_explanation',
            'label'   => 'AI Teacher Explanation &mdash; Overview',
        ])
    </div>
    @endif

    {{-- Hidden audio elements --}}
    <audio id="lfAudio_narration"      preload="none" @if($hasNarration)   src="{{ $narrationAudio->publicUrl() }}"    @endif></audio>
    <audio id="lfAudio_ai_explanation" preload="none" @if($hasExplanation) src="{{ $aiExplanationAudio->publicUrl() }}" @endif></audio>

</div>

<script>
(function() {
    const ALL_TYPES = ['narration', 'ai_explanation'];
    const audios    = {};
    const speeds    = [0.75, 1, 1.25, 1.5, 2];
    const speedIdx  = { narration: 1, ai_explanation: 1 };
    const saveKey   = 'lfAudioPos_{{ $lesson->id }}_';

    function fmt(s) {
        s = Math.floor(s || 0);
        return Math.floor(s / 60) + ':' + String(s % 60).padStart(2, '0');
    }

    function playIcon() {
        return '<svg width="16" height="16" viewBox="0 0 24 24" fill="white" stroke="none"><polygon points="5 3 19 12 5 21 5 3"/></svg>';
    }
    function pauseIcon() {
        return '<svg width="16" height="16" viewBox="0 0 24 24" fill="white" stroke="none"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>';
    }

    function resetPlayerUI(type) {
        const playBtn = document.getElementById('lfPlay_' + type);
        const seek    = document.getElementById('lfSeek_' + type);
        const curEl   = document.getElementById('lfCur_' + type);
        const dot     = document.getElementById('lfDot_' + type);
        if (playBtn) playBtn.innerHTML = playIcon();
        if (seek)    { seek.value = 0; seek.style.background = 'rgba(255,255,255,.2)'; }
        if (curEl)   curEl.textContent = '0:00';
        if (dot)     dot.classList.add('paused');
    }

    function initPlayer(type) {
        const audio = document.getElementById('lfAudio_' + type);
        if (!audio || audios[type]) return;
        audios[type] = audio;

        const seek   = document.getElementById('lfSeek_' + type);
        const curEl  = document.getElementById('lfCur_' + type);
        const durEl  = document.getElementById('lfDur_' + type);
        const playBtn= document.getElementById('lfPlay_' + type);
        const dot    = document.getElementById('lfDot_' + type);

        audio.addEventListener('loadedmetadata', () => {
            if (seek)  seek.max = audio.duration;
            if (durEl) durEl.textContent = fmt(audio.duration);
        });

        audio.addEventListener('timeupdate', () => {
            if (!audio.duration) return;
            const pct = (audio.currentTime / audio.duration) * 100;
            if (seek) {
                seek.value = audio.currentTime;
                seek.style.background = `linear-gradient(to right,#7c3aed ${pct}%,rgba(255,255,255,.2) 0)`;
            }
            if (curEl) curEl.textContent = fmt(audio.currentTime);
        });

        audio.addEventListener('play', () => {
            if (playBtn) playBtn.innerHTML = pauseIcon();
            if (dot)     dot.classList.remove('paused');
        });

        audio.addEventListener('pause', () => {
            if (playBtn) playBtn.innerHTML = playIcon();
            if (dot)     dot.classList.add('paused');
        });

        audio.addEventListener('ended', () => {
            resetPlayerUI(type);
            sessionStorage.removeItem(saveKey + type);
        });
    }

    // ── Public API (called by lesson-show.blade.php navigation) ──

    // Stop ALL audio immediately — called on every section change
    window.lfAudioStopAll = function() {
        ALL_TYPES.forEach(type => {
            const audio = audios[type];
            if (!audio) return;
            audio.pause();
            audio.currentTime = 0;
            resetPlayerUI(type);
            sessionStorage.removeItem(saveKey + type);
        });
    };

    window.lfAudioSwitch = function(type) {
        ALL_TYPES.forEach(t => {
            const panel = document.getElementById('lf-player-' + t);
            const tab   = document.getElementById('lf-tab-' + t);
            if (panel) panel.classList.toggle('active', t === type);
            if (tab)   tab.classList.toggle('active', t === type);
            // Pause & reset the tab being left
            if (t !== type && audios[t] && !audios[t].paused) {
                audios[t].pause();
                audios[t].currentTime = 0;
                resetPlayerUI(t);
            }
        });
        initPlayer(type);
    };

    window.lfAudioPlay = function(type) {
        initPlayer(type);
        const audio = audios[type];
        if (!audio) return;
        if (audio.paused) {
            // Stop any other playing audio first (only one at a time)
            ALL_TYPES.forEach(t => {
                if (t !== type && audios[t] && !audios[t].paused) {
                    audios[t].pause();
                    resetPlayerUI(t);
                }
            });
            audio.play().catch(() => {});
        } else {
            audio.pause();
        }
    };

    window.lfAudioStop = function(type) {
        const audio = audios[type];
        if (!audio) return;
        audio.pause();
        audio.currentTime = 0;
        resetPlayerUI(type);
        sessionStorage.removeItem(saveKey + type);
    };

    window.lfAudioSeek = function(type, el) {
        if (audios[type]) audios[type].currentTime = parseFloat(el.value);
    };

    window.lfAudioSpeed = function(type, btn) {
        speedIdx[type] = (speedIdx[type] + 1) % speeds.length;
        const sp = speeds[speedIdx[type]];
        if (audios[type]) audios[type].playbackRate = sp;
        if (btn) btn.textContent = sp + 'x';
    };

    // Init the visible player on page load
    @if($hasNarration)
    initPlayer('narration');
    @elseif($hasExplanation)
    initPlayer('ai_explanation');
    @endif
})();
</script>
