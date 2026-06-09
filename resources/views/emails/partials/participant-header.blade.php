<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $subject ?? 'SMS Training Services' }}</title>
<style>
  body { font-family: Arial, sans-serif; font-size: 14px; color: #1f2937; background: #f3f4f6; margin: 0; padding: 0; }
  .wrapper { max-width: 620px; margin: 32px auto; background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
  .header  { background: {{ $headerColor ?? '#14532d' }}; padding: 28px 32px; text-align: center; }
  .header h1 { color: #fff; font-size: 20px; margin: 0; }
  .header p  { color: #bbf7d0; font-size: 12px; margin: 4px 0 0; letter-spacing: .5px; text-transform: uppercase; }
  .body    { padding: 32px; }
  .body p  { margin: 0 0 14px; line-height: 1.65; }
  .info-card { background: #f8fafc; border-left: 4px solid {{ $accentColor ?? '#15803d' }}; border-radius: 6px; padding: 16px 20px; margin: 18px 0; }
  .info-card table { width: 100%; border-collapse: collapse; }
  .info-card td { padding: 5px 0; font-size: 13px; }
  .info-card td:first-child { color: #6b7280; width: 44%; font-weight: 600; }
  .info-card td:last-child  { color: #111827; font-weight: 700; }
  .btn { display: inline-block; background: {{ $accentColor ?? '#15803d' }}; color: #ffffff !important; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-weight: 700; font-size: 14px; margin: 8px 0; }
  .divider { border: none; border-top: 1px solid #e5e7eb; margin: 24px 0; }
  .alert-box { border-radius: 8px; padding: 14px 18px; margin: 16px 0; font-size: 13px; }
  .alert-green  { background: #f0fdf4; border: 1px solid #86efac; color: #15803d; }
  .alert-yellow { background: #fffbeb; border: 1px solid #fbbf24; color: #92400e; }
  .alert-red    { background: #fef2f2; border: 1px solid #fca5a5; color: #dc2626; }
  .alert-blue   { background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <h1>{{ $headerIcon ?? '' }} {{ $headerTitle ?? 'SMS Training Services' }}</h1>
    <p>SMS Training Services · Sustainable Management System Inc.</p>
  </div>
  <div class="body">
