<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Update - LEHSFF</title>
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
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
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
        .warning-icon {
            font-size: 48px;
            margin: 20px 0;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #dc3545;
            margin-bottom: 20px;
        }
        .message {
            margin-bottom: 25px;
            color: #555;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #dc3545;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .status-badge {
            display: inline-block;
            background-color: #dc3545;
            color: white;
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
        .support-box {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
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
            <div class="warning-icon">⚠️</div>
            <h1>Application Status Update</h1>
        </div>
        
        <div class="content">
            <div class="greeting">
                Hello {{ $user->username ?? $user->email }},
            </div>
            
            <div class="message">
                <p>Thank you for your interest in joining the  Lesotho Entrepreneurship Hub and Seed Financing Facility (LEHSFF).</p>
                
                <p>After careful review of your application, we regret to inform you that we are unable to approve your registration at this time.</p>
            </div>
            
            <div class="info-box">
                <p><strong>Application Details:</strong></p>
                <p>📧 Email: {{ $user->email }}</p>
                <p>📅 Submitted: {{ $user->created_at ? $user->created_at->format('F j, Y') : 'Recently' }}</p>
                <div class="status-badge">Status: Not Approved</div>
            </div>
            
            <div class="message">
                <p><strong>Common reasons for non-approval include:</strong></p>
                <ul style="margin-left: 20px;">
                    <li>Incomplete application information</li>
                    <li>Missing or invalid documentation</li>
                    <li>Inconsistent information provided</li>
                </ul>
            </div>
            
            <div class="support-box">
                <p style="margin-bottom: 10px;"><strong>Need Assistance?</strong></p>
                <p style="margin: 0;">If you believe this decision was made in error or would like more information about why your application was not approved, please contact our support team.</p>
            </div>
            
            <div class="message">
                <p><strong>What you can do:</strong></p>
                <ol style="margin-left: 20px;">
                    <li>Review your application and ensure all information is accurate</li>
                    <li>Prepare any missing documentation</li>
                    <li>Contact our support team for guidance</li>
                    <li>Submit a new application after addressing the issues</li>
                </ol>
            </div>
            
            <div style="text-align: center;">
                <a href="mailto:support@lehsff.org" class="button">Contact Support</a>
            </div>
            
            <div class="message">
                <p>We encourage you to reapply in the future once you've addressed the issues mentioned above. Our team is available to help guide you through the process.</p>
                <p>Thank you for your interest in LEHSFF, and we wish you the best in your entrepreneurial journey.</p>
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