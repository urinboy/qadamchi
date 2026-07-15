<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Hujjatlar' }} — Qadamchi</title>
    <link rel="icon" href="/assets/favicon.svg" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css?family=Inter:400,600,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/app.css">
    <link rel="stylesheet" href="/assets/docs.css">
    <script>
    (function(){try{var t=localStorage.getItem('qadamchi-theme');if(t!=='light'&&t!=='dark'){t=(window.matchMedia&&window.matchMedia('(prefers-color-scheme: dark)').matches)?'dark':'light';}document.documentElement.setAttribute('data-theme',t);}catch(e){}})();
    </script>
</head>
<body>
    <div class="progress"><div class="progress-bar"></div></div>

    <nav class="topbar">
        <a class="brand" href="{{ route('home') }}">
            <img src="/assets/favicon.svg" class="brand-mark" alt="Qadamchi">
            <span>Qadamchi</span>
        </a>
        <input type="checkbox" id="nav-toggle" class="nav-toggle" aria-hidden="true">
        <label for="nav-toggle" class="nav-burger" aria-label="Menyu">
            <span></span><span></span><span></span>
        </label>
        <div class="nav-links">
            <a href="{{ route('home') }}"><svg viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>Bosh sahifa</a>
            <a href="{{ route('docs.index') }}"><svg viewBox="0 0 24 24"><path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/></svg>Hujjatlar</a>
            <button class="theme-toggle" type="button" aria-label="Mavzu">
                <svg class="sun" viewBox="0 0 24 24"><path d="M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zM2 13h3v-2H2v2zm17 0h3v-2h-3v2zm-8-9V1h-2v3h2zm0 19v-3h-2v3h2zM5.99 4.58L4.58 5.99l1.41 1.41 1.41-1.41-1.41-1.41zM18.36 18.36l-1.41 1.41 1.41 1.41 1.41-1.41-1.41-1.41zM19.41 5.99l-1.41-1.41-1.41 1.41 1.41 1.41 1.41-1.41zM5.64 18.36l1.41-1.41-1.41-1.41-1.41 1.41 1.41 1.41z"/></svg>
                <svg class="moon" viewBox="0 0 24 24"><path d="M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9 9-4.03 9-9c0-.46-.04-.92-.1-1.36-.98 1.37-2.58 2.26-4.4 2.26-2.98 0-5.4-2.42-5.4-5.4 0-1.81.89-3.42 2.26-4.4-.44-.06-.9-.1-1.36-.1z"/></svg>
            </button>
            @auth
                <a href="{{ route('dashboard') }}"><svg viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>Dashboard</a>
                <form action="{{ route('logout') }}" method="POST">@csrf</form>
                <button type="submit" formaction="{{ route('logout') }}" formmethod="POST" class="btn ghost"><svg viewBox="0 0 24 24"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>Chiqish</button>
            @endauth
            @guest
                <a href="{{ route('login') }}" class="btn ghost"><svg viewBox="0 0 24 24"><path d="M11 7L9.6 8.4l2.6 2.6H2v2h10.2l-2.6 2.6L11 17l5-5zm9 12h-8v2h8c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-8v2h8v14z"/></svg>Kirish</a>
                <a href="{{ route('register') }}" class="btn white"><svg viewBox="0 0 24 24"><path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0-6c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zm0 8c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4zm-6 4c.22-.72 3.31-2 6-2 2.7 0 5.8 1.29 6 2H9z"/></svg>Ro'yxatdan o'tish</a>
            @endguest
        </div>
    </nav>

    <main class="docs-main">
        @yield('content')
    </main>

    <div class="hero-big">Qadamchi</div>

    <footer class="footer">
        &copy; {{ date('Y') }} <a href="https://urinboydev.uz" target="_blank" rel="noopener">UrinboyDev</a> · Qadamchi PHP Mikrofreymvork · v{{ \Qadamchi\Support\Version::VERSION }}
    </footer>

    <script src="/assets/docs.js" defer></script>
</body>
</html>