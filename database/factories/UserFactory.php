<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * User dengan peran Spatie 'admin'.
     *
     * Menggantikan pola lama `create(['role' => 'admin'])` + `assignRole('admin')`
     * yang harus ditulis manual di setiap test.
     */
    public function admin(): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole('admin'));
    }

    /**
     * User dengan peran peminjam.
     *
     * Mengambil peran pertama dari config('sipinjam.peran.peminjam') supaya
     * ikut berubah sendiri saat daftar peran berkembang — P1 mengganti 'user'
     * menjadi 'mahasiswa'.
     */
    public function peminjam(): static
    {
        $peran = config('sipinjam.peran.peminjam')[0] ?? 'mahasiswa';

        return $this->afterCreating(fn (User $user) => $user->assignRole($peran));
    }

    /**
     * User dengan peran tertentu, untuk matriks peran.
     */
    public function berperan(string $peran): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole($peran));
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
