<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'nip' => fake()->unique()->numerify('199#########00##'),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => UserRole::Pemohon,
            'is_active' => true,
            'unit_kerja_id' => null,
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::Admin,
        ]);
    }

    public function petugas(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::Petugas,
        ]);
    }

    public function pimpinan(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::Pimpinan,
        ]);
    }

    public function pemohon(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::Pemohon,
        ]);
    }

    public function withUnitKerja(?UnitKerja $unitKerja = null): static
    {
        return $this->state(fn (array $attributes) => [
            'unit_kerja_id' => $unitKerja?->id ?? UnitKerja::factory(),
        ]);
    }
}
