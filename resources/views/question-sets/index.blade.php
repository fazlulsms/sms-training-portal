@extends('layouts.app')
@section('page-title', 'Question Sets')
@section('content')

<x-page-header title="Question Sets" desc="Manage reusable knowledge test question sets for Instructor-Led Training." />

<style>
.qs-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,.05);}
.qs-toolbar{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:18px 20px;border-bottom:1px solid #f1f5f9;flex-wrap:wrap;}
.qs-search{display:flex;gap:8px;flex:1;min-width:220px;}
.qs-search input,.qs-search select{border:1px solid #cbd5e1;border-radius:8px;padding:8px 12px;font-size:13px;color:#334155;}
.btn-primary{background:#1e3a8a;color:#fff;border:none;border-radius:8px;padding:9px 18px;font-size:13px;font-weight:700;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:6px;}
.btn-secondary{background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;border-radius:8px;padding:8px 14px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:5px;}
.qs-table{width:100%;border-collapse:collapse;}
.qs-table th{padding:10px 16px;font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:.05em;border-bottom:2px solid #f1f5f9;text-align:left;background:#fafafa;}
.qs-table td{padding:13px 16px;font-size:13px;color:#374151;border-bottom:1px solid #f8fafc;vertical-align:middle;}
.qs-table tr:hover td{background:#fafeff;}
.badge{display:inline-flex;align-items:center;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:700;}
.badge-green{background:#dcfce7;color:#166534;}
.badge-red{background:#fee2e2;color:#991b1b;}
.badge-gray{background:#f1f5f9;color:#64748b;}
.action-btn{display:inline-flex;align-items:center;gap:4px;padding:5px 10px;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;border:none;cursor:pointer;}
.action-btn-blue{background:#eff6ff;color:#1d4ed8;}
.action-btn-amber{background:#fffbeb;color:#92400e;}
.action-btn-red{background:#fef2f2;color:#dc2626;}
.empty-state{text-align:center;padding:56px 20px;color:#94a3b8;}
</style>

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #86efac;color:#166534;border-radius:8px;padding:12px 18px;margin-bottom:16px;font-size:13px;">✅ {{ session('success') }}</div>
@endif

<div class="qs-card">
    <div class="qs-toolbar">
        <form method="GET" action="" class="qs-search">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search question sets…" style="flex:1;">
            <select name="status" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="Active" {{ request('status')=='Active'?'selected':'' }}>Active</option>
                <option value="Inactive" {{ request('status')=='Inactive'?'selected':'' }}>Inactive</option>
            </select>
            <button type="submit" class="btn-primary">Search</button>
        </form>
        <a href="/admin/question-sets/create" class="btn-primary">+ New Question Set</a>
    </div>

    @if($questionSets->isEmpty())
    <div class="empty-state">
        <div style="font-size:40px;margin-bottom:12px;">📋</div>
        <div style="font-size:16px;font-weight:700;color:#374151;margin-bottom:6px;">No Question Sets Yet</div>
        <div style="margin-bottom:20px;">Create your first reusable knowledge test question set.</div>
        <a href="/admin/question-sets/create" class="btn-primary">+ Create Question Set</a>
    </div>
    @else
    <table class="qs-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Status</th>
                <th>Total Marks</th>
                <th>Pass Mark</th>
                <th>Attempts</th>
                <th>Questions</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($questionSets as $qs)
        <tr>
            <td>
                <div style="font-weight:700;color:#1e293b;">{{ $qs->title }}</div>
                @if($qs->description)
                <div style="font-size:12px;color:#64748b;margin-top:2px;">{{ Str::limit($qs->description, 60) }}</div>
                @endif
            </td>
            <td>
                <span class="badge {{ $qs->status === 'Active' ? 'badge-green' : 'badge-red' }}">
                    {{ $qs->status }}
                </span>
            </td>
            <td>{{ $qs->total_marks }}</td>
            <td>
                @if($qs->pass_mark)
                    {{ $qs->pass_mark }} marks
                @elseif($qs->pass_percentage)
                    {{ $qs->pass_percentage }}%
                @else
                    50% (default)
                @endif
            </td>
            <td>{{ $qs->allowed_attempts }}</td>
            <td>
                <span class="badge badge-gray">{{ $qs->questions_count ?? 0 }} questions</span>
            </td>
            <td style="white-space:nowrap;">
                <a href="/admin/question-sets/{{ $qs->id }}/questions" class="action-btn action-btn-blue">✏️ Questions</a>
                <a href="/admin/question-sets/edit/{{ $qs->id }}" class="action-btn action-btn-amber">⚙️ Edit</a>
                <a href="/admin/question-sets/delete/{{ $qs->id }}"
                   class="action-btn action-btn-red"
                   onclick="return confirm('Delete this question set? This cannot be undone.')">🗑️ Delete</a>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    <div style="padding:14px 20px;">
        {{ $questionSets->links() }}
    </div>
    @endif
</div>

@endsection
