<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verified</title>
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
            color: #28a745;
            margin: 0;
            font-size: 28px;
        }
        .success-icon {
            font-size: 64px;
            color: #28a745;
            text-align: center;
            margin-bottom: 20px;
        }
        .content {
            color: #555555;
            line-height: 1.6;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #28a745;
            border-radius: 6px;
            padding: 20px;
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            background-color: #28a745;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #888888;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">✓</div>
        
        <div class="header">
            <h1>Email Successfully Verified!</h1>
        </div>
        
        <div class="content">
            <p>Hi {{ $user->full_name }},</p>
            
            <p>Congratulations! Your email address has been successfully verified.</p>
            
            <div class="info-box">
                <strong>Account Details:</strong><br>
                Username: <strong>{{ $user->username }}</strong><br>
                Email: <strong>{{ $user->email }}</strong><br>
                Verified: <strong>{{ $user->email_verified_at->format('F d, Y \a\t h:i A') }}</strong>
            </div>
            
            <p>You can now enjoy full access to all features of your account.</p>
            
            <p>If you have any questions or need assistance, feel free to reach out to our support team.</p>
            
            <p>Thank you for joining us!</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Your Company. All rights reserved.</p>
            <p>This is an automated message, please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
