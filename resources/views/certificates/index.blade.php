@extends('layouts.app')
@section('page-title', 'Certificate Management')
@section('content')

<style>
.cm-wrap{max-width:1300px;margin:0 auto}
.cm-filter-card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:22px 24px 18px;margin-bottom:20px;box-shadow:0 1px 6px rgba(0,0,0,.05)}
.cm-filter-title{font-size:12px;font-weight:800;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:14px}
.cm-filter-grid{display:grid;grid-template-columns:1fr 1fr 1fr 180px;gap:12px;align-items:end}
.cm-fg label{font-size:11px;font-weight:700;color:#475569;margin-bottom:5px;display:block}
.cm-fg input,.cm-fg select{width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:9px 12px;font-size:13px;color:#334155;background:#fff;box-sizing:border-box}
.cm-fg input:focus,.cm-fg select:focus{border-color:#173a8a;outline:none;box-shadow:0 0 0 3px rgba(23,58,138,.12)}
.status-pills{display:flex;gap:8px;flex-wrap:wrap}
.status-pill{padding:7px 14px;border-radius:20px;font-size:12px;font-weight:700;cursor:pointer;border:2px solid transparent;transition:all .15s;text-decoration:none;white-space:nowrap}
.pill-all{background:#f1f5f9;color:#475569;border-color:#e2e8f0}
.pill-all.active,.pill-all:hover{background:#1e3a8a;color:#fff;border-color:#1e3a8a}
.pill-notgen{background:#fef3c7;color:#92400e;border-color:#fde68a}
.pill-notgen.active,.pill-notgen:hover{background:#d97706;color:#fff;border-color:#d97706}
.pill-gen{background:#dcfce7;color:#166534;border-color:#bbf7d0}
.pill-gen.active,.pill-gen:hover{background:#16a34a;color:#fff;border-color:#16a34a}
.cm-stats{display:flex;gap:16px;margin-bottom:20px;flex-wrap:wrap}
.cm-stat-pill{padding:8px 18px;border-radius:20px;font-size:13px;font-weight:700;display:flex;align-items:center;gap:6px}
.cm-bulk-box{background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 100%);border-radius:12px;padding:18px 24px;margin-bottom:18px;display:flex;align-items:center;gap:16px;flex-wrap:wrap}
.cm-bulk-box label{color:rgba(255,255,255,.8);font-size:12px;font-weight:700;margin-bottom:4px;display:block}
.cm-bulk-box select,.cm-bulk-box input[type=date]{border:1px solid rgba(255,255,255,.25);border-radius:8px;padding:8px 12px;font-size:13px;color:#fff;background:rgba(255,255,255,.12);min-width:190px}
.cm-bulk-box select option{background:#1e3a8a;color:#fff}
.cm-bulk-field{min-width:190px}
.cm-bulk-sep{width:1px;height:48px;background:rgba(255,255,255,.2)}
.cm-bulk-btn{background:#16a34a;color:#fff;border:none;border-radius:8px;padding:10px 22px;font-size:13px;font-weight:700;cursor:pointer;white-space:nowrap}
.cm-bulk-btn:hover{background:#15803d}
.cm-bulk-select-all{display:flex;align-items:center;gap:8px;color:#fff;font-size:13px;font-weight:700;cursor:pointer;white-space:nowrap}
.cm-bulk-select-all input[type=checkbox]{width:16px;height:16px;cursor:pointer;accent-color:#16a34a}
.cm-bulk-count{background:rgba(255,255,255,.15);color:#fff;border-radius:20px;padding:4px 12px;font-size:12px;font-weight:700;white-space:nowrap;margin-left:auto}
.cm-table-wrap{background:#fff;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,.05)}
.cm-table{width:100%;border-collapse:collapse}
.cm-table thead th{padding:11px 14px;font-size:11px;font-weight:800;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;background:#f8fafc;border-bottom:1px solid #e2e8f0;text-align:left}
.cm-table td{padding:13px 14px;border-bottom:1px solid #f1f5f9;font-size:13px;color:#334155;vertical-align:middle}
.cm-table tr:last-child td{border-bottom:none}
.cm-table tr:hover td{background:#fafbff}
.cm-cb{width:40px;text-align:center}
.cm-cb input[type=checkbox]{width:15px;height:15px;cursor:pointer;accent-color:#1e3a8a}
.badge{padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;display:inline-block}
.badge-green{background:#dcfce7;color:#166534}
.badge-yellow{background:#fef9c3;color:#854d0e}
.badge-sent{background:#dcfce7;color:#166534}
.badge-notsent{background:#f3f4f6;color:#9ca3af}
.act-btn{padding:5px 10px;border-radius:6px;font-size:11px;font-weight:700;text-decoration:none;cursor:pointer;border:none;display:inline-block}
.act-view{background:#dbeafe;color:#1e40af}
.act-pdf{background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0}
.act-email{background:#fef3c7;color:#92400e}
.act-del{background:#fee2e2;color:#b91c1c}
.act-gen{background:#1e3a8a;color:#fff}
.cm-empty{text-align:center;padding:60px 20px;color:#9ca3af}
.cm-empty-icon{font-size:48px;margin-bottom:12px}
.cm-empty-title{font-size:16px;font-weight:700;color:#374151;margin-bottom:6px}
.cm-alert{border-radius:10px;padding:14px 18px;margin-bottom:18px;font-size:13px;display:flex;align-items:center;gap:10px}
.cm-alert-success{background:#f0fdf4;border:1px solid #bbf7d0;color:#166534}
.cm-alert-error{background:#fef2f2;border:1px solid #fecaca;color:#b91c1c}
.cm-alert-bulk{background:#eff6ff;border:1px solid #bfdbfe;color:#1e40af}
@media(max-width:900px){.cm-filter-grid{grid-template-columns:1fr 1fr}.cm-bulk-box{flex-direction:column;align-items:flex-start}.cm-table{display:block;overflow-x:auto}}
@media(max-width:600px){.cm-filter-grid{grid-template-columns:1fr}}
</style>

<x-page-header title="Certificate Management" desc="Generate and manage ILT training completion certificates." />
<x-flash-message />

<div class="cm-wrap">

@if(session('bulk_result'))
<div class="cm-alert cm-alert-bulk">📊 {{ session('bulk_result') }}</div>
@endif
@if(session('success'))
<div class="cm-alert cm-alert-success">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="cm-alert cm-alert-error">⚠️ {{ session('error') }}</div>
@endif

{{-- FILTER --}}
<div class="cm-filter-card">
    <div class="cm-filter-title">🔍 Search & Filter</div>
    <form method="GET" action="{{ route('certificates.index') }}" id="filterForm">
        <div class="cm-filter-grid">
            <div class="cm-fg">
                <label>Course</label>
                <select name="course_id" id="courseSelect" onchange="filterSchedules()">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ request('course_id')==$course->id?'selected':'' }}>{{ $course->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="cm-fg">
                <label>Batch / Schedule</label>
                <select name="schedule_id" id="scheduleSelect">
                    <option value="">All Schedules</option>
                    @foreach($schedules as $schedule)
                    <option value="{{ $schedule->id }}" data-course="{{ $schedule->course_id }}" {{ request('schedule_id')==$schedule->id?'selected':'' }}>
                        {{ $schedule->course->name??'' }} | {{ $schedule->batch_code??'N/A' }} | {{ \Carbon\Carbon::parse($schedule->start_date)->format('d M Y') }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="cm-fg">
                <label>Search Name / Company / Email / Cert No.</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Type to search…">
            </div>
            <div class="cm-fg" style="display:flex;gap:8px;align-items:flex-end;">
                <button type="submit" style="flex:1;background:#1e3a8a;color:#fff;border:none;border-radius:8px;padding:9px 0;font-size:13px;font-weight:700;cursor:pointer;">🔍 Filter</button>
                <a href="{{ route('certificates.index') }}" style="flex:1;background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;border-radius:8px;padding:9px 0;font-size:13px;font-weight:700;text-align:center;text-decoration:none;display:block;">↺ Reset</a>
            </div>
        </div>
        <div style="margin-top:14px;">
            <div class="cm-filter-title" style="margin-bottom:8px;">Certificate Status</div>
            @php $activeStatus = request('status','eligible'); @endphp
            <div class="status-pills">
                <a href="{{ route('certificates.index', array_merge(request()->except('status'),['status'=>'eligible'])) }}" class="status-pill pill-all {{ $activeStatus==='eligible'?'active':'' }}">✅ All Eligible</a>
                <a href="{{ route('certificates.index', array_merge(request()->except('status'),['status'=>'not_generated'])) }}" class="status-pill pill-notgen {{ $activeStatus==='not_generated'?'active':'' }}">⏳ Not Yet Generated</a>
                <a href="{{ route('certificates.index', array_merge(request()->except('status'),['status'=>'generated'])) }}" class="status-pill pill-gen {{ $activeStatus==='generated'?'active':'' }}">🏆 Certificate Generated</a>
            </div>
        </div>
    </form>
</div>

{{-- STATS --}}
@if(request()->anyFilled(['q','course_id','schedule_id','status']))
<div class="cm-stats">
    <div class="cm-stat-pill" style="background:#f0f9ff;border:1px solid #bae6fd;color:#0c4a6e;">👥 <strong>{{ $enrollments->count() }}</strong>&nbsp;shown</div>
    <div class="cm-stat-pill" style="background:#f0fdf4;border:1px solid #bbf7d0;color:#14532d;">🏆 <strong>{{ $totalGenerated }}</strong>&nbsp;/&nbsp;{{ $totalEligible }} certificates issued</div>
    @if($totalEligible > $totalGenerated)
    <div class="cm-stat-pill" style="background:#fefce8;border:1px solid #fde047;color:#713f12;">⏳ <strong>{{ $totalEligible - $totalGenerated }}</strong>&nbsp;pending</div>
    @endif
</div>
@endif

{{-- BULK BOX --}}
@if($enrollments->count() > 0)
@php $ungeneratedCount = $enrollments->whereNull('certificate_number')->count(); @endphp
@if($ungeneratedCount > 0)
<form method="POST" action="{{ route('certificates.bulk') }}" id="bulkForm">
    @csrf
    <input type="hidden" name="schedule_id" value="{{ request('schedule_id') }}">
    <div class="cm-bulk-box">
        <div>
            <label>Select Participants</label>
            <label class="cm-bulk-select-all">
                <input type="checkbox" id="selectAllCb" onchange="toggleSelectAll(this)">
                <span>Select all ({{ $ungeneratedCount }})</span>
            </label>
        </div>
        <div class="cm-bulk-sep"></div>
        <div class="cm-bulk-field">
            <label>Certificate Template <span style="color:#fca5a5">*</span></label>
            <select name="certificate_template" required>
                <option value="">Select template…</option>
                @foreach(\App\Http\Controllers\CertificateController::TEMPLATES as $tkey=>$tlabel)
                <option value="{{ $tkey }}">{{ $tlabel }}</option>
                @endforeach
            </select>
        </div>
        <div class="cm-bulk-field">
            <label>Issue Date <span style="color:#fca5a5">*</span></label>
            <input type="date" name="certificate_issue_date" required value="{{ date('Y-m-d') }}">
        </div>
        <div class="cm-bulk-sep"></div>
        <div>
            <label style="margin-bottom:4px;">Action</label>
            <button type="submit" class="cm-bulk-btn" onclick="return confirmBulk()">🏆 Generate Selected</button>
        </div>
        <div class="cm-bulk-count" id="selectedCount">0 selected</div>
    </div>
</form>
@endif
@endif

{{-- TABLE --}}
@if(!request()->anyFilled(['q','course_id','schedule_id','status']))
<div class="cm-table-wrap">
    <div class="cm-empty">
        <div class="cm-empty-icon">🎓</div>
        <div class="cm-empty-title">Select a filter to view eligible participants</div>
        <p style="font-size:13px;">Choose a course, batch, or search above, then click <strong>Filter</strong>.</p>
    </div>
</div>
@elseif($enrollments->count() === 0)
<div class="cm-table-wrap">
    <div class="cm-empty">
        <div class="cm-empty-icon">🔍</div>
        <div class="cm-empty-title">No eligible participants found</div>
        <p style="font-size:13px;max-width:480px;margin:0 auto;">
            Eligible participants must have <strong>Attendance Status = Present</strong> and <strong>Completion Status = Completed</strong>.
            Update these in the Trainer Portal.
        </p>
    </div>
</div>
@else
<div class="cm-table-wrap">
    <table class="cm-table">
        <thead>
            <tr>
                <th class="cm-cb">
                    <input type="checkbox" id="headerCb" style="width:15px;height:15px;accent-color:#1e3a8a;" onchange="toggleSelectAll(this)">
                </th>
                <th>Participant</th>
                <th>Company</th>
                <th>Course / Batch</th>
                <th>Template</th>
                <th>Certificate No.</th>
                <th>Issue Date</th>
                <th style="text-align:center">Email Status</th>
                <th style="text-align:center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($enrollments as $e)
            @php
                $hasCert = !empty($e->certificate_number);
                $tmplLabel = \App\Http\Controllers\CertificateController::TEMPLATES[$e->certificate_template??''] ?? '—';
            @endphp
            <tr>
                <td class="cm-cb">
                    @if(!$hasCert)
                    <input type="checkbox" name="enrollment_ids[]" value="{{ $e->id }}" form="bulkForm"
                           class="row-cb" onchange="updateCount()" style="width:15px;height:15px;accent-color:#1e3a8a;">
                    @else
                    <span style="color:#16a34a;font-size:16px;" title="Certificate issued">✓</span>
                    @endif
                </td>
                <td>
                    <div style="font-weight:700;color:#111827;">{{ $e->full_name }}</div>
                    @if($e->email)<div style="font-size:11px;color:#9ca3af;">{{ $e->email }}</div>@endif
                </td>
                <td style="color:#6b7280;font-size:12px;">{{ $e->company??'—' }}</td>
                <td>
                    <div style="font-size:12px;font-weight:600;">{{ $e->trainingSchedule?->course?->name??'—' }}</div>
                    <div style="font-size:11px;color:#9ca3af;">
                        {{ $e->trainingSchedule?->batch_code??'' }}
                        @if($e->trainingSchedule?->start_date)
                        · {{ \Carbon\Carbon::parse($e->trainingSchedule->start_date)->format('d M Y') }}
                        @endif
                    </div>
                </td>
                <td>
                    @if($hasCert)
                    <span class="badge badge-green">{{ $tmplLabel }}</span>
                    @else<span style="color:#d1d5db">—</span>@endif
                </td>
                <td>
                    @if($hasCert)
                    <code style="font-size:11px;font-family:monospace;background:#f0f9ff;padding:3px 7px;border-radius:5px;color:#1e3a8a;font-weight:700;">{{ $e->certificate_number }}</code>
                    @if($e->certificate_generated_by)
                    <div style="font-size:10px;color:#9ca3af;margin-top:2px;">by {{ $e->certificate_generated_by }}</div>
                    @endif
                    @else
                    <span class="badge badge-yellow">Not Generated</span>
                    @endif
                </td>
                <td style="white-space:nowrap;font-size:12px;">
                    {{ $e->certificate_issue_date ? \Carbon\Carbon::parse($e->certificate_issue_date)->format('d M Y') : '—' }}
                </td>
                <td style="text-align:center;">
                    @if($e->certificate_email_sent)
                    <span class="badge badge-sent">✓ Sent</span>
                    @if($e->certificate_email_sent_at)
                    <div style="font-size:10px;color:#9ca3af;">{{ $e->certificate_email_sent_at->format('d M') }}</div>
                    @endif
                    @else
                    <span class="badge badge-notsent">— Not Sent</span>
                    @endif
                </td>
                <td>
                    <div style="display:flex;gap:5px;justify-content:center;flex-wrap:wrap;">
                        @if($hasCert)
                        <a href="/admin/certificates/view/{{ $e->id }}" target="_blank" class="act-btn act-view">View</a>
                        <a href="/admin/certificates/pdf/{{ $e->id }}" class="act-btn act-pdf">PDF</a>
                        @if($e->email)
                        <form method="POST" action="{{ route('certificates.email',$e->id) }}" style="margin:0;"
                              onsubmit="return confirm('Send certificate email to {{ addslashes($e->email) }}?')">
                            @csrf
                            <button type="submit" class="act-btn act-email">📧 Email</button>
                        </form>
                        @endif
                        <a href="/admin/certificates/delete/{{ $e->id }}" class="act-btn act-del"
                           onclick="return confirm('Revoke certificate for {{ addslashes($e->full_name) }}?')">Delete</a>
                        @else
                        <a href="/admin/certificates/generate/{{ $e->id }}" class="act-btn act-gen">Generate</a>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

</div>

<script>
function filterSchedules(){
    var cid=document.getElementById('courseSelect').value;
    var sel=document.getElementById('scheduleSelect');
    Array.from(sel.options).forEach(function(o){
        if(!o.value){o.style.display='';return;}
        o.style.display=(!cid||o.dataset.course==cid)?'':'none';
    });
    var so=sel.options[sel.selectedIndex];
    if(so&&so.value&&cid&&so.dataset.course!=cid)sel.value='';
}
function toggleSelectAll(cb){
    var checked=cb.checked;
    document.querySelectorAll('.row-cb').forEach(function(c){c.checked=checked;});
    document.querySelectorAll('#selectAllCb,#headerCb').forEach(function(c){c.checked=checked;});
    updateCount();
}
function updateCount(){
    var n=document.querySelectorAll('.row-cb:checked').length;
    var el=document.getElementById('selectedCount');
    if(el)el.textContent=n+' selected';
    var total=document.querySelectorAll('.row-cb').length;
    ['headerCb','selectAllCb'].forEach(function(id){
        var c=document.getElementById(id);
        if(!c)return;
        c.indeterminate=n>0&&n<total;
        c.checked=n>0&&n===total;
    });
}
function confirmBulk(){
    var n=document.querySelectorAll('.row-cb:checked').length;
    if(!n){alert('Please select at least one participant.');return false;}
    var t=document.querySelector('[name="certificate_template"]');
    if(t&&!t.value){alert('Please select a certificate template.');return false;}
    var d=document.querySelector('[name="certificate_issue_date"]');
    if(d&&!d.value){alert('Please select an issue date.');return false;}
    return confirm('Generate certificates for '+n+' participant(s)?\nEmails will be sent automatically.');
}
document.addEventListener('DOMContentLoaded',filterSchedules);
</script>

@endsection