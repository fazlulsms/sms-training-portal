@extends('layouts.app')
@section('page-title', 'Email Notification Settings')
@section('content')

<style>
.ns-wrap   { max-width: 1100px; margin: 0 auto; }
.ns-section { margin-bottom: 28px; }
.ns-section-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 12px 20px; border-radius: 10px 10px 0 0;
    font-weight: 800; font-size: 14px;
}
.ns-head-participant { background: #14532d; color: #fff; }
.ns-head-admin       { background: #1e3a8a; color: #fff; }
.ns-table { width: 100%; border-collapse: collapse; background: #fff; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 10px 10px; overflow: hidden; }
.ns-table th { padding: 10px 16px; font-size: 12px; font-weight: 700; color: #6b7280; background: #f9fafb; border-bottom: 1px solid #e5e7eb; text-align: left; }
.ns-table td { padding: 13px 16px; border-bottom: 1px solid #f3f4f6; font-size: 13px; vertical-align: middle; }
.ns-table tr:last-child td { border-bottom: none; }
.ns-table tr:hover td { background: #fafafa; }
.ns-label { font-weight: 700; color: #111827; font-size: 13.5px; }
.ns-desc  { color: #6b7280; font-size: 12px; margin-top: 2px; }

/* Toggle switch */
.toggle-wrap { display: flex; align-items: center; gap: 10px; }
.toggle      { position: relative; display: inline-block; width: 44px; height: 24px; }
.toggle input { opacity: 0; width: 0; height: 0; }
.slider {
    position: absolute; cursor: pointer; inset: 0;
    background: #d1d5db; border-radius: 24px; transition: .2s;
}
.slider:before {
    position: absolute; content: "";
    height: 18px; width: 18px; left: 3px; bottom: 3px;
    background: #fff; border-radius: 50%; transition: .2s;
    box-shadow: 0 1px 3px rgba(0,0,0,.2);
}
input:checked + .slider { background: #15803d; }
input:checked + .slider:before { transform: translateX(20px); }
.toggle-label { font-size: 12px; font-weight: 700; color: #6b7280; min-width: 40px; }
.enabled-yes  { color: #15803d; }
.enabled-no   { color: #9ca3af; }

/* Log table */
.log-badge-sent   { background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 700; }
.log-badge-failed { background: #fee2e2; color: #991b1b; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 700; }

/* Bulk buttons */
.btn-bulk { background: rgba(255,255,255,.15); color: #fff; border: 1px solid rgba(255,255,255,.3); padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer; text-decoration: none; }
.btn-bulk:hover { background: rgba(255,255,255,.25); }
</style>

<x-page-header title="Email Notification Settings" desc="Enable or disable automatic email notifications for each event.">
</x-page-header>

<x-flash-message />

<div class="ns-wrap">

@foreach($settings as $group => $rows)
<div class="ns-section">
    <div class="ns-section-head {{ $group === 'participant' ? 'ns-head-participant' : 'ns-head-admin' }}">
        <span>{{ $group === 'participant' ? '👤 Participant Notifications' : '🔔 Admin Notifications' }}</span>
        <span style="display:flex;gap:8px;">
            <form method="POST" action="{{ route('notifications.toggle-all') }}" style="margin:0;">
                @csrf
                <input type="hidden" name="group" value="{{ $group }}">
                <input type="hidden" name="enabled" value="1">
                <button type="submit" class="btn-bulk">Enable All</button>
            </form>
            <form method="POST" action="{{ route('notifications.toggle-all') }}" style="margin:0;">
                @csrf
                <input type="hidden" name="group" value="{{ $group }}">
                <input type="hidden" name="enabled" value="0">
                <button type="submit" class="btn-bulk">Disable All</button>
            </form>
        </span>
    </div>
    <table class="ns-table">
        <thead>
            <tr>
                <th style="width:30%">Notification</th>
                <th>Description</th>
                <th style="width:130px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $setting)
            <tr>
                <td>
                    <div class="ns-label">{{ $setting->label }}</div>
                    <div style="font-family:monospace;font-size:11px;color:#9ca3af;margin-top:2px;">{{ $setting->key }}</div>
                </td>
                <td><div class="ns-desc">{{ $setting->description }}</div></td>
                <td>
                    <form method="POST" action="{{ route('notifications.toggle', $setting) }}" style="margin:0;">
                        @csrf
                        <div class="toggle-wrap">
                            <label class="toggle">
                                <input type="checkbox" onchange="this.form.submit()" {{ $setting->enabled ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                            <span class="toggle-label {{ $setting->enabled ? 'enabled-yes' : 'enabled-no' }}">
                                {{ $setting->enabled ? 'ON' : 'OFF' }}
                            </span>
                        </div>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endforeach

{{-- Email Log --}}
<div class="ns-section">
    <div class="ns-section-head" style="background:#374151;">
        <span>📋 Recent Email Log (last 50)</span>
        <span style="font-size:12px;font-weight:600;opacity:.8;">Sent + Failed history</span>
    </div>
    <div style="background:#fff;border:1px solid #e5e7eb;border-top:none;border-radius:0 0 10px 10px;overflow:auto;">
        <table class="ns-table" style="border:none;">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Recipient</th>
                    <th>Subject</th>
                    <th>Type</th>
                    <th>Model</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentLogs as $log)
                <tr>
                    <td style="white-space:nowrap;color:#6b7280;font-size:12px;">{{ $log->sent_at?->format('d M H:i') ?? $log->created_at->format('d M H:i') }}</td>
                    <td style="font-size:12px;">{{ $log->recipient }}</td>
                    <td style="font-size:12px;">{{ Str::limit($log->subject, 50) }}</td>
                    <td style="font-family:monospace;font-size:11px;color:#6b7280;">{{ $log->notification_type ?? '—' }}</td>
                    <td style="font-size:12px;color:#6b7280;">
                        @if($log->related_model_type)
                        {{ $log->related_model_type }} #{{ $log->related_model_id }}
                        @else —
                        @endif
                    </td>
                    <td>
                        @if($log->status === 'sent')
                        <span class="log-badge-sent">✓ Sent</span>
                        @else
                        <span class="log-badge-failed" title="{{ $log->error_message }}">✗ Failed</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:#9ca3af;padding:24px;">No email logs yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</div>
@endsection
