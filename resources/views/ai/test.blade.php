@extends('layouts.app')
@section('page-title', 'AI Test')
@section('content')

<x-page-header title="AI Test Console" desc="Send a prompt directly to OpenAI and inspect the response. Super Admin only.">
    <x-slot:actions>
        <a href="{{ route('ai.settings') }}" class="btn btn-secondary btn-sm">
            ← AI Settings
        </a>
    </x-slot:actions>
</x-page-header>

{{-- ── Configuration Banner ──────────────────────────────── --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:12px; margin-bottom:24px;">
    <div style="background:#fff; border:1px solid #e9ecf0; border-radius:10px; padding:14px 18px; display:flex; align-items:center; gap:10px;">
        @if(config('ai.enabled'))
            <span style="width:9px; height:9px; border-radius:50%; background:#16a34a; flex-shrink:0;"></span>
            <span style="font-size:13px; font-weight:700; color:#16a34a;">AI Enabled</span>
        @else
            <span style="width:9px; height:9px; border-radius:50%; background:#ef4444; flex-shrink:0;"></span>
            <span style="font-size:13px; font-weight:700; color:#ef4444;">AI Disabled</span>
        @endif
    </div>
    <div style="background:#fff; border:1px solid #e9ecf0; border-radius:10px; padding:14px 18px; display:flex; align-items:center; gap:10px;">
        @if(config('ai.api_key'))
            <span style="width:9px; height:9px; border-radius:50%; background:#16a34a; flex-shrink:0;"></span>
            <span style="font-size:13px; font-weight:700; color:#374151;">API Key Configured</span>
        @else
            <span style="width:9px; height:9px; border-radius:50%; background:#f59e0b; flex-shrink:0;"></span>
            <span style="font-size:13px; font-weight:700; color:#f59e0b;">API Key Missing</span>
        @endif
    </div>
    <div style="background:#fff; border:1px solid #e9ecf0; border-radius:10px; padding:14px 18px;">
        <span style="font-size:12px; color:#6b7280;">Model: </span>
        <span style="font-size:13px; font-weight:700; color:#1e3a8a;">{{ config('ai.model', 'gpt-4o-mini') }}</span>
    </div>
    <div style="background:#fff; border:1px solid #e9ecf0; border-radius:10px; padding:14px 18px;">
        <span style="font-size:12px; color:#6b7280;">Requests today: </span>
        <span style="font-size:13px; font-weight:700; color:#111827;" id="todayCount">—</span>
        <span style="font-size:12px; color:#9ca3af;"> / {{ config('ai.daily_request_limit', 100) }}</span>
    </div>
</div>

{{-- ── Prompt Input + Response ──────────────────────────── --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

    {{-- Left: input --}}
    <div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; padding:22px;">
        <h3 style="font-size:15px; font-weight:800; color:#111827; margin:0 0 16px;">Prompt</h3>

        <textarea id="promptInput"
                  style="width:100%; height:280px; padding:12px; border:1.5px solid #d1d5db; border-radius:8px;
                         font-size:14px; font-family:inherit; resize:vertical; outline:none; box-sizing:border-box;"
                  placeholder="Enter your prompt here…"
                  onfocus="this.style.borderColor='#1e3a8a'"
                  onblur="this.style.borderColor='#d1d5db'"
>Create 5 learning objectives for ISO 14001:2015 Internal Auditor Training.</textarea>

        <div style="margin-top:12px; display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
            <button id="sendBtn" onclick="runTest()"
                    style="background:#1e3a8a; color:#fff; padding:11px 24px; border:none; border-radius:8px;
                           font-weight:700; font-size:14px; cursor:pointer; display:flex; align-items:center; gap:8px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                Send to AI
            </button>
            <button onclick="clearAll()"
                    style="background:#f1f5f9; color:#374151; padding:11px 18px; border:none; border-radius:8px;
                           font-weight:600; font-size:14px; cursor:pointer;">
                Clear
            </button>
            <span id="statusText" style="font-size:13px; color:#6b7280;"></span>
        </div>

        {{-- Quick Prompt Templates --}}
        <div style="margin-top:18px; padding-top:18px; border-top:1px solid #f0f2f5;">
            <p style="font-size:12px; font-weight:700; color:#6b7280; margin:0 0 10px; text-transform:uppercase; letter-spacing:.5px;">Quick Prompts</p>
            <div style="display:flex; flex-direction:column; gap:6px;">
                @foreach([
                    'Create 5 learning objectives for ISO 14001:2015 Internal Auditor Training.',
                    'Write a short description for an ISO 9001:2015 Lead Auditor course.',
                    'Generate 3 multiple-choice questions about ISO 45001 hazard identification.',
                    'List 5 key topics to include in a Food Safety Management training course.',
                    'Write a professional LinkedIn post promoting an ISO 9001 training programme.',
                ] as $tpl)
                <button onclick="setPrompt(this)"
                        data-prompt="{{ $tpl }}"
                        style="text-align:left; background:#f8fafc; border:1px solid #e9ecf0; border-radius:6px;
                               padding:8px 12px; font-size:12.5px; color:#374151; cursor:pointer; line-height:1.4;">
                    {{ $tpl }}
                </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Right: response --}}
    <div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; padding:22px; display:flex; flex-direction:column;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h3 style="font-size:15px; font-weight:800; color:#111827; margin:0;">Response</h3>
            <button onclick="copyResponse()"
                    style="background:#f1f5f9; color:#374151; padding:6px 14px; border:none; border-radius:6px;
                           font-size:12.5px; font-weight:600; cursor:pointer;" id="copyBtn">
                Copy
            </button>
        </div>

        {{-- Loading state --}}
        <div id="loadingState" style="display:none; text-align:center; padding:60px 0;">
            <div style="width:32px; height:32px; border:3px solid #e9ecf0; border-top-color:#1e3a8a; border-radius:50%;
                        animation:spin .8s linear infinite; margin:0 auto 12px;"></div>
            <p style="font-size:14px; color:#6b7280;">Thinking…</p>
        </div>

        {{-- Empty state --}}
        <div id="emptyState" style="flex:1; display:flex; align-items:center; justify-content:center; color:#9ca3af; font-size:14px;">
            Response will appear here.
        </div>

        {{-- Response text --}}
        <pre id="responseText"
             style="display:none; flex:1; margin:0; padding:14px; background:#f8fafc; border-radius:8px;
                    font-family:inherit; font-size:14px; line-height:1.7; white-space:pre-wrap;
                    overflow-y:auto; max-height:420px; color:#111827;"></pre>

        {{-- Error state --}}
        <div id="errorState" style="display:none; background:#fee2e2; border-radius:8px; padding:14px;">
            <p style="font-size:13.5px; font-weight:700; color:#b91c1c; margin:0 0 6px;">Error</p>
            <p id="errorText" style="font-size:13px; color:#7f1d1d; margin:0;"></p>
        </div>

        {{-- Usage stats --}}
        <div id="usageStats" style="display:none; margin-top:14px; padding:12px 14px; background:#f0f4ff;
             border-radius:8px; display:none; flex-wrap:wrap; gap:16px;">
            <div><span style="font-size:11px; color:#6b7280; display:block;">Prompt tokens</span>
                 <span id="uPrompt" style="font-size:14px; font-weight:700; color:#1e3a8a;">—</span></div>
            <div><span style="font-size:11px; color:#6b7280; display:block;">Completion tokens</span>
                 <span id="uCompletion" style="font-size:14px; font-weight:700; color:#1e3a8a;">—</span></div>
            <div><span style="font-size:11px; color:#6b7280; display:block;">Total tokens</span>
                 <span id="uTotal" style="font-size:14px; font-weight:700; color:#1e3a8a;">—</span></div>
            <div><span style="font-size:11px; color:#6b7280; display:block;">Est. cost</span>
                 <span id="uCost" style="font-size:14px; font-weight:700; color:#1e3a8a;">—</span></div>
        </div>
    </div>

</div>

<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>

<script>
const CSRF = '{{ csrf_token() }}';

async function runTest() {
    const prompt = document.getElementById('promptInput').value.trim();
    if (!prompt) { alert('Please enter a prompt.'); return; }

    setState('loading');

    try {
        const res = await fetch('{{ route('ai.test.run') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ prompt }),
        });

        const data = await res.json();

        if (data.success) {
            document.getElementById('responseText').textContent = data.text;
            setState('success');

            if (data.usage && data.usage.total_tokens) {
                document.getElementById('uPrompt').textContent     = data.usage.prompt_tokens;
                document.getElementById('uCompletion').textContent = data.usage.completion_tokens;
                document.getElementById('uTotal').textContent      = data.usage.total_tokens;
                document.getElementById('uCost').textContent       = '$' + parseFloat(data.usage.estimated_cost || 0).toFixed(5);
                document.getElementById('usageStats').style.display = 'flex';
            }
        } else {
            document.getElementById('errorText').textContent = data.error || 'Unknown error.';
            setState('error');
        }
    } catch (e) {
        document.getElementById('errorText').textContent = 'Network error: ' + e.message;
        setState('error');
    }
}

function setState(state) {
    document.getElementById('loadingState').style.display  = state === 'loading' ? 'block' : 'none';
    document.getElementById('emptyState').style.display    = state === 'empty'   ? 'flex'  : 'none';
    document.getElementById('responseText').style.display  = state === 'success' ? 'block' : 'none';
    document.getElementById('errorState').style.display    = state === 'error'   ? 'block' : 'none';
    document.getElementById('usageStats').style.display    = state === 'success' ? 'flex'  : 'none';
    const btn = document.getElementById('sendBtn');
    btn.disabled = state === 'loading';
    btn.style.opacity = state === 'loading' ? '.6' : '1';
    document.getElementById('statusText').textContent = state === 'loading' ? 'Waiting for OpenAI…' : '';
}

function setPrompt(btn) {
    document.getElementById('promptInput').value = btn.dataset.prompt;
}

function clearAll() {
    document.getElementById('promptInput').value = '';
    setState('empty');
    document.getElementById('usageStats').style.display = 'none';
}

function copyResponse() {
    const text = document.getElementById('responseText').textContent;
    if (!text) return;
    navigator.clipboard.writeText(text).then(() => {
        const btn = document.getElementById('copyBtn');
        btn.textContent = 'Copied!';
        setTimeout(() => btn.textContent = 'Copy', 2000);
    });
}
</script>

@endsection
