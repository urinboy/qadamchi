# Qadamchi — tushunchalar bo'yicha qo'llanma

Har bir bo'lim: **Qadamchi'da qanday** + **Laravel'da bu ...** eslatmasi.
Batafsil o'tish xaritasi uchun [`laravel-otish.md`](laravel-otish.md).

---

## Sozlash va ishga tushirish

```bash
php install.php            # bitta fayldan to'liq loyiha
php qadamchi key:generate  # APP_KEY
php qadamchi migrate       # jadvallar
php qadamchi db:seed        # namuna ma'lumot
php qadamchi serve         # http://localhost:8080
```

> **Laravel'da bu:** `php artisan key:generate`, `php artisan migrate --seed`, `php artisan serve`. Buyruq nomlari deyarli bir xil.

---

## Route

`routes/web.php`:
```php
Route::get('/', [WelcomeController::class, 'index'])->name('home');
Route::get('/about/{slug}', [WelcomeController::class, 'about'])->name('about');
Route::post('/posts', [PostController::class, 'store'])->middleware('auth');
```
- Parametrli route: `/about/{slug}` → `$request->routeParam('slug')`.
- Named route: `route('posts.show', ['id' => 1])` → URL.
- Middleware alias: `'auth'`, `'guest'`, `'csrf'`.

> **Laravel'da bu:** bir xil `Route::get/post/...`, named routes, `route()` helper. Qo'shimcha: route model binding, resource controllers.

---

## Controller + DI

```php
class PostController extends \Qadamchi\Http\Controller {
    public function __construct() { $this->middleware('auth', ['only' => ['create','store']]); }
    public function show(Request $request) {
        return view('posts.show', ['post' => Post::find($request->routeParam('id'))]);
    }
}
```
Controller metodlariga `Request` (va boshqa sinflar) **Container orqali avtomatik inject** qilinadi (reflection autowire).

> **Laravel'da bu:** bir xil method injection. Qo'shimcha: route model binding (`show(Post $post)`).

---

## Blade view

`resources/views/*.blade.php`. Sintaksis Laravel Blade bilan bir xil:
- `{{ $x }}` — escape; `{!! $x !!}` — xom.
- `@if/@elseif/@else/@endif`, `@foreach/@endforeach`, `@forelse/@empty/@endforelse`, `@isset/@endisset`.
- `@extends('layouts.app')`, `@section('content')...@endsection`, `@yield('content')` — layout inheritance.
- `@include('partials.nav')`, `@csrf`, `@method('PUT')`, `@auth/@guest`, `@php/@endphp`, `{{-- comment --}}`.
- Compile + cache: `storage/framework/views/`.

> **Laravel'da bu:** Blade — deyarli 100% bir xil sintaksis. Qo'shimcha: class-based components, `@props`, `@stack/@push`.

---

## Model / QueryBuilder (Eloquent'ga o'xshash)

```php
class User extends \Qadamchi\Database\Model {
    protected $fillable = ['name','email','password'];
    protected $hidden = ['password'];
    public function posts() { return $this->hasMany(Post::class); }
}

User::all();
User::where('active', 1)->orderBy('id','DESC')->paginate(15);
User::find(1)->posts;                    // lazy relation
User::create(['name'=>'Ali','email'=>'a@b.c','password'=>bcrypt('123')]);
$user->save();  $user->delete();
$user->toArray();                        // hidden (password) yo'q
```
Relations: `hasOne`, `hasMany`, `belongsTo`, `belongsToMany`. Accessors: `getNameAttribute`.

> **Laravel'da bu:** Eloquent — API deyarli bir xil. Qo'shimcha: scopes, factories, `with()` eager loading, soft deletes.

---

## Request / Validation

```php
$request->input('name');   $request->all();   $request->only(['name','email']);
$request->method();        $request->isAjax(); $request->file('avatar');
$request->validate(['email' => 'required|email']);   // ValidationException → redirect back
```
FormRequest:
```php
class CreateUserRequest extends \Qadamchi\Validation\FormRequest {
    public function rules(): array { return ['email'=>'required|email|unique:users,email','password'=>'required|min:8|confirmed']; }
    public function messages(): array { return ['email.unique'=>'Bu email band']; }
}
// controller: $data = (new CreateUserRequest)->validate();
```
Xato bo'lsa: `_errors` + `_old_input` flash, `ValidationException`, redirect back. Blade'da `old('email')` va `$errors`.

> **Laravel'da bu:** FormRequest — bir xil `rules()/messages()/authorize()`. Laravel controller'ga avtomatik inject qiladi va middleware kabi to'xtatadi.

---

## Middleware / CSRF

```php
class AuthMiddleware extends \Qadamchi\Http\Middleware {
    public function handle($request, \Closure $next) {
        if (!Auth::check()) return redirect('/login');
        return $next($request);
    }
}
```
Onion pipeline: `$next($request)` chaqirig'i orqali "oldin/so'ng" qatlamlari. Global `VerifyCSRF` POST/PUT/PATCH/DELETE'ni token bo'yicha tekshiradi (`hash_equals` timing-safe). Formda `@csrf`.

