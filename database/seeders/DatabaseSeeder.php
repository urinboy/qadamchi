<?php
/**
 * Asosiy seeder — boshqa seeder'larni chaqiradi.
 * Ishga tushirish: php qadamchi db:seed
 */
namespace Database\Seeders;

use Qadamchi\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(UserSeeder::class);
    }
}