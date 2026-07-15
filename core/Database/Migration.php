<?php
namespace Qadamchi\Database;

/**
 * Migration bazasi (Laravel uslubida).
 * Har bir migration up() va down() ni implement qiladi.
 */
abstract class Migration
{
    abstract public function up(): void;
    abstract public function down(): void;
}