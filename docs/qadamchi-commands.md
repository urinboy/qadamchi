# Qadamchi CLI — Buyruqlar ro‘yxati

Quyida Qadamchi framework uchun CLI (`php qadamchi ...`) orqali ishlatiladigan barcha asosiy buyruqlar ro‘yxati va qisqacha tavsifi keltirilgan.

---

## Yordam va umumiy buyruqlar

| Buyruq             | Ma’nosi/tavsifi                                 |
|---------------------|------------------------------------------------|
| `php qadamchi --help`  | Yordam va mavjud buyruqlar ro‘yxatini ko‘rsatadi. |
| `php qadamchi list`    | Mavjud barcha buyruqlar va qisqacha tavsifini chiqaradi. |

---

## Generator buyruqlari

| Buyruq                                         | Tavsifi                                      |
|------------------------------------------------|-----------------------------------------------|
| `php qadamchi make:controller Nomi`            | Yangi controller yaratadi (`app/Controllers`).|
| `php qadamchi make:model Nomi`                 | Yangi model yaratadi (`app/Models`).          |
| `php qadamchi make:migration nomi`             | Yangi migration fayli yaratadi (`app/Migrations`).|
| `php qadamchi make:middleware Nomi`            | Yangi middleware yaratadi (`app/Middlewares`).|
| `php qadamchi make:seeder Nomi`                | Yangi seeder yaratadi (`app/Seeders`).        |
| `php qadamchi make:command Nomi`               | Yangi CLI command (buyruq) fayli yaratadi.    |
| `php qadamchi make:view nomi`                  | Yangi view fayli yaratadi (`app/Views`).      |

---

## Migration va seeder buyruqlari

| Buyruq                                    | Tavsifi                                                          |
|--------------------------------------------|------------------------------------------------------------------|
| `php qadamchi migrate`                     | Barcha migrationlarni bajaradi (bazani yangilaydi).              |
| `php qadamchi migrate:rollback`            | Oxirgi migrationlarni orqaga qaytaradi.                          |
| `php qadamchi migrate:reset`               | Barcha migrationlarni orqaga qaytaradi.                          |
| `php qadamchi migrate:fresh`               | Baza tozalanadi va migrationlar qayta ishlaydi.                  |

| `php qadamchi db:seed`                     | Barcha seederlarni bajaradi (bazaga dastlabki ma’lumot qo‘shadi).|
| `php qadamchi db:seed --class=Nomi`        | Faqat ma’lum bir seeder klassini bajaradi.                       |

---

## Qo‘shimcha va xizmat buyruqlari

| Buyruq                                | Tavsifi                                               |
|----------------------------------------|-------------------------------------------------------|
| `php qadamchi route:list`              | Loyihadagi barcha ro‘yxatdan o‘tgan marshrutlarni ko‘rsatadi.|
| `php qadamchi cache:clear`             | Cache’ni tozalaydi (`storage/cache` papkasini).        |
| `php qadamchi log:clear`               | Log fayllarini tozalaydi (`storage/logs`).             |
| `php qadamchi session:clear`           | Sessiya fayllarini tozalaydi (`storage/sessions`).     |
| `php qadamchi key:generate`            | Yangi ilova kaliti generatsiya qiladi (.env uchun).    |
| `php qadamchi serve`                   | Lokal serverni ishga tushiradi (php built-in server).  |

---

## Namuna

```sh
php qadamchi make:controller UserController
php qadamchi make:model User
php qadamchi make:migration create_users_table
php qadamchi migrate
php qadamchi db:seed
php qadamchi route:list
php qadamchi serve
```

---

## Eslatma

- Har bir buyruq uchun yordam yoki namunalarni ko‘rish uchun:
  ```sh
  php qadamchi make:controller --help
  php qadamchi migrate --help
  ```

---

**Qadamchi CLI — Yangi loyihalar, startaplar va o‘zbek dasturchilari uchun qulay va tezkor framework!**