# Qadamchi 2.2 - Views

## Asosiy View
View fayllar `app/Views/` papkasida joylashadi.

Controller da:
```php
return $this->view('about', ['title' => 'Sarlavha']);
```

## Layout Ishlatish
Avtomatik layout (`layouts/app.php`) ishlatiladi.

View fayl faqat content qismini o'z ichiga oladi:
```php
<h1><?php echo $title; ?></h1>
<p>Content</p>
```

## Sub-View lar
Papka ichida: `about/team.php`
Controller da: `$this->view('about.team')`

## Misol View Fayl
`app/Views/about.php`:
```php
<h1><?php echo $title; ?></h1>
<p><?php echo $content; ?></p>
<a href="<?php echo \Route::url('home'); ?>">Bosh sahifa</a>
```

## Layout Fayl
`app/Views/layouts/app.php`:
```php
<!DOCTYPE html>
<html>
<head><title><?php echo $title ?? 'Qadamchi'; ?></title></head>
<body>
    <nav>...</nav>
    <main><?php echo $content; ?></main>
    <footer>...</footer>
</body>
</html>
```