<?php
/** migrate:reset — barcha migrationlarni down. */
$argv[1] = 'reset';
require __DIR__ . '/migrate.php';