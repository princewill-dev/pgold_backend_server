<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #333333;
            margin: 0;
            font-size: 24px;
        }
        .content {
            color: #555555;
            line-height: 1.6;
        }
        .otp-box {
            background-color: #f8f9fa;
            border: 2px solid #007bff;
            border-radius: 6px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            font-size: 36px;
            font-weight: bold;
            color: #007bff;
            letter-spacing: 8px;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #888888;
            font-size: 12px;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Email Verification</h1>
        </div>
        
        <div class="content">
            <p>Hello,</p>
            <p>Thank you for {{ ucfirst($reason) }}. Please use the following OTP code to verify your email address:</p>
            
            <div class="otp-box">
                <div>Your OTP Code</div>
                <div class="otp-code">{{ $otp }}</div>
            </div>
            
            <div class="warning">
                <strong>⚠️ Important:</strong> This OTP will expire in 10 minutes. Please do not share this code with anyone.
            </div>
            
            <p>If you didn't request this code, please ignore this email.</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Your Company. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
