<!doctype html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #dddddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #2563eb;
            padding: 20px;
            text-align: center;
            color: white;
        }

        .header img {
            height: 100px;
            padding: 5px;
            border-radius: 4px;
        }

        .content {
            padding: 20px;
            text-align: center;
        }

        .content h1 {
            font-size: 20px;
            color: #333333;
            margin-bottom: 20px;
        }

        .content p {
            font-size: 16px;
            color: #555555;
            margin-bottom: 20px;
        }

        .confirmation-code {
            font-size: 24px;
            font-weight: bold;
            color: #0078f0;
            margin-bottom: 20px;
        }

        .footer {
            background-color: #f1f1f1;
            text-align: center;
            padding: 10px;
            font-size: 12px;
            color: #888888;
        }

        .footer a {
            color: #2563eb;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <img src="https://cdn2.f-cdn.com/contestentries/392089/3249793/57192d913e1c7_thumb900.jpg" alt="WebPoS"
                style="max-width: 100%; height: auto; width: 50px;">
        </div>

        <!-- Content -->
        <div class="content">
            <h1>Hi {{ $name }},</h1>
            <p>You requested to reset your password.</p>
            <p>Use the following OTP code to proceed with your password reset:</p>
            <p class="confirmation-code">{{ $otp }}</p>
            <p style="color: #888888; font-size: 14px;">This code is valid for 5 minutes from now.</p>
            <p>If you didn’t request a password reset, please ignore this email or contact our support team immediately.
            </p>
            <p>Thanks,<br>The Web PoS Team</p>
        </div>


        <!-- Footer -->
        <div class="footer">
            <p>© 2024 Web PoS. All rights reserved.</p>
            <p><a href="#">Contact Us</a></p>
        </div>
    </div>
</body>

</html>
