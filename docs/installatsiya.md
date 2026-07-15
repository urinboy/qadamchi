# O'rnatish — install.php

Qadamchi mikrofreymvorkini yuklab olish va o'rnatish uchun **bitta fayl** — `install.php` ishlatiladi. U butun loyihani (core, app, config, routes, public, docs, tests) o'z ichiga oladi.

## install.php nima?

`install.php` — bu **bitta-fayl o'rnatuvchi**. U `php qadamchi build:installer` buyrug'i bilan generatsiya qilinadi va loyiha ildizida joylashadi.

> **Eslatma:** `install.php` `public/` papkasi tashqarisida bo'lgani uchun veb-server orqali to'g'ridan-to'g'ri yetib bormaydi. Shu sababli yuklab olish uchun alohida **route** ishlatiladi.

## Yuklab olish

Shu sahifaning yuqorisidagi **"install.php yuklab olish"** tugmasi joriy `install.php` faylini brauzerga yuklab beradi. Tugma quyidagi route'ga bog'langan:

```
GET /docs/installatsiya/yuklab
```

Bu route `install.php` faylini `Content-Disposition: attachment` header bilan yuboradi — brauzer uni to'g'ridan-to'g'ri yuklab oladi.

## O'rnatish

Faylni yuklab olganingizdan so'ng, ikki usulda o'rnatishingiz mumkin.

### 1. CLI orqali (tavsiya)

```bash
php install.php
```

Bu:

- 160 ta faylni ajratib chiqaradi
- `.env` faylini yaratadi (`.env.namuna` asosida)
- `APP_KEY` generatsiya qiladi
- o'zini (install.php) o'chiradi

### 2. Veb orqali

`install.php` faylini veb-server'ning public papkasiga joylang va brauzerdan oching:

```
http://saying.uz/install.php
```

> **Talab:** PHP 8+ kerak.

## Keyingi qadamlar

O'rnatish tugagach:

```bash
php qadamchi migrate        # jadval yaratish
php qadamchi db:seed         # namuna ma'lumot
php qadamchi serve           # http://localhost:8080
```

## Apache (.htaccess) — serverga joylash

PHP built-in server (`php qadamchi serve`) routing'ni o'zi hal qiladi, lekin **Apache**'da `mod_rewrite` uchun `.htaccess` kerak. Loyihada ikkita `.htaccess` mavjud:

### 1. `public/.htaccess` (tavsiya)

Dokument root **`public/`** papkasiga teng bo'lganda (standart Laravel uslubi). Barcha so'rovlarni `index.php` front controller'ga yo'naltiradi, statik fayllar (assets) va `install.php` to'g'ridan-to'g'li serve qilinadi.

> **Eng to'g'ri usul:** subdomain/domain dokument root'ini `public/` ga tenglang. Masalan: `qadamchi.urinboydev.uz → /home/user/qadamchi/public`.

### 2. root `.htaccess`

Dokument root **loyiha ildiziga** teng bo'lganda (public/ emas). Bu holda `install.php` to'g'ridan-to'g'li ishlaydi, app so'rovlari `public/index.php`'ga, assets esa `public/` ichidan serve qilinadi.

```
RewriteCond %{DOCUMENT_ROOT}/public%{REQUEST_URI} -f
RewriteRule ^(.*)$ public/$1 [L]
RewriteRule ^ public/index.php [L]
```

> **Eslatma:** `mod_rewrite` Apache moduli yoqilgan bo'lishi kerak (`a2enmod rewrite`). `.htaccess`'da `Options -Indexes` orqali katalog ro'yxati o'chirilgan, yashirin fayllar (`.env`) bloklangan.

## Qayta generatsiya

`install.php` faylini har doim quyidagi buyruq bilan qayta yaratishingiz mumkin:

```bash
php qadamchi build:installer
```

Manba — repo fayllari (source of truth). Shu sababli **drift yo'q**: har doim yangi generatsiya qilinadi.