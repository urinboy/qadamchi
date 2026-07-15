<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Qadamchi - O'zbekistonlik dasturchilar uchun zamonaviy PHP mikro freymvorki. Ochiq manba. Tez. Oddiy.">
    <title>Qadamchi – Xush kelibsiz!</title>
    <link rel="icon" href="/assets/favicon.svg" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css?family=Inter:400,600,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/app.css">
    <style>
        body { display: flex; flex-direction: column; align-items: center; }
        /* Qisqa ekranda tepa kesilmasligi uchun markazlashmani o'chiramiz */
        @media (max-width: 768px) {
            body { justify-content: flex-start; padding-top: 32px; }
        }
        .landing { width: 100%; max-width: 720px; text-align: center; padding: 48px 20px 0; }
        .logo { width: 84px; height: 84px; margin: 0 auto 18px; background: #fff; padding: 4px; box-shadow: var(--shadow-sm); border-radius: 20px; }
        .landing h1 { font-size: clamp(2.2rem, 6vw, 2.9rem); font-weight: 800; letter-spacing: -.02em; color: var(--primary); margin: 0 0 10px; }
        .subtitle { font-size: clamp(1.05rem, 2.5vw, 1.25rem); color: var(--muted); margin-bottom: 1.4rem; font-weight: 500; line-height: 1.5; }
        .subtitle strong { color: var(--text); }
        .subtitle .tag { color: #228be6; }
        .links { display: flex; flex-wrap: wrap; justify-content: center; gap: 14px; margin: 26px 0 14px; }
        .links .btn { padding: 13px 24px; }
        .app-label { font-size: .82rem; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: #adb5bd; margin: 22px 0 10px; }
        .greeting { color: var(--muted); font-weight: 500; font-size: .98rem; margin: 0 6px; }
        .flash { max-width: 560px; margin: 24px auto 0; }
    </style>
</head>
<body>
    @if (session()->getFlash('success'))
        <div class="flash alert success">{{ session()->getFlash('success') }}</div>
    @endif

    <main class="landing">
        <img src="/assets/favicon.svg" class="logo" alt="Qadamchi Logo" />
        <h1>Qadamchi.uz</h1>
        <div class="subtitle">
            O'zbekistonlik dasturchilar uchun <strong>Zamonaviy PHP mikrofreymvork</strong>.<br>
            <span class="tag">Ochiq manba. Tez. Oddiy.</span>
        </div>

        <div class="links">
            <a class="btn ghost" href="{{ route('docs.index') }}" title="Hujjatlar">
                <svg viewBox="0 0 24 24"><path d="M12 3L2 21h20L12 3zm0 3.3L18.6 19H5.4L12 6.3zm0 5.7c-.83 0-1.5.67-1.5 1.5S11.17 15 12 15s1.5-.67 1.5-1.5S12.83 12 12 12z"></path></svg>
                Hujjatlar
            </a>
            <a class="btn ghost" href="https://github.com/urinboy/qadamchi" target="_blank" rel="noopener">
                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.58 2 12.26c0 4.49 2.87 8.31 6.84 9.66.5.09.68-.22.68-.49 0-.24-.01-.87-.01-1.71-2.78.62-3.37-1.36-3.37-1.36-.45-1.18-1.1-1.5-1.1-1.5-.9-.63.07-.62.07-.62 1 .08 1.53 1.05 1.53 1.05.89 1.56 2.34 1.11 2.91.85.09-.66.35-1.11.63-1.37-2.22-.26-4.56-1.14-4.56-5.08 0-1.12.39-2.04 1.03-2.76-.1-.26-.45-1.3.1-2.7 0 0 .84-.28 2.76 1.04A9.48 9.48 0 0 1 12 7.07c.85.004 1.7.11 2.5.31 1.92-1.32 2.76-1.04 2.76-1.04.55 1.4.2 2.44.1 2.7.64.72 1.03 1.64 1.03 2.76 0 3.95-2.34 4.82-4.57 5.08.36.32.68.96.68 1.94 0 1.4-.01 2.53-.01 2.88 0 .27.18.59.69.49C19.13 20.57 22 16.75 22 12.26 22 6.58 17.52 2 12 2z"></path></svg>
                GitHub
            </a>
            <a class="btn ghost" href="https://urinboydev.uz" target="_blank" rel="noopener">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9 9h6v6H9z" fill="#fff"/></svg>
                Portfolio
            </a>
        </div>

        <div class="app-label">Ilova demo</div>
        <div class="links">
            <a class="btn" href="{{ route('dashboard') }}">
                <svg viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
                Dashboard
            </a>
            @auth
                <span class="greeting">Salom, {{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST">@csrf</form>
                <button type="submit" formaction="{{ route('logout') }}" formmethod="POST" class="btn ghost">
                    <svg viewBox="0 0 24 24"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
                    Chiqish
                </button>
            @endauth
            @guest
                <a class="btn ghost" href="{{ route('login') }}">
                    <svg viewBox="0 0 24 24"><path d="M11 7L9.6 8.4l2.6 2.6H2v2h10.2l-2.6 2.6L11 17l5-5zm9 12h-8v2h8c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-8v2h8v14z"/></svg>
                    Kirish
                </a>
                <a class="btn" href="{{ route('register') }}">
                    <svg viewBox="0 0 24 24"><path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0-6c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zm0 8c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4zm-6 4c.22-.72 3.31-2 6-2 2.7 0 5.8 1.29 6 2H9z"/></svg>
                    Ro'yxatdan o'tish
                </a>
            @endguest
        </div>
    </main>

    <div class="hero-big">Qadamchi</div>

    <footer class="footer">
        &copy; {{ date('Y') }} <a href="https://urinboydev.uz" target="_blank" rel="noopener">UrinboyDev</a> tomonidan yaratilgan<br>Qadamchi PHP Mikrofreymvork
    </footer>
</body>
</html>