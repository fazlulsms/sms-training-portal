@extends('layouts.app')
@section('page-title', $template->template_name)
@section('content')

<x-page-header title="{{ $template->template_name }}" desc="{{ $template->category }} — v{{ $template->version_number }}">
    <x-slot:actions>
        <a href="{{ route('ai.prompt-templates.edit', $template) }}" class="btn btn-primary btn-sm">Edit</a>
        <a href="{{ route('ai.prompt-templates.versions', $template) }}" class="btn btn-secondary btn-sm">Version History</a>
        <a href="{{ route('ai.prompt-templates.index') }}" class="btn btn-secondary btn-sm">← Templates</a>
    </x-slot:actions>
</x-page-header>

<x-flash-message />

<div style="display:grid; grid-template-columns:2fr 1fr; gap:20px; align-items:start;">

    {{-- ── Left: prompt content ─────────────────────────────── --}}
    <div style="display:flex; flex-direction:column; gap:18px;">

        {{-- System Prompt --}}
        <div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; overflow:hidden;">
            <div style="padding:14px 20px; border-bottom:1px solid #f0f2f5; display:flex; justify-content:space-between; align-items:center;">
                <h3 style="font-size:14px; font-weight:800; color:#111827; margin:0;">System Prompt</h3>
                <span style="font-size:11.5px; background:#fffbeb; color:#92400e; padding:2px 10px; border-radius:20px; font-weight:700;">Template-specific only — master prompt prepended at runtime</span>
            </div>
            <pre style="margin:0; padding:18px 20px; background:#f8fafc; font-family:monospace; font-size:13px; line-height:1.7; white-space:pre-wrap; color:#374151; max-height:280px; overflow-y:auto;">{{ $template->system_prompt }}</pre>
        </div>

        {{-- User Prompt Template --}}
        <div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; overflow:hidden;">
            <div style="padding:14px 20px; border-bottom:1px solid #f0f2f5; display:flex; justify-content:space-between; align-items:center;">
                <h3 style="font-size:14px; font-weight:800; color:#111827; margin:0;">User Prompt Template</h3>
                <span style="font-size:11.5px; background:#eff6ff; color:#1e3a8a; padding:2px 10px; border-radius:20px; font-weight:700; font-family:monospace;">{input} placeholder active</span>
            </div>
            <pre style="margin:0; padding:18px 20px; background:#f8fafc; font-family:monospace; font-size:13px; line-height:1.7; white-space:pre-wrap; color:#374151; max-height:320px; overflow-y:auto;">{{ $template->user_prompt_template }}</pre>
        </div>

        @if($template->output_format_instructions)
        <div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; overflow:hidden;">
            <div style="padding:14px 20px; border-bottom:1px solid #f0f2f5;">
                <h3 style="font-size:14px; font-weight:800; color:#111827; margin:0;">Output Format Instructions</h3>
            </div>
            <pre style="margin:0; padding:18px 20px; background:#f8fafc; font-family:monospace; font-size:13px; line-height:1.7; white-space:pre-wrap; color:#374151;">{{ $template->output_format_instructions }}</pre>
        </div>
        @endif

        {{-- ── Test Console ─────────────────────────────────── --}}
        <div id="test-section" style="background:#fff; border:2px solid #1e3a8a; border-radius:12px; overflow:hidden;">
            <div style="padding:14px 20px; background:#1e3a8a; display:flex; justify-content:space-between; align-items:center;">
                <h3 style="font-size:14px; font-weight:800; color:#fff; margin:0;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:middle; margin-right:6px;"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                    Test This Template
                </h3>
                @if(!$template->is_active)
                    <span style="font-size:12px; background:#ef4444; color:#fff; padding:3px 10px; border-radius:20px; font-weight:700;">Inactive — activate to test</span>
                @else
                    <span style="font-size:12px; background:#16a34a; color:#fff; padding:3px 10px; border-radius:20px; font-weight:700;">Model: {{ $template->effectiveModel() }}</span>
                @endif
            </div>
            <div style="padding:20px;">
                <div style="margin-bottom:14px;">
                    <label style="font-size:12.5px; font-weight:700; color:#374151; display:block; margin-bottom:6px;">
                        Test Input <span style="font-size:11.5px; color:#9ca3af; font-weight:400;">(replaces <code>{input}</code> in the user prompt)</span>
                    </label>
                    <textarea id="testInput" rows="4"
                              placeholder="Enter a topic or input to test this template…"
                              style="width:100%; padding:10px 12px; border:1.5px solid #d1d5db; border-radius:7px; font-size:13.5px; resize:vertical; box-sizing:border-box; line-height:1.6;"
                              {{ !$template->is_active ? 'disabled' : '' }}>{{ match($template->template_code) {
                                  'course_generator_v1'    => 'ISO 9001:2015 Internal Auditor Training',
                                  'lesson_generator_v1'    => 'Understanding Clause 6 — Planning and Risk Assessment in ISO 9001:2015',
                                  'quiz_generator_v1'      => 'ISO 45001:2018 Hazard Identification and Risk Assessment',
                                  'case_study_generator_v1'=> 'A garment factory failing to meet SLCP social compliance requirements',
                                  'linkedin_post_v1'       => 'ISO 9001:2015 Lead Auditor Certification Programme',
                                  'facebook_post_v1'       => 'ISO 45001 Occupational Health & Safety Management Training',
                                  default                  => '',
                              } }}</textarea>
                </div>
                <div style="display:flex; gap:10px; align-items:center;">
                    <button id="testBtn" onclick="runTest()" {{ !$template->is_active ? 'disabled' : '' }}
                            style="background:#1e3a8a; color:#fff; padding:10px 24px; border:none; border-radius:8px; font-weight:700; font-size:14px; cursor:pointer; display:flex; align-items:center; gap:7px; {{ !$template->is_active ? 'opacity:.5; cursor:not-allowed;' : '' }}">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                        Run Test
                    </button>
                    <span id="testStatus" style="font-size:13px; color:#6b7280;"></span>
                </div>

                {{-- Loading --}}
                <div id="testLoading" style="display:none; text-align:center; padding:40px 0;">
                    <div style="width:28px; height:28px; border:3px solid #e9ecf0; border-top-color:#1e3a8a; border-radius:50%; animation:spin .8s linear infinite; margin:0 auto 10px;"></div>
                    <p style="font-size:13px; color:#6b7280;">Sending prompt to {{ $template->effectiveModel() }}…</p>
                </div>

                {{-- Result --}}
                <div id="testResult" style="display:none; margin-top:16px;">
                    {{-- Usage bar --}}
                    <div id="usageBar" style="display:flex; gap:16px; padding:10px 14px; background:#f0f4ff; border-radius:8px; margin-bottom:12px; flex-wrap:wrap;">
                        <div><span style="font-size:11px; color:#6b7280; display:block;">Model</span><span id="uModel" style="font-size:13px; font-weight:700; color:#1e3a8a;">—</span></div>
                        <div><span style="font-size:11px; color:#6b7280; display:block;">Prompt tokens</span><span id="uPrompt" style="font-size:13px; font-weight:700; color:#1e3a8a;">—</span></div>
                        <div><span style="font-size:11px; color:#6b7280; display:block;">Completion tokens</span><span id="uCompletion" style="font-size:13px; font-weight:700; color:#1e3a8a;">—</span></div>
                        <div><span style="font-size:11px; color:#6b7280; display:block;">Total tokens</span><span id="uTotal" style="font-size:13px; font-weight:700; color:#1e3a8a;">—</span></div>
                        <div><span style="font-size:11px; color:#6b7280; display:block;">Est. cost</span><span id="uCost" style="font-size:13px; font-weight:700; color:#1e3a8a;">—</span></div>
                        <div><span style="font-size:11px; color:#6b7280; display:block;">Response time</span><span id="uTime" style="font-size:13px; font-weight:700; color:#1e3a8a;">—</span></div>
                    </div>
                    {{-- Response text --}}
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                        <span style="font-size:12.5px; font-weight:700; color:#374151;">Generated Output</span>
                        <button onclick="copyOutput()" id="copyBtn" style="background:#f1f5f9; color:#374151; padding:5px 12px; border:none; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;">Copy</button>
                    </div>
                    <pre id="testOutput" style="margin:0; padding:14px; background:#f8fafc; border-radius:8px; font-family:inherit; font-size:13.5px; line-height:1.8; white-space:pre-wrap; max-height:500px; overflow-y:auto; color:#111827; border:1px solid #e9ecf0;"></pre>
                </div>

                {{-- Error --}}
                <div id="testError" style="display:none; margin-top:14px; background:#fee2e2; border-radius:8px; padding:12px 14px;">
                    <p style="font-size:13px; font-weight:700; color:#b91c1c; margin:0 0 4px;">Error</p>
                    <p id="testErrorText" style="font-size:13px; color:#7f1d1d; margin:0;"></p>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Right: metadata ──────────────────────────────────── --}}
    <div style="display:flex; flex-direction:column; gap:18px;">

        {{-- Template Details --}}
        <div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; padding:20px;">
            <h3 style="font-size:14px; font-weight:800; color:#111827; margin:0 0 14px;">Template Details</h3>
            <table style="width:100%; font-size:13px; border-collapse:collapse;">
                <tr style="border-bottom:1px solid #f0f2f5;">
                    <td style="padding:8px 0; color:#6b7280; font-weight:600; width:120px;">Code</td>
                    <td style="padding:8px 0; font-family:monospace; color:#374151; font-size:12px;">{{ $template->template_code }}</td>
                </tr>
                <tr style="border-bottom:1px solid #f0f2f5;">
                    <td style="padding:8px 0; color:#6b7280; font-weight:600;">Category</td>
                    <td style="padding:8px 0; color:#374151;">{{ $template->category }}</td>
                </tr>
                <tr style="border-bottom:1px solid #f0f2f5;">
                    <td style="padding:8px 0; color:#6b7280; font-weight:600;">Version</td>
                    <td style="padding:8px 0;"><span style="background:#f1f5f9; color:#374151; padding:2px 9px; border-radius:20px; font-weight:700; font-size:12.5px;">v{{ $template->version_number }}</span></td>
                </tr>
                <tr style="border-bottom:1px solid #f0f2f5;">
                    <td style="padding:8px 0; color:#6b7280; font-weight:600;">Status</td>
                    <td style="padding:8px 0;">
                        @if($template->is_active)
                            <span style="background:#dcfce7; color:#16a34a; padding:2px 9px; border-radius:20px; font-weight:700; font-size:12.5px;">Active</span>
                        @else
                            <span style="background:#f1f5f9; color:#9ca3af; padding:2px 9px; border-radius:20px; font-weight:700; font-size:12.5px;">Inactive</span>
                        @endif
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #f0f2f5;">
                    <td style="padding:8px 0; color:#6b7280; font-weight:600;">Model</td>
                    <td style="padding:8px 0; font-size:12.5px; color:#374151;">{{ $template->effectiveModel() }}{{ $template->model_override ? ' (override)' : ' (global default)' }}</td>
                </tr>
                <tr style="border-bottom:1px solid #f0f2f5;">
                    <td style="padding:8px 0; color:#6b7280; font-weight:600;">Temperature</td>
                    <td style="padding:8px 0; color:#374151;">{{ $template->effectiveTemperature() }}</td>
                </tr>
                <tr style="border-bottom:1px solid #f0f2f5;">
                    <td style="padding:8px 0; color:#6b7280; font-weight:600;">Max Tokens</td>
                    <td style="padding:8px 0; color:#374151;">{{ number_format($template->effectiveMaxTokens()) }}</td>
                </tr>
                <tr style="border-bottom:1px solid #f0f2f5;">
                    <td style="padding:8px 0; color:#6b7280; font-weight:600;">Created</td>
                    <td style="padding:8px 0; font-size:12px; color:#6b7280;">{{ $template->created_at->format('d M Y') }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0; color:#6b7280; font-weight:600;">Updated</td>
                    <td style="padding:8px 0; font-size:12px; color:#6b7280;">{{ $template->updated_at->format('d M Y H:i') }}</td>
                </tr>
            </table>
        </div>

        @if($template->description)
        <div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; padding:20px;">
            <h3 style="font-size:13px; font-weight:800; color:#111827; margin:0 0 8px;">Description</h3>
            <p style="font-size:13px; color:#6b7280; margin:0; line-height:1.6;">{{ $template->description }}</p>
        </div>
        @endif

        {{-- Quick Actions --}}
        <div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; padding:20px; display:flex; flex-direction:column; gap:8px;">
            <h3 style="font-size:13px; font-weight:800; color:#111827; margin:0 0 6px;">Quick Actions</h3>
            <a href="{{ route('ai.prompt-templates.edit', $template) }}"
               style="display:block; text-align:center; padding:9px; background:#eff6ff; color:#1e3a8a; border-radius:7px; font-size:13px; font-weight:700; text-decoration:none;">Edit Template</a>
            <form method="POST" action="{{ route('ai.prompt-templates.clone', $template) }}" onsubmit="return confirm('Clone this template?')">
                @csrf
                <button type="submit" style="display:block; width:100%; padding:9px; background:#f5f3ff; color:#7c3aed; border:none; border-radius:7px; font-size:13px; font-weight:700; cursor:pointer;">Clone Template</button>
            </form>
            <form method="POST" action="{{ route('ai.prompt-templates.toggle', $template) }}">
                @csrf
                <button type="submit" style="display:block; width:100%; padding:9px; background:{{ $template->is_active ? '#fff1f2' : '#f0fdf4' }}; color:{{ $template->is_active ? '#be123c' : '#16a34a' }}; border:none; border-radius:7px; font-size:13px; font-weight:700; cursor:pointer;">
                    {{ $template->is_active ? 'Deactivate' : 'Activate' }}
                </button>
            </form>
            <a href="{{ route('ai.prompt-templates.versions', $template) }}"
               style="display:block; text-align:center; padding:9px; background:#f8fafc; color:#374151; border-radius:7px; font-size:13px; font-weight:700; text-decoration:none;">Version History ({{ $template->versions->count() }})</a>
        </div>

    </div>
</div>

<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>

<script>
const CSRF       = '{{ csrf_token() }}';
const TEST_URL   = '{{ route('ai.prompt-templates.test', $template) }}';

async function runTest() {
    const input = document.getElementById('testInput').value.trim();
    if (!input) { alert('Please enter a test input.'); return; }

    setTestState('loading');

    try {
        const res  = await fetch(TEST_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ test_input: input }),
        });
        const data = await res.json();

        if (data.success) {
            document.getElementById('testOutput').textContent = data.text;
            if (data.usage) {
                document.getElementById('uModel').textContent      = data.usage.model || '—';
                document.getElementById('uPrompt').textContent     = data.usage.prompt_tokens;
                document.getElementById('uCompletion').textContent = data.usage.completion_tokens;
                document.getElementById('uTotal').textContent      = data.usage.total_tokens;
                document.getElementById('uCost').textContent       = '$' + parseFloat(data.usage.estimated_cost || 0).toFixed(5);
                document.getElementById('uTime').textContent       = (data.usage.response_time_ms || 0) + ' ms';
            }
            setTestState('success');
        } else {
            document.getElementById('testErrorText').textContent = data.error || 'Unknown error.';
            setTestState('error');
        }
    } catch (e) {
        document.getElementById('testErrorText').textContent = 'Network error: ' + e.message;
        setTestState('error');
    }
}

function setTestState(state) {
    document.getElementById('testLoading').style.display = state === 'loading'  ? 'block' : 'none';
    document.getElementById('testResult').style.display  = state === 'success'  ? 'block' : 'none';
    document.getElementById('testError').style.display   = state === 'error'    ? 'block' : 'none';
    const btn = document.getElementById('testBtn');
    btn.disabled      = state === 'loading';
    btn.style.opacity = state === 'loading' ? '.5' : '1';
    document.getElementById('testStatus').textContent = state === 'loading' ? 'Waiting for AI response…' : '';
}

function copyOutput() {
    const text = document.getElementById('testOutput').textContent;
    if (!text) return;
    navigator.clipboard.writeText(text).then(() => {
        const btn = document.getElementById('copyBtn');
        btn.textContent = 'Copied!';
        setTimeout(() => btn.textContent = 'Copy', 2000);
    });
}
</script>

@endsection
