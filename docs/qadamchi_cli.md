# Qadamchi CLI binary — qo'llanma

Ushbu hujjat **`qadamchi` CLI binary** haqida: fayl qayerda joylashadi, qanday ishga
tushiriladi, ruxsat va shebang, Windows'da ishlatish, va yangi buyruq qo'shish retsepti.
Buyruqlarning to'liq reference'i — [`qadamchi-commands.md`](qadamchi-commands.md).

`qadamchi` — bu Laravel'ning `artisan`'iga o'xshash buyruq satri vositasi. U loyiha
ildizidagi `qadamchi` fayli (kengaytmasiz) bo'lib, `app/Cli/*.php` fayllariga dispatch qiladi:
har buyruq alohida fayl — o'rganish uchun ochiq.

---

## CLI binary nima?

`qadamchi` — kengaytmasiz PHP skript, loyiha ildizida turadi. U:

1. `bootstrap/autoload.php`'ni yuklaydi (PSR-4 + aliaslar).
2. `--version` / `--help` flag'larini dispatch'dan oldin tekshiradi.
3. Berilgan buyruqni `app/Cli/<command>.php` fayliga yo'naltiradi (`:` → `_`).
    - Masalan: `make:controller` → `app/Cli/make_controller.php`.

Buyruq topilmasa: `Noto'g'ri buyruq: ...` xatosi.

---

## Fayl qayerga joylanadi?

`qadamchi` CLI faylini loyiha **asosiy (root) papkasiga** joylang — `install.php` bilan birga.
Fayl **`qadamchi`** deb nomlanishi kerak (kengaytmasiz — `.php`/`.txt` emas).

```
/sizning-loyihangiz/
├── qadamchi       <-- CLI fayl aynan shu yerda
├── install.php
├── core/   app/   routes/   config/   public/   ...
```

---

## Joylash va ruxsat

### 1. Ruxsat berish (Linux/macOS)

```sh
chmod +x qadamchi
```

### 2. (Ixtiyoriy) Shebang qo'shish

Fayl boshida allaqachon shebang bor (generatsiya qilingan):

```
#!/usr/bin/env php
```

Bu `./qadamchi` ko'rinishida to'g'ridan-to'g'ri ishlatishga imkon beradi. Aks holda — har
doim `php qadamchi`.

---

## `php qadamchi` vs `./qadamchi`

| Usul | Shart | Misol |
|---|---|---|
| `php qadamchi <buyruq>` | PHP `PATH`'da bo'lsa (har doim ishlaydi) | `php qadamchi migrate` |
| `./qadamchi <buyruq>` | unix + `chmod +x` + shebang | `./qadamchi migrate` |

### Windows eslatma

Windows'da har doim `php qadamchi ...` ishlating (`./qadamchi` ishlamaydi).
Windows'da ruxsat (`chmod`) kerak emas.

---

## Foydalanish

Loyiha root papkasida:

```sh
php qadamchi list                 # barcha buyruqlar
php qadamchi --version            # versiya
php qadamchi make:controller PostController
php qadamchi migrate
php qadamchi serve                # http://localhost:8080
```

To'liq buyruqlar ro'yxati va sintaksis: [`qadamchi-commands.md`](qadamchi-commands.md).

---

## Yangi buyruq qo'shish

`qadamchi` binary'ni tahrirlash shart emas — dispatch avtomatik. Yangi buyruq uchun
`app/Cli/` papkasiga fayl qo'shasiz. Masalan, `php qadamchi stats` buyrug'i:

1. `app/Cli/stats.php` faylini yarating:

```php
<?php
/**
 * stats — loyiha statistikasini chiqaradi.
 * Foydalanish: php qadamchi stats
 */
echo "Qadamchi statistikasi\n";
echo "  Route'lar: " . count(app(\Qadamchi\Routing\Router::class)->routes()) . "\n";
echo "  Versiya:   " . \Qadamchi\Support\Version::VERSION . "\n";
```

2. Darhol ishlatish:

```sh
php qadamchi stats
```

`qadamchi` binary `:` ni `_` ga aylantiradi: `db:seed` → `app/Cli/db_seed.php`.
Shu sababli `app/Cli/` ichidagi har bir fayl — bitta buyruq.

> Yordamchi: `php qadamchi make:command Stats` `app/Commands/Stats.php` skeleton yaratadi
> (bu boshqa joy — `app/Commands/`). CLI dispatch uchun fayl `app/Cli/`'da bo'lishi kerak.

---

## Xatoliklar va muammolar

- Buyruqlarni har doim **loyiha ildizida** yozing.
- PHP o'rnatilgan va `PATH`'da bo'lishi kerak.
- `Permission denied` → `chmod +x qadamchi` qayta yozing.
- Windows'da `./qadamchi` ishlamaydi — `php qadamchi ...` ishlating.
- `Noto'g'ri buyruq: ...` → `app/Cli/<buyruq>.php` fayli yo'q (`:` → `_` e'tibor bering).