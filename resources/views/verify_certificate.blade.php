<!DOCTYPE html>
<html>
<head>
    <title>Certificate Verification</title>
</head>
<body style="font-family:Arial; background:#f3f4f6; padding:40px;">

<div style="max-width:600px; margin:auto; background:white; padding:30px; border-radius:10px;">
    <h2>Certificate Verification</h2>

    <form method="POST" action="/verify-certificate">
        @csrf

        <p>Certificate Number</p>
        <input type="text" name="certificate_number" style="width:100%; padding:10px;" required>

        <p>Participant Name</p>
        <input type="text" name="full_name" style="width:100%; padding:10px;" required>

        <br><br>

        <button type="submit" style="background:#173a8a; color:white; padding:12px 20px; border:none;">
            Verify Certificate
        </button>
    </form>
</div>

</body>
</html>