<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Notice</title>
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
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }
        .login-icon {
            font-size: 48px;
            color: #007bff;
            text-align: center;
            margin-bottom: 20px;
        }
        .content {
            color: #555555;
            line-height: 1.6;
        }
        .login-details {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            border-radius: 6px;
            padding: 20px;
            margin: 30px 0;
        }
        .login-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .login-details td {
            padding: 8px 0;
        }
        .login-details td:first-child {
            font-weight: bold;
            width: 120px;
            color: #333333;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
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
        <div class="login-icon">🔐</div>
        
        <div class="header">
            <h1>New Login Detected</h1>
        </div>
        
        <div class="content">
            <p>Hi {{ $user->full_name }},</p>
            
            <p>We detected a new login to your account. Here are the details:</p>
            
            <div class="login-details">
                <table>
                    <tr>
                        <td>Account:</td>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td>Username:</td>
                        <td>{{ $user->username }}</td>
                    </tr>
                    <tr>
                        <td>Date & Time:</td>
                        <td>{{ $loginTime }}</td>
                    </tr>
                    <tr>
                        <td>IP Address:</td>
                        <td>{{ $ipAddress }}</td>
                    </tr>
                    <tr>
                        <td>Device/Browser:</td>
                        <td>{{ $userAgent }}</td>
                    </tr>
                </table>
            </div>
            
            <p>If this was you, you can safely ignore this email.</p>
            
            <div class="warning">
                <strong>⚠️ Didn't log in?</strong><br>
                If you didn't perform this login, please secure your account immediately by changing your password and contacting our support team.
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Your Company. All rights reserved.</p>
            <p>This is an automated security notice, please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
