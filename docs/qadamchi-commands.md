# Qadamchi CLI — Buyruqlar reference'i

Quyida Qadamchi CLI (`php qadamchi ...`) orqali ishlatiladigan barcha buyruqlarning to'liq
reference'i: sintaksis, misol va natija. CLI binary'ning o'zi haqida (joylash, shebang,
ruxsat, kengaytirish) — [`qadamchi_cli.md`](qadamchi_cli.md).

Har bir buyruq alohida `app/Cli/<buyruq>.php` fayl sifatida amalga oshirilgan (ochiq, o'qib
chiqish mumkin). `php qadamchi --help` va `php qadamchi list` ham to'liq ro'yxat beradi.

---

## Umumiy

| Buyruq | Sintaksis | Tavsif |
|---|---|---|
| `list` | `php qadamchi list` | Mavjud barcha buyruqlar + qisqacha tavsif |
| `--version` | `php qadamchi --version` (`-v`) | Versiyani chiqaradi (`Qadamchi 3.2.0 (CLI)`) |
| `--help` | `php qadamchi --help` | Yordam matni |

---

## Generator buyruqlari

Har generator fayl mavjud bo'lsa, ustidan yozmaydi va ogohlantiradi.

| Buyruq | Sintaksis | Yaratadigan fayl |
|---|---|---|
| `make:controller` | `php qadamchi make:controller PostController` | `app/Controllers/PostController.php` |
| `make:model` | `php qadamchi make:model Post` | `app/Models/Post.php` |
| `make:migration` | `php qadamchi make:migration create_posts_table` | `database/migrations/<timestamp>_create_posts_table.php` |
| `make:seeder` | `php qadamchi make:seeder PostSeeder` | `database/seeders/PostSeeder.php` (`Database\Seeders`) |
| `make:factory` | `php qadamchi make:factory Post` | `database/factories/PostFactory.php` (`Database\Factories`) |
| `make:middleware` | `php qadamchi make:middleware AuthAdmin` | `app/Middlewares/AuthAdmin.php` |
| `make:request` | `php qadamchi make:request StorePostRequest` | `app/Requests/StorePostRequest.php` |
| `make:view` | `php qadamchi make:view posts/show` | `resources/views/posts/show.blade.php` |
| `make:command` | `php qadamchi make:command SendEmails` | `app/Commands/SendEmails.php` |
| `make:test` | `php qadamchi make:test PostTest` | `tests/PostTest.php` |

**Misol:**
```bash
php qadamchi make:controller PostController
# Natija: app/Controllers/PostController.php yaratildi (boshlang'ich skeleton bilan)
```

---

## Migration buyruqlari

| Buyruq | Sintaksis | Natija |
|---|---|---|
| `migrate` | `php qadamchi migrate` | Bajarilmagan barcha migrationlarni ishga tushiradi (`up`) |
| `migrate:rollback` | `php qadamchi migrate:rollback` | Oxirgi batch'ni orqaga qaytaradi (`down`) |
| `migrate:reset` | `php qadamchi migrate:reset` | Barcha migrationlarni orqaga qaytaradi |
| `migrate:fresh` | `php qadamchi migrate:fresh` | Barcha jadvallarni drop + qayta migrate |

> **SQLite default:** `migrate` `database/database.sqlite` faylini avtomatik yaratadi.
> MySQL/PostgreSQL uchun `.env`'da `DB_CONNECTION` sozlang.

**Misol:**
```bash
php qadamchi make:migration create_posts_table
php qadamchi migrate        # jadval yaratildi
php qadamchi migrate:fresh  # drop + qayta yaratish
```

---

## Seeder buyruqlari

| Buyruq | Sintaksis | Natija |
|---|---|---|
| `db:seed` | `php qadamchi db:seed` | `DatabaseSeeder::run()` bajariladi |
| `db:seed` | `php qadamchi db:seed --class=UserSeeder` | Faqat `Database\Seeders\UserSeeder` |

> Qisqa nom (`UserSeeder`) avtomatik `Database\Seeders\UserSeeder` FQCN'ga resolve qilinadi.

---

## Xizmat buyruqlari

| Buyruq | Sintaksis | Natija |
|---|---|---|
| `route:list` | `php qadamchi route:list` | Ro'yxatdan o'tgan route'lar jadvali (method/uri/name/action) |
| `key:generate` | `php qadamchi key:generate` | Yangi `APP_KEY` generatsiya + `.env`'ga yozish |
| `cache:clear` | `php qadamchi cache:clear` | View/cache fayllarini tozalash (`storage/framework`) |
| `session:clear` | `php qadamchi session:clear` | Session fayllarini tozalash |
| `log:clear` | `php qadamchi log:clear` | Log fayllarini tozalash (`storage/logs`) |
| `serve` | `php qadamchi serve` | PHP built-in server → `http://localhost:8080` |
| `test` | `php qadamchi test` | Mini test runner (PHPUnit'siz) — `tests/*.php` |
| `build:installer` | `php qadamchi build:installer` | repo → bitta `install.php` (gzdeflate+base64) |

**Misol:**
```bash
php qadamchi route:list
php qadamchi serve
php qadamchi test
```

---

## Tipik ish oqimi

```bash
# 1) Yangi o'rnatish
php install.php
# 2) Model + migration + factory + seeder yaratish
php qadamchi make:model Post
php qadamchi make:migration create_posts_table
php qadamchi make:factory Post
php qadamchi make:seeder PostSeeder
# 3) Bazani yangilash + namuna ma'lumot
php qadamchi migrate:fresh
php qadamchi db:seed
# 4) Dev server
php qadamchi serve
# 5) Test
php qadamchi test
```

---

## Eslatmalar

- Buyruqlarni har doim **loyiha ildizida** (root papkada) ishlating.
- PHP o'rnatilgan va `PATH`'da bo'lishi kerak.
- Windows'da har doim `php qadamchi ...` (`./qadamchi` emas).
- Yangi buyruq qo'shish retsepti: [`qadamchi_cli.md`](qadamchi_cli.md#yangi-buyruq-qo-shish).