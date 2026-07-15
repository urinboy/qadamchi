<?php
namespace Database\Seeders;

use App\Models\User;
use Qadamchi\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@qadamchi.uz'],
            ['name' => 'Admin', 'password' => bcrypt('password')]
        );
    }
}