# AGENTS.md

Qadamchi loyihasida ishlaydigan har qanday AI kodlash vositasi (Claude Code, GLM asosidagi vositalar va boshqalar) uchun umumiy qoidalar. Claude Code uchun qo'shimcha, vositaga xos ko'rsatmalar `CLAUDE.md`da.

## Commit qilish qoidasi

- Har bir tugallangan mustaqil ish darhol, alohida git commit sifatida saqlanadi — foydalanuvchi so'rashini kutmang.
- Bir-biriga aloqasi yo'q o'zgarishlarni bitta commitga qo'shmang; bir nechta mustaqil bo'lakdan iborat topshiriqni har bir bo'lak uchun alohida, ketma-ket commit qiling.
- Commit xabari **o'zbek tilida**, qisqa va aniq: birinchi qator — imperativ, lo'nda sarlavha; kerak bo'lsa, bo'sh qatordan so'ng "nima uchun" qilinganini tushuntiruvchi qisqa tana.
- `.env` va boshqa maxfiy fayllarni hech qachon commit qilmang (`.gitignore`da).
- Faqat local commit qiling — `git push` faqat aniq so'ralganda.

## Versiyalash qoidasi (SemVer)

- Versiya yagona manbasi: `core/Support/Version.php` (`Version::VERSION`). Boshqa joyda versiya raqamini qattiq yozmang.
- `MAJOR.MINOR.PATCH` formati: MAJOR — breaking o'zgarish, MINOR — orqaga mos yangi imkoniyat, PATCH — xatolik tuzatish/kichik yaxshilash.
- Versiya oshirilganda `Version.php` bilan birga `CHANGELOG.md` ham ([Keep a Changelog](https://keepachangelog.com/) uslubida, o'zbekcha bo'lim nomlari bilan) yangilanadi va `vX.Y.Z` git tag yaratiladi.
- Claude Code loyihasida shu ish uchun tayyor `/commit-uz` (to'plangan o'zgarishlarni guruhlab commit qilish) va `/release` (versiya oshirish) buyruqlari mavjud — boshqa vosita ishlatilsa, xuddi shu qoidalarga qo'lda amal qilinsin.
