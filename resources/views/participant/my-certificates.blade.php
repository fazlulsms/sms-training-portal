@extends('layouts.participant')
@section('page-title', 'My Certificates')
@section('content')

<x-flash-message />

<div style="margin-bottom:20px;">
    <h2 style="font-size:20px;font-weight:800;color:#111827;margin:0 0 4px;">My Certificates</h2>
    <p style="color:#6b7280;font-size:14px;margin:0;">Your earned certificates from completed courses.</p>
</div>

<style>
.cert-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
}

.cert-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(15,23,42,.07);
    display: flex;
    flex-direction: column;
    transition: box-shadow .15s, transform .15s;
}
.cert-card:hover { box-shadow: 0 8px 24px rgba(15,23,42,.12); transform: translateY(-2px); }

.cert-card-banner {
    height: 8px;
}
.banner-issued   { background: linear-gradient(90deg, #16a34a, #4ade80); }
.banner-eligible { background: linear-gradient(90deg, #2563eb, #60a5fa); }

.cert-card-body { padding: 22px; flex: 1; display: flex; flex-direction: column; }

.cert-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 800;
    text-transform: uppercase; letter-spacing: .5px;
    margin-bottom: 14px;
}
.cb-issued   { background: #dcfce7; color: #166534; }
.cb-eligible { background: #dbeafe; color: #1e40af; }

.cert-course-name { font-size: 17px; font-weight: 800; color: #111827; margin: 0 0 16px; line-height: 1.3; }

.cert-details { display: flex; flex-direction: column; gap: 8px; margin-bottom: 18px; }
.cert-detail-row { display: flex; align-items: center; gap: 10px; font-size: 13px; }
.cert-detail-icon { width: 30px; height: 30px; border-radius: 8px; background: #f8fafc; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.cert-detail-label { color: #9ca3af; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; }
.cert-detail-val   { color: #111827; font-weight: 700; font-size: 13px; }

.cert-actions { display: flex; flex-direction: column; gap: 8px; margin-top: auto; }
.cert-btn-dl {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    background: #0f766e; color: white;
    padding: 11px; border-radius: 10px;
    text-decoration: none; font-weight: 700; font-size: 13px;
    transition: background .15s;
}
.cert-btn-dl:hover { background: #0d9488; }
.cert-btn-verify {
    display: flex; align-items: center; justify-content: center; gap: 7px;
    background: #f8fafc; color: #374151;
    padding: 9px; border-radius: 9px;
    text-decoration: none; font-weight: 600; font-size: 12.5px;
    border: 1px solid #e5e7eb;
    transition: background .15s;
}
.cert-btn-verify:hover { background: #f1f5f9; }
.cert-btn-pending {
    display: flex; align-items: center; justify-content: center; gap: 7px;
    background: #eff6ff; color: #1e40af;
    padding: 11px; border-radius: 10px;
    font-weight: 700; font-size: 13px;
    cursor: default;
}

/* ── Empty state ── */
.empty-cert {
    text-align: center;
    padding: 60px 40px;
    background: #fff;
    border: 2px dashed #e5e7eb;
    border-radius: 16px;
    color: #9ca3af;
}
.empty-cert-icon { margin-bottom: 16px; }
.empty-cert-title { font-size: 17px; font-weight: 800; color: #374151; margin-bottom: 8px; }
.empty-cert-desc  { font-size: 14px; line-height: 1.6; }
</style>

@if($elCertificates->isEmpty())
    <div class="empty-cert">
        <div class="empty-cert-icon">
            <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
        </div>
        <div class="empty-cert-title">No certificates yet</div>
        <div class="empty-cert-desc">
            Complete an e-learning course to earn your certificate.<br>
            <a href="{{ route('participant.my-courses') }}" style="color:#1e3a8a;font-weight:700;text-decoration:none;">Go to My Courses →</a>
        </div>
    </div>
@else
    <div class="cert-grid">
        @foreach($elCertificates as $enrollment)
        @php
            $issued   = $enrollment->certificate_status === 'issued';
            $eligible = $enrollment->certificate_status === 'eligible';
        @endphp
        <div class="cert-card">
            <div class="cert-card-banner {{ $issued ? 'banner-issued' : 'banner-eligible' }}"></div>
            <div class="cert-card-body">

                <span class="cert-badge {{ $issued ? 'cb-issued' : 'cb-eligible' }}">
                    @if($issued)
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                        Issued
                    @else
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Eligible
                    @endif
                </span>

                <div class="cert-course-name">{{ $enrollment->course->name ?? 'Course' }}</div>

                <div class="cert-details">
                    @if($issued && $enrollment->certificate_number)
                    <div class="cert-detail-row">
                        <div class="cert-detail-icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0f766e" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </div>
                        <div>
                            <div class="cert-detail-label">Certificate No.</div>
                            <div class="cert-detail-val">{{ $enrollment->certificate_number }}</div>
                        </div>
                    </div>
                    @endif

                    @if($enrollment->updated_at)
                    <div class="cert-detail-row">
                        <div class="cert-detail-icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </div>
                        <div>
                            <div class="cert-detail-label">{{ $issued ? 'Issue Date' : 'Completed' }}</div>
                            <div class="cert-detail-val">{{ $enrollment->updated_at->format('d M Y') }}</div>
                        </div>
                    </div>
                    @endif

                    <div class="cert-detail-row">
                        <div class="cert-detail-icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        </div>
                        <div>
                            <div class="cert-detail-label">Progress</div>
                            <div class="cert-detail-val">{{ $enrollment->progress_percentage }}% completed</div>
                        </div>
                    </div>
                </div>

                <div class="cert-actions">
                    @if($issued)
                        <a href="{{ route('elearning.certificate.generate', $enrollment->id) }}" target="_blank" class="cert-btn-dl">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            Download Certificate PDF
                        </a>
                        <a href="{{ route('elearning.certificate.verify.public') }}?code={{ $enrollment->certificate_number }}"
                           target="_blank" class="cert-btn-verify">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                            Verify Certificate
                        </a>
                    @else
                        <div class="cert-btn-pending">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            Awaiting Issuance by Admin
                        </div>
                        <a href="{{ route('participant.elearning-details', $enrollment->id) }}" class="cert-btn-verify">
                            View Course
                        </a>
                    @endif
                </div>

            </div>
        </div>
        @endforeach
    </div>
@endif

@endsection
