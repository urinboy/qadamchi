---
description: To'plangan barcha saqlanmagan o'zgarishlarni mantiqiy guruhlarga bo'lib, ketma-ket, o'zbek tilidagi commit xabarlari bilan commit qiladi.
---

Vazifang: joriy git repozitoriyasidagi barcha commit qilinmagan o'zgarishlarni (staged, unstaged va tracked bo'lmagan fayllarni) ko'rib chiqib, ularni mantiqiy jihatdan bog'liq guruhlarga ajratib, har bir guruhni alohida, ketma-ket git commit sifatida saqla.

Bosqichlar:

1. `git status` orqali joriy holatni tekshir. Agar staged va unstaged o'zgarishlar aralashib ketgan bo'lsa, `git reset` bilan hammasini unstage qil (bu xavfsiz — working tree'ga tegmaydi, faqat index'ni tozalaydi).
2. O'zgargan fayllarni papka/modul/xususiyat bo'yicha mantiqiy guruhlarga ajrat (masalan: bitta controller va uning view'lari, bitta core nomlar fazosi, config o'zgarishlari, hujjatlar va h.k.). Bir-biriga aloqasi bo'lmagan narsalarni bitta commitga qo'shma — kerak bo'lsa 10+ ta kichik commitga bo'lishdan qo'rqma, 2026-07-15 dagi core/app qayta tashkil etish (~28 ta commit) shunday qilingan.
3. Har bir guruh uchun: aynan o'sha guruhga tegishli fayllarni `git add` qil (aniq fayl nomlari bilan, `git add -A` yoki `git add .` ishlatma), so'ngra qisqa, aniq, imperativ o'zbekcha sarlavha bilan commit qil (`git commit -m "$(cat <<'EOF' ... EOF)"` heredoc uslubida, apostrof/o' harflari to'g'ri chiqishi uchun); kerak bo'lsa 1-3 qatorli "nima uchun" tushuntirishini qo'sh.
4. `.env` yoki boshqa maxfiy/credential ko'rinishidagi fayllarni hech qachon commit qilma — agar shunday fayl staged bo'lib qolsa, uni chiqarib tashla (`git restore --staged` yoki `git rm --cached`) va foydalanuvchini ogohlantir.
5. Git identity (`user.name`/`user.email`) sozlanmagan bo'lsa, avval mavjud commitlar muallifidan (`git log --format='%an <%ae>'`) foydalanib local sozla; aniqlab bo'lmasa foydalanuvchidan so'ra.
6. Oxirida `git status` bilan working tree toza ekanini tasdiqla va yaratilgan commitlar ro'yxatini (`git log --oneline`) qisqacha ko'rsat.
7. Hech qachon `git push` qilma, agar aniq so'ralmagan bo'lsa.

Agar o'zgarishlar tabiati noaniq yoki xavfli ko'rinsa (masalan, katta hajmdagi kutilmagan o'chirish, tushunarsiz binary fayl yoki maxfiy ma'lumot bo'lishi mumkin bo'lgan fayl), commit qilishdan oldin foydalanuvchidan tasdiq so'ra.
