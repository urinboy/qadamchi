<h1><?php echo $title; ?></h1>
<div style="text-align: center; margin-bottom: 30px;">
    <p style="font-size: 1.2rem; color: #6c7086;"><?php echo $content; ?></p>
    <?php if ($slug): ?>
        <p style="background: #f0f3ff; padding: 10px; border-radius: 8px; display: inline-block;">Slug: <strong><?php echo $slug; ?></strong></p>
    <?php endif; ?>
</div>
<div style="text-align: center;">
    <a href="<?php echo \Route::url('home'); ?>" class="btn">Bosh sahifaga qaytish</a>
    <a href="<?php echo \Route::url('team'); ?>" class="btn" style="margin-left: 10px;">Jamoani ko'rish</a>
</div>