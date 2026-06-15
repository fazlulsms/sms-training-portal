@php
    $recapRecord      = $audioRecords->firstWhere('audio_type', 'lesson_recap');
    $blockCoachMap    = $audioRecords->where('audio_type', 'ai_coach')->keyBy('block_id');
    $audioEnabledBlocks = $blocks->where('audio_enabled', true)->where('status', 'active');
    $voices = [
        'nova'    => 'Nova (Female)',
        'alloy'   => 'Alloy (Neutral)',
        'echo'    => 'Echo (Male)',
        'fable'   => 'Fable (Male)',
        'onyx'    => 'Onyx (Male)',
        'shimmer' => 'Shimmer (Female)',
    ];
@endphp

<style>
.aap-card { background:var(--surface); border:1px solid var(--border); border-radius:var(--r-xl); box-shadow:var(--shadow-sm); overflow:hidden; }
.aap-head { background:linear-gradient(135deg,#1e40af 0%,#7c3aed 100%); padding:14px 18px; display:flex; align-items:center; gap:10px; }
.aap-head-icon { width:34px;height:34px;border-radius:8px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.aap-head-title { font-size:13px;font-weight:800;color:white;line-height:1.2; }
.aap-head-sub { font-size:11px;color:rgba(255,255,255,.65);margin-top:1px; }
.aap-body { padding:16px; }
.aap-row { padding:12px; border:1px solid var(--border); border-radius:var(--r-lg); background:#fafbfc; margin-bottom:10px; }
.aap-row:last-child { margin-bottom:0; }
.aap-row-head { display:flex;align-items:flex-start;gap:8px;margin-bottom:10px; }
.aap-type-label { font-size:12.5px;font-weight:700;color:var(--text); }
.aap-status-pill { padding:2px 9px;border-radius:20px;font-size:11px;font-weight:700;white-space:nowrap; }
.aap-status-pill.ready      { background:#dcfce7;color:#16a34a; }
.aap-status-pill.pending    { background:#fef9c3;color:#92400e; }
.aap-status-pill.processing { background:#dbeafe;color:#1d4ed8; }
.aap-status-pill.failed     { background:#fee2e2;color:#dc2626; }
.aap-status-pill.none       { background:#f3f4f6;color:#6b7280; }
.aap-mini-player { margin-bottom:10px; }
.aap-mini-player audio { width:100%;height:36px;border-radius:6px; }
.aap-duration-badge { display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:600;color:#6b7280;background:#f3f4f6;padding:2px 8px;border-radius:5px; }
.aap-actions { display:flex;flex-wrap:wrap;gap:6px;align-items:center;margin-top:6px; }
.aap-btn { display:inline-flex;align-items:center;gap:5px;padding:5px 11px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;border:none;transition:.15s; }
.aap-btn:disabled { opacity:.5;cursor:not-allowed; }
.aap-btn-generate { background:#7c3aed;color:white; }
.aap-btn-generate:hover:not(:disabled) { background:#6d28d9; }
.aap-btn-regen { background:#e0e7ff;color:#4338ca; }
.aap-btn-regen:hover:not(:disabled) { background:#c7d2fe; }
.aap-btn-del { background:#fee2e2;color:#dc2626; }
.aap-btn-del:hover:not(:disabled) { background:#fecaca; }
.aap-error { font-size:11px;color:#dc2626;margin-top:6px;line-height:1.4; }
.aap-meta { font-size:11px;color:var(--text-muted);margin-top:4px; }
.aap-spinner { display:inline-block;width:13px;height:13px;border:2px solid rgba(255,255,255,.3);border-top-color:white;border-radius:50%;animation:aap-spin .7s linear infinite;flex-shrink:0; }
.aap-spinner.dark { border-color:rgba(67,56,202,.3);border-top-color:#4338ca; }
@keyframes aap-spin { to { transform:rotate(360deg); } }
.aap-block-section { margin-top:6px; }
.aap-block-item { padding:10px 12px;border:1px solid var(--border-light);border-radius:var(--r);background:white;margin-bottom:8px; }
.aap-block-item:last-child { margin-bottom:0; }
.aap-block-item-head { display:flex;align-items:center;gap:7px;margin-bottom:8px; }
.aap-block-type-badge { font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.4px;padding:2px 7px;border-radius:10px;color:white;flex-shrink:0; }
.aap-block-title { font-size:12px;font-weight:600;color:var(--text);flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
.aap-no-audio-blocks { font-size:12px;color:var(--text-muted);font-style:italic;padding:6px 0; }
.aap-voice-sel { padding:4px 8px;border-radius:6px;border:1px solid var(--border);font-size:11.5px;font-family:inherit;color:var(--text);background:white;cursor:pointer; }
</style>

<div class="aap-card" id="aap-card" style="margin-top:14px;">
    <div class="aap-head">
        <div class="aap-head-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
        </div>
        <div>
            <div class="aap-head-title">Audio Management</div>
            <div class="aap-head-sub">Generate and review audio before publishing &mdash; learners play instantly</div>
        </div>
    </div>
    <div class="aap-body">

        {{-- ── Lesson Audio Summary ── --}}
        <div class="aap-row" id="aap-row-recap">
            <div class="aap-row-head">
                <div style="flex:1;">
                    <div class="aap-type-label">🎧 Lesson Audio Summary</div>
                    <div style="font-size:11px;color:var(--text-muted);">End-of-lesson summary — key concepts, common mistakes, workplace tips (approx. 2 min)</div>
                </div>
                <div style="flex-shrink:0;">
                    @if(!$recapRecord)
                        <span class="aap-status-pill none">Not generated</span>
                    @else
                        <span class="aap-status-pill {{ $recapRecord->status }}">{{ ucfirst($recapRecord->status) }}</span>
                    @endif
                </div>
            </div>

            @if($recapRecord && $recapRecord->isReady())
            <div class="aap-mini-player">
                <audio controls preload="none" src="{{ $recapRecord->publicUrl() }}"></audio>
                <div class="aap-meta" style="display:flex;align-items:center;gap:8px;margin-top:5px;">
                    @if($recapRecord->duration_seconds)
                        <span class="aap-duration-badge">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            {{ gmdate('i:s', $recapRecord->duration_seconds) }}
                        </span>
                    @endif
                    <span>Voice: {{ $voices[$recapRecord->voice] ?? $recapRecord->voice }}</span>
                    @if($recapRecord->generated_at) &nbsp;·&nbsp; Generated {{ $recapRecord->generated_at->diffForHumans() }} @endif
                </div>
            </div>
            @elseif($recapRecord && $recapRecord->status === 'failed')
            <div class="aap-error">
                <strong>Error:</strong> {{ $recapRecord->error_message }}
            </div>
            @endif

            <div class="aap-actions">
                <select class="aap-voice-sel" id="aap-recap-voice">
                    @foreach($voices as $val => $lbl)
                        <option value="{{ $val }}" {{ ($recapRecord?->voice ?? 'nova') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
                @if(!$recapRecord || $recapRecord->status === 'failed')
                <button class="aap-btn aap-btn-generate" id="aap-btn-recap" onclick="aapGenerateRecap()">
                    <span class="aap-spinner" id="aap-spin-recap" style="display:none;"></span>
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                    Generate Audio
                </button>
                @elseif($recapRecord && $recapRecord->status === 'ready')
                <button class="aap-btn aap-btn-regen" id="aap-btn-recap" onclick="aapGenerateRecap()">
                    <span class="aap-spinner dark" id="aap-spin-recap" style="display:none;"></span>
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.39"/></svg>
                    Regenerate
                </button>
                <button class="aap-btn aap-btn-del" onclick="aapDeleteRecap({{ $recapRecord->id }})">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg>
                    Delete
                </button>
                @endif
            </div>
        </div>

        {{-- ── Per-block audio ── --}}
        <div class="aap-row">
            <div class="aap-row-head">
                <div style="flex:1;">
                    <div class="aap-type-label">▶ Block Audio</div>
                    <div style="font-size:11px;color:var(--text-muted);">Audio for individual content blocks &mdash; enable per block in the edit form, then generate here</div>
                </div>
                <div style="flex-shrink:0;">
                    @php $readyCount = $blockCoachMap->where('status','ready')->count(); @endphp
                    <span class="aap-status-pill {{ $readyCount > 0 ? 'ready' : 'none' }}">
                        {{ $readyCount }} / {{ $audioEnabledBlocks->count() }} ready
                    </span>
                </div>
            </div>

            @if($audioEnabledBlocks->isEmpty())
                <p class="aap-no-audio-blocks">
                    No blocks have audio enabled. Edit a block and set <strong>Audio: Enabled</strong> to add audio to it.
                </p>
            @else
            <div class="aap-block-section" id="aap-block-section">
                @foreach($audioEnabledBlocks as $aBlock)
                    @php
                        $bAudio  = $blockCoachMap->get($aBlock->id);
                        $bStatus = $bAudio?->status ?? 'none';
                        $bId     = $aBlock->id;
                    @endphp
                    <div class="aap-block-item" id="aap-block-{{ $bId }}">
                        <div class="aap-block-item-head">
                            <span class="aap-block-type-badge" style="background:{{ $aBlock->getTypeColor() }}">{{ $aBlock->getTypeLabel() }}</span>
                            <span class="aap-block-title">{{ $aBlock->title ?: '(no title)' }}</span>
                            <span class="aap-status-pill {{ $bStatus }}">{{ $bStatus === 'none' ? 'Not generated' : ucfirst($bStatus) }}</span>
                        </div>

                        @if($bAudio && $bAudio->isReady())
                        <div class="aap-mini-player">
                            <audio controls preload="none" src="{{ $bAudio->publicUrl() }}"></audio>
                            <div class="aap-meta" style="display:flex;align-items:center;gap:8px;margin-top:4px;">
                                @if($bAudio->duration_seconds)
                                    <span class="aap-duration-badge">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        {{ gmdate('i:s', $bAudio->duration_seconds) }}
                                    </span>
                                @endif
                                @if($bAudio->generated_at) Generated {{ $bAudio->generated_at->diffForHumans() }} @endif
                            </div>
                        </div>
                        @elseif($bAudio && $bAudio->status === 'failed')
                        <div class="aap-error">{{ $bAudio->error_message }}</div>
                        @endif

                        <div class="aap-actions">
                            <select class="aap-voice-sel" id="aap-block-voice-{{ $bId }}">
                                @foreach($voices as $val => $lbl)
                                    <option value="{{ $val }}" {{ ($bAudio?->voice ?? 'nova') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                            @if(!$bAudio || in_array($bStatus, ['none', 'failed']))
                            <button class="aap-btn aap-btn-generate" id="aap-btn-block-{{ $bId }}"
                                onclick="aapGenerateBlock({{ $bId }}, '{{ route('elearning.audio.block.generate', [$course, $lesson, $aBlock]) }}')">
                                <span class="aap-spinner" id="aap-spin-block-{{ $bId }}" style="display:none;"></span>
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                Generate
                            </button>
                            @else
                            <button class="aap-btn aap-btn-regen" id="aap-btn-block-{{ $bId }}"
                                onclick="aapGenerateBlock({{ $bId }}, '{{ route('elearning.audio.block.generate', [$course, $lesson, $aBlock]) }}')">
                                <span class="aap-spinner dark" id="aap-spin-block-{{ $bId }}" style="display:none;"></span>
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.39"/></svg>
                                Regenerate
                            </button>
                            @if($bAudio)
                            <button class="aap-btn aap-btn-del"
                                onclick="aapDeleteBlock({{ $bAudio->id }}, '{{ url('elearning/courses/' . $course->id . '/lessons/' . $lesson->id . '/audio/block') }}')">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg>
                                Delete
                            </button>
                            @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>
</div>

<script>
(function () {
    const csrfToken    = '{{ csrf_token() }}';
    const recapUrl     = '{{ route("elearning.audio.recap.generate", [$course, $lesson]) }}';
    const recapDelBase = '{{ url("elearning/courses/" . $course->id . "/lessons/" . $lesson->id . "/audio/recap") }}';

    function toast(msg, ok) {
        const el = document.createElement('div');
        el.textContent = msg;
        el.style.cssText = `position:fixed;bottom:20px;right:20px;z-index:9999;padding:10px 16px;border-radius:8px;font-size:13px;font-weight:600;color:white;background:${ok ? '#16a34a' : '#dc2626'};box-shadow:0 4px 12px rgba(0,0,0,.2);transition:.3s;`;
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 3500);
    }

    function setBusy(btnId, spinId, busy) {
        const btn  = document.getElementById(btnId);
        const spin = document.getElementById(spinId);
        if (btn)  btn.disabled = busy;
        if (spin) spin.style.display = busy ? 'inline-block' : 'none';
    }

    // ── Lesson Audio Summary ──────────────────────────────────────────────
    window.aapGenerateRecap = function() {
        setBusy('aap-btn-recap', 'aap-spin-recap', true);
        const voice = document.getElementById('aap-recap-voice')?.value || 'nova';

        fetch(recapUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ voice }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) { toast(data.error, false); setBusy('aap-btn-recap', 'aap-spin-recap', false); return; }
            toast(data.status === 'ready' ? '✓ Audio summary generated!' : '✗ Generation failed.', data.status === 'ready');
            location.reload();
        })
        .catch(() => { toast('Request failed. Please try again.', false); location.reload(); });
    };

    window.aapDeleteRecap = function(id) {
        if (!confirm('Delete this audio summary? This cannot be undone.')) return;
        fetch(`${recapDelBase}/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken },
        })
        .then(r => r.json())
        .then(data => {
            toast(data.error ? data.error : '✓ Audio deleted.', !data.error);
            location.reload();
        })
        .catch(() => toast('Request failed.', false));
    };

    // ── Block Audio ───────────────────────────────────────────────────────
    window.aapGenerateBlock = function(blockId, url) {
        setBusy(`aap-btn-block-${blockId}`, `aap-spin-block-${blockId}`, true);
        const voice = document.getElementById(`aap-block-voice-${blockId}`)?.value || 'nova';

        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ voice }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) { toast(data.error, false); setBusy(`aap-btn-block-${blockId}`, `aap-spin-block-${blockId}`, false); return; }
            toast(data.status === 'ready' ? '✓ Block audio generated!' : '✗ Generation failed.', data.status === 'ready');
            location.reload();
        })
        .catch(() => { toast('Request failed. Please try again.', false); location.reload(); });
    };

    window.aapDeleteBlock = function(audioId, baseUrl) {
        if (!confirm('Delete this block audio? This cannot be undone.')) return;
        fetch(`${baseUrl}/${audioId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken },
        })
        .then(r => r.json())
        .then(data => {
            toast(data.error ? data.error : '✓ Block audio deleted.', !data.error);
            location.reload();
        })
        .catch(() => toast('Request failed.', false));
    };
})();
</script>
