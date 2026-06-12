<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Thank You – {{ config('app.name') }}</title>
<style>
*{box-sizing:border-box;}
body{margin:0;background:#f8fafc;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;color:#111827;display:flex;align-items:center;justify-content:center;min-height:100vh;}
.card{background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,.08);padding:48px 40px;text-align:center;max-width:480px;width:100%;margin:24px 16px;}
.icon{font-size:64px;margin-bottom:20px;display:block;}
h1{font-size:26px;font-weight:800;color:#065f46;margin:0 0 10px;}
p{font-size:15px;color:#6b7280;line-height:1.6;margin:0 0 24px;}
.rating-display{font-size:28px;color:#f59e0b;letter-spacing:2px;margin-bottom:8px;}
.rating-label{font-size:13px;color:#9ca3af;margin-bottom:28px;}
.divider{border:none;border-top:1px solid #f3f4f6;margin:24px 0;}
.badge{display:inline-block;background:#d1fae5;color:#065f46;font-size:13px;font-weight:700;padding:6px 18px;border-radius:99px;margin-bottom:24px;}
.btn{display:inline-block;background:linear-gradient(135deg,#1e3a8a,#2563eb);color:#fff;padding:12px 28px;border-radius:10px;font-size:14px;font-weight:700;text-decoration:none;}
.note{font-size:12px;color:#9ca3af;margin-top:20px;}
</style>
</head>
<body>
<div class="card">
    <span class="icon">🎉</span>
    <h1>Thank You!</h1>
    <p>Your feedback has been successfully submitted.<br>We appreciate you taking the time to share your experience.</p>

    @if($response->overall_rating)
    <div class="rating-display">
        @for($i = 1; $i <= 5; $i++){{ $i <= round($response->overall_rating) ? '★' : '☆' }}@endfor
    </div>
    <div class="rating-label">Your overall rating: {{ number_format($response->overall_rating, 1) }} / 5</div>
    @endif

    @if($response->testimonial_consent && $response->testimonial_text)
    <div class="badge">✓ Testimonial Submitted</div>
    <p style="font-size:13px;">Your testimonial will be reviewed before public display. Thank you for sharing!</p>
    @endif

    <div class="divider"></div>

    <p style="font-size:13.5px;">Your feedback helps us continuously improve our training programs and deliver better learning experiences.</p>

    <div class="note">This feedback form is now closed. If you have additional comments, please contact us directly.</div>
</div>
</body>
</html>
