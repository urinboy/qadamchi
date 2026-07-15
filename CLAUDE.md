# CLAUDE.md

Qadamchi — Composer'siz ishlaydigan, Laravel'ga o'xshash o'zbekcha PHP mikrofreymvork (hozircha A-versiya; B-versiyaga — Composer bilan ishlaydigan versiyaga — o'tish `composer.json` orqali tayyorlanmoqda, qarang: `docs/a-b-otish.md`).

## Commit qilish qoidasi

- Har bir tugallangan mustaqil ish (bitta modul, bitta xususiyat, bitta tuzatish) darhol, foydalanuvchidan so'ramasdan, alohida git commit sifatida saqlanadi.
- Bir-biriga aloqasi yo'q o'zgarishlarni bitta commitga qo'shma. Agar topshiriq bir nechta mustaqil bo'lakdan iborat bo'lsa (masalan, bir nechta modulni qayta tashkil etish), har bir bo'lakni alohida, ketma-ket commit qil — 2026-07-15 dagi core/app qayta tashkil etish shu tarzda ~28 ta mantiqiy commitga bo'lib chiqilgan, o'sha yondashuvni namuna sifatida ol.
- Commit xabari **o'zbek tilida**, qisqa va aniq: birinchi qator — imperativ, lo'nda sarlavha (nima qilindi); zarur bo'lsa bo'sh qatordan so'ng nima uchun qilinganini tushuntiruvchi 1-3 qatorli tana qo'shiladi.
- `.env` va boshqa maxfiy/credential fayllarni hech qachon commit qilma (`.gitignore`da allaqachon istisno qilingan).
- Faqat local commit qil — `git push` hech qachon avtomatik bajarilmaydi, aniq so'ralmaguncha.
- Katta, tartibsiz to'plangan o'zgarishlarni mantiqiy guruhlarga bo'lib tozalash kerak bo'lsa — `/commit-uz` buyrug'idan foydalan.

## Versiyalash qoidasi (SemVer)

- Versiya yagona manbasi: `core/Support/Version.php` (`Version::VERSION`). Boshqa hech qanday joyda versiya raqami qattiq yozilmasin — hammasi shu yerdan o'qiladi (`config/app.php`, CLI banner va h.k.).
- Format — `MAJOR.MINOR.PATCH` (masalan, `3.0.0`):
  - **MAJOR** — orqaga mos kelmaydigan (breaking) o'zgarish: public API/CLI buyrug'i/nomlar fazosi imzosi buziladigan qayta qurish.
  - **MINOR** — orqaga mos, yangi imkoniyat qo'shilishi.
  - **PATCH** — orqaga mos, xatolik tuzatish yoki kichik ichki yaxshilash.
- Versiya oshirish alohida so'rovsiz, mustaqil qaror sifatida qilinmaydi — buning uchun `/release` buyrug'i ishlatiladi: u oxirgi tag'dan beri bo'lgan commitlarni tahlil qilib, qaysi darajani (major/minor/patch) oshirish kerakligini sababi bilan taklif qiladi, foydalanuvchi tasdiqlaydi yoki o'zgartiradi. Shundan so'ng `Version.php`, `CHANGELOG.md` yangilanadi va `vX.Y.Z` git tag yaratiladi.
- Har bir versiya oshirilishi `CHANGELOG.md`da ([Keep a Changelog](https://keepachangelog.com/) uslubida, o'zbekcha bo'lim nomlari bilan: Qo'shildi / O'zgartirildi / Tuzatildi / Olib tashlandi) qayd etiladi.

## Boshqa AI vositalari uchun

Ushbu loyihada Claude Code'dan tashqari boshqa AI kodlash vositalari (masalan, GLM asosidagi vositalar) bilan ham ishlanishi mumkin. Yuqoridagi ikkala qoida `AGENTS.md` faylida ham (vositadan qat'i nazar o'qiladigan umumiy shaklda) takrorlangan — CLAUDE.md faqat Claude Code orqali kirganda o'qiladi, shuning uchun ikkala fayl ham yangilanib turishi kerak.
