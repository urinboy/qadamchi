# O'zgarishlar tarixi (Changelog)

Ushbu loyihadagi barcha muhim o'zgarishlar shu faylda qayd etiladi.
Format — [Keep a Changelog](https://keepachangelog.com/) asosida, versiyalash esa [SemVer](https://semver.org/) qoidalariga amal qiladi.

## [3.2.0] - 2026-07-16

### Qo'shildi

- Docs viewer UI/UX to'liq qayta dizayn: dark mode (light/dark almashtirgich + localStorage,
  FOUC'siz inline script), reading progress bar, TOC + scrollspy (IntersectionObserver),
  heading anchor'lar, kod blokidan nusxa olish tugmasi, index sahifada client-side qidiruv,
  prev/next navigatsiya, breadcrumb.
- `public/assets/docs.css` — docs'gina komponentlar (prose, code, callout, toc, prev-next,
  progress, syntax highlight token'lari, dark variantlar).
- `public/assets/docs.js` — theme toggle, scrollspy, copy, search, progress (vanilla JS, defer).
- `core/Support/Markdown.php` kengaytirildi: `slugify()`, heading ID + anchor, `toc()`,
  fenced code block (til label + nusxa tugmasi), `highlight()` — php/bash/sh/json/env
  sintaksis bo'yogi (o'z tokenizer, Composer'siz), callout blockquote (`> **Eslatma:**` va h.k.),
  ichki doc havolalarini qayta yozish (`foo.md#slug` → `/docs/foo#slug`).
- Yangi hujjatlar: `docs/tarix.md` (versiyalar bo'yicha to'liq tarix), `docs/tuzilma.md`
  (joriy 3.2.0 loyiha daraxti).

### O'zgartirildi

- `app/Controllers/DocsController.php` — metadata (title/desc/cat/icon) + kategoriya
  guruhlari (Boshlash/Yo'riqnomalar/Reference/Tarix) + `neighbors()` (prev/next) + TOC.
- Docs view'lari (`layouts/docs`, `docs/index`, `docs/show`) to'liq qayta yozildi.
- `public/assets/app.css` — dark token override'lar (`html[data-theme="dark"]`) + `.prose` dark.
- 7 ta `.md` + `README.md` to'liq polish: 3.2.0 struktura, Factory bo'limlari, PSR-4 map,
  CLI reference jadvali, tarixiy banner (`qadamchi_v2.1.md`).
- `qadamchi_cli.md` (CLI binary) va `qadamchi-commands.md` (reference jadvali) rollarga ajratildi.
- Versiya: `3.1.0` → `3.2.0`.

## [3.1.0] - 2026-07-16

### O'zgartirildi

- Direktoriya strukturalari Laravel'ga moslashtirildi:
  - `app/Migrations/` → `database/migrations/` (global namespace saqlandi — `glob`+`require` orqali yuklanadi).
  - `app/Seeders/` → `database/seeders/` (`Database\Seeders` nomlar fazosiga o'tdi, PSR-4 autoload).
  - `app/Views/` → `resources/views/` (Blade `.blade.php`).
  - `app/Lang/` → `lang/` (Laravel 11+ root `lang/`).
- Barcha path-reference'lar yangi helper'larga o'tkazildi: `database_path()`, `resource_path()`, `lang_path()`.

### Qo'shildi

- `resource_path()` va `lang_path()` global helper'lari (`core/Support/helpers.php`).
- PSR-4 prefix'lar: `Database\Seeders\` → `database/seeders/`, `Database\Factories\` → `database/factories/` (`bootstrap/autoload.php`, `composer.json`).
- Factory infratuzilmasi (Laravel uslubi): `core/Database/Factory` bazasi, `Model::factory()` metodi (`Database\Factories\<Model>Factory` konventsiyasi, PSR-4 autoload).
- `database/factories/UserFactory.php` namunasi.
- `make:factory` CLI buyrug'i + `factory.stub` + `Factory` aliasi.
- `db:seed` qayta yozildi: qisqa nom → `Database\Seeders\` FQCN resolve (glob-preload olib tashlandi).
- `build:installer` endi `resources/` va `lang/` papkalarini ham pakkalaydi.

### Tuzilma (Laravel bilan mos)

| Qadamchi 3.1.0 | Laravel |
|---|---|
| `database/migrations` | `database/migrations` |
| `database/seeders` (`Database\Seeders`) | `database/seeders` |
| `database/factories` (`Database\Factories`) | `database/factories` |
| `resources/views` | `resources/views` |
| `lang/` | `lang/` |

## [3.0.0] - 2026-07-15

### O'zgartirildi

- Freymvorkning `core/` va `app/` qatlamlari flat fayl tuzilishidan PSR-4 nomlar fazosi asosidagi tuzilishga o'tkazildi: `Auth`, `Database`, `Http`, `Routing`, `Security`, `Support`, `Validation`, `View`, `Container`, `Contracts`, `Exceptions`.
- `app/Views` shablonlari `.blade.php` formatiga o'tkazildi.
- `install.php` B-versiyaga (Composer bilan ishlaydigan versiyaga) o'tishga tayyorlab soddalashtirildi.
- Versiya raqami to'liq SemVer formatiga (`3.0` → `3.0.0`) o'tkazildi.

### Qo'shildi

- `composer.json` (PSR-4 avtoload) va `bootstrap/` yuklovchi fayllari.
- `config/auth.php`, `config/session.php`.
- `AuthController`, `DocsController`, `AuthMiddleware`, `GuestMiddleware`.
- `app/Lang/uz/` tarjima fayllari va `docs/` hujjatlari.
- `.htaccess` fayllari (loyiha ildizi va `public/`), Apache mod_rewrite sozlamalari bilan.
- Testlash uchun `TestResponse`, `ExampleTest`, `make:test`/`test` CLI buyruqlari.
- `storage/` runtime papkalari.
- `CLAUDE.md`/`AGENTS.md` — commit va versiyalash qoidalari, `/commit-uz` va `/release` Claude Code buyruqlari.

### Olib tashlandi

- `.env` fayli versiya nazoratidan chiqarildi (endi `.gitignore`da).
- Eski flat core/app fayllari, ishlatilmayotgan `Post` modeli va eski migratsiyalar.

[3.2.0]: https://github.com/urinboy/qadamchi/releases/tag/v3.2.0
[3.1.0]: https://github.com/urinboy/qadamchi/releases/tag/v3.1.0
[3.0.0]: https://github.com/urinboy/qadamchi/releases/tag/v3.0.0
