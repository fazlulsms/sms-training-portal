@extends('layouts.app')
@section('page-title', 'AI Settings')
@section('content')

<x-page-header title="AI Settings" desc="OpenAI configuration status, usage statistics, and audit log.">
    <x-slot:actions>
        <a href="{{ route('ai.test') }}" class="btn btn-primary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
            Open AI Test
        </a>
    </x-slot:actions>
</x-page-header>

<x-flash-message />

{{-- ── Status Cards ──────────────────────────────────────── --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:16px; margin-bottom:28px;">

    {{-- Feature Toggle --}}
    <div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; padding:20px;">
        <div style="font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:#6b7280; margin-bottom:10px;">AI Feature</div>
        @if(config('ai.enabled'))
            <div style="display:flex; align-items:center; gap:8px;">
                <span style="width:10px; height:10px; border-radius:50%; background:#16a34a; display:inline-block;"></span>
                <span style="font-size:18px; font-weight:800; color:#16a34a;">Enabled</span>
            </div>
        @else
            <div style="display:flex; align-items:center; gap:8px;">
                <span style="width:10px; height:10px; border-radius:50%; background:#ef4444; display:inline-block;"></span>
                <span style="font-size:18px; font-weight:800; color:#ef4444;">Disabled</span>
            </div>
            <p style="font-size:12px; color:#9ca3af; margin:6px 0 0;">Set <code>AI_FEATURE_ENABLED=true</code> in .env</p>
        @endif
    </div>

    {{-- API Key --}}
    <div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; padding:20px;">
        <div style="font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:#6b7280; margin-bottom:10px;">API Key</div>
        @if(config('ai.api_key'))
            <div style="display:flex; align-items:center; gap:8px;">
                <span style="width:10px; height:10px; border-radius:50%; background:#16a34a; display:inline-block;"></span>
                <span style="font-size:16px; font-weight:800; color:#16a34a;">Configured</span>
            </div>
            <p style="font-size:12px; color:#9ca3af; margin:6px 0 0;">sk-••••••••{{ substr(config('ai.api_key'), -4) }}</p>
        @else
            <div style="display:flex; align-items:center; gap:8px;">
                <span style="width:10px; height:10px; border-radius:50%; background:#f59e0b; display:inline-block;"></span>
                <span style="font-size:16px; font-weight:800; color:#f59e0b;">Missing</span>
            </div>
            <p style="font-size:12px; color:#9ca3af; margin:6px 0 0;">Add <code>OPENAI_API_KEY</code> to .env</p>
        @endif
    </div>

    {{-- Model --}}
    <div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; padding:20px;">
        <div style="font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:#6b7280; margin-bottom:10px;">Model</div>
        <div style="font-size:18px; font-weight:800; color:#1e3a8a;">{{ config('ai.model', 'gpt-4o-mini') }}</div>
        <p style="font-size:12px; color:#9ca3af; margin:6px 0 0;">Timeout: {{ config('ai.timeout', 30) }}s</p>
    </div>

    {{-- Today's Usage --}}
    <div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; padding:20px;">
        <div style="font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:#6b7280; margin-bottom:10px;">Today's Requests</div>
        <div style="font-size:24px; font-weight:800; color:#111827;">{{ number_format($todayCount) }}</div>
        <p style="font-size:12px; color:#9ca3af; margin:6px 0 0;">Limit: {{ number_format(config('ai.daily_request_limit', 100)) }}/day</p>
    </div>

    {{-- This Month --}}
    <div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; padding:20px;">
        <div style="font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:#6b7280; margin-bottom:10px;">This Month</div>
        <div style="font-size:24px; font-weight:800; color:#111827;">{{ number_format($monthCount) }} <span style="font-size:14px; color:#6b7280;">requests</span></div>
        <p style="font-size:12px; color:#9ca3af; margin:6px 0 0;">Est. cost: ${{ number_format($monthCostUsd, 4) }} / ${{ config('ai.monthly_budget_usd', 10) }} budget</p>
    </div>

    {{-- Total Lifetime --}}
    <div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; padding:20px;">
        <div style="font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:#6b7280; margin-bottom:10px;">Lifetime Requests</div>
        <div style="font-size:24px; font-weight:800; color:#111827;">{{ number_format($totalCount) }}</div>
        <p style="font-size:12px; color:#9ca3af; margin:6px 0 0;">All time across all features</p>
    </div>

</div>

{{-- ── Future Features ──────────────────────────────────── --}}
<div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; padding:22px; margin-bottom:28px;">
    <h3 style="font-size:14px; font-weight:800; color:#111827; margin:0 0 14px;">AI Feature Roadmap</h3>
    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:10px;">
        @foreach([
            ['Training AI', 'Course Generator',   'course_generator'],
            ['Training AI', 'Lesson Generator',   'lesson_generator'],
            ['Training AI', 'Quiz Generator',     'quiz_generator'],
            ['Training AI', 'Case Study',         'case_study'],
            ['Marketing AI','Facebook Content',   'facebook_content'],
            ['Marketing AI','LinkedIn Content',   'linkedin_content'],
            ['Marketing AI','Website Content',    'website_content'],
            ['Learning AI', 'AI Tutor',           'ai_tutor'],
            ['Learning AI', 'Learning Assistant', 'learning_assistant'],
        ] as [$group, $label, $key])
        <div style="background:#f8fafc; border-radius:8px; padding:12px; border:1px solid #f0f2f5;">
            <div style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#9ca3af; margin-bottom:4px;">{{ $group }}</div>
            <div style="font-size:13.5px; font-weight:700; color:#374151;">{{ $label }}</div>
            <div style="margin-top:6px;">
                @if(config('ai.features.' . $key))
                    <span style="font-size:11px; background:#dcfce7; color:#16a34a; padding:2px 8px; border-radius:20px; font-weight:700;">Active</span>
                @else
                    <span style="font-size:11px; background:#f1f5f9; color:#94a3b8; padding:2px 8px; border-radius:20px; font-weight:600;">Coming Soon</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ── Recent Usage Log ─────────────────────────────────── --}}
<div style="background:#fff; border:1px solid #e9ecf0; border-radius:12px; overflow:hidden;">
    <div style="padding:18px 22px; border-bottom:1px solid #f0f2f5; display:flex; justify-content:space-between; align-items:center;">
        <h3 style="font-size:14px; font-weight:800; color:#111827; margin:0;">Recent AI Requests (last 10)</h3>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="padding:10px 14px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6b7280;">Time</th>
                    <th style="padding:10px 14px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6b7280;">User</th>
                    <th style="padding:10px 14px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6b7280;">Feature</th>
                    <th style="padding:10px 14px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6b7280;">Model</th>
                    <th style="padding:10px 14px; text-align:right; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6b7280;">Tokens</th>
                    <th style="padding:10px 14px; text-align:right; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6b7280;">Cost (USD)</th>
                    <th style="padding:10px 14px; text-align:center; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6b7280;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentLogs as $log)
                <tr style="border-top:1px solid #f0f2f5;">
                    <td style="padding:10px 14px; color:#6b7280; white-space:nowrap;">{{ $log->created_at->format('d M H:i') }}</td>
                    <td style="padding:10px 14px;">{{ $log->user?->name ?? '—' }}</td>
                    <td style="padding:10px 14px;"><span style="font-family:monospace; font-size:12px; background:#f1f5f9; padding:2px 7px; border-radius:4px;">{{ $log->feature_name }}</span></td>
                    <td style="padding:10px 14px; font-size:12px; color:#6b7280;">{{ $log->model }}</td>
                    <td style="padding:10px 14px; text-align:right; font-weight:600;">{{ number_format($log->total_tokens) }}</td>
                    <td style="padding:10px 14px; text-align:right; color:#6b7280;">${{ number_format($log->estimated_cost_usd, 5) }}</td>
                    <td style="padding:10px 14px; text-align:center;">
                        @if($log->request_status === 'success')
                            <span style="font-size:11px; background:#dcfce7; color:#16a34a; padding:2px 8px; border-radius:20px; font-weight:700;">Success</span>
                        @elseif($log->request_status === 'disabled')
                            <span style="font-size:11px; background:#f1f5f9; color:#6b7280; padding:2px 8px; border-radius:20px; font-weight:700;">Disabled</span>
                        @elseif($log->request_status === 'limit_reached')
                            <span style="font-size:11px; background:#fef3c7; color:#d97706; padding:2px 8px; border-radius:20px; font-weight:700;">Limit</span>
                        @else
                            <span style="font-size:11px; background:#fee2e2; color:#ef4444; padding:2px 8px; border-radius:20px; font-weight:700;">Failed</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding:32px; text-align:center; color:#9ca3af;">No AI requests logged yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
