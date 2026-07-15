# O'zgarishlar tarixi (Changelog)

Ushbu loyihadagi barcha muhim o'zgarishlar shu faylda qayd etiladi.
Format — [Keep a Changelog](https://keepachangelog.com/) asosida, versiyalash esa [SemVer](https://semver.org/) qoidalariga amal qiladi.

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

[3.0.0]: https://github.com/urinboy/qadamchi/releases/tag/v3.0.0
