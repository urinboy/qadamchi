# Qadamchi → Laravel o'tish yo'riqnomasi

Qadamchi Laravel'ning **asosiy tushunchalarini** o'rgatish uchun mo'ljallangan.
Bu hujjat Qadamchi'da o'rgangan har bir narsaning Laravel'dagi to'liq mosini ko'rsatadi.
Maqsad: Qadamchi'da o'rganib, Laravel'ga o'tishda "hammasi tanish" bo'lsin.

---

## 1. Papka tuzilmasi

| Qadamchi | Laravel | Izoh |
|---|---|---|
| `app/Controllers` | `app/Http/Controllers` | Laravel `Http/` ostiga oladi |
| `app/Middlewares` | `app/Http/Middleware` | bir xil mantiq |
| `app/Models` | `app/Models` | **bir xil** |
| `app/Requests` | `app/Http/Requests` | FormRequest |
| `app/Migrations` | `database/migrations` | **3.1.0'da ko'chirildi** — endi `database/migrations` |
| `app/Seeders` | `database/seeders` | **3.1.0'da ko'chirildi** — endi `database/seeders` (`Database\Seeders`) |
| `database/factories` | `database/factories` | **3.1.0'da qo'shildi** — `Database\Factories` |
| `app/Views` | `resources/views` | **3.1.0'da ko'chirildi** — endi `resources/views` (Blade `.blade.php`) |
| `app/Lang` | `lang/` | **3.1.0'da ko'chirildi** — endi `lang/` (Laravel 11+) |
| `app/Cli` | `app/Console/Commands` | |
| `routes/web.php` | `routes/web.php` | **bir xil** |
| `routes/api.php` | `routes/api.php` | **bir xil** |
| `config/*.php` | `config/*.php` | **bir xil** |
| `public/index.php` | `public/index.php` | front controller |
| `storage/framework/*` | `storage/framework/*` | view/sess/cache |
| `bootstrap/app.php` | `bootstrap/app.php` | |

