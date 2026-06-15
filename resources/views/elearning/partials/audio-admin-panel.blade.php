@php
    $narration   = $audioRecords->firstWhere('audio_type', 'narration');
    $explanation = $audioRecords->firstWhere('audio_type', 'ai_explanation');
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
.aap-status-pill.ready     { background:#dcfce7;color:#16a34a; }
.aap-status-pill.pending   { background:#fef9c3;color:#92400e; }
.aap-status-pill.processing{ background:#dbeafe;color:#1d4ed8; }
.aap-status-pill.failed    { background:#fee2e2;color:#dc2626; }
.aap-status-pill.none      { background:#f3f4f6;color:#6b7280; }
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
.aap-voice-select { font-size:12px;padding:4px 8px;border:1px solid var(--border);border-radius:6px;background:white;color:var(--text);cursor:pointer; }
.aap-error { font-size:11px;color:#dc2626;margin-top:6px;line-height:1.4; }
.aap-meta { font-size:11px;color:var(--text-muted);margin-top:4px; }
.aap-spinner { display:inline-block;width:14px;height:14px;border:2px solid rgba(255,255,255,.4);border-top-color:white;border-radius:50%;animation:aap-spin .7s linear infinite;flex-shrink:0; }
@keyframes aap-spin { to { transform:rotate(360deg); } }
</style>

<div class="aap-card" id="aap-card" style="margin-top:14px;">
    <div class="aap-head">
        <div class="aap-head-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
        </div>
        <div>
            <div class="aap-head-title">AI Audio Learning Assistant</div>
            <div class="aap-head-sub">Generate TTS narration &amp; AI teacher audio</div>
        </div>
    </div>
    <div class="aap-body">

        @foreach([
            ['type' => 'narration',      'label' => 'Lesson Narration',     'desc' => 'Reads the lesson content aloud', 'record' => $narration],
            ['type' => 'ai_explanation', 'label' => 'AI Teacher Mode',      'desc' => 'GPT-generated teaching explanation', 'record' => $explanation],
        ] as $item)
        @php $rec = $item['record']; @endphp

        <div class="aap-row" id="aap-row-{{ $item['type'] }}">
            <div class="aap-row-head">
                <div>
                    <div class="aap-type-label">{{ $item['label'] }}</div>
                    <div style="font-size:11px;color:var(--text-muted);">{{ $item['desc'] }}</div>
                </div>
                <div style="margin-left:auto;">
                    @if(!$rec)
                        <span class="aap-status-pill none">Not generated</span>
                    @else
                        <span class="aap-status-pill {{ $rec->status }}" id="aap-pill-{{ $item['type'] }}">
                            {{ ucfirst($rec->status) }}
                        </span>
                    @endif
                </div>
            </div>

            @if($rec && $rec->isReady())
            <div class="aap-mini-player" id="aap-player-{{ $item['type'] }}">
                <audio controls preload="none" src="{{ $rec->publicUrl() }}"></audio>
                <div class="aap-meta">
                    Voice: {{ $voices[$rec->voice] ?? $rec->voice }}
                    @if($rec->generated_at) &nbsp;·&nbsp; Generated {{ $rec->generated_at->diffForHumans() }} @endif
                </div>
            </div>
            @elseif($rec && $rec->status === 'failed')
            <div class="aap-error" id="aap-err-{{ $item['type'] }}">
                <strong>Error:</strong> {{ $rec->error_message }}
            </div>
            @elseif($rec && in_array($rec->status, ['pending', 'processing']))
            <div style="font-size:12px;color:#1d4ed8;margin-bottom:8px;" id="aap-progress-{{ $item['type'] }}">
                <span class="aap-spinner" style="border-color:rgba(29,78,216,.3);border-top-color:#1d4ed8;"></span>
                &nbsp; {{ $rec->status === 'processing' ? 'Generating audio…' : 'Queued, please wait…' }}
            </div>
            @endif

            <div class="aap-actions">
                @if(!$rec || in_array($rec->status, ['failed', 'pending']))
                <select class="aap-voice-select" id="aap-voice-{{ $item['type'] }}">
                    @foreach($voices as $v => $vl)
                        <option value="{{ $v }}" {{ ($rec?->voice ?? 'nova') === $v ? 'selected' : '' }}>{{ $vl }}</option>
                    @endforeach
                </select>
                <button class="aap-btn aap-btn-generate" onclick="aapGenerate('{{ $item['type'] }}')">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                    Generate
                </button>
                @elseif($rec && $rec->status === 'ready')
                <select class="aap-voice-select" id="aap-voice-{{ $item['type'] }}">
                    @foreach($voices as $v => $vl)
                        <option value="{{ $v }}" {{ $rec->voice === $v ? 'selected' : '' }}>{{ $vl }}</option>
                    @endforeach
                </select>
                <button class="aap-btn aap-btn-regen" onclick="aapRegenerate('{{ $item['type'] }}', {{ $rec->id }})">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.39"/></svg>
                    Regenerate
                </button>
                <button class="aap-btn aap-btn-del" onclick="aapDelete('{{ $item['type'] }}', {{ $rec->id }})">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                    Delete
                </button>
                @elseif($rec && $rec->status === 'processing')
                <button class="aap-btn" style="background:#e5e7eb;color:#6b7280;cursor:not-allowed;" disabled>
                    <span class="aap-spinner" style="border-color:rgba(107,114,128,.3);border-top-color:#6b7280;"></span>
                    &nbsp;Processing…
                </button>
                @endif
            </div>

        </div>
        @endforeach

    </div>
</div>

<script>
(function () {
    const csrfToken = '{{ csrf_token() }}';
    const generateUrl  = '{{ route("elearning.audio.generate",  [$course, $lesson]) }}';
    const statusUrl    = '{{ route("elearning.audio.status",    [$course, $lesson]) }}';
    const regenBase    = '{{ url("elearning/courses/" . $course->id . "/lessons/" . $lesson->id . "/audio") }}';
    const destroyBase  = regenBase;

    let pollTimer = null;

    function toast(msg, ok) {
        const el = document.createElement('div');
        el.textContent = msg;
        el.style.cssText = `position:fixed;bottom:20px;right:20px;z-index:9999;padding:10px 16px;border-radius:8px;font-size:13px;font-weight:600;color:white;background:${ok ? '#16a34a' : '#dc2626'};box-shadow:0 4px 12px rgba(0,0,0,.2);`;
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 3000);
    }

    window.aapGenerate = function(type) {
        const voice = document.getElementById('aap-voice-' + type)?.value ?? 'nova';
        fetch(generateUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ type, voice }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) { toast(data.error, false); return; }
            toast('Generation queued!', true);
            setPillProcessing(type, 'pending');
            startPolling();
        })
        .catch(() => toast('Request failed. Please try again.', false));
    };

    window.aapRegenerate = function(type, id) {
        if (!confirm('Replace the existing audio? The current file will be deleted.')) return;
        const voice = document.getElementById('aap-voice-' + type)?.value ?? 'nova';
        fetch(`${regenBase}/${id}/regenerate`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ voice }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) { toast(data.error, false); return; }
            toast('Regeneration queued!', true);
            location.reload();
        })
        .catch(() => toast('Request failed.', false));
    };

    window.aapDelete = function(type, id) {
        if (!confirm('Delete this audio file? This cannot be undone.')) return;
        fetch(`${destroyBase}/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken },
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) { toast(data.error, false); return; }
            toast('Audio deleted.', true);
            location.reload();
        })
        .catch(() => toast('Request failed.', false));
    };

    function setPillProcessing(type, status) {
        const pill = document.getElementById('aap-pill-' + type);
        if (pill) {
            pill.className = 'aap-status-pill ' + status;
            pill.textContent = status === 'processing' ? 'Processing…' : 'Queued…';
        }
    }

    function startPolling() {
        if (pollTimer) return;
        pollTimer = setInterval(poll, 4000);
    }

    function poll() {
        fetch(statusUrl, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            let anyPending = false;
            ['narration', 'ai_explanation'].forEach(type => {
                const rec = data[type];
                if (!rec) return;
                if (rec.status === 'pending' || rec.status === 'processing') {
                    anyPending = true;
                    setPillProcessing(type, rec.status);
                }
                if (rec.status === 'ready' || rec.status === 'failed') {
                    location.reload();
                }
            });
            if (!anyPending) {
                clearInterval(pollTimer);
                pollTimer = null;
            }
        })
        .catch(() => {});
    }

    // Auto-start polling if any record is in pending/processing state
    @if(($narration && in_array($narration->status, ['pending','processing'])) || ($explanation && in_array($explanation->status, ['pending','processing'])))
    startPolling();
    @endif
})();
</script>
