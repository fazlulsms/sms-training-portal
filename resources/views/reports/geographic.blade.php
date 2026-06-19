@extends('layouts.app')
@section('title','Geographic Coverage Reports')
@section('content')

<style>
.rpt-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;}
@media(max-width:900px){.rpt-stats{grid-template-columns:repeat(2,1fr);}}
.rpt-stat{background:#fff;border:1px solid #e5e9f0;border-radius:14px;padding:16px 18px 13px;position:relative;overflow:hidden;box-shadow:0 1px 4px rgba(15,23,42,.04);}
.rpt-stat-accent{position:absolute;top:0;left:0;right:0;height:3px;border-radius:14px 14px 0 0;}
.rpt-stat-icon{font-size:20px;margin-bottom:8px;}
.rpt-stat-num{font-size:24px;font-weight:900;line-height:1;margin-bottom:3px;}
.rpt-stat-label{font-size:11px;color:#6b7280;font-weight:700;text-transform:uppercase;letter-spacing:.5px;}
.rpt-filter{background:#fff;border:1px solid #e5e9f0;border-radius:14px;padding:14px 18px;margin-bottom:16px;box-shadow:0 1px 3px rgba(15,23,42,.03);}
.rpt-filter-row{display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap;}
.rft{height:36px;padding:0 10px;border:1.5px solid #e5e9f0;border-radius:8px;font-size:13px;font-family:inherit;color:#374151;background:#fafbfd;outline:none;}
.rft:focus{border-color:#1e3a8a;box-shadow:0 0 0 3px rgba(30,58,138,.08);}
.rft-search{padding-left:32px;min-width:200px;flex:1;}
.rft-wrap{position:relative;flex:1;min-width:180px;}
.rft-icon{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none;}
.geo-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;}
@media(max-width:800px){.geo-grid{grid-template-columns:1fr;}}
.geo-card{background:#fff;border:1px solid #e5e9f0;border-radius:14px;padding:18px 20px;box-shadow:0 1px 4px rgba(15,23,42,.04);}
.geo-card-title{font-size:13px;font-weight:800;color:#374151;margin-bottom:14px;text-transform:uppercase;letter-spacing:.4px;}
.geo-row{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #f4f5f8;}
.geo-row:last-child{border-bottom:none;}
.geo-country{font-size:13.5px;font-weight:600;color:#111827;}
.geo-bar-wrap{flex:1;margin:0 12px;background:#f0f2f7;border-radius:10px;height:6px;overflow:hidden;}
.geo-bar{height:100%;border-radius:10px;}
.geo-count{font-size:13px;font-weight:700;color:#6b7280;white-space:nowrap;min-width:36px;text-align:right;}
.export-bar{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;align-items:center;}
.export-bar span{font-size:12.5px;color:#9ca3af;font-weight:700;}
.btn-export-pdf{background:#ef4444;color:#fff;border:none;padding:7px 14px;border-radius:8px;font-size:12.5px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:5px;}
.btn-export-csv{background:#16a34a;color:#fff;border:none;padding:7px 14px;border-radius:8px;font-size:12.5px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:5px;}
.btn-export-pdf:hover,.btn-export-csv:hover{opacity:.88;}
</style>

<div class="page-header">
    <div>
        <div style="font-size:13px;color:#9ca3af;margin-bottom:4px;">
            <a href="{{ route('reports.index') }}" style="color:#6b7280;text-decoration:none;">Reports</a> /
        </div>
        <h1 class="page-title">Geographic Coverage Reports</h1>
        <p class="page-subtitle">Country, city, and facility coverage across all training programs</p>
    </div>
</div>

{{-- Summary stats --}}
<div class="rpt-stats">
    <div class="rpt-stat">
        <div class="rpt-stat-accent" style="background:#2563eb;"></div>
        <div class="rpt-stat-icon">🌍</div>
        <div class="rpt-stat-num" style="color:#2563eb;">{{ $stats['countries_ilt'] + $stats['countries_el'] }}</div>
        <div class="rpt-stat-label">Countries Covered</div>
    </div>
    <div class="rpt-stat">
        <div class="rpt-stat-accent" style="background:#d97706;"></div>
        <div class="rpt-stat-icon">🏙</div>
        <div class="rpt-stat-num" style="color:#d97706;">{{ $stats['venues'] }}</div>
        <div class="rpt-stat-label">Venues / Cities</div>
    </div>
    <div class="rpt-stat">
        <div class="rpt-stat-accent" style="background:#0891b2;"></div>
        <div class="rpt-stat-icon">👥</div>
        <div class="rpt-stat-num" style="color:#0891b2;">{{ number_format($stats['participants_ilt']) }}</div>
        <div class="rpt-stat-label">ILT Participants</div>
    </div>
    <div class="rpt-stat">
        <div class="rpt-stat-accent" style="background:#7c3aed;"></div>
        <div class="rpt-stat-icon">📚</div>
        <div class="rpt-stat-num" style="color:#7c3aed;">{{ number_format($stats['participants_el']) }}</div>
        <div class="rpt-stat-label">eLearning Participants</div>
    </div>
    <div class="rpt-stat">
        <div class="rpt-stat-accent" style="background:#16a34a;"></div>
        <div class="rpt-stat-icon">📅</div>
        <div class="rpt-stat-num" style="color:#16a34a;">{{ number_format($stats['sessions']) }}</div>
        <div class="rpt-stat-label">Sessions Conducted</div>
    </div>
    <div class="rpt-stat">
        <div class="rpt-stat-accent" style="background:#6b7280;"></div>
        <div class="rpt-stat-icon">💳</div>
        <div class="rpt-stat-num" style="color:#6b7280;">{{ $stats['countries_inv'] }}</div>
        <div class="rpt-stat-label">Invoice Countries</div>
    </div>
</div>

{{-- Filter --}}
<div class="rpt-filter">
    <form method="GET" action="{{ route('reports.geographic') }}">
        <div class="rpt-filter-row">
            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="rft" title="From">
            <input type="date" name="date_to"   value="{{ $filters['date_to']   ?? '' }}" class="rft" title="To">
            <select name="country" class="rft" style="min-width:140px;">
                <option value="">All Countries</option>
                @foreach($countries as $c)
                <option value="{{ $c }}" {{ ($filters['country'] ?? '') === $c ? 'selected' : '' }}>{{ $c }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary" style="height:36px;padding:0 16px;font-size:13px;">Filter</button>
            @if(collect($filters)->filter()->isNotEmpty())
            <a href="{{ route('reports.geographic') }}" style="font-size:12.5px;color:#9ca3af;font-weight:600;padding:4px 8px;border-radius:7px;text-decoration:none;white-space:nowrap;">✕ Clear</a>
            @endif
        </div>
    </form>
</div>

{{-- Export --}}
<div class="export-bar">
    <span>Export:</span>
    <a href="{{ route('reports.geographic.pdf', request()->query()) }}" class="btn-export-pdf">📄 PDF</a>
    <a href="{{ route('reports.geographic.csv', request()->query()) }}" class="btn-export-csv">⬇ CSV</a>
</div>

{{-- Country tables --}}
<div class="geo-grid">
    {{-- ILT by Country --}}
    <div class="geo-card">
        <div class="geo-card-title">🎓 ILT Participants by Country</div>
        @php $iltMax = $iltByCountry->max('count') ?: 1; @endphp
        @forelse($iltByCountry->take(15) as $row)
        <div class="geo-row">
            <div class="geo-country">{{ $row->country }}</div>
            <div class="geo-bar-wrap"><div class="geo-bar" style="background:#1e3a8a;width:{{ round($row->count/$iltMax*100) }}%;"></div></div>
            <div class="geo-count">{{ number_format($row->count) }}</div>
        </div>
        @empty
        <div style="text-align:center;padding:24px;color:#9ca3af;font-size:13px;">No data yet.</div>
        @endforelse
    </div>

    {{-- eLearning by Country --}}
    <div class="geo-card">
        <div class="geo-card-title">📚 eLearning Participants by Country</div>
        @php $elMax = $elByCountry->max('count') ?: 1; @endphp
        @forelse($elByCountry->take(15) as $row)
        <div class="geo-row">
            <div class="geo-country">{{ $row->country }}</div>
            <div class="geo-bar-wrap"><div class="geo-bar" style="background:#7c3aed;width:{{ round($row->count/$elMax*100) }}%;"></div></div>
            <div class="geo-count">{{ number_format($row->count) }}</div>
        </div>
        @empty
        <div style="text-align:center;padding:24px;color:#9ca3af;font-size:13px;">No data yet.</div>
        @endforelse
    </div>

    {{-- Sessions by Venue --}}
    <div class="geo-card">
        <div class="geo-card-title">🏢 Sessions by Venue / City</div>
        @php $venueMax = $sessionsByVenue->max('count') ?: 1; @endphp
        @forelse($sessionsByVenue->take(15) as $row)
        <div class="geo-row">
            <div class="geo-country">{{ $row->venue }}</div>
            <div class="geo-bar-wrap"><div class="geo-bar" style="background:#d97706;width:{{ round($row->count/$venueMax*100) }}%;"></div></div>
            <div class="geo-count">{{ number_format($row->count) }}</div>
        </div>
        @empty
        <div style="text-align:center;padding:24px;color:#9ca3af;font-size:13px;">No session venues recorded yet.</div>
        @endforelse
    </div>

    {{-- Invoice by Country --}}
    <div class="geo-card">
        <div class="geo-card-title">💳 Invoice Revenue by Country</div>
        @php $invMax = $invoiceByCountry->max('count') ?: 1; @endphp
        @forelse($invoiceByCountry->take(15) as $row)
        <div class="geo-row">
            <div class="geo-country">{{ $row->client_country }}</div>
            <div class="geo-bar-wrap"><div class="geo-bar" style="background:#16a34a;width:{{ round($row->count/$invMax*100) }}%;"></div></div>
            <div class="geo-count" style="text-align:right;">
                {{ $row->count }}<br>
                <span style="font-size:11px;color:#16a34a;font-weight:700;">{{ number_format($row->paid,0) }}</span>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:24px;color:#9ca3af;font-size:13px;">No invoice country data yet.</div>
        @endforelse
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endsection