> **Laravel'da farq:** `app/` ichida `Http/`, `Console/` qavatlari bor. Qadamchi'da controllers/middlewares/requests `app/` ichida tekis qoladi (boshlang'ichlar uchun sodda). `database/`, `resources/`, `lang/` esa 3.1.0'dan boshlab Laravel bilan bir xil joylashgan.

---

## 2. Route

```php
// Qadamchi — routes/web.php
Route::get('/posts/{id}', [PostController::class, 'show'])->name('posts.show');
Route::post('/posts', [PostController::class, 'store'])->middleware('auth');
```

```php
// Laravel — routes/web.php   (DEYARLI BIR XIL)
Route::get('/posts/{id}', [PostController::class, 'show'])->name('posts.show');
Route::post('/posts', [PostController::class, 'store'])->middleware('auth');
```

**Laravel'da qo'shimcha:** `Route::resource()`, route model binding (`{post}` o'rniga `Post $post`), route groups with prefixes.

---

## 3. Controller

```php
// Qadamchi
class PostController extends \Qadamchi\Http\Controller {
    public function show(Request $request) {
        $post = Post::find($request->routeParam('id'));
        return view('posts.show', ['post' => $post]);
    }
}
```

```php
// Laravel
class PostController extends Controller {
    public function show(Request $request, $id) {           // yoki: show(Post $post)
        $post = Post::find($id);
        return view('posts.show', ['post' => $post]);
    }
}
```

> **Laravel'da:** route model binding — `{post}` ni to'g'ridan-to'g'ri `Post $post` parametri sifatida olasiz. Qadamchi'da hozir `routeParam()` orqali.

---

## 4. Blade view

**Bir xil sintaksis** — `{{ }}`, `@if`, `@foreach`, `@extends`, `@section`, `@yield`, `@csrf`, `@method`, `@auth`, `@guest`, `@include`.

```blade
{{-- Qadamchi va Laravel uchun bir xil --}}
@extends('layouts.app')
@section('content')
    @foreach ($posts as $post)
        <a href="{{ route('posts.show', ['id' => $post->id]) }}">{{ $post->title }}</a>
    @endforeach
@endsection
```

**Laravel'da qo'shimcha:** components (x-based & class-based), `@props`, `@stack`/`@push`, slots. Qadamchi'da `component()` funksiyasi mavjud (sodda variant).

---

## 5. Model / Eloquent

```php
// Qadamchi va Laravel — DEYARLI BIR XIL API
class User extends Model {
    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password'];
    public function posts() { return $this->hasMany(Post::class); }
}

User::where('active', 1)->orderBy('id', 'desc')->paginate(15);
User::find(1)->posts;          // relation
Auth::user()->name;
```

**Laravel'da qo'shimcha:** scopes, observers, factories, `with()` eager loading, soft deletes, mutators (Laravel 9+ attribute casting).

---

## 6. Request / Validation

```php
// Qadamchi — FormRequest
class CreateUserRequest extends \Qadamchi\Validation\FormRequest {
    public function rules(): array { return ['email' => 'required|email|unique:users,email']; }
    public function messages(): array { return [...]; }
}
// controller: $data = (new CreateUserRequest)->validate();
```

```php
// Laravel — FormRequest  (imzolar bir xil)
class CreateUserRequest extends \Illuminate\Foundation\Http\FormRequest {
    public function rules(): array { return ['email' => 'required|email|unique:users,email']; }
    public function messages(): array { return [...]; }
    public function authorize(): bool { return true; }
}
// controller: public function store(CreateUserRequest $request) { $request->validated(); }
```

> **Laravel'da:** FormRequest controller'ga avtomatik inject qilinadi va middleware kabi validatsiya qiladi. Qadamchi'da `(new XxxRequest)->validate()` qo'lda chaqiladi.

---

## 7. Middleware / CSRF

```php
// Qadamchi
class AuthMiddleware extends \Qadamchi\Http\Middleware {
    public function handle($request, \Closure $next) {
        if (!Auth::check()) return redirect('/login');
        return $next($request);
    }
}
```

```php
// Laravel — BIR XIL imzo
class AuthMiddleware {
    public function handle(Request $request, Closure $next): Response {
        if (!Auth::check()) return redirect('/login');
        return $next($request);
    }
}
```

CSRF: Qadamchi global `VerifyCSRF` (POST/PUT/PATCH/DELETE). Laravel global `VerifyCsrfToken` — **bir xil g'oya**. `@csrf` blade direktivasi ham bir xil.

---

## 8. Auth

```php
// Qadamchi va Laravel — bir xil statik facade
Auth::attempt(['email' => $e, 'password' => $p]);
Auth::user();  Auth::id();  Auth::check();
Auth::logout();
```

> **Laravel'da:** guard'lar (web/api), multiple providers, bCrypt/argon, `Auth::routes()`. Qadamchi'da session-based web guard.

---

## 9. Migration / Schema

```php
// Qadamchi va Laravel — BIR XIL Blueprint API
Schema::create('users', function (Blueprint $t) {
    $t->id();
    $t->string('email')->unique();
    $t->string('password');
    $t->timestamps();
});
```

Buyruqlar: `migrate`, `migrate:rollback`, `migrate:reset`, `migrate:fresh` — **bir xil nomlar**.

> **Laravel'da qo'shimcha:** `foreignId()->constrained()`, polymorphic relations migration, `php artisan migrate:fresh --seed`.

---

## 10. Service Container / DI

```php
// Qadamchi
$container = new \Qadamchi\Container\Container();
$container->singleton(Logger::class, fn() => new Logger(...));
$user = $container->make(UserRepository::class);   // reflection autowire
```

```php
// Laravel
app()->singleton(Logger::class, fn() => new Logger(...));
$user = app(UserRepository::class);                // bir xil autowire
```

> **Laravel'da:** service providers (`register`/`boot`), contextual binding, tags, events. Qadamchi Container PSR-11 mos imzo bilan — B versiyasida `league/container` ga almashtirish mumkin.

---

## 11. Config / Env

```php
// Qadamchi va Laravel — bir xil
config('app.name');        // config/app.php -> name
env('APP_DEBUG', false);   // .env
```

---

## O'tish bosqichlari (qisqacha)

1. Qadamchi loyihangiz ishlab turganda — **butun app kodi (controllers, models, views, routes) deyarli o'zgarishsiz** Laravel'ga ko'chadi.
2. `composer create-project laravel/laravel` yangi Laravel loyiha oching.
3. Papkalarni yuqoridagi xarita bo'yicha ko'chiring.
4. Namespace'larni `Qadamchi\` → `App\` ga moslang (App\ zaten Laravel'da bor).
5. Qadamchi facade aliaslari (`Route`, `Auth`, `DB`...) Laravel'da ham shu nomlar bilan ishlaydi.
6. Migrationlarni `database/migrations` ga ko'chiring — Blueprint bir xil.
7. `composer require` bilan qo'shimcha paketlar (queue, mail, events).

Batafsil: [`a-b-otish.md`](a-b-otish.md).