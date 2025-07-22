<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 – Sahifa topilmadi | Qadamchi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            overflow: hidden;
            position: relative;
        }
        
        /* Animated background particles */
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }
        
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        
        .particle:nth-child(1) { left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { left: 20%; animation-delay: 1s; }
        .particle:nth-child(3) { left: 30%; animation-delay: 2s; }
        .particle:nth-child(4) { left: 40%; animation-delay: 3s; }
        .particle:nth-child(5) { left: 50%; animation-delay: 4s; }
        .particle:nth-child(6) { left: 60%; animation-delay: 5s; }
        .particle:nth-child(7) { left: 70%; animation-delay: 0.5s; }
        .particle:nth-child(8) { left: 80%; animation-delay: 1.5s; }
        .particle:nth-child(9) { left: 90%; animation-delay: 2.5s; }
        
        @keyframes float {
            0%, 100% { transform: translateY(100vh) scale(0); }
            50% { transform: translateY(-100px) scale(1); }
        }
        
        .container {
            text-align: center;
            z-index: 10;
            position: relative;
            max-width: 600px;
            padding: 2rem;
        }
        
        .error-code {
            font-size: clamp(8rem, 15vw, 12rem);
            font-weight: 800;
            line-height: 1;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, #fff, #f0f9ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            animation: pulse 2s ease-in-out infinite alternate;
        }
        
        @keyframes pulse {
            from { opacity: 0.8; transform: scale(1); }
            to { opacity: 1; transform: scale(1.05); }
        }
        
        .error-title {
            font-size: clamp(1.5rem, 4vw, 2.5rem);
            font-weight: 700;
            margin-bottom: 1rem;
            color: #f0f9ff;
            animation: slideInUp 0.8s ease-out;
        }
        
        .error-message {
            font-size: clamp(1rem, 2.5vw, 1.25rem);
            line-height: 1.6;
            margin-bottom: 2.5rem;
            color: rgba(255, 255, 255, 0.9);
            animation: slideInUp 1s ease-out;
        }
        
        .actions {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            align-items: center;
            animation: slideInUp 1.2s ease-out;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn-primary {
            background: rgba(255, 255, 255, 0.95);
            color: #4c1d95;
            border: 2px solid transparent;
        }
        
        .btn-primary:hover {
            background: #fff;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        
        .btn-secondary {
            background: transparent;
            color: #fff;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.6);
            transform: translateY(-2px);
        }
        
        .icon {
            width: 20px;
            height: 20px;
            fill: currentColor;
        }
        
        .suggestions {
            margin-top: 3rem;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideInUp 1.4s ease-out;
        }
        
        .suggestions h3 {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            color: #f0f9ff;
        }
        
        .suggestion-links {
            display: flex;
            flex-wrap: wrap;
            gap: 0.8rem;
            justify-content: center;
        }
        
        .suggestion-link {
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 25px;
            text-decoration: none;
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .suggestion-link:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            transform: translateY(-1px);
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .actions {
                flex-direction: column;
                width: 100%;
            }
            
            .btn {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }
            
            .suggestion-links {
                flex-direction: column;
                align-items: center;
            }
            
            .suggestion-link {
                width: 100%;
                max-width: 200px;
                text-align: center;
            }
        }
        
        /* Easter egg - shake animation on 404 click */
        .error-code:active {
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px) rotate(-5deg); }
            75% { transform: translateX(10px) rotate(5deg); }
        }
    </style>
</head>
<body>
    <!-- Animated background particles -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="container">
        <div class="error-code" onclick="this.style.animation='shake 0.5s ease-in-out'">404</div>
        <h1 class="error-title">Oops! Sahifa topilmadi</h1>
        <p class="error-message">
            Kechirasiz, siz qidirayotgan sahifa mavjud emas yoki ko'chirilgan. 
            Balki URL manzilda xatolik bor yoki sahifa olib tashlangan.
        </p>
        
        <div class="actions">
            <a href="/" class="btn btn-primary">
                <svg class="icon" viewBox="0 0 24 24">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                </svg>
                Bosh sahifaga qaytish
            </a>
            
            <a href="javascript:history.back()" class="btn btn-secondary">
                <svg class="icon" viewBox="0 0 24 24">
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                </svg>
                Orqaga qaytish
            </a>
        </div>
        
        <div class="suggestions">
            <h3>Mashhur sahifalar:</h3>
            <div class="suggestion-links">
                <a href="/docs" class="suggestion-link">Hujjatlar</a>
                <a href="/contact" class="suggestion-link">Aloqa</a>
                <a href="/help" class="suggestion-link">Yordam</a>
            </div>
        </div>
    </div>

    <script>
        // Add some interactive magic
        document.addEventListener('DOMContentLoaded', function() {
            // Random particle positions
            const particles = document.querySelectorAll('.particle');
            particles.forEach(particle => {
                particle.style.top = Math.random() * 100 + '%';
                particle.style.animationDuration = (Math.random() * 3 + 4) + 's';
            });
            
            // Add click effect to 404
            const errorCode = document.querySelector('.error-code');
            errorCode.addEventListener('click', function() {
                this.style.animation = 'none';
                setTimeout(() => {
                    this.style.animation = 'pulse 2s ease-in-out infinite alternate, shake 0.5s ease-in-out';
                }, 10);
                setTimeout(() => {
                    this.style.animation = 'pulse 2s ease-in-out infinite alternate';
                }, 500);
            });
            
            // Mouse parallax effect
            document.addEventListener('mousemove', function(e) {
                const container = document.querySelector('.container');
                const x = (e.clientX - window.innerWidth / 2) / window.innerWidth;
                const y = (e.clientY - window.innerHeight / 2) / window.innerHeight;
                
                container.style.transform = `translate(${x * 10}px, ${y * 10}px)`;
            });
        });
    </script>
</body>
</html>
<!-- <!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <title>404 – Sahifa topilmadi</title>
    <style>
        body { font-family: Rubik, Arial, sans-serif; background: #f8fafc; color: #222; text-align: center; }
        .code { font-size: 6rem; color: #3b5bdb; margin-top: 60px; }
        .msg { font-size: 1.6rem; margin: 24px 0 10px 0; }
        a { color: #3b5bdb; text-decoration: none; }
    </style>
</head>
<body>
    <div class="code">404</div>
    <div class="msg">Kechirasiz, sahifa topilmadi!</div>
    <div><a href="/">Bosh sahifa</a></div>
</body>
</html> -->