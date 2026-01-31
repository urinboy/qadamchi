# Qadamchi 2.2 - Boshlash

## Birinchi Sahifa Yaratish

### 1. Controller Yaratish
```bash
php qadamchi make:controller HomeController
```

### 2. Route Qo'shish
`routes/web.php` ga qo'shing:
```php
Route::get('/home', 'HomeController@index');
```

### 3. Controller Kodini Yozish
`app/Controllers/HomeController.php`:
```php
<?php
namespace App\Controllers;

class HomeController extends Controller {
    public function index() {
        return $this->view('home');
    }
}
```

### 4. View Yaratish
`app/Views/home.php`:
```php
<h1>Salom, Qadamchi!</h1>
<p>Bu sizning birinchi sahifangiz.</p>
```

## Migration va Model
### Migration Yaratish
```bash
php qadamchi make:migration create_posts_table
```

### Model Yaratish
```bash
php qadamchi make:model Post
```

## Test Ishga Tushirish
```bash
vendor/bin/phpunit
```

## Keyingi Qadamlar
- [Routing](routing.md)
- [Database](database.md)