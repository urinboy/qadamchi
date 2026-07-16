<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Qadamchi' }}</title>
    <link rel="icon" href="/assets/favicon.svg" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css?family=Inter:400,600,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/app.css">
</head>
<body>
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
            @auth
                <a href="{{ route('dashboard') }}"><svg viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>Dashboard</a>
                <span class="greeting">Salom, {{ auth()->user()->name }}</span>
                <button type="button" class="btn ghost" data-logout aria-haspopup="dialog"><svg viewBox="0 0 24 24"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>Chiqish</button>
            @endauth
            @guest
                <a href="{{ route('login') }}" class="btn ghost"><svg viewBox="0 0 24 24"><path d="M11 7L9.6 8.4l2.6 2.6H2v2h10.2l-2.6 2.6L11 17l5-5zm9 12h-8v2h8c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-8v2h8v14z"/></svg>Kirish</a>
                <a href="{{ route('register') }}" class="btn white"><svg viewBox="0 0 24 24"><path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0-6c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zm0 8c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4zm-6 4c.22-.72 3.31-2 6-2 2.7 0 5.8 1.29 6 2H9z"/></svg>Ro'yxatdan o'tish</a>
            @endguest
        </div>
    </nav>

    <main class="container @yield('container_class')">
        @if (session()->getFlash('success'))
            <div class="alert success">{{ session()->getFlash('success') }}</div>
        @endif
        @if (session()->getFlash('error'))
            <div class="alert danger">{{ session()->getFlash('error') }}</div>
        @endif

        @yield('content')
    </main>

    <div class="hero-big">Qadamchi</div>

    <footer class="footer">
        &copy; {{ date('Y') }} <a href="https://urinboydev.uz" target="_blank" rel="noopener">UrinboyDev</a> · Qadamchi PHP Mikrofreymvork
    </footer>

    <script>
        // Parol maydonini ko'rsatish/yashirish (eye toggle) — delegated, JS'siz framework uchun yagona handler.
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.pwd-toggle');
            if (!btn) return;
            var wrap = btn.closest('.input-wrap');
            var input = wrap && wrap.querySelector('input');
            if (!input) return;
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.classList.toggle('is-revealed', show);
            btn.setAttribute('aria-label', show ? 'Parolni yashirish' : 'Parolni ko‘rsatish');
        });
    </script>

    @auth
    <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="logout-form">@csrf</form>
    <div class="modal" id="logoutModal" hidden role="dialog" aria-modal="true" aria-labelledby="logoutTitle" aria-hidden="true">
        <div class="modal-backdrop" data-modal-close></div>
        <div class="modal-dialog" role="document">
            <div class="modal-icon">
                <svg viewBox="0 0 24 24"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
            </div>
            <h3 id="logoutTitle">Chiqishni tasdiqlang</h3>
            <p class="modal-text">Siz rostdan ham tizimdan chiqmoqchimisiz? Shu andan boshlab sessiyangiz tugatiladi va boshqa sahifalarga kirish uchun qayta kirishingiz kerak bo'ladi.</p>
            <div class="modal-actions">
                <button type="button" class="btn ghost" data-modal-close>Bekor qilish</button>
                <button type="submit" form="logoutForm" class="btn danger">
                    <svg viewBox="0 0 24 24"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
                    Chiqish
                </button>
            </div>
        </div>
    </div>
    <script>
        (function () {
            var modal = document.getElementById('logoutModal');
            if (!modal) return;
            var lastFocus = null;
            function open() {
                lastFocus = document.activeElement;
                modal.hidden = false;
                modal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
                var f = modal.querySelector('button');
                if (f) f.focus();
            }
            function close() {
                modal.hidden = true;
                modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
                if (lastFocus && lastFocus.focus) lastFocus.focus();
            }
            document.addEventListener('click', function (e) {
                if (e.target.closest('[data-logout]')) { e.preventDefault(); open(); return; }
                if (e.target.closest('[data-modal-close]')) { e.preventDefault(); close(); }
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && !modal.hidden) close();
            });
        })();
    </script>
    @endauth
</body>
</html>