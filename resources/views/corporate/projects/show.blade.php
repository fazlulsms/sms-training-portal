@extends('layouts.app')
@section('title', $project->project_name)
@section('content')

@php $sc = \App\Models\CorporateProject::statusColors()[$project->status] ?? '#6b7280'; @endphp

<div class="page-header">
    <div>
        <div style="font-size:13px;color:#9ca3af;margin-bottom:4px;">
            <a href="{{ route('corporate.projects.index') }}" style="color:#6b7280;text-decoration:none;">Corporate</a> / Projects
        </div>
        <h1 class="page-title">{{ $project->project_name }}</h1>
        <p class="page-subtitle">{{ $project->company_name }}</p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a href="{{ route('corporate.projects.report', $project) }}" class="btn btn-secondary">📊 Report</a>
        <a href="{{ route('corporate.sessions.create') }}?project_id={{ $project->id }}" class="btn btn-secondary">+ Session</a>
        <a href="{{ route('corporate.projects.edit', $project) }}" class="btn btn-primary">Edit</a>
    </div>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

<div style="display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start;" class="corporate-two-col">
<style>@media(max-width:768px){.corporate-two-col{grid-template-columns:1fr!important;}}</style>

    <div>
        {{-- Stats --}}
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:24px;" class="corporate-stats-grid">
<style>@media(max-width:480px){.corporate-stats-grid{grid-template-columns:1fr!important;}}</style>
            <div style="background:#fff;border:1px solid #e9ecf0;border-radius:12px;padding:18px;text-align:center;">
                <div style="font-size:28px;font-weight:900;color:#1e3a8a;">{{ $stats['total_sessions'] }}</div>
                <div style="font-size:12px;color:#6b7280;font-weight:700;text-transform:uppercase;letter-spacing:.4px;">Sessions</div>
            </div>
            <div style="background:#fff;border:1px solid #e9ecf0;border-radius:12px;padding:18px;text-align:center;">
                <div style="font-size:28px;font-weight:900;color:#d97706;">{{ $stats['total_participants'] }}</div>
                <div style="font-size:12px;color:#6b7280;font-weight:700;text-transform:uppercase;letter-spacing:.4px;">Participants</div>
            </div>
            <div style="background:#fff;border:1px solid #e9ecf0;border-radius:12px;padding:18px;text-align:center;">
                <div style="font-size:28px;font-weight:900;color:#7c3aed;">{{ $stats['total_certificates'] }}</div>
                <div style="font-size:12px;color:#6b7280;font-weight:700;text-transform:uppercase;letter-spacing:.4px;">Certificates</div>
            </div>
        </div>

        {{-- Sessions --}}
        <div class="card">
            <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
                <h3 class="card-title">Training Sessions</h3>
                <a href="{{ route('corporate.sessions.create') }}?project_id={{ $project->id }}" class="btn btn-sm btn-primary">+ Add</a>
            </div>
            <div class="card-body" style="padding:0;">
                @forelse($project->sessions as $session)
                @php $ss = \App\Models\CorporateSession::statusColors()[$session->status] ?? '#6b7280'; @endphp
                <div style="padding:16px 20px;border-bottom:1px solid #f0f2f5;display:flex;align-items:flex-start;gap:14px;flex-wrap:wrap;">
                    <div style="width:48px;height:48px;background:#f0f4ff;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">📚</div>
                    <div style="flex:1;min-width:200px;">
                        <div style="font-weight:800;font-size:15px;color:#111827;">
                            <a href="{{ route('corporate.sessions.show', $session) }}" style="text-decoration:none;color:inherit;">{{ $session->course_name }}</a>
                        </div>
                        <div style="font-size:13px;color:#6b7280;margin-top:3px;">
                            📅 {{ $session->training_date->format('d M Y') }}
                            @if($session->trainer_name) &nbsp;·&nbsp; 👤 {{ $session->trainer_name }} @endif
                            @if($session->venue) &nbsp;·&nbsp; 📍 {{ $session->venue }} @endif
                        </div>
                        <div style="margin-top:5px;font-size:12px;color:#9ca3af;">
                            👥 {{ $session->participants_count }} participants
                            &nbsp;·&nbsp; 🏆 {{ $session->certificates->count() }} certificates
                        </div>
                    </div>
                    <div style="display:flex;gap:8px;align-items:center;flex-shrink:0;">
                        <span style="background:{{ $ss }}22;color:{{ $ss }};padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700;">{{ $session->status }}</span>
                        <a href="{{ route('corporate.sessions.show', $session) }}" class="btn btn-sm btn-secondary">View</a>
                    </div>
                </div>
                @empty
                <div style="text-align:center;padding:36px;color:#9ca3af;">No sessions yet. <a href="{{ route('corporate.sessions.create') }}?project_id={{ $project->id }}" style="color:#1e3a8a;font-weight:700;">Add first session →</a></div>
                @endforelse
            </div>
        </div>
    </div>

    <aside>
        <div class="card" style="margin-bottom:16px;">
            <div class="card-header"><h3 class="card-title">Project Details</h3></div>
            <div class="card-body" style="padding:0;">
                <div style="padding:10px 16px;border-bottom:1px solid #f0f2f5;font-size:14px;display:flex;justify-content:space-between;align-items:center;">
                    <span style="color:#6b7280;">Status</span>
                    <span style="background:{{ $sc }}22;color:{{ $sc }};padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700;">{{ $project->status }}</span>
                </div>
                @if($project->contact_person)
                <div style="padding:10px 16px;border-bottom:1px solid #f0f2f5;font-size:14px;">
                    <div style="color:#6b7280;font-size:11.5px;text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px;">Contact Person</div>
                    <div style="font-weight:700;">{{ $project->contact_person }}</div>
                    @if($project->contact_designation)<div style="font-size:12.5px;color:#9ca3af;">{{ $project->contact_designation }}</div>@endif
                </div>
                @endif
                @if($project->email)
                <div style="padding:10px 16px;border-bottom:1px solid #f0f2f5;font-size:14px;">
                    <div style="color:#6b7280;font-size:11.5px;text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px;">Email</div>
                    <div style="font-weight:700;word-break:break-all;">{{ $project->email }}</div>
                </div>
                @endif
                @if($project->phone)
                <div style="padding:10px 16px;border-bottom:1px solid #f0f2f5;font-size:14px;">
                    <div style="color:#6b7280;font-size:11.5px;text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px;">Phone</div>
                    <div style="font-weight:700;">{{ $project->phone }}</div>
                </div>
                @endif
                @if($project->address)
                <div style="padding:10px 16px;font-size:13.5px;color:#374151;line-height:1.6;">📍 {{ $project->address }}</div>
                @endif
            </div>
        </div>

        @if($project->remarks)
        <div class="card" style="margin-bottom:16px;">
            <div class="card-header"><h3 class="card-title">Remarks</h3></div>
            <div class="card-body" style="font-size:14px;color:#374151;line-height:1.7;">{{ $project->remarks }}</div>
        </div>
        @endif

        <form method="POST" action="{{ route('corporate.projects.destroy', $project) }}"
              onsubmit="return confirm('Delete this project and ALL its data? This cannot be undone.')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger" style="width:100%;">🗑 Delete Project</button>
        </form>
    </aside>
</div>
@endsection
