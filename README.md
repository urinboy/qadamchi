# Qadamchi

**Qadamchi** — o'zbekcha, Composer'siz ishlaydigan, Laravel'ga o'xshash PHP mikrofreymvork.
Maqsad: Laravel'ning to'liq *ma'nosini* kam kod bilan berish — shu bilan boshlang'ich dasturchi,
startup va kichik loyihalar tez ish boshlasin va keyinchalik Laravel'ga oson o'tsin.

- Composer kerak emas (o'z PSR-4 autoloader)
- Blade view dvigateli (Laravel bilan bir xil sintaksis)
- Eloquent'ga o'xshash Model (fillable, hidden, casts, relations, timestamps, factory)
- Route parametrlari, named routes, middleware pipeline (onion)
- CSRF, session-based Auth, FormRequest validatsiya
- Migration/Schema (Blueprint), Seeder, Factory
- Service Container + DI (reflection autowire)
- SQLite default (MySQL/PostgreSQL ham mavjud), driver grammar qatlami
- Bitta fayl bilan o'rnatish: `install.php`

> PHP **8.0+**, `pdo_sqlite` (yoki `pdo_mysql`), `mbstring` kerak.

---

## Tez boshlash

```bash
# 1) O'rnatish (bitta fayldan to'liq loyiha)
php install.php

# 2) .env avtomatik yaratiladi + APP_KEY generatsiya qilinadi
#    Default: SQLite — hech qanday tashqi DB server kerak emas.

# 3) Jadvallar + namuna ma'lumot
php qadamchi migrate
php qadamchi db:seed

# 4) Dev server
php qadamchi serve          # http://localhost:8080
```

Lazim bo'lsa: `php qadamchi key:generate` (APP_KEY ni qayta generatsiya qilish).
MySQL/PostgreSQL'ga o'tish uchun `.env`'da `DB_CONNECTION` va `DB_*` qiymatlarini o'zgartiring.

---

## CLI buyruqlari

`php qadamchi <buyruq>` — to'liq ro'yxat: `php qadamchi list`. Batafsil reference: [`docs/qadamchi-commands.md`](docs/qadamchi-commands.md).

| Buyruq | Vazifa |
|---|---|
| `migrate` / `migrate:fresh` / `migrate:rollback` / `migrate:reset` | Migration boshqaruvi |
| `db:seed [--class=]` | Seederlarni ishga tushirish |
| `make:controller` / `model` / `migration` / `seeder` / `factory` | Generatorlar |
| `make:middleware` / `request` / `view` / `command` / `test` | Generatorlar (davomi) |
| `route:list` | Route'lar jadvali |
| `key:generate` | APP_KEY generatsiya |
| `cache:clear` / `session:clear` / `log:clear` | Tozalash |
| `serve` | PHP built-in server |
| `test` | Mini test runner (PHPUnit'siz) |
| `build:installer` | repo -> bitta `install.php` |
| `--version` / `list` | Versiya / buyruqlar ro'yxati |

> CLI binary haqida (joylash, shebang, ruxsat, kengaytirish): [`docs/qadamchi_cli.md`](docs/qadamchi_cli.md).

---

## Loyiha tuzilmasi (3.2.0)

```
app/
  Commands/   Controllers/   Middlewares/   Models/   Requests/   Cli/
bootstrap/   autoload.php (PSR-4 + aliaslar),  app.php, cli.php
core/        Routing, Http, Database, View, Auth, Validation,
             Container, Support, Exceptions, Contracts
config/      app.php, db.php, auth.php, session.php
routes/      web.php, api.php
database/    migrations/  seeders/  factories/      (Laravel bilan mos)
resources/   views/  (Blade .blade.php)              (Laravel bilan mos)
lang/        uz/  ...                                  (Laravel 11+ bilan mos)
public/      index.php (front controller), assets/
storage/     logs, framework/views|cache|sessions
docs/        hujjatlar (markdown)
tests/       ExampleTest, TestResponse
qadamchi     CLI router (file-per-command: app/Cli/*.php)
install.php  bitta-fayl o'rnatuvchi (generatsiya qilingan)
composer.json  B versiyasiga o'tish uchun stub
```

Batafsil daraxt: [`docs/tuzilma.md`](docs/tuzilma.md).

Namespace'lar: `Qadamchi\` → `core/`, `App\` → `app/`, `Database\Seeders\` → `database/seeders/`,
`Database\Factories\` → `database/factories/` (PSR-4).
Qisqa aliaslar (`Route`, `DB`, `Auth`, `Model`, `Schema`, `View`, `Factory`...) `class_alias`
orqali — `routes/web.php`'da `Route::get()` Laravel'dagidek ishlaydi.

---

## Namuna (repo'ning o'zi)

Repo Laravel'ning yangi install'idagi kabi toza **welcome** ilovasi:
bosh sahifa (`/`), auth bilan himoyalangan `/dashboard`, route parametri namunasi (`/about/{slug}`),
Auth (register/login/logout), CSRF, FormRequest validatsiya, `User` + `Post` modellari
(hasMany/belongsTo relation), migration, seeder, factory. Shu oqimni o'qib chiqish = butun
freymvorkni tushunish.

---

## Hujjatlar (`docs/`)

To'liq hujjatlar ro'yxati — `/docs` sahifasida (qidiruv bilan). Asosiylar:

- [`tushunchalar.md`](docs/tushunchalar.md) — har tushuncha + "Laravel'da bu..."
- [`laravel-otish.md`](docs/laravel-otish.md) — Qadamchi → Laravel o'tish xaritasi
- [`a-b-otish.md`](docs/a-b-otish.md) — A (Composer'siz) → B (Composer) bosqichlari
- [`tuzilma.md`](docs/tuzilma.md) — joriy loyiha strukturasi (daraxt)
- [`tarix.md`](docs/tarix.md) — versiyalar bo'yicha to'liq tarix

---

## A → B versiyasi

A (hozirgi): Composer'siz, bitta-fayl `install.php`, ta'lim/kichik loyihalar uchun.
B: Composer, `vendor/autoload.php`, paketlar (league/container, monolog, nyholm/psr7, PSR-15).
**App kodi o'zgarmaydi** — yadro PSR-11/PSR-3 mos interfeyslar orqali paketga almashtiriladi.
Batafsil: [`docs/a-b-otish.md`](docs/a-b-otish.md).

---

## Litsenziya

MIT