<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Qadamchi'; ?></title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <header>
        <nav>
            <a href="<?php echo \Route::url('home'); ?>">Bosh sahifa</a>
            <a href="<?php echo \Route::url('about'); ?>">Biz haqimizda</a>
            <a href="<?php echo \Route::url('team'); ?>">Jamoa</a>
            <a href="<?php echo \Route::url('contact'); ?>">Aloqa</a>
        </nav>
    </header>

    <main>
        <?php echo $content ?? ''; ?>
    </main>

    <footer>
        <p>&copy; 2026 Qadamchi Framework</p>
    </footer>
</body>
</html>
