# Loyiha tuzilmasi (3.2.0)

Quyida Qadamchi'ning **joriy (3.2.0)** loyiha strukturasi — to'liq daraxt va har papkaning
vazifasi. Tarixiy versiyalar strukturasi uchun [`tarix`](tarix) hujjatiga qarang.

> **Eslatma:** `storage/` ichidagi runtime fayllari (loglar, kompilyatsiya qilingan view'lar,
> sessionlar, `.sqlite`) va `install.php` (generatsiya qilingan) bu ro'yxatda ko'rsatilmagan.

---

## Ildiz

```
qadamchi/                      loyiha ildizi
├── qadamchi                   CLI binary (dispatch → app/Cli/*.php)
├── install.php                bitta-fayl o'rnatuvchi (generatsiya qilingan, build:installer)
├── composer.json              B versiyasi uchun stub (PSR-4 autoload)
├── .env / .env.namuna         sozlamalar / namuna
├── .htaccess                  root Apache rewrite (public/ emas holati uchun)
├── .gitignore
├── README.md, CHANGELOG.md
├── CLAUDE.md, AGENTS.md       commit/versiya qoidalari
└── .claude/, .vscode/         lokal sozlamalar
```

## `app/` — ilova kodi (`App\` nom fazosi)

```
app/
├── Commands/                  make:command generatsiyasi (CLI dispatch emas)
├── Controllers/               AuthController, DocsController, WelcomeController
├── Middlewares/               AuthMiddleware, GuestMiddleware, VerifyCSRF
├── Models/                    User
├── Requests/                  CreateUserRequest (FormRequest)
└── Cli/                        har CLI buyruq alohida fayl (file-per-command)
    ├── migrate.php, migrate_fresh.php, migrate_rollback.php, migrate_reset.php
    ├── db_seed.php
    ├── make_controller.php, make_model.php, make_migration.php, make_seeder.php
    ├── make_factory.php, make_middleware.php, make_request.php, make_view.php
    ├── make_command.php, make_test.php
    ├── route_list.php, key_generate.php, serve.php, test.php
    ├── cache_clear.php, session_clear.php, log_clear.php
    ├── build_installer.php, list.php
    └── stub/                   generator shablonlari (.stub)
```

## `core/` — freymvork yadrosi (`Qadamchi\` nom fazosi)

```
core/
├── Auth/                       Auth (session-based web guard), Guard
├── Container/                  Container (PSR-11 mos, reflection autowire)
├── Contracts/                 ContainerInterface, ContainerException, NotFoundException
├── Database/
│   ├── DB.php, Schema.php, Blueprint.php
│   ├── Model.php, QueryBuilder.php
│   ├── Migration.php, Seeder.php, Factory.php
│   └── Grammars/               SQLiteGrammar, MySqlGrammar, PostgresGrammar, SchemaGrammar
├── Exceptions/                Handler, QadamchiException, RouteNotFoundException, ValidationException
├── Http/                       Request, Response, Controller, Middleware, Pipeline, Session, CSRF
├── Routing/                    Route, RouteRegistrar
├── Security/                   Security
├── Support/                    Markdown, Version, Lang, env, helpers
├── Testing/                    TestCase, TestResponse
├── Validation/                 Validator, FormRequest
└── View/                       View, Blade
```

## `database/` — ma'lumotlar bazasi (Laravel bilan mos)

```
database/
├── database.sqlite             (runtime, avtomatik yaratiladi — SQLite default)
├── migrations/                 2025_01_01_000001_create_users_table.php
├── seeders/                    DatabaseSeeder, UserSeeder   (Database\Seeders nom fazosi)
└── factories/                  UserFactory                  (Database\Factories nom fazosi)
```

## `resources/`, `lang/`, `public/`

```
resources/views/               Blade .blade.php
├── layouts/                    app.blade.php, docs.blade.php
├── docs/                       index.blade.php, show.blade.php
├── auth/                       login.blade.php, register.blade.php
├── components/                 alert.blade.php
├── pages/                      dashboard.blade.php
├── errors/                     404, 419, 422, 500, 503, debug, default
└── welcome.blade.php

lang/uz/                        messages.php, validation.php        (Laravel 11+ root lang/)

public/                         front controller + assets
├── index.php
├── .htaccess                   mod_rewrite → index.php
└── assets/                     app.css, docs.css, docs.js, favicon.svg, logo.svg, ...
```

## Qolgan papkalar

```
bootstrap/                      autoload.php (PSR-4 + aliaslar), app.php, cli.php, aliases.php
config/                         app.php, db.php, auth.php, session.php
routes/                         web.php, api.php
docs/                           hujjatlar (markdown) + arxiv (v1/, v2/, v2.1/)
tests/                          ExampleTest.php
storage/                        logs/, framework/{views,cache,sessions}, cache/   (runtime)
```

---

## Namespace xaritasi (PSR-4)

| Namespace | Papka |
|---|---|
| `Qadamchi\` | `core/` |
| `App\` | `app/` |
| `Database\Seeders\` | `database/seeders/` |
| `Database\Factories\` | `database/factories/` |

Qisqa aliaslar (`Route`, `DB`, `Auth`, `Model`, `Schema`, `View`, `Factory`...)
`class_alias` orqali — `bootstrap/aliases.php`.