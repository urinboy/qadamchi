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

        /* Ilova demo — terminal mockup (har doim qorong'i — terminal estetikasi) */
        .terminal { max-width: 560px; margin: 6px auto 0; background: #1b2030; border: 1px solid #2a3045; border-radius: 14px; box-shadow: var(--shadow-md); overflow: hidden; text-align: left; }
        .term-bar { display: flex; align-items: center; gap: 10px; padding: 10px 14px; background: #15192a; border-bottom: 1px solid #2a3045; }
        .term-dots { display: inline-flex; gap: 7px; }
        .term-dots span { width: 12px; height: 12px; border-radius: 50%; display: block; }
        .term-dots .r { background: #ff5f57; } .term-dots .y { background: #febc2e; } .term-dots .g { background: #28c840; }
        .term-title { color: #6c7086; font-size: 13px; font-family: var(--mono); }
        .term-body { padding: 16px 20px; font-family: var(--mono); font-size: 13.5px; line-height: 1.85; color: #c7cbe0; white-space: pre; overflow-x: auto; }
        .term-body .p { color: #5b7cfa; }   /* prompt $ */
        .term-body .c { color: #e7e9f3; }   /* buyruq */
        .term-body .o { color: #9aa1b8; }   /* output */
        .term-body .v { color: #febc2e; }   /* versiya */
        .term-body .a { color: #28c840; }   /* o'q / ok */
        .term-body .r { color: #8aa4ff; }   /* route path */
        .term-body .m { color: #6c7086; }   /* method/hint */
        .term-cursor { display: inline-block; width: 8px; height: 15px; background: #5b7cfa; vertical-align: -2px; margin-left: 3px; animation: qd-blink 1.1s steps(1) infinite; }
        @keyframes qd-blink { 50% { opacity: 0; } }
        @media (prefers-reduced-motion: reduce) { .term-cursor { animation: none; } }
        .demo-cta { display: flex; flex-wrap: wrap; justify-content: center; align-items: center; gap: 12px; margin-top: 20px; }
        .demo-cta .btn { padding: 12px 22px; }
    </style>
</head>
<body>
    @php
        $flashes = [
            'success' => session()->getFlash('success'),
            'error'   => session()->getFlash('error'),
            'info'    => session()->getFlash('info'),
        ];
        $toastIcons = [
            'success' => 'M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z',
            'error'   => 'M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z',
            'info'    => 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z',
        ];
    @endphp
    @if (array_filter($flashes))
        <div class="toast-container" id="toastContainer">
            @foreach ($flashes as $type => $msg)
                @if ($msg)
                    <div class="toast toast-{{ $type }}" data-auto="4000" role="{{ $type === 'error' ? 'alert' : 'status' }}">
                        <svg viewBox="0 0 24 24"><path d="{{ $toastIcons[$type] }}"></path></svg>
                        <span class="toast-body">{{ $msg }}</span>
                        <button type="button" class="toast-close" aria-label="Yopish">&times;</button>
                        <span class="toast-progress"></span>
                    </div>
                @endif
            @endforeach
        </div>
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
        <div class="terminal" role="img" aria-label="Qadamchi CLI serve va route'lar ro'yxati">
            <div class="term-bar">
                <span class="term-dots"><span class="r"></span><span class="y"></span><span class="g"></span></span>
                <span class="term-title">~/qadamchi</span>
            </div>
            <div class="term-body"><span class="p">$</span> <span class="c">php qadamchi serve</span>
<span class="o">Qadamchi</span> <span class="v">{{ \Qadamchi\Support\Version::VERSION }}</span> <span class="a">→</span> <span class="o">http://localhost:8080</span>

<span class="m">  GET</span>  <span class="r">/</span>            <span class="a">→</span> <span class="o">home</span>
<span class="m">  GET</span>  <span class="r">/dashboard</span>   <span class="a">→</span> <span class="o">auth</span>
<span class="m">  GET</span>  <span class="r">/docs</span>        <span class="a">→</span> <span class="o">docs</span>
<span class="m">  GET</span>  <span class="r">/login</span>       <span class="a">→</span> <span class="o">guest</span>
<span class="m">  GET</span>  <span class="r">/register</span>    <span class="a">→</span> <span class="o">guest</span><span class="term-cursor"></span></div>
        </div>

        <div class="demo-cta">
            @auth
                <a class="btn" href="{{ route('dashboard') }}">
                    <svg viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
                    Dashboard
                </a>
                <div class="user-chip">
                    <span class="user-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                    <span class="greeting">Salom, {{ auth()->user()->name }}</span>
                    <button type="button" class="user-logout" data-logout aria-haspopup="dialog" aria-label="Chiqish" title="Chiqish">
                        <svg viewBox="0 0 24 24"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
                    </button>
                </div>
            @endauth
            @guest
                <a class="btn" href="{{ route('register') }}">
                    <svg viewBox="0 0 24 24"><path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0-6c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zm0 8c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4zm-6 4c.22-.72 3.31-2 6-2 2.7 0 5.8 1.29 6 2H9z"/></svg>
                    Ro'yxatdan o'tish
                </a>
                <a class="btn ghost" href="{{ route('login') }}">
                    <svg viewBox="0 0 24 24"><path d="M11 7L9.6 8.4l2.6 2.6H2v2h10.2l-2.6 2.6L11 17l5-5zm9 12h-8v2h8c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-8v2h8v14z"/></svg>
                    Kirish
                </a>
            @endguest
        </div>
    </main>

    <div class="hero-big">Qadamchi</div>

    <footer class="footer">
        &copy; {{ date('Y') }} <a href="https://urinboydev.uz" target="_blank" rel="noopener">UrinboyDev</a> tomonidan yaratilgan<br>Qadamchi PHP Mikrofreymvork
    </footer>

    <script src="/assets/toast.js"></script>

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