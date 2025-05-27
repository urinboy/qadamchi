<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Qadamchi - O‘zbekistonlik dasturchilar uchun zamonaviy PHP mikro freymvorki. Ochiq manba. Tez. Oddiy.">
    <meta name="keywords" content="Qadamchi, PHP Framework, O‘zbekiston, Dasturchilar, Mikro freymvork, Ochiq manba">
    <meta name="author" content="UrinboyDev">
    <meta name="theme-color" content="#3b5bdb">
    <meta property="og:title" content="Qadamchi – Xush kelibsiz!">
    <meta property="og:description" content="O‘zbekistonlik dasturchilar uchun zamonaviy PHP mikro freymvorki. Ochiq manba. Tez. Oddiy.">
    <meta property="og:image" content="/logo.png">
    <meta property="og:url" content="https://qadamchi.urinboydev.uz">
    <meta property="og:type" content="website">
    <title>Qadamchi – Xush kelibsiz!</title>
    <link href="https://fonts.googleapis.com/css?family=Rubik:400,700&display=swap" rel="stylesheet">
    <link rel="icon" href="/favicon.png" type="image/png">
    <style>
        body {
            background: linear-gradient(120deg, #e0e7ff 0%, #f9fafb 100%);
            color: #22223b;
            font-family: 'Rubik', Arial, sans-serif;
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .container {
            max-width: 540px;
            margin: auto;
            padding: 48px 24px 24px 24px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(50,50,93,0.10), 0 1.5px 10px rgba(60,60,60,0.08);
        }
        .logo {
            /* width: 84px; */
            height: 84px;
            margin: 0 auto 16px auto;
            display: block;
        }
        h1 {
            text-align: center;
            font-size: 2.3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            letter-spacing: 1px;
            color: #3b5bdb;
        }
        .subtitle {
            text-align: center;
            font-size: 1.15rem;
            color: #495057;
            margin-bottom: 1.7rem;
        }
        .links {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-top: 30px;
            margin-bottom: 22px;
        }
        .links a {
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            background: #f1f3f5;
            border-radius: 8px;
            padding: 12px 18px;
            color: #22223b;
            font-weight: 500;
            font-size: 1.04rem;
            transition: background 0.18s;
        }
        .links a:hover {
            background: #e7f5ff;
            color: #228be6;
        }
        .links svg {
            width: 22px;
            height: 22px;
            fill: #3b5bdb;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #adb5bd;
            font-size: 0.98rem;
        }
        @media (max-width: 600px) {
            .container { max-width: 98vw; padding: 16px 5vw; }
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="/favicon.svg" class="logo" alt="Qadamchi Logo" />
        <h1>Qadamchiga xush kelibsiz!</h1>
        <div class="subtitle">O‘zbekistonlik dasturchilar uchun zamonaviy <br>PHP mikro freymvorki.<br>
        <span style="color:#228be6;">Ochiq manba. Tez. Oddiy.</span>
        </div>
        <div class="links">
            <a href="https://qadamchi.urinboydev.uz" target="_blank">
                <svg viewBox="0 0 24 24"><path d="M12 3L2 21h20L12 3zm0 3.3L18.6 19H5.4L12 6.3zm0 5.7c-.83 0-1.5.67-1.5 1.5S11.17 15 12 15s1.5-.67 1.5-1.5S12.83 12 12 12z"></path></svg>
                Qadamchi Documentation
            </a>
            <a href="https://github.com/urinboy/qadamchi" target="_blank">
                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.58 2 12.26c0 4.49 2.87 8.31 6.84 9.66.5.09.68-.22.68-.49 0-.24-.01-.87-.01-1.71-2.78.62-3.37-1.36-3.37-1.36-.45-1.18-1.1-1.5-1.1-1.5-.9-.63.07-.62.07-.62 1 .08 1.53 1.05 1.53 1.05.89 1.56 2.34 1.11 2.91.85.09-.66.35-1.11.63-1.37-2.22-.26-4.56-1.14-4.56-5.08 0-1.12.39-2.04 1.03-2.76-.1-.26-.45-1.3.1-2.7 0 0 .84-.28 2.76 1.04A9.48 9.48 0 0 1 12 7.07c.85.004 1.7.11 2.5.31 1.92-1.32 2.76-1.04 2.76-1.04.55 1.4.2 2.44.1 2.7.64.72 1.03 1.64 1.03 2.76 0 3.95-2.34 4.82-4.57 5.08.36.32.68.96.68 1.94 0 1.4-.01 2.53-.01 2.88 0 .27.18.59.69.49C19.13 20.57 22 16.75 22 12.26 22 6.58 17.52 2 12 2z"></path></svg>
                Qadamchi on GitHub
            </a>
            <a href="https://itorda.uz" target="_blank">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="#e67e22"/><text x="12" y="17" text-anchor="middle" fill="#fff" font-size="11" font-family="Arial" dy=".3em">IT</text></svg>
                ITORDA Community
            </a>
            <a href="https://urinboydev.uz" target="_blank">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="#3b5bdb"/><path d="M9 9h6v6H9z" fill="#fff"/></svg>
                Developer Portfolio (Urinboydev)
            </a>
        </div>
        <div class="footer">
            &copy; <?= date('Y') ?> <a href="https://urinboydev.uz" target="_blank" style="color:#3b5bdb;text-decoration:none;">UrinboyDev</a> tomonidan yaratilgan Qadamchi PHP Framework
        </div>
    </div>
</body>
</html>