@extends('layouts.app')
@section('title','Export Center')
@section('content')

<style>
.ec-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(340px,1fr)); gap:20px; margin-top:20px; }
.ec-card { background:#fff; border:1px solid #e5e9f0; border-radius:16px; overflow:hidden; box-shadow:0 2px 8px rgba(15,23,42,.05); }
.ec-card-header { padding:18px 22px 14px; border-bottom:1px solid #f0f2f7; display:flex; align-items:center; gap:12px; }
.ec-card-icon { width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0; }
.ec-card-title { font-size:15px; font-weight:800; color:#111827; }
.ec-card-sub   { font-size:12.5px; color:#6b7280; margin-top:2px; }
.ec-card-body  { padding:16px 22px; }
.ec-filter-row { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:14px; align-items:flex-end; }
.ef { height:34px; padding:0 10px; border:1.5px solid #e5e9f0; border-radius:8px; font-size:13px; font-family:inherit; color:#374151; background:#fafbfd; outline:none; }
.ef:focus { border-color:#1e3a8a; }
.ec-export-row { display:flex; gap:8px; flex-wrap:wrap; }
.btn-pdf   { background:#ef4444; color:#fff; padding:8px 16px; border-radius:8px; font-size:12.5px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:5px; }
.btn-csv   { background:#16a34a; color:#fff; padding:8px 16px; border-radius:8px; font-size:12.5px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:5px; }
.btn-excel { background:#1d6c3a; color:#fff; padding:8px 16px; border-radius:8px; font-size:12.5px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:5px; }
.btn-pdf:hover,.btn-csv:hover,.btn-excel:hover { opacity:.88; }
</style>

<div class="page-header">
    <div>
        <div style="font-size:13px;color:#9ca3af;margin-bottom:4px;">
            <a href="{{ route('reports.index') }}" style="color:#6b7280;text-decoration:none;">Reports</a> /
        </div>
        <h1 class="page-title">Export Center</h1>
        <p class="page-subtitle">Quick-access exports for all report types — filter and download in one click</p>
    </div>
</div>

<div class="ec-grid">

    {{-- eLearning Export --}}
    <div class="ec-card">
        <div class="ec-card-header">
            <div class="ec-card-icon" style="background:#eff6ff;">📊</div>
            <div>
                <div class="ec-card-title">eLearning Report</div>
                <div class="ec-card-sub">Enrollments, completion, revenue</div>
            </div>
        </div>
        <div class="ec-card-body">
            <form action="" id="elForm">
                <div class="ec-filter-row">
                    <input type="date" name="date_from" class="ef" placeholder="From" title="From date">
                    <input type="date" name="date_to"   class="ef" placeholder="To"   title="To date">
                </div>
                <div class="ec-export-row">
                    <a href="{{ route('reports.elearning.pdf') }}"   class="btn-pdf"   id="elPdf">📄 PDF</a>
                    <a href="{{ route('reports.elearning.csv') }}"   class="btn-csv"   id="elCsv">⬇ CSV</a>
                    <a href="{{ route('reports.elearning.excel') }}" class="btn-excel" id="elExcel">📊 Excel</a>
                </div>
            </form>
        </div>
    </div>

    {{-- ILT Export --}}
    <div class="ec-card">
        <div class="ec-card-header">
            <div class="ec-card-icon" style="background:#fefce8;">🎓</div>
            <div>
                <div class="ec-card-title">Instructor-Led Training Report</div>
                <div class="ec-card-sub">Participants, attendance, certificates</div>
            </div>
        </div>
        <div class="ec-card-body">
            <form action="" id="iltForm">
                <div class="ec-filter-row">
                    <input type="date" name="date_from" class="ef" placeholder="From">
                    <input type="date" name="date_to"   class="ef" placeholder="To">
                </div>
                <div class="ec-export-row">
                    <a href="{{ route('reports.ilt.pdf') }}"   class="btn-pdf"   id="iltPdf">📄 PDF</a>
                    <a href="{{ route('reports.ilt.csv') }}"   class="btn-csv"   id="iltCsv">⬇ CSV</a>
                    <a href="{{ route('reports.ilt.excel') }}" class="btn-excel" id="iltExcel">📊 Excel</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Financial Export --}}
    <div class="ec-card">
        <div class="ec-card-header">
            <div class="ec-card-icon" style="background:#f0fdf4;">💰</div>
            <div>
                <div class="ec-card-title">Financial Report</div>
                <div class="ec-card-sub">Invoices, payments, dues</div>
            </div>
        </div>
        <div class="ec-card-body">
            <form action="" id="finForm">
                <div class="ec-filter-row">
                    <input type="date" name="date_from" class="ef" placeholder="From">
                    <input type="date" name="date_to"   class="ef" placeholder="To">
                    <select name="payment_status" class="ef">
                        <option value="">All Status</option>
                        @foreach(['Paid','Partial','Pending','Overdue'] as $s)
                        <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="ec-export-row">
                    <a href="{{ route('reports.financial.pdf') }}"   class="btn-pdf"   id="finPdf">📄 PDF</a>
                    <a href="{{ route('reports.financial.csv') }}"   class="btn-csv"   id="finCsv">⬇ CSV</a>
                    <a href="{{ route('reports.financial.excel') }}" class="btn-excel" id="finExcel">📊 Excel</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Geographic Export --}}
    <div class="ec-card">
        <div class="ec-card-header">
            <div class="ec-card-icon" style="background:#fdf4ff;">🌍</div>
            <div>
                <div class="ec-card-title">Geographic Coverage Report</div>
                <div class="ec-card-sub">Countries, cities, facilities</div>
            </div>
        </div>
        <div class="ec-card-body">
            <form action="" id="geoForm">
                <div class="ec-filter-row">
                    <input type="date" name="date_from" class="ef" placeholder="From">
                    <input type="date" name="date_to"   class="ef" placeholder="To">
                </div>
                <div class="ec-export-row">
                    <a href="{{ route('reports.geographic.pdf') }}" class="btn-pdf" id="geoPdf">📄 PDF</a>
                    <a href="{{ route('reports.geographic.csv') }}" class="btn-csv" id="geoCsv">⬇ CSV</a>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
// Dynamically update export link URLs based on filter inputs
function wireForm(formId, links) {
    const form = document.getElementById(formId);
    if (!form) return;
    form.addEventListener('change', () => {
        const params = new URLSearchParams();
        new FormData(form).forEach((v, k) => { if (v) params.append(k, v); });
        const qs = params.toString() ? '?' + params.toString() : '';
        links.forEach(([id, base]) => {
            const el = document.getElementById(id);
            if (el) el.href = base + qs;
        });
    });
}
wireForm('elForm',  [['elPdf','{{ route("reports.elearning.pdf") }}'],['elCsv','{{ route("reports.elearning.csv") }}'],['elExcel','{{ route("reports.elearning.excel") }}']]);
wireForm('iltForm', [['iltPdf','{{ route("reports.ilt.pdf") }}'],['iltCsv','{{ route("reports.ilt.csv") }}'],['iltExcel','{{ route("reports.ilt.excel") }}']]);
wireForm('finForm', [['finPdf','{{ route("reports.financial.pdf") }}'],['finCsv','{{ route("reports.financial.csv") }}'],['finExcel','{{ route("reports.financial.excel") }}']]);
wireForm('geoForm', [['geoPdf','{{ route("reports.geographic.pdf") }}'],['geoCsv','{{ route("reports.geographic.csv") }}']]);
</script>
@endsection
