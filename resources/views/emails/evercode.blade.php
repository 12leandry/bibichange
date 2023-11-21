{{-- resources/views/emails/evercode.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} Verification Code</title>
</head>
<body>
    <p>Dear User,</p>
    
    <p>
        Welcome to {{ config('app.name') }}! Your verification code is: 
        <strong>{{ $verificationCode }}</strong>. Please use this code to verify your account.
    </p>
    
    <p>Thank you for choosing {{ config('app.name') }}.</p>
</body>
</html>
