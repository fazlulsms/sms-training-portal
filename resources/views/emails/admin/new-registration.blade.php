@extends('emails.layouts.master')
@section('subject-strip') 🔔 New Registration Alert @endsection
@section('subject-theme', 'blue')

@section('content')
<p style="margin:0 0 14px;line-height:1.7;color:#374151;">
  A new participant has registered. Here are the details:
</p>

<div class="em-info-card" style="background:#f0f6ff;border-left:4px solid #2563eb;border-radius:8px;padding:18px 22px;margin:20px 0;">
  <table style="width:100%;border-collapse:collapse;">
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Name</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $name }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Email</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $email }}</td>
    </tr>
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Course</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $courseName }}</td>
    </tr>
    @if(isset($enrollment->company) && $enrollment->company)
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Company</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $enrollment->company }}</td>
    </tr>
    @endif
    @php $phone = $enrollment->mobile_number ?? $enrollment->phone ?? null; @endphp
    @if($phone)
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Phone</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $phone }}</td>
    </tr>
    @endif
    @if(isset($enrollment->selected_mode) && $enrollment->selected_mode)
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Mode</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ $enrollment->selected_mode }}</td>
    </tr>
    @endif
    <tr class="em-row" style="border-bottom:1px solid #dbeafe;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Payment Status</td>
      <td class="em-val" style="padding:7px 0;font-size:13px;vertical-align:top;">
        @php $ps = strtolower($enrollment->payment_status ?? 'pending'); @endphp
        @if(in_array($ps, ['paid','manual_approved']))
          <span class="em-badge-green" style="display:inline-block;background:#dcfce7;color:#15803d;padding:2px 10px;border-radius:12px;font-size:12px;font-weight:700;">{{ ucfirst($enrollment->payment_status) }}</span>
        @else
          <span class="em-badge-amber" style="display:inline-block;background:#fef3c7;color:#b45309;padding:2px 10px;border-radius:12px;font-size:12px;font-weight:700;">{{ ucfirst($enrollment->payment_status ?? 'Pending') }}</span>
        @endif
      </td>
    </tr>
    <tr class="em-row" style="border-bottom:none;">
      <td class="em-label" style="padding:7px 0;color:#6b7280;font-size:13px;font-weight:600;width:44%;vertical-align:top;">Registered At</td>
      <td class="em-val" style="padding:7px 0;color:#111827;font-size:13px;font-weight:700;vertical-align:top;">{{ now()->format('d M Y, H:i') }}</td>
    </tr>
  </table>
</div>

<div class="em-btn-wrap" style="text-align:center;margin:24px 0;">
  @php
    $adminUrl = method_exists($enrollment, 'getCourse') || isset($enrollment->course_id)
      ? url('/admin/elearning/enrollments')
      : url('/admin/enrollments');
  @endphp
  <a href="{{ $adminUrl }}" class="em-btn" style="display:inline-block;background:#2563eb;color:#ffffff !important;text-decoration:none;padding:13px 32px;border-radius:8px;font-size:14px;font-weight:700;">View Enrollments →</a>
</div>

<p style="margin:0 0 14px;line-height:1.7;color:#374151;font-size:13px;color:#6b7280;">
  This is an automated admin notification from SMS Training Academy.
</p>
@endsection
