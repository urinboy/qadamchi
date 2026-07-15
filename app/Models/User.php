<?php
namespace App\Models;

use Qadamchi\Database\Model;

/**
 * User modeli (Eloquent'ga o'xshash).
 * Laravel'da: app/Models/User.php — deyarli bir xil.
 */
class User extends Model
{
    protected ?string $table = 'users';
    protected array $fillable = ['name', 'email', 'password'];
    protected array $hidden = ['password'];
    public bool $timestamps = true;

    public static function findByEmail(string $email): ?self
    {
        return static::where('email', $email)->first();
    }
}