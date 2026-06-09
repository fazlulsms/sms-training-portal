@php $headerTitle = '🎓 Course Access Activated'; $accentColor = '#1d4ed8'; $headerColor = '#1e3a8a'; @endphp
@include('emails.partials.participant-header')

<p>Dear <strong>{{ $enrollment->participant_name }}</strong>,</p>
<p>Your access to <strong>{{ $courseName }}</strong> has been <strong>activated</strong>. You can now log in and start learning!</p>

<div class="info-card">
  <table>
    <tr><td>Course</td><td>{{ $courseName }}</td></tr>
    <tr><td>Access Valid Until</td><td>{{ $accessDays }}</td></tr>
    @if($tempPassword)
    <tr><td>Username</td><td>{{ $enrollment->email }}</td></tr>
    <tr><td>Temporary Password</td><td><code style="background:#f3f4f6;padding:2px 6px;border-radius:4px;font-size:13px;">{{ $tempPassword }}</code></td></tr>
    @endif
  </table>
</div>

@if($tempPassword)
<div class="alert-yellow">
  ⚠ Please log in and <strong>change your password</strong> immediately for security.
</div>
@endif

<div style="text-align:center;margin:20px 0;">
  <a href="{{ $loginUrl }}" class="btn" style="background:#1d4ed8;">🚀 Start Learning Now</a>
</div>

@include('emails.partials.participant-footer')
