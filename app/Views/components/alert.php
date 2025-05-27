<div class="alert alert-<?= $type ?? 'info' ?>">
    <?= $message ?? $slot ?? '' ?>
    <?= component('button', ['text' => 'Bosing']) ?> 
    <?= component('button', ['color'=>'primary', 'text'=>'Saqlash']); ?>
    <?= component('button', ['href'=>'/login', 'color'=>'secondary'], 'Kirish'); ?>
</div>