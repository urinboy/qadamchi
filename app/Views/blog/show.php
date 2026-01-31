<h1><?php echo $title; ?> #<?php echo $id; ?></h1>
<div style="background: #f9fafb; padding: 30px; border-radius: 12px; margin-bottom: 30px;">
    <p style="font-size: 1.1rem; line-height: 1.6;">Bu blog posti <?php echo $id; ?> haqida batafsil ma'lumot. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
    <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
</div>
<div style="text-align: center;">
    <a href="<?php echo \Route::url('blog'); ?>" class="btn">Blogga qaytish</a>
    <a href="<?php echo \Route::url('home'); ?>" class="btn" style="margin-left: 10px;">Bosh sahifa</a>
</div>