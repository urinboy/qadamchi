<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>422 — Qadamchi</title>
    <link rel="icon" href="/assets/favicon.svg" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css?family=Inter:400,600,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/app.css">
    <style>
        body { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 24px; }
        .error-card { background: #fff; border-radius: var(--r-lg); padding: 48px 40px; text-align: center;
            box-shadow: var(--shadow-lg); border: 1px solid rgba(224,231,255,.6); max-width: 460px; width: 100%; }
        .code { font-size: clamp(56px, 14vw, 80px); font-weight: 800; letter-spacing: -.04em; color: var(--danger); line-height: 1; margin-bottom: 12px; }
        .msg { color: var(--muted); font-size: 15px; margin-bottom: 24px; line-height: 1.6; }
        @media (max-width: 480px) { .error-card { padding: 32px 22px; } }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="code">422</div>
        <p class="msg">Yuborilgan ma'lumotlar noto'g'ri.</p>
        <a class="btn block" href="javascript:history.back()"><svg viewBox="0 0 24 24"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>Orqaga qaytish</a>
    </div>
</body>
</html>