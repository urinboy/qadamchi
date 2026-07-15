<?php
namespace Qadamchi\Database\Grammars;

use Qadamchi\Database\Blueprint;
use Qadamchi\Database\ColumnDefinition;

/**
 * SchemaGrammar — driver-ga bog'liq SQL generatsiyasi uchun abstrakt baza.
 *
 * Laravel'ning SchemaGrammar g'oyasi. Har bir driver (MySQL, SQLite, PostgreSQL)
 * o'z grammari bilan SQL farqlarini (quote, ustun turlari, INDEX/ENGINE/CHARSET,
 * SHOW TABLES / sqlite_master va h.k.) shu yerda hal qiladi.
 *
 * Blueprint va Schema ushbu grammar orqali ishlaydi — shu sababli migrationlar
 * driver-agnostik yoziladi.
 */
abstract class SchemaGrammar
{
    /** Driver nomi bo'yicha tegishli grammar'ni qaytaradi. */
    public static function forDriver(string $driver): self
    {
        $driver = strtolower($driver);
        if ($driver === 'sqlite') return new SQLiteGrammar();
        if ($driver === 'pgsql')  return new PostgresGrammar();
        return new MySqlGrammar();
    }

    /** Identifikatorni quote qiladi: mysql `name`, sqlite/pgsql "name". */
    abstract public function wrap(string $name): string;

    /** To'liq ustun ta'rifi: "name" TYPE [PRIMARY KEY] [NOT NULL] [DEFAULT ..] [COMMENT ..]. */
    public function compileColumn(ColumnDefinition $c): string
    {
        $sql = $this->wrap($c->name) . ' ' . $this->typeSql($c);

        if ($this->isIncrement($c) && $this->primaryKeyAppended()) {
            // MySQL: AUTO_INCREMENT + alohida PRIMARY KEY kalit so'zi.
            $sql .= ' PRIMARY KEY';
        }

        $sql .= $c->nullable ? ' NULL' : ' NOT NULL';

        if ($c->default !== null) {
            $sql .= ' DEFAULT ' . $this->formatDefault($c->default);
        } elseif ($c->nullable && in_array($c->type, ['timestamp', 'datetime'], true)) {
            $sql .= ' DEFAULT NULL';
        }

        if ($c->comment && $this->supportsColumnComment()) {
            $sql .= " COMMENT '" . addslashes($c->comment) . "'";
        }

        return $sql;
    }

    /** Ustun turining SQL ko'rinishi (faqat TYPE qismi, constraints'siz). */
    abstract protected function typeSql(ColumnDefinition $c): string;

    /** increments/bigIncrements ustun uchun PRIMARY KEY alohida yoziladimi?
     *  mysql: true (TYPE'da yo'q, keyin PRIMARY KEY qo'shiladi).
     *  sqlite: false (TYPE ichida `INTEGER PRIMARY KEY AUTOINCREMENT`). */
    protected function primaryKeyAppended(): bool { return true; }

    protected function isIncrement(ColumnDefinition $c): bool
    {
        return in_array($c->type, ['bigIncrements', 'increments'], true);
    }

    /** CREATE TABLE oxiridagi qo'shimcha (mysql: ENGINE/CHARSET, boshqalar: ''). */
    abstract public function createTableSuffix(): string;

    /**
     * Indexlarni kompilyatsiya qiladi.
     * ['inline' => string[], 'after' => string[]]
     *   inline — CREATE TABLE ichiga qo'shiladigan bandlar (mysql: UNIQUE KEY/KEY).
     *   after  — CREATE TABLE'dan keyin alohida statementlar (sqlite: CREATE [UNIQUE] INDEX).
     */
    abstract public function compileIndexes(Blueprint $b): array;

    /** ALTER TABLE ... <prefix> <column> — mysql: ADD, sqlite/pgsql: ADD COLUMN. */
    abstract public function alterAddColumnPrefix(): string;

    /** Barcha jadvallar nomlari ro'yxatini qaytaruvchi so'rov (parametrsiz). */
    abstract public function listTablesSql(): string;

    /** Jadval mavjudligini tekshiruvchi so'rov (? placeholder bilan). */
    abstract public function hasTableSql(): string;

    /** Jadval ustunlari haqida ma'lumot qaytaruvchi so'rov (table nomi interpolyatsiya). */
    abstract public function listColumnsSql(string $table): string;

    /** Barcha jadvallarni drop qiluvchi statementlar (FK checklarni o'chirib/yoqib). */
    abstract public function dropAllTables(array $tables): array;

    // ---- Umumiy yordamchilar ----

    protected function formatDefault($value): string
    {
        if (is_bool($value))   return $value ? '1' : '0';
        if (is_int($value) || is_float($value)) return (string) $value;
        if ($value === null)   return 'NULL';
        // String — quote qilamiz.
        return "'" . addslashes((string) $value) . "'";
    }

    protected function supportsColumnComment(): bool { return false; }

    protected function quoteStringList(array $values): string
    {
        return implode(', ', array_map(fn($v) => "'" . addslashes((string) $v) . "'", $values));
    }
}