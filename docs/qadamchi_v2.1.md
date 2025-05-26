# Qadamchi PHP Framework — So‘nggi Strukturasi (v2.1)

Quyida Qadamchi frameworkining to‘liq, zamonaviy va “Laravelga o‘xshash” asosiy fayl va papkalar strukturasini ko‘rishingiz mumkin.

---

## Asosiy papkalar va fayllar
/your-project/ 
│ 
├── app/ 
│ ├── Controllers/ 
│ │ └── WelcomeController.php 
│ ├── Models/ 
│ ├── Middlewares/ 
│ ├── Migrations/ 
│ ├── Seeders/ 
│ ├── Views/ 
│ │ └── welcome.php 
│ └── Lang/ 
│ 
├── config/ 
│ ├── app.php 
│ └── db.php 
│ 
├── core/ 
│ ├── Route.php 
│ ├── Controller.php 
│ ├── Model.php 
│ ├── View.php 
│ ├── Middleware.php 
│ ├── Request.php 
│ ├── Response.php 
│ ├── Validator.php 
│ ├── Session.php 
│ ├── Auth.php 
│ ├── ErrorHandler.php 
│ ├── Logger.php 
│ ├── Lang.php 
│ ├── Migration.php 
│ └── Seeder.php 
│ 
├── public/ 
│ ├── index.php # Kirish nuqtasi 
│ └── logo.svg # Welcome sahifa uchun rasm va boshqa static fayllar 
│ 
├── routes/ 
│ ├── web.php # Web marshrutlar 
│ └── api.php # API marshrutlar (hozircha bo'sh) 
│ 
├── storage/ 
│ ├── logs/ 
│ ├── cache/ 
│ └── sessions/ 
│ 
├── .env # Muhit sozlamalari 
├── install.php # Frameworkni avtomatik o'rnatuvchi skript 
├── qadamchi # Qadamchi CLI (versiya 2.1, executable) 
└── README.md # Loyihaga oid

---

## Muhim fayllar va ularning vazifasi

- **install.php** — Framework tuzilmasini, welcome sahifasini va CLI’ni avtomatik yaratadi.
- **qadamchi** — Artisan’ga o‘xshash CLI: controller, model, migration, view va boshqalarni tez yaratadi, migration va seed ishlatadi, local serverni ishga tushiradi.
- **public/index.php** — Kirish nuqtasi, static fayllarni to‘g‘ri berish uchun PHP built-in serverga moslashtirilgan.
- **app/Controllers/WelcomeController.php** — Welcome sahifani boshqaruvchi controller.
- **app/Views/welcome.php** — Zamonaviy Welcome sahifasi (hamma havolalar, logo va boshqalar bilan).
- **routes/web.php** — Asosiy marshrutlar: `Route::get('/', 'WelcomeController@index');`
- **public/logo.svg** — Welcome uchun logo yoki rasm.

---

## Static fayllar

- Barcha rasm, css, js va boshqa static fayllar **public** papkasida bo‘lishi kerak.
  - Masalan: `public/logo.svg` yoki `public/images/logo.svg`
- Sahifada `<img src="/logo.svg">` yoki `<img src="/images/logo.svg">` orqali chaqiriladi.

---

## Eslatma

- Har bir papka va fayl o‘z vazifasiga mos keladi.
- Welcome sahifa va route to‘g‘ridan-to‘g‘ri ishlaydi.
- CLI orqali loyihani yaratish, migration, serve va boshqa buyruqlar bajariladi.

---

**Qadamchi** — O‘zbek dasturchilari uchun zamonaviy, oddiy va yengil PHP framework!
