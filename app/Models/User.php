<?php
// app/Models/User.php - namuna model

namespace App\Models;

use Model;

class User extends Model {
    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password'];
    
    public function posts() {
        return Post::where('user_id', $this->id)->get();
    }
    
    public static function findByEmail($email) {
        return static::where('email', $email)->first();
    }
}

// Foydalanish namunalari:
$user = User::find(1);
$users = User::where('name', 'John')->orderBy('created_at', 'DESC')->get();
$newUser = User::create(['name' => 'Akmal', 'email' => 'akmal@example.com']);