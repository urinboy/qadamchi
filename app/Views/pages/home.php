<h1>Bosh sahifa</h1>
<?php
echo component('alert', ['type' => 'success', 'message' => 'Xush kelibsiz!']);

echo component('alert', ['type' => 'danger'], 'Xatolik yuz berdi!');

echo component('alert', ['type' => 'warning'], '<b>Diqqat!</b> Bu ogohlantirish sloti.');
?>