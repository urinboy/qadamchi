<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Qadamchi - O‘zbekistonlik dasturchilar uchun zamonaviy PHP mikro freymvorki. Ochiq manba. Tez. Oddiy.">
    <title>Qadamchi – Xush kelibsiz!</title>
    <link rel="icon" href="/assets/favicon.svg" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css?family=Inter:400,600,800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #3b5bdb;
            --primary-light: #f0f3ff;
            --text: #22223b;
            --muted: #6c7086;
            --border: #e0e7ff;
        }
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            min-height: 100vh;
            background: linear-gradient(120deg, #e0e7ff 0%, #f9fafb 100%);
            font-family: 'Inter', Arial, sans-serif;
            color: var(--text);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        header {
            width: 100%;
            padding: 32px 0 0 0;
            text-align: center;
        }
        .logo {
            width: 84px;
            height: 84px;
            margin: 0 auto 18px auto;
            background-color: #fff;
            padding: 4px;
            display: block;
            box-shadow: 0 2px 12px #3b5bdb13;
            border-radius: 20px;
        }
        h1 {
            font-size: 2.7rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--primary);
            margin: 0 0 10px 0;
        }
        .subtitle {
            font-size: 1.2rem;
            color: var(--muted);
            margin-bottom: 2.3rem;
            font-weight: 500;
            line-height: 1.5;
        }
        .links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 18px;
            margin: 32px 0 22px 0;
        }
        .links a {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--primary-light);
            border-radius: 10px;
            padding: 13px 28px;
            color: var(--primary);
            font-weight: 600;
            font-size: 1.07rem;
            text-decoration: none;
            box-shadow: 0 2px 12px #3b5bdb11;
            border: 1.5px solid transparent;
            transition: background .16s, color .16s, border .18s;
        }
        .links a:hover, .links a:focus {
            background: #e7f0ff;
            color: #2549b7;
            border: 1.5px solid #b6c3f7;
        }
        .links svg {
            width: 22px;
            height: 22px;
            fill: var(--primary);
            flex-shrink: 0;
        }
        .hero {
            text-align: center;
            margin: 32px 0 0 0;
        }
        .hero-big {
            font-size: 7vw;
            font-weight: 800;
            color: rgba(59,91,219,0.08);
            letter-spacing: -0.04em;
            margin: 70px 0 10px 0;
            line-height: 1;
            user-select: none;
            pointer-events: none;
        }
        footer {
            text-align: center;
            color: #adb5bd;
            font-size: 1.05rem;
            margin: 60px 0 24px 0;
        }
        @media (max-width: 700px) {
            h1 { font-size: 2.1rem; }
            .hero-big { font-size: 18vw; margin: 40px 0 6px 0;}
            .links { gap: 10px; }
            .links a { padding: 11px 16px; font-size: .99rem;}
            .logo { width: 66px; height: 66px;}
        }
        @media (max-width: 480px) {
            .container { padding: 0 2vw; }
            footer { font-size: .93rem; }
        }
    </style>
</head>
<body>
    <header>
        <img src="/assets/favicon.svg" class="logo" alt="Qadamchi Logo" />
        <h1><?= env('APP_NAME', 'Qadamchi');?></h1>
        <div class="subtitle">
            O‘zbekistonlik dasturchilar uchun <br><strong>Zamonaviy PHP mikrofreymvork</strong>.<br>
            <span style="color:#228be6;">Ochiq manba. Tez. Oddiy.</span>
        </div>
        <div class="links">
            <a href="/docs" title="Documentation">
                <svg viewBox="0 0 24 24"><path d="M12 3L2 21h20L12 3zm0 3.3L18.6 19H5.4L12 6.3zm0 5.7c-.83 0-1.5.67-1.5 1.5S11.17 15 12 15s1.5-.67 1.5-1.5S12.83 12 12 12z"></path></svg>
                Hujjatlar
            </a>
            <a href="https://github.com/urinboy/qadamchi" target="_blank" rel="noopener">
                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.58 2 12.26c0 4.49 2.87 8.31 6.84 9.66.5.09.68-.22.68-.49 0-.24-.01-.87-.01-1.71-2.78.62-3.37-1.36-3.37-1.36-.45-1.18-1.1-1.5-1.1-1.5-.9-.63.07-.62.07-.62 1 .08 1.53 1.05 1.53 1.05.89 1.56 2.34 1.11 2.91.85.09-.66.35-1.11.63-1.37-2.22-.26-4.56-1.14-4.56-5.08 0-1.12.39-2.04 1.03-2.76-.1-.26-.45-1.3.1-2.7 0 0 .84-.28 2.76 1.04A9.48 9.48 0 0 1 12 7.07c.85.004 1.7.11 2.5.31 1.92-1.32 2.76-1.04 2.76-1.04.55 1.4.2 2.44.1 2.7.64.72 1.03 1.64 1.03 2.76 0 3.95-2.34 4.82-4.57 5.08.36.32.68.96.68 1.94 0 1.4-.01 2.53-.01 2.88 0 .27.18.59.69.49C19.13 20.57 22 16.75 22 12.26 22 6.58 17.52 2 12 2z"></path></svg>
                GitHub
            </a>
            <a href="https://urinboydev.uz" target="_blank" rel="noopener">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="#3b5bdb"/><path d="M9 9h6v6H9z" fill="#fff"/></svg>
                Portfolio
            </a>
        </div>
    </header>
    <div class="hero">
        <div class="hero-big">Qadamchi.uz</div>
    </div>
    <footer>
        &copy; <?= date('Y') ?> <a href="https://urinboydev.uz" target="_blank" style="color:#3b5bdb;text-decoration:none;">UrinboyDev</a> tomonidan yaratilgan <br>Qadamchi PHP Mikrofreymvork
    </footer>
</body>
</html>