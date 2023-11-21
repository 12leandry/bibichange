<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Verification Code</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        p {
            font-size: 16px;
            line-height: 1.6;
            color: #555;
        }

        strong {
            color: #0066cc;
        }
    </style>
</head>

<body>
    <div class="container">

        <!-- Your logo or header goes here -->

        <h2>Password Reset Verification Code</h2>

        <p>Hi there!</p>

        <p>Your password reset verification code is: <strong>{{ $code }}</strong>.</p>

        <p>This code will expire in a short period, so please use it promptly.</p>

        <p>If you didn't request this password reset, you can ignore this email.</p>

        <!-- Add any additional information or instructions -->

        <p>Best regards,<br>{{ 'app.name' }}</p>

        <!-- Your company's contact information or footer -->

    </div>
</body>

</html>
