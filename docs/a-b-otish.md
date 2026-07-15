# A versiyasi → B versiyasiga o'tish

Qadamchi ikki bosqichli:

- **A versiyasi** (hozirgi): Composer'siz, o'z PSR-4 autoloader, bitta-fayl `install.php`. Ta'lim va kichik loyihalar uchun.
- **B versiyasi**: Composer asosida, `vendor/autoload.php`, paketlar (PSR-11 container, PSR-3 logger, PSR-7 HTTP). Ishlab chiqarish uchun.

**Ahamiyatli:** A'dagi app kodi (controllers, models, views, routes, migrations) B'ga **o'zgarmaydi**. Faqat yadro (core/) seam'lari orqali paketga almashtiriladi.

---

## Nima uchun A avval?

1. Composer'siz boshlash — boshlang'ich dasturchi "bu qanday ishlaydi" ni ko'radi (autoloader, container, pipeline — barchasi ochiq).
2. Kam qatlam — debugging oson.
3. Bitta fayl bilan o'rnatish — `install.php` ni serverga tashlang, `php install.php`.
4. Laravel'ga o'tish uchun tushunchalar pishadi.

## Nima uchun B keyin?

1. Paketlar ekotizimidan foydalanish (monolog, league/container, nyholm/psr7, guzzle...).
2. PSR-mos interfeyslar — boshqa PSR-kod bilan ishlaydi.
3. Autoloading/optimizatsiya Composer tomonidan.
4. Ishlab chiqarish kuchi.

---

## Seam'lar (A'da tayyor, B'da almashtiriladi)

A versiyasida yadro sinflar **PSR-mos imzolar** bilan yozilgan — shu sababli B'da paketga almashtirishda **app kodi o'zgarmaydi**:

| A (o'z nusxamiz) | B (paket) | Interface |
|---|---|---|
| `Qadamchi\Container\Container` | `league/container` | PSR-11 (`get`, `has`, `make`) |
| `Qadamchi\Support\Logger` | `monolog/monolog` | PSR-3 (`error`, `info`, ... levels) |
| `Qadamchi\Http\Request/Response` | `nyholm/psr7` + PSR-15 middleware | PSR-7 / PSR-15 |

`composer.json` (A'da ishlatilmaydi, lekin tayyor) `suggest` orqali buni ko'rsatadi.

---

## A → B qadamlari

### 1. Composer ni yoqing
```bash
composer init    # yoki mavjud composer.json stub'ni ishlatish
composer install
```

### 2. Autoloader'ni almashtiring
`bootstrap/autoload.php`:
```php
// A:
require __DIR__ . '/autoload.php';   // o'z PSR-4 autoloader

// B:
require __DIR__ . '/../vendor/autoload.php';
```
PSR-4 map `composer.json`'da autoloader map bilan **bir xil**:
```json
{
  "autoload": {
    "psr-4": {
      "Qadamchi\\": "core/",
      "App\\": "app/",
      "Database\\Seeders\\": "database/seeders/",
      "Database\\Factories\\": "database/factories/"
    }
  }
}
```
> Namespace va fayl joylashuvi o'zgartirilmaydi — faqat kim yuklashi.
> `Database\Seeders\` va `Database\Factories\` 3.1.0'da qo'shilgan (Laravel bilan bir xil joylashuv).

### 3. Container'ni paketga almashtiring
```bash
composer require league/container
```
`bootstrap/app.php`'da:
```php
// A:
$container = new \Qadamchi\Container\Container();

// B:
$container = new \League\Container\Container();
```
PSR-11 imzo mos — `singleton`, `make`, `get`, `has` ishlayveradi. App kodi (DI injection) o'zgarmaydi.

### 4. Logger'ni almashtiring
```bash
composer require monolog/monolog
```
PSR-3 mos — `error()`, `info()`, `debug()` metodlari bir xil.

### 5. HTTP layerni PSR-7/15 ga (ixtiyoriy, kuchliroq)
```bash
composer require nyholm/psr7
composer require psr/http-server-middleware
```
Middleware PSR-15 (`Psr\Http\Server\MiddlewareInterface`) — `process(ServerRequestInterface, RequestHandlerInterface)`.
A'dagi `handle($request, $next)` → PSR-15 `process($request, $handler)`.

### 6. Aliaslar saqlanadi
`class_alias` orqali `Route`, `Auth`, `DB`, `View` qisqa nomlari — Composer autoloader bilan ham ishlaydi (bootstrap/aliases.php o'z joyida).

---

## Tekshirish: A → B'dan keyin

- `php qadamchi test` — hammasi yashil.
- `php qadamchi route:list` — route'lar bir xil.
- `php qadamchi serve` — ilova ishlaydi.
- Hech bir controller/model/view o'zgarmagan.

## Qachon o'tish kerak?

- Loyiha katta bo'lib, paket kerak bo'lsa (mail, queue, PDF...).
- Jamoa Composer'da ishlamoqchi bo'lsa.
- PSR-mos integratsiya kerak bo'lsa (boshqa PSR-7 ilova bilan).

Kichik/ta'lim loyihalar — A versiyasida qolaversangiz, hech qanday zarar yo'q.