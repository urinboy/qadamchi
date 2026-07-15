<?php
use App\Models\User;

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