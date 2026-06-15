@php
    $recapRecord = $audioRecords->firstWhere('audio_type', 'lesson_recap');
    $blockCoachRecords = $audioRecords->where('audio_type', 'ai_coach');
    $voices = ['nova' => 'Nova (Female)', 'alloy' => 'Alloy (Neutral)', 'echo' => 'Echo (Male)', 'fable' => 'Fable (Male)', 'onyx' => 'Onyx (Male)', 'shimmer' => 'Shimmer (Female)'];
@endphp

<style>
.aap-card { background:var(--surface); border:1px solid var(--border); border-radius:var(--r-xl); box-shadow:var(--shadow-sm); overflow:hidden; }
.aap-head { background:linear-gradient(135deg,#7c3aed 0%,#6d28d9 100%); padding:14px 18px; display:flex; align-items:center; gap:10px; }
.aap-head-icon { width:34px;height:34px;border-radius:8px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.aap-head-title { font-size:13px;font-weight:800;color:white;line-height:1.2; }
.aap-head-sub { font-size:11px;color:rgba(255,255,255,.65);margin-top:1px; }
.aap-body { padding:16px; }
.aap-row { padding:12px; border:1px solid var(--border); border-radius:var(--r-lg); background:#fafbfc; margin-bottom:10px; }
.aap-row:last-child { margin-bottom:0; }
.aap-row-head { display:flex;align-items:center;gap:8px;margin-bottom:10px; }
.aap-type-label { font-size:12.5px;font-weight:700;color:var(--text); }
.aap-status-pill { padding:2px 9px;border-radius:20px;font-size:11px;font-weight:700; }
.aap-status-pill.ready      { background:#dcfce7;color:#16a34a; }
.aap-status-pill.pending    { background:#fef9c3;color:#92400e; }
.aap-status-pill.processing { background:#dbeafe;color:#1d4ed8; }
.aap-status-pill.failed     { background:#fee2e2;color:#dc2626; }
.aap-status-pill.none       { background:#f3f4f6;color:#6b7280; }
.aap-mini-player { margin-bottom:10px; }
.aap-mini-player audio { width:100%;height:36px;border-radius:6px; }
.aap-actions { display:flex;flex-wrap:wrap;gap:6px;align-items:center; }
.aap-btn { display:inline-flex;align-items:center;gap:5px;padding:5px 11px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;border:none;transition:.15s; }
.aap-btn-generate { background:#7c3aed;color:white; }
.aap-btn-generate:hover { background:#6d28d9; }
.aap-btn-regen { background:#e0e7ff;color:#4338ca; }
.aap-btn-regen:hover { background:#c7d2fe; }
.aap-btn-del { background:#fee2e2;color:#dc2626; }
.aap-btn-del:hover { background:#fecaca; }
.aap-error { font-size:11px;color:#dc2626;margin-top:6px;line-height:1.4; }
.aap-meta { font-size:11px;color:var(--text-muted);margin-top:4px; }
.aap-spinner { display:inline-block;width:14px;height:14px;border:2px solid rgba(124,58,237,.3);border-top-color:#7c3aed;border-radius:50%;animation:aap-spin .7s linear infinite;flex-shrink:0; }
@keyframes aap-spin { to { transform:rotate(360deg); } }
.aap-block-list { margin-top:8px;font-size:11.5px;color:var(--text-muted);line-height:1.8; }
</style>

<div class="aap-card" id="aap-card" style="margin-top:14px;">
    <div class="aap-head">
        <div class="aap-head-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2"><path d="M12 2a3 3 0 0 1 3 3v7a3 3 0 0 1-6 0V5a3 3 0 0 1 3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/><line x1="8" y1="23" x2="16" y2="23"/></svg>
        </div>
        <div>
            <div class="aap-head-title">AI Audio &amp; AI Coach</div>
            <div class="aap-head-sub">Per-block AI Coach (generated on demand by learners) &amp; Lesson Recap</div>
        </div>
    </div>
    <div class="aap-body">

        {{-- Lesson Recap row --}}
        <div class="aap-row" id="aap-row-recap">
            <div class="aap-row-head">
                <div>
                    <div class="aap-type-label">AI Lesson Recap</div>
                    <div style="font-size:11px;color:var(--text-muted);">GPT + TTS end-of-lesson summary (key concepts, mistakes, workplace tips)</div>
                </div>
                <div style="margin-left:auto;">
                    @if(!$recapRecord)
                        <span class="aap-status-pill none">Not generated</span>
                    @else
                        <span class="aap-status-pill {{ $recapRecord->status }}" id="aap-pill-recap">
                            {{ ucfirst($recapRecord->status) }}
                        </span>
                    @endif
                </div>
            </div>

            @if($recapRecord && $recapRecord->isReady())
            <div class="aap-mini-player" id="aap-player-recap">
                <audio controls preload="none" src="{{ $recapRecord->publicUrl() }}"></audio>
                <div class="aap-meta">
                    Voice: {{ $voices[$recapRecord->voice] ?? $recapRecord->voice }}
                    @if($recapRecord->generated_at) &nbsp;·&nbsp; Generated {{ $recapRecord->generated_at->diffForHumans() }} @endif
                </div>
            </div>
            @elseif($recapRecord && $recapRecord->status === 'failed')
            <div class="aap-error" id="aap-err-recap">
                <strong>Error:</strong> {{ $recapRecord->error_message }}
            </div>
            @elseif($recapRecord && $recapRecord->status === 'processing')
            <div style="font-size:12px;color:#1d4ed8;margin-bottom:8px;display:flex;align-items:center;gap:6px;">
                <span class="aap-spinner"></span>
                Generating recap&hellip;
            </div>
            @endif

            <div class="aap-actions">
                @if(!$recapRecord || in_array($recapRecord->status, ['failed']))
                <button class="aap-btn aap-btn-generate" onclick="aapGenerateRecap()" id="aap-gen-recap-btn">
                    <span class="aap-spinner" id="aap-gen-recap-spin" style="display:none;border-color:rgba(255,255,255,.4);border-top-color:white;"></span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                    Generate Recap
                </button>
                @elseif($recapRecord && $recapRecord->status === 'ready')
                <button class="aap-btn aap-btn-regen" onclick="aapGenerateRecap()" id="aap-gen-recap-btn">
                    <span class="aap-spinner" id="aap-gen-recap-spin" style="display:none;border-color:rgba(67,56,202,.4);border-top-color:#4338ca;"></span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.39"/></svg>
                    Regenerate
                </button>
                <button class="aap-btn aap-btn-del" onclick="aapDeleteRecap({{ $recapRecord->id }})">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                    Delete
                </button>
                @endif
            </div>
        </div>

        {{-- Per-block AI Coach summary --}}
        <div class="aap-row">
            <div class="aap-row-head">
                <div>
                    <div class="aap-type-label">Per-Block AI Coach</div>
                    <div style="font-size:11px;color:var(--text-muted);">Generated on demand when learners click "AI Coach" on eligible content blocks</div>
                </div>
                <div style="margin-left:auto;">
                    <span class="aap-status-pill" style="background:#f0fdf4;color:#16a34a;">
                        {{ $blockCoachRecords->where('status','ready')->count() }} ready
                    </span>
                </div>
            </div>
            @if($blockCoachRecords->isNotEmpty())
            <div class="aap-block-list">
                @foreach($blockCoachRecords as $bca)
                <span>Block #{{ $bca->block_id }}:
                    <strong style="color:{{ $bca->status === 'ready' ? '#16a34a' : ($bca->status === 'failed' ? '#dc2626' : '#6b7280') }}">
                        {{ $bca->status }}
                    </strong>
                </span>
                &nbsp;
                @endforeach
            </div>
            @else
            <p style="font-size:12px;color:var(--text-muted);margin:0;">No block audio generated yet. Learners generate these on demand.</p>
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
        el.style.cssText = `position:fixed;bottom:20px;right:20px;z-index:9999;padding:10px 16px;border-radius:8px;font-size:13px;font-weight:600;color:white;background:${ok ? '#16a34a' : '#dc2626'};box-shadow:0 4px 12px rgba(0,0,0,.2);`;
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 3000);
    }

    window.aapGenerateRecap = function() {
        const btn  = document.getElementById('aap-gen-recap-btn');
        const spin = document.getElementById('aap-gen-recap-spin');
        if (btn)  btn.disabled = true;
        if (spin) spin.style.display = '';

        fetch(recapUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) { toast(data.error, false); }
            else { toast(data.status === 'ready' ? 'Recap generated!' : 'Generation failed.', data.status === 'ready'); }
            location.reload();
        })
        .catch(() => { toast('Request failed. Please try again.', false); location.reload(); });
    };

    window.aapDeleteRecap = function(id) {
        if (!confirm('Delete this recap audio? This cannot be undone.')) return;
        fetch(`${recapDelBase}/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken },
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) { toast(data.error, false); return; }
            toast('Recap deleted.', true);
            location.reload();
        })
        .catch(() => toast('Request failed.', false));
    };
})();
</script>
