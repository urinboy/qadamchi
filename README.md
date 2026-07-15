# Qadamchi

**Qadamchi** — o'zbekcha, Composer'siz ishlaydigan, Laravel'ga o'xshash PHP mikrofremvork.
Maqsad: Laravel'ning to'liq *ma'nosini* kam kod bilan berish — shu bilan boshlang'ich dasturchi,
startup va kichik loyihalar tez ish boshlasin va keyinchalik Laravel'ga oson o'tsin.

- Composer kerak emas (o'z PSR-4 autoloader)
- Blade view dvigateli (Laravel bilan bir xil sintaksis)
- Eloquent'ga o'xshash Model (fillable, hidden, casts, relations, timestamps)
- Route parametrlari, named routes, middleware pipeline (onion)
- CSRF, session-based Auth, FormRequest validatsiya
- Migration/Schema (Blueprint), Seeder
- Service Container + DI (reflection autowire)
- Bitta fayl bilan o'rnatish: `install.php`

> PHP **8.0+**, `pdo_mysql`, `mbstring` kerak.

---

## Tez boshlash

```bash
# 1) O'rnatish (bitta fayldan to'liq loyiha)
php install.php

# 2) .env ni sozlang (DB_HOST, DB_NAME, DB_USER, DB_PASS ...)
#    va APP_KEY avtomatik generatsiya qilinadi

# 3) Jadvallar + namuna ma'lumot
php qadamchi migrate
php qadamchi db:seed

# 4) Dev server
php qadamchi serve          # http://localhost:8080
```

Lazim bo'lsa: `php qadamchi key:generate` (APP_KEY ni qayta generatsiya qilish).

---

## CLI buyruqlari

`php qadamchi <buyruq>` — to'liq ro'yxat: `php qadamchi list`.

| Buyruq | Vazifa |
|---|---|
| `migrate` | Migrationlarni ishga tushirish |
| `migrate:rollback` | Oxirgi batch'ni bekor qilish |
| `migrate:reset` | Barcha migrationlarni bekor qilish |
| `migrate:fresh` | Drop + qayta migrate |
| `db:seed [--class=]` | Seederlarni ishga tushirish |
| `route:list` | Route'lar jadvali |
| `key:generate` | APP_KEY generatsiya |
| `make:controller/model/migration/seeder/middleware/request/view/test/command` | Generatorlar |
| `cache:clear` / `session:clear` / `log:clear` | Tozalash |
| `serve` | PHP built-in server |
| `test` | Mini test runner (PHPUnit'siz) |
| `build:installer` | repo -> bitta `install.php` |

---

## Loyiha tuzilmasi

```
app/
  Controllers/   Middlewares/   Models/   Requests/
  Migrations/    Seeders/       Views/    Cli/   Lang/
bootstrap/   autoload.php (PSR-4 + aliaslar),  app.php, cli.php
core/        Routing, Http, Database, View, Auth, Validation, Container, Support, Exceptions
config/      app.php, db.php, auth.php, session.php
routes/      web.php, api.php
public/      index.php (front controller)
storage/     logs, framework/views|cache|sessions
qadamchi     CLI router (file-per-command)
install.php  bitta-fayl o'rnatuvchi (generatsiya qilingan)
composer.json  B versiyasiga o'tish uchun stub
```

Namespace'lar: `Qadamchi\` → `core/`, `App\` → `app/` (PSR-4).
Qisqa aliaslar (`Route`, `DB`, `Auth`, `Model`, `Schema`, `View`...) `class_alias` orqali —
`routes/web.php`'da `Route::get()` Laravel'dagidek ishlaydi.

---

## Namuna (repo'ning o'zi)

Repo Laravel'ning yangi install'idagi kabi toza **welcome** ilovasi:
bosh sahifa (`/`), auth bilan himoyalangan `/dashboard`, route parametri namunasi (`/about/{slug}`),
Auth (register/login/logout), CSRF, FormRequest validatsiya, `User` modeli + migration + seeder.
Shu oqimni o'qib chiqish = butun fremvorkni tushunish.

---

## Hujjatlar (`docs/`)

- [`tushunchalar.md`](docs/tushunchalar.md) — har tushuncha + "Laravel'da bu..."
- [`laravel-otish.md`](docs/laravel-otish.md) — Qadamchi → Laravel o'tish xaritasi
- [`a-b-otish.md`](docs/a-b-otish.md) — A (Composer'siz) → B (Composer) bosqichlari

---

## A → B versiyasi

A (hozirgi): Composer'siz, bitta-fayl `install.php`, ta'lim/kichik loyihalar uchun.
B: Composer, `vendor/autoload.php`, paketlar (league/container, monolog, nyholm/psr7, PSR-15).
**App kodi o'zgarmaydi** — yadro PSR-11/PSR-3 mos interfeyslar orqali paketga almashtiriladi.
Batafsil: [`docs/a-b-otish.md`](docs/a-b-otish.md).

---

## Litsenziya

MIT