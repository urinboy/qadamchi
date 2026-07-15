---
description: Oxirgi versiya tag'idan beri bo'lgan o'zgarishlarni tahlil qilib, SemVer bo'yicha qaysi darajani (major/minor/patch) oshirish kerakligini taklif qiladi, tasdiqlangach Version.php va CHANGELOG.md'ni yangilab, git tag yaratadi.
---

Vazifang: loyihaning versiyasini [SemVer](https://semver.org/) (`MAJOR.MINOR.PATCH`) qoidalariga muvofiq oshirish.

Bosqichlar:

1. Joriy versiyani `core/Support/Version.php`dagi `Version::VERSION` konstantasidan o'qi. Oxirgi versiya git tag'ini `git describe --tags --abbrev=0` orqali top (tag topilmasa, birinchi commitdan hisobla).
2. Oxirgi tag'dan (yoki tag bo'lmasa loyiha boshidan) hozirgi `HEAD`gacha bo'lgan commitlar ro'yxatini (`git log <oxirgi-tag>..HEAD --oneline`) ko'rib chiq.
3. O'sha commitlar tarkibidagi haqiqiy o'zgarishlarni (nafaqat sarlavhalarni) tahlil qilib, quyidagi qoidalar asosida qaysi darajani oshirish kerakligini aniqla:
   - **MAJOR** — public API/CLI buyrug'i/nomlar fazosi imzosi buziladigan, orqaga mos kelmaydigan o'zgarish bo'lsa.
   - **MINOR** — yangi, orqaga mos xususiyat yoki modul qo'shilgan bo'lsa.
   - **PATCH** — faqat xatolik tuzatish yoki kichik ichki yaxshilashlar bo'lsa.
   Taklifingni sababi bilan birga foydalanuvchiga ko'rsat (masalan, AskUserQuestion orqali) va tasdiqlashini so'ra — u boshqa darajani tanlashi yoki aniq versiya raqamini berishi mumkin.
4. Tasdiqlangach:
   - `core/Support/Version.php`dagi `VERSION` konstantasini yangi qiymatga yangila.
   - `CHANGELOG.md`ga yangi versiya bo'limini (sana bilan, `YYYY-MM-DD`) qo'sh — oxirgi tag'dan beri bo'lgan asosiy o'zgarishlarni o'zbek tilida, Keep a Changelog uslubidagi bo'lim nomlari bilan (`Qo'shildi` / `O'zgartirildi` / `Tuzatildi` / `Olib tashlandi`) qisqacha sanab o't.
   - Ikkala faylni birga, "Versiya X.Y.Z ga oshirildi" kabi qisqa o'zbekcha commit xabari bilan commit qil.
   - `git tag -a vX.Y.Z -m "Versiya X.Y.Z"` orqali annotated tag yarat.
5. Tag'ni yoki commitni hech qachon avtomatik `git push`/`git push --tags` qilma — bu alohida, aniq so'ralganda bajariladi.
