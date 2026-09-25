<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Alert: New Account Sign-in</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #0b0c10;
            color: #e2e8f0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #12141c;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.08);
            margin-top: 40px;
            margin-bottom: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
        }
        .header {
            background: linear-gradient(135deg, #1f2430 0%, #0d0e14 100%);
            padding: 32px 24px;
            text-align: center;
            border-bottom: 2px solid #ef6a26;
        }
        .logo-text {
            font-size: 28px;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: -0.5px;
            text-transform: lowercase;
        }
        .logo-accent {
            color: #ef6a26;
        }
        .badge {
            display: inline-block;
            background-color: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
            font-size: 11px;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 12px;
            border: 1px solid rgba(96, 165, 250, 0.3);
        }
        .content {
            padding: 36px 32px;
        }
        h1 {
            font-size: 20px;
            font-weight: 800;
            color: #ffffff;
            margin-top: 0;
            margin-bottom: 16px;
        }
        p {
            font-size: 15px;
            line-height: 1.6;
            color: #94a3b8;
            margin-bottom: 20px;
        }
        .details-box {
            background-color: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 12px;
            padding: 20px;
            margin: 24px 0;
        }
        .detail-item {
            margin-bottom: 12px;
        }
        .detail-item:last-child {
            margin-bottom: 0;
        }
        .detail-label {
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 4px;
        }
        .detail-value {
            color: #f1f5f9;
            font-size: 14px;
            font-weight: 600;
            word-break: break-all;
        }
        .warning-notice {
            background-color: rgba(239, 68, 68, 0.1);
            border-left: 4px solid #ef4444;
            padding: 14px 18px;
            border-radius: 6px;
            font-size: 13px;
            color: #fca5a5;
            margin-top: 24px;
        }
        .footer {
            background-color: #0b0c10;
            padding: 24px;
            text-align: center;
            font-size: 12px;
            color: #475569;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo-text">movies<span class="logo-accent">.</span></div>
            <div class="badge">Security Notice</div>
        </div>
        
        <div class="content">
            <h1>New Sign-In Detected 🔒</h1>
            <p>Hi <strong>{{ $name }}</strong>,</p>
            <p>We noticed a successful sign-in to your Movies Cinema account. Here are the sign-in details:</p>
            
            <div class="details-box">
                <div class="detail-item">
                    <span class="detail-label">Account Email</span>
                    <span class="detail-value">{{ $email }}</span>
                </div>
                <div class="detail-item" style="margin-top: 12px;">
                    <span class="detail-label">Date & Time</span>
                    <span class="detail-value">{{ $loginTime }}</span>
                </div>
                <div class="detail-item" style="margin-top: 12px;">
                    <span class="detail-label">IP Address</span>
                    <span class="detail-value">{{ $ipAddress }}</span>
                </div>
                <div class="detail-item" style="margin-top: 12px;">
                    <span class="detail-label">Device / Browser</span>
                    <span class="detail-value">{{ $userAgent }}</span>
                </div>
            </div>

            <div class="warning-notice">
                <strong>Didn't log in recently?</strong> If this wasn't you, please secure your account immediately or contact support.
            </div>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} Movies Cinema. Automated security notification.
        </div>
    </div>
</body>
</html>
