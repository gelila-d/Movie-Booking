<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Movies Cinema</title>
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
            background-color: rgba(239, 106, 38, 0.15);
            color: #ef6a26;
            font-size: 11px;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 12px;
            border: 1px solid rgba(239, 106, 38, 0.3);
        }
        .content {
            padding: 36px 32px;
        }
        h1 {
            font-size: 22px;
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
        .account-card {
            background-color: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 12px;
            padding: 20px;
            margin: 24px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed rgba(255, 255, 255, 0.08);
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .info-value {
            color: #f1f5f9;
            font-size: 14px;
            font-weight: 600;
        }
        .btn-container {
            text-align: center;
            margin: 32px 0 20px 0;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #ef6a26 0%, #d9530f 100%);
            color: #ffffff !important;
            text-decoration: none;
            font-weight: 800;
            font-size: 14px;
            padding: 14px 32px;
            border-radius: 10px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            box-shadow: 0 4px 15px rgba(239, 106, 38, 0.35);
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
            <div class="badge">Welcome Aboard</div>
        </div>
        
        <div class="content">
            <h1>Hello, {{ $name }}! 🎉</h1>
            <p>Thank you for creating an account with <strong>Movies Cinema</strong>. Your account is now fully active, giving you instant access to browse showtimes, reserve premium seats, and enjoy top blockbusters!</p>
            
            <div class="account-card">
                <div class="info-row">
                    <span class="info-label">Account Name:</span>
                    <span class="info-value">{{ $name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email Address:</span>
                    <span class="info-value">{{ $email }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value" style="color: #22c55e;">Active ✓</span>
                </div>
            </div>

            <p>Ready to catch the next big blockbuster? Explore our latest movies and select your favorite seats today!</p>

            <div class="btn-container">
                <a href="{{ config('app.url') }}/movies" class="btn">Explore Movies & Book Tickets</a>
            </div>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} Movies Cinema. All rights reserved.<br>
            If you did not register for this account, please ignore this email.
        </div>
    </div>
</body>
</html>
