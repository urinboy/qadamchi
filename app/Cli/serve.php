<?php
$host = 'localhost';
$port = '8080';
echo "Qadamchi server ishga tushdi: http://$host:$port\n";
echo "To'xtatish uchun Ctrl+C bosing\n";
passthru("php -S $host:$port -t public public/index.php");