> **Laravel'da bu:** bir xil `handle($request, Closure $next)`. Laravel PSR-15 emas, o'z interface. Global `VerifyCsrfToken` — bir xil g'oya.

---

## Auth / Session

```php
Auth::attempt(['email'=>$e,'password'=>$p]);   // qidir + parol + login + session regenerate
Auth::user();  Auth::id();  Auth::check();  Auth::guest();
Auth::logout();
```
Session: `session()->put('key',$v)`, `session()->get('key')`, `session()->flash('success','...')`. Flash keyingi so'rovda yashaydi.

> **Laravel'da bu:** `Auth::attempt/user/logout` — bir xil. Guard'lar (web/api), multiple providers.

---

## Migration / Schema

```php
class CreateUsersTable extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('email')->unique();
            $t->string('password');
            $t->timestamps();
        });
    }
    public function down(): void { Schema::drop('users'); }
}
```
Blueprint: `id`, `string`, `text/longText`, `integer/bigInt`, `float/decimal`, `boolean`, `datetime/date/timestamp`, `enum`, `json`, `foreignId`, `nullable`, `default`, `unique`, `index`.
Buyruqlar: `migrate`, `migrate:rollback`, `migrate:reset`, `migrate:fresh`.

> **Laravel'da bu:** Blueprint — bir xil API. Qo'shimcha: `foreignId()->constrained()`, polymorphic.

---

## Factory (test/seed uchun soxta ma'lumot)

`database/factories/UserFactory.php`:
```php
class UserFactory extends \Qadamchi\Database\Factory {
    protected string $model = User::class;
    public function definition(): array {
        return [
            'name'     => 'Test User',
            'email'    => 'user@example.com',
            'password' => bcrypt('password'),
        ];
    }
}
```
```php
User::factory()->create();                 // 1 ta user
User::factory()->count(10)->create();      // 10 ta user
User::factory()->create(['name' => 'Ali']); // override
```
Konventsiya: `Database\Factories\<Model>Factory` (`User::factory()` → `UserFactory`).
Generator: `php qadamchi make:factory User`.

> **Laravel'da bu:** `database/factories/UserFactory.php` + `User::factory()->create()` — **bir xil API**. Qadamchi 3.1.0'da qo'shilgan, Laravel uslubidagi `definition()`/`count()`/`create()`.

---

## CLI (qadamchi)

`php qadamchi <buyruq>`:
- `migrate`, `migrate:fresh`, `migrate:rollback`, `migrate:reset`
- `db:seed [--class=]`
- `make:controller/model/migration/seeder/factory/middleware/request/view/command`
- `key:generate`, `route:list`, `cache:clear`, `session:clear`, `log:clear`
- `serve`, `test`, `build:installer`, `list`

> **Laravel'da bu:** `php artisan ...` — buyruq nomlari deyarli bir xil. Qadamchi har bir buyruq alohida `app/Cli/*.php` fayl (o'rganish uchun ochiq).

---

## Service Container / DI

```php
$container = app();
$container->singleton(Logger::class, fn() => new Logger(...));
$repo = $container->make(UserRepository::class);   // reflection autowire
$container->call([$controller, 'method'], $params);
```
PSR-11 mos imzo (`get`, `has`). B versiyasida `league/container` ga almashtirish mumkin.

> **Laravel'da bu:** `app()`, `app()->bind/singleton/make` — bir xil. Qo'shimcha: service providers, contextual binding.

---

## Config / Env

`config/app.php`, `config/db.php`, ... + `.env`:
```php
config('app.name');          // dot notation
config('db.host');
env('APP_DEBUG', false);
```

> **Laravel'da bu:** bir xil `config()` va `env()`.

---

## Lang (tarjima)

`lang/uz/messages.php`:
```php
return ['welcome' => 'Xush kelibsiz, :name'];
```
```php
trans('messages.welcome', ['name' => 'Ali']);   // "Xush kelibsiz, Ali"
```

> **Laravel'da bu:** `lang/uz/messages.php` + `__('messages.welcome', ['name'=>'Ali'])`. Bir xil interpolation.

---

## Misol blog ilovasi

Repo'ning o'zi to'liq misol: `routes/web.php` + `app/Controllers/{Welcome,Auth,Post}Controller` + `app/Models/{User,Post}` (hasMany/belongsTo relation) + `resources/views/*` Blade + `database/migrations/*` + `database/seeders/*` + `database/factories/*` + Auth (register/login/logout) + CSRF + validation. Shu oqimni o'qib chiqish = butun fremvorkni tushunish.

---

## Bitta-fayl o'rnatish

```bash
php qadamchi build:installer   # repo -> install.php (gzdeflate+base64)
# install.php ni serverga tashlang:
php install.php                 # 155 fayl unpack + .env + APP_KEY
```

> **Laravel'da bu ekvivalenti yo'q** (Composer orqali). Bu Qadamchi'ning ta'lim/oddiy-deploy konsepsiyasi.