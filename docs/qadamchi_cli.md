# Qadamchi CLI (`qadamchi`) – Qo‘llanma

## Qadamchi CLI nima?

Qadamchi CLI — bu Qadamchi framework uchun yaratilgan buyruq satri vositasi.  
Laravel’dagi `artisan` kabi, u sizga controller, model, migration, seeder va boshqa fayllarni terminal orqali tez va oson generatsiya qilish imkonini beradi.

---

## `qadamchi` CLI faylini qayerga joylash kerak?

- `qadamchi` CLI faylini loyihangizning **asosiy (root) papkasiga** joylashtiring. Bu papkada odatda `install.php` ham bo‘ladi.
- Fayl **qadamchi** deb nomlanishi kerak (hech qanday kengaytmasiz, ya’ni `.php` yoki `.txt` emas).

**Papka namunasi:**

```
/sizning-loyihangiz/
│
├── qadamchi       <-- CLI fayl aynan shu yerda bo‘lishi kerak
├── install.php
├── core/
├── app/
├── routes/
├── config/
├── public/
├── ...
```

---

## Qadamchi CLI faylini qanday ishga tushirish mumkin?

1. **Ruxsat berish (Linux/macOS):**

   ```sh
   chmod +x qadamchi
   ```

2. **(Ixtiyoriy) Fayl boshiga shebang qo‘shish:**

   ```
   #!/usr/bin/env php
   ```

   > Bu orqali siz `./qadamchi` deb to‘g‘ridan-to‘g‘ri ishlatishingiz mumkin bo‘ladi. Aks holda, har doim `php qadamchi` deb yozasiz.

---

## Qadamchi CLI’dan qanday foydalaniladi?

- **Loyihangiz root papkasida** quyidagicha buyruqlarni yozing:

  ```sh
  php qadamchi make:controller MyController
  php qadamchi make:model MyModel
  php qadamchi make:migration create_my_table
  php qadamchi make:middleware MyMiddleware
  php qadamchi make:seeder MySeeder
  ```

- **Yordamni ko‘rish uchun:**

  ```sh
  php qadamchi --help
  ```

- **Agar executable ruxsat bersangiz va shebang bo‘lsa:**

  ```sh
  ./qadamchi make:controller MyController
  ```

---

## Qadamchi CLI nimalar qiladi?

- Kerakli papkalarga (`app/Controllers`, `app/Models` va boshqalar) mos PHP fayl yaratadi.
- Sizga boshlang‘ich (skeleton) kod yozib beradi, shunda siz darhol o‘z logikingizni yozishni boshlashingiz mumkin.
- Fayl mavjud bo‘lsa, ustidan yozmaydi va ogohlantiradi.

---

## Xatoliklar va muammolar

- Buyruqlarni har doim Qadamchi loyihangizning asosiy papkasida yozing.
- PHP o‘rnatilgan va PATH’da mavjud bo‘lishi kerak.
- “Permission denied” chiqsa, `chmod +x qadamchi` buyrug‘ini yana bir marta yozing.
- Windows’da har doim `php qadamchi ...` deb ishlating. (`./qadamchi` emas!)

---

## Qadamchi CLI’ni kengaytirish

- Yangi buyruqlar qo‘shmoqchi bo‘lsangiz, `qadamchi` faylini oching va strukturaga mos holda yangi case yozing.

---

## Namuna: Controller yaratish

```sh
php qadamchi make:controller BlogController
```

- Natijada `app/Controllers/BlogController.php` fayli yaratiladi va boshlang‘ich kod bilan to‘ldiriladi.

---

## Qisqacha xulosa

- **`qadamchi` faylini loyihaning asosiy papkasiga joylashtiring.**
- **Terminalda `php qadamchi ...` orqali buyruqlar yozing.**
- **`--help` orqali mavjud buyruqlar ro‘yxatini ko‘ring.**

Batafsil ishlash yoki kengaytirish uchun `qadamchi` faylidagi izohlar va kod tuzilmasini o‘rganing.

---