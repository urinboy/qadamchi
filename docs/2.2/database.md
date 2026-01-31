# Qadamchi 2.2 - Database

## Konfiguratsiya
`config/db.php`:
```php
return [
    'driver' => 'sqlite', // yoki mysql
    'path' => __DIR__ . '/../storage/database.sqlite', // sqlite uchun
    // MySQL uchun:
    // 'host' => 'localhost',
    // 'name' => 'qadamchi',
    // 'user' => 'root',
    // 'pass' => '',
];
```

## Migration
### Yaratish
```bash
php qadamchi make:migration create_users_table
```

### Kod
`app/Migrations/20250131120000_create_users_table.php`:
```php
<?php
use Core\Schema;

class CreateUsersTable extends Migration {
    public function up() {
        Schema::create('users', function($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::drop('users');
    }
}
```

### Ishga Tushirish
```bash
php qadamchi migrate
```

## Model
### Yaratish
```bash
php qadamchi make:model User
```

### Kod
`app/Models/User.php`:
```php
<?php
namespace App\Models;

class User extends Model {
    protected $table = 'users';
    protected $fillable = ['name', 'email'];
}
```

### Ishlatish
```php
$user = User::create(['name' => 'John', 'email' => 'john@example.com']);
$users = User::all();
```

## Seeder
### Yaratish
```bash
php qadamchi make:seeder UserSeeder
```

### Kod
`app/Seeders/UserSeeder.php`:
```php
<?php
class UserSeeder extends Seeder {
    public function run() {
        User::create(['name' => 'Admin', 'email' => 'admin@example.com']);
    }
}
```

### Ishga Tushirish
```bash
php qadamchi db:seed
```