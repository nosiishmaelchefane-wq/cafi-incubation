<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Approved - LEHSFF</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #05923b 0%, #0a6e2e 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header img {
            max-width: 150px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .success-icon {
            font-size: 48px;
            margin: 20px 0;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #05923b;
            margin-bottom: 20px;
        }
        .message {
            margin-bottom: 25px;
            color: #555;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #05923b;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .role-badge {
            display: inline-block;
            background-color: #05923b;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 10px;
        }
        .button {
            display: inline-block;
            background-color: #05923b;
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 25px;
            margin: 20px 0;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #0a6e2e;
        }
        .feature-list {
            list-style: none;
            padding: 0;
        }
        .feature-list li {
            padding: 8px 0;
            padding-left: 25px;
            position: relative;
        }
        .feature-list li:before {
            content: "✓";
            color: #05923b;
            position: absolute;
            left: 0;
            font-weight: bold;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #888;
            border-top: 1px solid #e0e0e0;
        }
        @media (max-width: 600px) {
            .container {
                margin: 10px;
            }
            .content {
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/logo.png') }}" alt="LEHSFF Logo">
            <div class="success-icon">✅</div>
            <h1>Congratulations!</h1>
        </div>
        
        <div class="content">
            <div class="greeting">
                Hello {{ $user->username ?? $user->email }}!
            </div>
            
            <div class="message">
                <p>We are pleased to inform you that your application to join the  Lesotho Entrepreneurship Hub and Seed Financing Facility (LEHSFF) has been <strong>APPROVED</strong>!</p>
                
                <p>Your account has been activated and you now have full access to the LEHSFF ecosystem.</p>
            </div>
            
            <div class="info-box">
                <p><strong>Account Details:</strong></p>
                <p>📧 Email: {{ $user->email }}</p>
                @if($assignedRole)
                    <p>👤 Role: <span style="color: #05923b; font-weight: 600;">{{ $assignedRole }}</span></p>
                @endif
                <p>✅ Status: <span style="color: #05923b;">Active</span></p>
                <div class="role-badge">Account Verified</div>
            </div>
            
            <div class="message">
                <p><strong>What you can do now:</strong></p>
                <ul class="feature-list">
                    <li>Access your personalized dashboard</li>
                    <li>Browse and apply for Calls for Applications</li>
                    <li>Access the Knowledge Hub with resources and training materials</li>
                    <li>Connect with mentors and other entrepreneurs</li>
                    <li>Submit and track your applications</li>
                    <li>Access enterprise support services</li>
                </ul>
            </div>
            
            <div class="message">
                <p><strong>Getting Started:</strong></p>
                <ol style="margin-left: 20px;">
                    <li>Log in to your account using your registered email</li>
                    <li>Complete your profile information</li>
                    <li>Explore available opportunities</li>
                    <li>Review the Knowledge Hub resources</li>
                    <li>Start applying for programs that match your business</li>
                </ol>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ url('/login') }}" class="button">Log in to Your Account</a>
            </div>
            
            <div class="message">
                <p>If you have any questions or need assistance, please contact our support team at <a href="mailto:support@lehsff.org" style="color: #05923b;">support@lehsff.org</a>.</p>
                <p>We're excited to have you on board and look forward to supporting your entrepreneurial journey!</p>
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Lesotho Highlands Smallholder Farmers Forum. All rights reserved.</p>
            <p>Empowering Entrepreneurs, Building Futures</p>
            <p style="margin-top: 10px;">
                <small>This is an automated message, please do not reply to this email.</small>
            </p>
        </div>
    </div>
</body>
</html>