<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Qadamchi Docs'; ?></title>
    <link rel="stylesheet" href="/assets/style.css">
    <link href="https://fonts.googleapis.com/css?family=Inter:400,600,800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #3b5bdb;
            --primary-light: #f0f3ff;
            --text: #22223b;
            --muted: #6c7086;
            --border: #e0e7ff;
        }
        body {
            background: linear-gradient(120deg, #e0e7ff 0%, #f9fafb 100%);
            font-family: 'Inter', Arial, sans-serif;
            color: var(--text);
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        header {
            background-color: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        nav {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        nav a {
            color: var(--text);
            text-decoration: none;
            padding: 10px 15px;
            margin: 0 10px;
            border-radius: 8px;
            transition: all 0.3s;
            font-weight: 500;
        }
        nav a:hover {
            background-color: var(--primary-light);
            color: var(--primary);
        }
        main {
            flex: 1;
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(50,50,93,0.10), 0 1.5px 10px rgba(60,60,60,0.08);
        }
        .content {
            line-height: 1.6;
        }
        .content h1, .content h2, .content h3 {
            color: var(--primary);
            margin-top: 30px;
        }
        .content code {
            background: #f0f3ff;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
        }
        .content pre {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
        }
        .content ul {
            padding-left: 20px;
        }
        footer {
            text-align: center;
            padding: 20px;
            color: var(--muted);
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="<?php echo \Route::url('home'); ?>">Bosh sahifa</a>
            <a href="<?php echo \Route::url('docs'); ?>">Docs</a>
            <a href="https://github.com/urinboy/qadamchi">GitHub</a>
        </nav>
    </header>

    <main>
        <div class="content">
            <?php echo $content ?? ''; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 <a href="https://urinboydev.uz" style="color: var(--primary);">Qadamchi Framework</a> Dokumentatsiyasi</p>
    </footer>
</body>
</html>