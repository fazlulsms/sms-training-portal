@extends('layouts.app')
@section('title','Reports & Analytics')
@section('content')

<style>
.rpt-hub-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(320px,1fr)); gap:20px; margin-bottom:32px; }
.rpt-card {
    background:#fff; border:1px solid #e5e9f0; border-radius:18px;
    padding:0; overflow:hidden; text-decoration:none; color:inherit;
    box-shadow:0 2px 8px rgba(15,23,42,.05);
    transition:box-shadow .18s, transform .18s, border-color .18s;
    display:flex; flex-direction:column;
}
.rpt-card:hover { box-shadow:0 8px 32px rgba(15,23,42,.12); transform:translateY(-2px); border-color:#c5d4f0; }
.rpt-card-top { padding:24px 24px 16px; flex:1; }
.rpt-card-icon { width:50px; height:50px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:22px; margin-bottom:14px; }
.rpt-card-title { font-size:17px; font-weight:800; color:#111827; margin-bottom:6px; }
.rpt-card-desc  { font-size:13px; color:#6b7280; line-height:1.6; }
.rpt-card-footer { padding:14px 24px; border-top:1px solid #f0f2f7; display:flex; align-items:center; justify-content:space-between; }
.rpt-card-meta  { font-size:12px; color:#9ca3af; font-weight:600; }
.rpt-card-arrow { color:#1e3a8a; font-size:18px; font-weight:700; }

.rpt-global-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:28px; }
@media(max-width:900px){ .rpt-global-stats{grid-template-columns:repeat(2,1fr);} .rpt-hub-grid{grid-template-columns:1fr;} }
@media(max-width:560px){ .rpt-global-stats{grid-template-columns:repeat(2,1fr);} }

.gstat { background:#fff; border:1px solid #e5e9f0; border-radius:14px; padding:18px 20px 14px; position:relative; overflow:hidden; box-shadow:0 1px 4px rgba(15,23,42,.04); }
.gstat-accent { position:absolute; top:0; left:0; right:0; height:3px; border-radius:14px 14px 0 0; }
.gstat-num   { font-size:28px; font-weight:900; color:#111827; line-height:1; margin-bottom:4px; }
.gstat-label { font-size:11px; color:#6b7280; font-weight:700; text-transform:uppercase; letter-spacing:.5px; }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">Reports & Analytics</h1>
        <p class="page-subtitle">Training performance, financial summaries, and geographic coverage</p>
    </div>
</div>

{{-- Global stats --}}
<div class="rpt-global-stats">
    <div class="gstat">
        <div class="gstat-accent" style="background:#2563eb;"></div>
        <div class="gstat-num">{{ number_format($stats['elearning_enrollments'] + $stats['ilt_enrollments']) }}</div>
        <div class="gstat-label">Total Enrollments</div>
    </div>
    <div class="gstat">
        <div class="gstat-accent" style="background:#7c3aed;"></div>
        <div class="gstat-num">{{ number_format($stats['certificates_elearning'] + $stats['certificates_ilt'] + $stats['certificates_corp']) }}</div>
        <div class="gstat-label">Certificates Issued</div>
    </div>
    <div class="gstat">
        <div class="gstat-accent" style="background:#16a34a;"></div>
        <div class="gstat-num">{{ number_format($stats['total_paid'], 0) }}</div>
        <div class="gstat-label">Total Revenue (BDT)</div>
    </div>
    <div class="gstat">
        <div class="gstat-accent" style="background:#d97706;"></div>
        <div class="gstat-num">{{ $stats['countries'] }}</div>
        <div class="gstat-label">Countries Reached</div>
    </div>
</div>

{{-- Report cards --}}
<div class="rpt-hub-grid">

    <a href="{{ route('reports.elearning') }}" class="rpt-card">
        <div class="rpt-card-top">
            <div class="rpt-card-icon" style="background:#eff6ff;">📊</div>
            <div class="rpt-card-title">eLearning Reports</div>
            <div class="rpt-card-desc">Enrollment analytics, completion rates, certificate issuance, and revenue for self-paced online courses.</div>
        </div>
        <div class="rpt-card-footer">
            <span class="rpt-card-meta">{{ number_format($stats['elearning_enrollments']) }} total enrollments</span>
            <span class="rpt-card-arrow">→</span>
        </div>
    </a>

    <a href="{{ route('reports.ilt') }}" class="rpt-card">
        <div class="rpt-card-top">
            <div class="rpt-card-icon" style="background:#fefce8;">🎓</div>
            <div class="rpt-card-title">Instructor-Led Training</div>
            <div class="rpt-card-desc">Session-based training analytics, attendance records, trainer performance, and facility coverage.</div>
        </div>
        <div class="rpt-card-footer">
            <span class="rpt-card-meta">{{ number_format($stats['ilt_enrollments']) }} total participants</span>
            <span class="rpt-card-arrow">→</span>
        </div>
    </a>

    <a href="{{ route('reports.financial') }}" class="rpt-card">
        <div class="rpt-card-top">
            <div class="rpt-card-icon" style="background:#f0fdf4;">💰</div>
            <div class="rpt-card-title">Financial Reports</div>
            <div class="rpt-card-desc">Invoice summaries, payment collections, method-wise breakdown, and outstanding dues.</div>
        </div>
        <div class="rpt-card-footer">
            <span class="rpt-card-meta">{{ number_format($stats['invoices']) }} invoices on record</span>
            <span class="rpt-card-arrow">→</span>
        </div>
    </a>

    <a href="{{ route('reports.geographic') }}" class="rpt-card">
        <div class="rpt-card-top">
            <div class="rpt-card-icon" style="background:#fdf4ff;">🌍</div>
            <div class="rpt-card-title">Geographic Coverage</div>
            <div class="rpt-card-desc">Countries, cities, and facilities covered. Participant distribution by location.</div>
        </div>
        <div class="rpt-card-footer">
            <span class="rpt-card-meta">{{ $stats['countries'] }} countries covered</span>
            <span class="rpt-card-arrow">→</span>
        </div>
    </a>

    <a href="{{ route('reports.export-center') }}" class="rpt-card">
        <div class="rpt-card-top">
            <div class="rpt-card-icon" style="background:#fff7ed;">📥</div>
            <div class="rpt-card-title">Export Center</div>
            <div class="rpt-card-desc">Quick-access exports for all report types. Download PDF, CSV, or Excel in one click.</div>
        </div>
        <div class="rpt-card-footer">
            <span class="rpt-card-meta">PDF · CSV · Excel</span>
            <span class="rpt-card-arrow">→</span>
        </div>
    </a>

</div>

@endsection
