<h1><?php echo $title; ?></h1>
<p><?php echo $content; ?></p>
<?php if ($slug): ?>
    <p>Slug: <?php echo $slug; ?></p>
<?php endif; ?>
<a href="<?php echo \Route::url('home'); ?>">Bosh sahifaga qaytish</a>