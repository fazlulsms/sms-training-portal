<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Closed — {{ $course->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0; padding: 0;
            font-family: 'Inter', Arial, sans-serif;
            background: #f0f2f8;
            color: #111827;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: #fff;
            border-radius: 12px;
            padding: 48px 40px;
            max-width: 520px;
            width: 100%;
            margin: 24px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            text-align: center;
        }
        .icon { font-size: 48px; margin-bottom: 16px; }
        h1 { font-size: 22px; font-weight: 700; margin: 0 0 12px; color: #1e293b; }
        p  { font-size: 15px; color: #475569; line-height: 1.6; margin: 0 0 8px; }
        .course-name { font-weight: 600; color: #1e293b; }
        .contact {
            margin-top: 28px;
            padding: 16px 20px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            color: #374151;
        }
        .contact strong { display: block; margin-bottom: 4px; color: #0f172a; }
        a { color: #3b82f6; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">🔒</div>
        <h1>Registration Currently Closed</h1>
        <p>Online self-registration for <span class="course-name">{{ $course->name }}</span> is not available at this time.</p>
        <p>Please contact the <strong>SMS Training Team</strong> for enrollment support.</p>
        <div class="contact">
            <strong>Need to enroll?</strong>
            Contact us and we will assist with your registration and course access.
        </div>
    </div>
</body>
</html>
