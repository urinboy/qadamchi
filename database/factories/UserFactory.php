<?php
namespace Database\Factories;

use App\Models\User;
use Qadamchi\Database\Factory;

/**
 * User factory — test/seed ma'lumot generatsiyasi.
 *   User::factory()->create(['email' => 'x@y.uz'])
 */
class UserFactory extends Factory
{
    protected string $model = User::class;

    public function definition(): array
    {
        return [
            'name'     => 'Test User',
            'email'    => 'test@example.uz',
            'password' => bcrypt('password'),
        ];
    }
}