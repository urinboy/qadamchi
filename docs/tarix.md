# Qadamchi tarixi

Qadamchi — o'zbekcha, Composer'siz, Laravel'ga o'xshash PHP mikrofreymvork. Bu hujjat
loyihaning **eng boshidan hozirgacha** bo'lgan yo'lini versiyalar bo'yicha bayon qiladi:
har versiyada nimadir qilingan/o'zgartirilgan/olib tashlangan va **nima uchun**.

> Arxivlangan manbalar: `docs/v1/`, `docs/v2/`, `docs/v2.1/` papkalari + `qadamchi_v2.1.md`.
> Joriy struktura: [`tuzilma`](tuzilma). Rasmiy o'zgarishlar ro'yxati: `CHANGELOG.md`.

## Loyiha maqsadi

Laravel'ning to'liq *ma'nosini* kam kod bilan berish — boshlang'ich dasturchi, startup va
kichik loyihalar tez ish boshlasin, keyinchalik Laravel'ga oson o'tsin. Shu sababli loyiha
ikki bosqichli: **A versiyasi** (Composer'siz — hozirgi) va **B versiyasi** (Composer — kelajak).
Batafsil: [`a-b-otish`](a-b-otish).

---

## v1 — boshlang'ich Composer'siz mikrofreymvork

Manba: `docs/v1/install_v1.php`, `docs/v1/qadamchi`.

Birinchi iteratsiya — minimal, "qanday ishlaydi"ni ochiq ko'rsatuvchi freymvork. Yadro
**flat fayllar** sifatida: `core/Route.php`, `core/Controller.php`, `core/Model.php`,
`core/View.php` va h.k. (nom fazosisiz). O'rnatuvchi `install_v1.php` papka/fayllarni
ketma-ket `mkdir`/`file_put_contents` bilan yaratgan. CLI (`qadamchi`) faqat
`make:controller` va `make:model` bilab cheklangan, `printHelp()` orqali yordam bergan.

| Qo'shildi | Tavsif |
|---|---|
| `core/*.php` (flat) | Route, Controller, Model, View, Request, Response, Session, Auth, Validator |
| `install_v1.php` | papka/fayl yaratuvchi installer |
| `qadamchi` CLI | `make:controller`, `make:model` |
| `app/Controllers`, `app/Models`, `app/Views`, `routes/`, `config/` | asos papkalari |

> **Nima uchun:** Composer'siz boshlash — autoloader, container, pipeline barchasi ochiq
> ko'rinishi uchun. Boshlang'ich dasturchi "bu qanday ishlaydi"ni o'qiy oladi.

---

## v2 — Blade, migration/seeder, o'zbekcha installer

Manba: `docs/v2/install_v2-uz.php`, `docs/v2/install_v2.1.php`, `docs/v2/qadamchi`.

Freymvork kengaydi: **Blade view dvigateli** (Laravel bilan bir xil sintaksis), migration +
seeder tizimi, `Schema`/`Blueprint`. Installer o'zbek tiliga o'tdi (`install_v2-uz.php` —
`papkaYarat`/`faylYarat` funksiyalari bilan). CLI buyruklari ko'paydi: `migrate`, `db:seed`,
`serve`, `make:seeder`, `make:migration`.

| Qo'shildi | Tavsif |
|---|---|
| Blade view dvigateli | `{{ }}`, `@if`, `@foreach`, `@extends`, `@section`, `@csrf` |
| Migration/Schema/Blueprint | `Schema::create()`, `$t->id()`, `$t->string()` |
| Seeder tizimi | `db:seed` |
| `install_v2-uz.php` | o'zbekcha installer |
| CLI kengaytirish | `migrate`, `db:seed`, `serve`, `make:seeder`, `make:migration` |

> **Nima uchun:** Laravel'ning asosiy hujjatlashtirilgan tushunchalarini (Blade, migration,
> seeder) berish — foydalanuvchi real loyiha qila oladigan bo'lsin.

---

