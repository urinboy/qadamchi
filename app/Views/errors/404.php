<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sahifa topilmadi - Qadamchi PHP Mikrofreymvork">
    <title>404 – Sahifa topilmadi | Qadamchi</title>
    <link rel="icon" href="/assets/favicon.svg" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css?family=Inter:400,600,800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #3b5bdb;
            --primary-light: #f0f3ff;
            --text: #22223b;
            --muted: #6c7086;
            --border: #e0e7ff;
            --error: #f03e3e;
            --error-light: #fff5f5;
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
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }
        .logo:hover {
            opacity: 1;
        }
        .error-code {
            font-size: 2.7rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--error);
            margin: 0 0 10px 0;
            animation: pulse 2s ease-in-out infinite alternate;
        }
        @keyframes pulse {
            from { opacity: 0.8; }
            to { opacity: 1; }
        }
        .subtitle {
            font-size: 1.2rem;
            color: var(--muted);
            margin-bottom: 2.3rem;
            font-weight: 500;
            line-height: 1.5;
        }
        .error-message {
            font-size: 1.1rem;
            color: var(--text);
            margin-bottom: 2rem;
            max-width: 500px;
            line-height: 1.6;
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
            transition: background .16s, color .16s, border .18s, transform .2s;
        }
        .links a:hover, .links a:focus {
            background: #e7f0ff;
            color: #2549b7;
            border: 1.5px solid #b6c3f7;
            transform: translateY(-2px);
        }
        .links a.secondary {
            background: var(--error-light);
            color: var(--error);
            box-shadow: 0 2px 12px rgba(240, 62, 62, 0.1);
        }
        .links a.secondary:hover {
            background: #ffe0e0;
            color: #d63031;
            border: 1.5px solid #fab1a0;
        }
        .links svg {
            width: 22px;
            height: 22px;
            fill: currentColor;
            flex-shrink: 0;
        }
        .hero {
            text-align: center;
            margin: 32px 0 0 0;
        }
        .hero-big {
            font-size: 7vw;
            font-weight: 800;
            color: rgba(240, 62, 62, 0.08);
            letter-spacing: -0.04em;
            margin: 70px 0 10px 0;
            line-height: 1;
            user-select: none;
            pointer-events: none;
        }
        .suggestions {
            background: rgba(255, 255, 255, 0.7);
            border-radius: 16px;
            padding: 24px;
            margin: 24px 0;
            box-shadow: 0 4px 16px rgba(59, 91, 219, 0.1);
            border: 1px solid var(--border);
            backdrop-filter: blur(8px);
        }
        .suggestions h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text);
            margin: 0 0 16px 0;
        }
        .suggestion-links {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
        }
        .suggestion-link {
            padding: 8px 16px;
            background: rgba(59, 91, 219, 0.05);
            border-radius: 20px;
            text-decoration: none;
            color: var(--primary);
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }
        .suggestion-link:hover {
            background: var(--primary-light);
            border-color: var(--primary);
            transform: translateY(-1px);
        }
        footer {
            text-align: center;
            color: #adb5bd;
            font-size: 1.05rem;
            margin: 60px 0 24px 0;
        }
        .error-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.6;
        }
        .container {
            max-width: 600px;
            padding: 0 2rem;
            text-align: center;
        }
        @media (max-width: 700px) {
            .error-code { font-size: 2.1rem; }
            .hero-big { font-size: 18vw; margin: 40px 0 6px 0;}
            .links { gap: 10px; }
            .links a { padding: 11px 16px; font-size: .99rem;}
            .logo { width: 66px; height: 66px;}
            .container { padding: 0 1rem; }
            .suggestion-links { flex-direction: column; align-items: center; }
            .suggestion-link { width: 100%; max-width: 200px; text-align: center; }
        }
        @media (max-width: 480px) {
            footer { font-size: .93rem; }
            .suggestions { padding: 16px; margin: 16px 0; }
        }
        /* Hover animatsiya */
        .logo {
            transition: transform 0.3s ease;
        }
        .logo:hover {
            transform: scale(1.05);
        }
        /* Error code click effect */
        .error-code:active {
            animation: shake 0.5s ease-in-out;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-8px); }
            75% { transform: translateX(8px); }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <a href="/">
                <img src="/assets/favicon.svg" class="logo" alt="Qadamchi Logo" />
            </a>
            <div class="error-code" onclick="this.style.animation='shake 0.5s ease-in-out'">Xatolik yuz berdi.</div>
            <!-- <div class="subtitle">
                Sahifa topilmadi<br><strong>Qidirilgan sahifa mavjud emas</strong>.<br>
                <span style="color:#f03e3e;">Xatolik yuz berdi.</span>
            </div> -->
            <div class="error-message">
                Kechirasiz, siz qidirayotgan sahifa topilmadi. Sahifa o'chirilgan, nomi o'zgartirilgan yoki vaqtincha mavjud emas.
            </div>
            <div class="links">
                <a href="javascript:history.back()" class="secondary" title="Orqaga">
                    <svg viewBox="0 0 24 24"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
                    Orqaga
                </a>
                <a href="/" title="Bosh sahifa">
                    <svg viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                    Bosh sahifa
                </a>
            </div>
        </header>
                
        <div class="hero">
            <div class="hero-big">404</div>
        </div>
        
        <footer>
            &copy; <?= date('Y') ?> <a href="https://urinboydev.uz" target="_blank" style="color:#3b5bdb;text-decoration:none;">UrinboyDev</a> tomonidan yaratilgan <br>Qadamchi PHP Mikrofreymvork
        </footer>
    </div>

    <script>
        // Logo bosilganda bosh sahifaga o'tish
        document.querySelector('.logo').addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = '/';
        });
        
        // 404 raqamiga bosganda shake effekti
        document.querySelector('.error-code').addEventListener('click', function() {
            this.style.animation = 'none';
            setTimeout(() => {
                this.style.animation = 'pulse 2s ease-in-out infinite alternate, shake 0.5s ease-in-out';
            }, 10);
            setTimeout(() => {
                this.style.animation = 'pulse 2s ease-in-out infinite alternate';
            }, 500);
        });
        
        // Klaviatura shortcut'lari
        document.addEventListener('keydown', function(e) {
            // ESC - orqaga
            if (e.key === 'Escape') {
                history.back();
            }
            // Enter - bosh sahifa
            if (e.key === 'Enter') {
                window.location.href = '/';
            }
        });
    </script>
</body>
</html>