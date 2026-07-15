<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Qadamchi</title>
    <link rel="icon" href="/assets/favicon.svg" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css?family=Inter:400,600,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/app.css">
    <style>
        body { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 24px; }
        .error-card { background: #fff; border-radius: var(--r-lg); padding: 48px 40px; text-align: center;
            box-shadow: var(--shadow-lg); border: 1px solid rgba(224,231,255,.6); max-width: 420px; width: 100%; }
        .code { font-size: clamp(56px, 14vw, 80px); font-weight: 800; letter-spacing: -.04em; color: var(--primary); line-height: 1; margin-bottom: 12px; }
        .msg { color: var(--muted); font-size: 16px; margin-bottom: 24px; }
        @media (max-width: 480px) { .error-card { padding: 32px 22px; } }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="code">404</div>
        <p class="msg">Sahifa topilmadi.</p>
        <a class="btn block" href="/"><svg viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>Bosh sahifaga qaytish</a>
    </div>
</body>
</html>