## v2.1 — zamonaviy welcome, .htaccess, strukturaviy tozalash

Manba: `docs/v2.1/` (to'liq arxiv, 27 fayl), `qadamchi_v2.1.md`.

Zamonaviy **welcome** sahifa (logo, havolalar bilan), Apache `mod_rewrite` uchun `.htaccess`
(loyiha ildizi va `public/`), strukturaviy tozalash. Bu versiya `qadamchi_v2.1.md` hujjatida
"so'nggi struktura" sifatida tasvirlangan — endi u tarixiy.

| Qo'shildi/O'zgartirildi | Tavsif |
|---|---|
| Zamonaviy welcome sahifa | `app/Views/welcome.php` + logo |
| `.htaccess` (root + public/) | Apache mod_rewrite sozlamalari |
| `public/index.php` | static fayllar bilan front controller |
| Strukturaviy tozalash | papka/fayl joylashuvi standartlashtirildi |

> **Nima uchun:** to'liq, joyga qo'yilgan namuna ilova — bosh sahifa + route + CLI to'liq
> ishlaydigan bo'lsin. Bu versiya 3.0'gacha asos edi.

---

## v3.0.0 (2026-07-15) — PSR-4 nom fazoslari, SemVer

Yadro zamonaviy arxitekturaga o'tdi. `core/` va `app/` flat fayllardan **PSR-4 nom fazosi**
aslidagi tuzilmaga: `Auth`, `Database`, `Http`, `Routing`, `Security`, `Support`,
`Validation`, `View`, `Container`, `Contracts`, `Exceptions` qatlamlari. View'lar
`.blade.php` formatiga o'tdi. Versiya raqami to'liq SemVer formatiga (`3.0` → `3.0.0`).

| Qo'shildi | Tavsif |
|---|---|
| `composer.json` + `bootstrap/` | PSR-4 autoload + B versiyaga tayyor stub |
| `config/auth.php`, `config/session.php` | auth/session sozlamalari |
| AuthController, DocsController, AuthMiddleware, GuestMiddleware | auth + docs viewer |
| `lang/uz/` + `docs/` | tarjima fayllari + hujjatlar |
| `.htaccess` (root + public) | Apache mod_rewrite |
| Testlash infra | `TestResponse`, `ExampleTest`, `make:test`/`test` |
| `storage/` runtime papkalari | logs/framework/views|cache|sessions |
| `CLAUDE.md`/`AGENTS.md` | commit/versiya qoidalari |

| O'zgartirildi | Tavsif |
|---|---|
| `core/`, `app/` → PSR-4 qatlamlari | flat → nom fazosli tuzilma |
| `app/Views` → `.blade.php` | Blade format |
| Versiya → `3.0.0` | to'liq SemVer |
| `install.php` | B versiyaga tayyorlab soddalashtirildi |

| Olib tashlandi | Tavsif |
|---|---|
| `.env` versiya nazoratidan | `gitignore`'ga |
| Eski flat core/app fayllari | ishlatilmayotgan `Post` modeli + eski migratsiyalar |

> **Nima uchun:** Composer'siz bo'lsa ham, to'g'ri arxitektura (nom fazoslari, qatlamlar)
> o'rgatilsin — bu Laravel'ga o'tishda yordam beradi. SemVer esa aniq versiya kulture.

---

## v3.1.0 (2026-07-16) — Laravel'ga mos direktoriya struktura

Direktoriya strukturalari Laravel bilan to'liq moslashtirildi — shu sababli foydalanuvchi
Qadamchi'dan Laravel'ga ko'chayotganda papka joylashuvi bir xil bo'ladi.

| Qo'shildi | Tavsif |
|---|---|
| `resource_path()`, `lang_path()` helper | `core/Support/helpers.php` |
| `Database\Seeders\` / `Database\Factories\` PSR-4 | `bootstrap/autoload.php`, `composer.json` |
| Factory infratuzilmasi | `core/Database/Factory`, `Model::factory()`, `UserFactory` |
| `make:factory` + `factory.stub` + `Factory` aliasi | generator + qisqa nom |
| `database/factories/UserFactory.php` | namuna factory |

| O'zgartirildi | Tavsif |
|---|---|
| `app/Migrations/` → `database/migrations/` | global namespace saqlandi |
| `app/Seeders/` → `database/seeders/` | `Database\Seeders` nom fazosi |
| `app/Views/` → `resources/views/` | Blade `.blade.php` |
| `app/Lang/` → `lang/` | Laravel 11+ root `lang/` |
| path-reference'lar | `database_path()`, `resource_path()`, `lang_path()` |
| `db:seed` | qisqa nom → `Database\Seeders\` FQCN resolve |
| `build:installer` | `resources/` + `lang/` ni ham pakkalaydi |

> **Nima uchun:** papka joylashuvi Laravel bilan bir xil bo'lsa, o'tish "hammasi tanish"
> bo'ladi. Factory esa test/seed uchun standart Laravel uslubini beradi.

---

## v3.2.0 (2026-07-16) — docs UI/UX to'liq qayta dizayn

Hujjatlar viewer'i UI/UX jihatidan to'liq qayta ishlandi + har bir markdown hujjat to'liq
sozlandi + loyiha tarixi hujjati qo'shildi.

| Qo'shildi | Tavsif |
|---|---|
| `public/assets/docs.css` | docs'gina komponentlar (prose, code, callout, toc, prev-next, progress) |
| `public/assets/docs.js` | theme toggle, scrollspy, copy, search, reading progress |
| Dark mode | `html[data-theme="dark"]` + FOUC'siz inline script + localStorage |
| Syntax highlight | `Markdown::highlight()` — php/bash/sh/json/env tokenizer (Composer'siz) |
| Heading anchor + TOC | `slugify()`, `Markdown::toc()`, scrollspy |
| Callout blockquote | `> **Eslatma/Maslahat/Ogohlantirish/Xato:**` → note/tip/warn/danger |
| Ichki doc havolalari | `[t](foo.md#slug)` → `/docs/foo#slug` qayta yozish |
| `docs/tarix.md` | versiyalar bo'yicha to'liq tarix (shu hujjat) |
| `docs/tuzilma.md` | joriy 3.2.0 loyiha daraxti |
| Search filter | index sahifada client-side qidiruv |
| prev/next navigatsiya | DocsController `neighbors()` |

| O'zgartirildi | Tavsif |
|---|---|
| `core/Support/Markdown.php` | kengaytirildi: slug/anchor/toc/highlight/callout/link-rewrite |
| `app/Controllers/DocsController.php` | metadata + groups + neighbors + toc |
| Docs view'lari (layout/index/show) | to'liq qayta dizayn |
| `public/assets/app.css` | dark token override + .prose dark |
| 7 md + README | to'liq polish — 3.2.0 struktura, factory bo'limlari, reference jadval |
| `qadamchi_cli.md` / `qadamchi-commands.md` | rollarga ajratildi (binary vs reference) |
| `qadamchi_v2.1.md` | tarixiy banner callout qo'shildi |

> **Nima uchun:** hujjatlar loyihaning yuzi — sodda viewer o'rniga zamonaviy, o'qishli,
> qidiruvli, qorong'i/yorug rejimli interfeys. Va foydalanuvchi so'rovida aniq tilangan
> "ajoyib tarix" — eng boshidan hozirgacha, versiyalar bo'yicha.

---

## Kelajak — B versiyasi

A (hozirgi, Composer'siz) ishlab chiqarish uchun yetarli bo'lgach, navbat **B versiyasiga**:
Composer, `vendor/autoload.php`, paketlar (`league/container` — PSR-11, `monolog` — PSR-3,
`nyholm/psr7` — PSR-7/15). **App kodi o'zgarmaydi** — yadro seam interfeyslari orqali
paketga almashtiriladi. Batafsil: [`a-b-otish`](a-b-otish).