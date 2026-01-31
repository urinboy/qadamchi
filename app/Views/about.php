<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?> - Qadamchi</title>
</head>
<body>
    <h1><?php echo $title; ?></h1>
    <p><?php echo $content; ?></p>
    <?php if ($slug): ?>
        <p>Slug: <?php echo $slug; ?></p>
    <?php endif; ?>
    <a href="<?php echo Route::url('home'); ?>">Bosh sahifaga qaytish</a>
</body>
</html>