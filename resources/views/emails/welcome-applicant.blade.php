<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to LEHSFF</title>
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
        .info-box p {
            margin: 5px 0;
        }
        .status-badge {
            display: inline-block;
            background-color: #ffc107;
            color: #856404;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
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
        .footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #888;
            border-top: 1px solid #e0e0e0;
        }
        .social-links {
            margin-top: 15px;
        }
        .social-links a {
            color: #05923b;
            text-decoration: none;
            margin: 0 10px;
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
            <h1>Welcome to LEHSFF!</h1>
        </div>
        
        <div class="content">
            <div class="greeting">
                Hello {{ $fullName }}!
            </div>
            
            <div class="message">
                <p>Thank you for registering  Lesotho Entrepreneurship Hub and Seed Financing Facility (LEHSFF). We're excited to have you join our ecosystem!</p>
                
                <p>Your application has been successfully submitted and is now pending review by our administrative team. This process typically takes 2-3 business days.</p>
            </div>
            
            <div class="info-box">
                <p><strong>Application Details:</strong></p>
                <p>📧 Email: {{ $user->email }}</p>
                <p>🏢 Organization: {{ $user->userable->organization_name ?? 'Not specified' }}</p>
                <p>📅 Submitted: {{ now()->format('F j, Y, g:i a') }}</p>
                <div class="status-badge">Status: Pending Review</div>
            </div>
            
            <div class="message">
                <p><strong>What happens next?</strong></p>
                <ol style="margin-left: 20px;">
                    <li>Our team will review your application and verify your documents</li>
                    <li>You'll receive an email notification once your account is approved</li>
                    <li>After approval, you'll be able to access all entrepreneur features</li>
                    <li>You can start applying for calls and accessing resources</li>
                </ol>
            </div>
            
            <div class="message">
                <p>In the meantime, you can:</p>
                <ul style="margin-left: 20px;">
                    <li>Review the <a href="#" style="color: #05923b;">Knowledge Hub</a> for resources</li>
                    <li>Prepare your business documentation</li>
                    <li>Check out upcoming <a href="#" style="color: #05923b;">Calls for Applications</a></li>
                </ul>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ url('/') }}" class="button">Visit LEHSFF Portal</a>
            </div>
            
            <div class="message">
                <p>If you have any questions or need assistance, please don't hesitate to contact our support team at <a href="mailto:support@lehsff.org" style="color: #05923b;">support@lehsff.org</a>.</p>
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Lesotho Highlands Smallholder Farmers Forum. All rights reserved.</p>
            <p>Empowering Entrepreneurs, Building Futures</p>
            <div class="social-links">
                <a href="#">Facebook</a> | 
                <a href="#">Twitter</a> | 
                <a href="#">LinkedIn</a>
            </div>
        </div>
    </div>
</body>
</html>