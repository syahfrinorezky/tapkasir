<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        }

        .header {
            background: linear-gradient(135deg, #6a6db8 0%, #8b90a5 100%);
            padding: 50px 40px;
            text-align: center;
            position: relative;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #6a6db8, #8b90a5, #6a6db8);
        }

        .icon-circle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            margin-bottom: 20px;
            backdrop-filter: blur(10px);
        }

        .icon-circle i {
            font-size: 36px;
            color: #ffffff;
        }

        .header h1 {
            color: #ffffff;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }

        .header p {
            color: rgba(255, 255, 255, 0.95);
            font-size: 15px;
            font-weight: 500;
        }

        .content {
            padding: 45px 40px;
        }

        .greeting {
            font-size: 20px;
            color: #2d3748;
            margin-bottom: 25px;
            font-weight: 700;
        }

        .message {
            color: #4a5568;
            font-size: 15px;
            margin-bottom: 30px;
            line-height: 1.8;
        }

        .button-container {
            text-align: center;
            margin: 45px 0;
        }

        .button {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #6a6db8 0%, #8b90a5 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 18px 45px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 16px;
            box-shadow: 0 8px 25px rgba(106, 109, 184, 0.35);
            transition: all 0.3s ease;
        }

        .button:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(106, 109, 184, 0.45);
        }

        .button i {
            font-size: 18px;
        }

        .info-box {
            background: linear-gradient(135deg, #EAF3F8 0%, #e0eef5 100%);
            border-left: 5px solid #6a6db8;
            padding: 20px 25px;
            margin: 30px 0;
            border-radius: 8px;
        }

        .info-box .title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .info-box .title i {
            color: #6a6db8;
        }

        .info-box p {
            color: #4a5568;
            font-size: 13px;
            margin: 5px 0;
        }

        .link-text {
            color: #6a6db8;
            word-break: break-all;
            font-size: 13px;
            font-weight: 600;
            background: #f7fafc;
            padding: 10px;
            border-radius: 6px;
            margin-top: 8px;
            display: block;
        }

        .warning {
            background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
            border-left: 5px solid #fc8181;
            padding: 20px 25px;
            margin: 30px 0;
            border-radius: 8px;
        }

        .warning .title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            color: #742a2a;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .warning .title i {
            color: #fc8181;
        }

        .warning p {
            color: #742a2a;
            font-size: 13px;
            margin: 0;
        }

        .footer {
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            padding: 35px 40px;
            text-align: center;
            border-top: 2px solid #e2e8f0;
        }

        .footer .brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 18px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .footer .brand i {
            color: #6a6db8;
        }

        .footer p {
            color: #718096;
            font-size: 13px;
            margin: 5px 0;
        }

        .divider {
            height: 2px;
            background: linear-gradient(to right, transparent, #cbd5e0, transparent);
            margin: 35px 0;
        }

        .security-note {
            background: #f7fafc;
            border-radius: 8px;
            padding: 15px 20px;
            margin-top: 25px;
            display: flex;
            align-items: start;
            gap: 12px;
        }

        .security-note i {
            color: #6a6db8;
            font-size: 20px;
            margin-top: 2px;
        }

        .security-note p {
            color: #4a5568;
            font-size: 13px;
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="icon-circle">
                <i class="fas fa-shield-halved"></i>
            </div>
            <h1>Reset Password</h1>
            <p>Sistem Keamanan Akun Tapkasir</p>
        </div>

        <div class="content">
            <p class="greeting">Halo, <?= esc($nama) ?>!</p>

            <p class="message">
                Kami menerima permintaan untuk mereset password akun Anda. Untuk melanjutkan proses reset password,
                silakan klik tombol di bawah ini.
            </p>

            <div class="button-container">
                <a href="<?= $link ?>" class="button">
                    <i class="fas fa-key"></i>
                    <span>Reset Password Saya</span>
                </a>
            </div>

            <div class="info-box">
                <div class="title">
                    <i class="fas fa-info-circle"></i>
                    <span>Tidak bisa klik tombol?</span>
                </div>
                <p>Salin dan tempel link berikut ke browser Anda:</p>
                <span class="link-text"><?= $link ?></span>
            </div>

            <div class="warning">
                <div class="title">
                    <i class="fas fa-clock"></i>
                    <span>Penting - Link Berlaku Terbatas</span>
                </div>
                <p>Link reset password ini hanya berlaku selama <strong>1 jam</strong> sejak email ini dikirim untuk
                    menjaga keamanan akun Anda.</p>
            </div>

            <div class="divider"></div>

            <div class="security-note">
                <i class="fas fa-lock"></i>
                <p>
                    <strong>Catatan Keamanan:</strong> Jika Anda tidak meminta reset password,
                    abaikan email ini. Password Anda akan tetap aman dan tidak akan berubah.
                </p>
            </div>
        </div>

        <div class="footer">
            <div class="brand">
                <i class="fas fa-cash-register"></i>
                <span>Tapkasir</span>
            </div>
            <p>&copy; <?= date('Y') ?> Tapkasir. All rights reserved.</p>
            <p style="margin-top: 15px; font-size: 12px; color: #a0aec0;">
                <i class="fas fa-envelope"></i> Email otomatis - Mohon tidak membalas
            </p>
        </div>
    </div>
</body>

</html>