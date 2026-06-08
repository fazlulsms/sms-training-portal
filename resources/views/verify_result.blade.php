<!DOCTYPE html>
<html>
<head>
    <title>Verification Result</title>
</head>
<body style="font-family:Arial; background:#f3f4f6; padding:40px;">

<div style="max-width:800px; margin:auto; background:white; padding:30px; border-radius:10px;">

@if($enrollment)

    <h2 style="color:green;">VALID CERTIFICATE</h2>

    <p><strong>Certificate Number:</strong> {{ $enrollment->certificate_number }}</p>
    <p><strong>Participant Name:</strong> {{ $enrollment->full_name }}</p>
    <p><strong>Company:</strong> {{ $enrollment->company }}</p>
    <p><strong>Issue Date:</strong> {{ $enrollment->certificate_issue_date }}</p>
    <p><strong>Validity:</strong> No Expiry</p>
    <p><strong>Verified by:</strong> Sustainable Management System Inc.</p>

@else

    <h2 style="color:red;">Certificate Not Found</h2>
    <p>Please check the certificate number and participant name.</p>

@endif

</div>

</body>
</html>