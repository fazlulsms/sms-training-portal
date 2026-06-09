@extends('layouts.app')

@section('title', 'Testimonials')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Testimonials & Reviews</h1>
        <p class="page-subtitle">Moderate participant reviews before they appear on the public site</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- Stats row --}}
@php
    $counts = \App\Models\Testimonial::selectRaw('status, count(*) as n')->groupBy('status')->pluck('n','status');
@endphp
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">
    @foreach(['pending'=>['🕐','#d97706'],'approved'=>['✅','#16a34a'],'featured'=>['⭐','#7c3aed'],'rejected'=>['❌','#dc2626']] as $status => $meta)
    <div style="background:#fff;border:1px solid #e9ecf0;border-radius:12px;padding:16px;text-align:center;">
        <div style="font-size:28px;">{{ $meta[0] }}</div>
        <div style="font-size:26px;font-weight:900;color:{{ $meta[1] }};">{{ $counts[$status] ?? 0 }}</div>
        <div style="font-size:12px;color:#6b7280;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ ucfirst($status) }}</div>
    </div>
    @endforeach
</div>

{{-- Filter --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="padding:14px 20px;">
        <form method="GET" action="{{ route('admin.testimonials.index') }}" style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
            <div style="position:relative;">
                <span style="position:absolute;left:9px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name, company, course…"
                       style="padding:8px 12px 8px 30px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;width:220px;">
            </div>
            <select name="status" style="padding:8px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;">
                <option value="">All Statuses</option>
                @foreach(['pending','approved','featured','rejected'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <select name="rating" style="padding:8px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;">
                <option value="">All Ratings</option>
                @foreach([5,4,3,2,1] as $r)
                <option value="{{ $r }}" {{ request('rating') == $r ? 'selected' : '' }}>{{ str_repeat('★',$r) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary" style="padding:8px 16px;">Filter</button>
            @if(request()->hasAny(['q','status','rating']))
            <a href="{{ route('admin.testimonials.index') }}" style="font-size:13px;color:#6b7280;text-decoration:none;">✕ Clear</a>
            @endif
            <div style="margin-left:auto;font-size:12.5px;color:#9ca3af;font-weight:600;">{{ $testimonials->total() }} record(s)</div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Reviewer</th>
                    <th>Rating</th>
                    <th>Feedback</th>
                    <th>Course</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($testimonials as $t)
                @php
                    $statusColors = ['pending'=>'#d97706','approved'=>'#16a34a','featured'=>'#7c3aed','rejected'=>'#dc2626'];
                    $sc = $statusColors[$t->status] ?? '#6b7280';
                @endphp
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            @if($t->photo)
                            <img src="{{ asset('storage/'.$t->photo) }}" alt="" style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                            @else
                            <div style="width:36px;height:36px;border-radius:50%;background:#dbeafe;display:flex;align-items:center;justify-content:center;font-weight:900;color:#1e3a8a;font-size:14px;flex-shrink:0;">
                                {{ strtoupper(substr($t->name,0,1)) }}
                            </div>
                            @endif
                            <div>
                                <div style="font-weight:700;font-size:14px;">{{ $t->name }}</div>
                                <div style="font-size:12px;color:#9ca3af;">{{ $t->designation }}{{ $t->company ? ' · '.$t->company : '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="color:#f59e0b;font-size:15px;white-space:nowrap;">{{ str_repeat('★',$t->rating) }}</td>
                    <td>
                        <div style="font-size:13.5px;color:#374151;max-width:260px;">{{ Str::limit($t->feedback, 100) }}</div>
                    </td>
                    <td style="font-size:13px;color:#6b7280;">{{ $t->course_name ?? $t->course?->name ?? '—' }}</td>
                    <td>
                        <span style="background:{{ $sc }}22;color:{{ $sc }};padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700;">
                            {{ ucfirst($t->status) }}
                        </span>
                    </td>
                    <td style="font-size:13px;color:#9ca3af;">{{ $t->created_at->format('d M Y') }}</td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            @if($t->status !== 'approved' && $t->status !== 'featured')
                            <form method="POST" action="{{ route('admin.testimonials.approve', $t) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm" style="background:#dcfce7;color:#16a34a;border:1px solid #86efac;">✅ Approve</button>
                            </form>
                            @endif
                            @if($t->status !== 'featured')
                            <form method="POST" action="{{ route('admin.testimonials.feature', $t) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm" style="background:#ede9fe;color:#7c3aed;border:1px solid #c4b5fd;">⭐ Feature</button>
                            </form>
                            @endif
                            @if($t->status !== 'rejected')
                            <form method="POST" action="{{ route('admin.testimonials.reject', $t) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm" style="background:#fee2e2;color:#dc2626;border:1px solid #fca5a5;">Reject</button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('admin.testimonials.destroy', $t) }}"
                                  onsubmit="return confirm('Permanently delete this review?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">🗑</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:40px;color:#9ca3af;">No testimonials found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($testimonials->hasPages())
    <div style="padding:16px 20px;border-top:1px solid #f0f2f5;">{{ $testimonials->links() }}</div>
    @endif
</div>
@endsection
