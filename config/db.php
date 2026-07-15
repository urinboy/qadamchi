<?php
/**
 * DB sozlamalari (Laravel 13 uslubida).
 *
 * Default — SQLite (developer/lokal ish uchun zero-config):
 *   .env'da DB_DATABASE bo'sh → database/database.sqlite (auto-yaratiladi).
 * Istalgan paytda MySQL yoki PostgreSQL'ga o'tish mumkin — faqat .env'dagi
 * DB_CONNECTION va tegishli DB_* qiymatlarini o'zgartiring.
 *
 * driver: sqlite | mysql | pgsql
 */
return [
    'driver'  => env('DB_CONNECTION', 'sqlite'),

    // MySQL/PostgreSQL uchun server manzili; SQLite e'tibor bermaydi.
    'host'    => env('DB_HOST', '127.0.0.1'),
    // Bo'sh → har bir driver o'z default porti (mysql 3306, pgsql 5432).
    'port'    => env('DB_PORT', ''),

    // mysql/pgsql: database nomi.
    // sqlite: abs yo'l (masalan /var/data/app.sqlite), ':memory:' (xotira),
    //         yoki bo'sh → database/database.sqlite.
    'name'    => env('DB_DATABASE', 'qadamchi'),

    'user'    => env('DB_USERNAME', 'root'),
    'pass'    => env('DB_PASSWORD', ''),

    // mysql uchun charset; sqlite/pg e'tibor bermaydi.
    'charset' => env('DB_CHARSET', 'utf8mb4'),

    // sqlite: PRAGMA foreign_keys = ON (FK cheklovlarini yoqish).
    'foreign_keys' => env('DB_FOREIGN_KEYS', true),

    // Migration tracking jadvali nomi.
    'migration_table' => env('DB_MIGRATION_TABLE', 'migrations'),
];