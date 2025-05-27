<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <title>Xatolik yuz berdi</title>
    <style>
        body { font-family: Rubik, Arial, sans-serif; background: #f8fafc; color: #222; text-align: center; }
        .code { font-size: 4rem; color: #868e96; margin-top: 60px; }
        .msg { font-size: 1.3rem; margin: 24px 0 10px 0; }
        a { color: #3b5bdb; text-decoration: none; }
    </style>
</head>
<body>
    <div class="code"><?= $code ?? 'Xato' ?></div>
    <div class="msg"><?= $message ?? 'Noma’lum xatolik yuz berdi.' ?></div>
    <div><a href="/">Bosh sahifa</a></div>
</body>
</html>