<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'nama',
    'nip',
    'email',
    'password',
    'unit_kerja_id',
    'role',
    'is_active',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser, HasName
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUuids, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    /**
     * Compatibility accessor for default Laravel/Filament name attribute.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->nama,
            set: fn ($value) => ['nama' => $value],
        );
    }

    /**
     * Filament display name.
     */
    public function getFilamentName(): string
    {
        return $this->nama ?? $this->email;
    }

    /**
     * Determine if user can access a specific Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->is_active) {
            return false;
        }

        return match ($panel->getId()) {
            'admin' => in_array($this->role, [UserRole::Admin, UserRole::Petugas]),
            'pimpinan' => in_array($this->role, [UserRole::Admin, UserRole::Pimpinan]),
            'portal' => in_array($this->role, [UserRole::Admin, UserRole::Pemohon]),
            default => true,
        };
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isPetugas(): bool
    {
        return $this->role === UserRole::Petugas;
    }

    public function isPimpinan(): bool
    {
        return $this->role === UserRole::Pimpinan;
    }

    public function isPemohon(): bool
    {
        return $this->role === UserRole::Pemohon;
    }

    public function unitKerja(): BelongsTo
    {
        return $this->belongsTo(UnitKerja::class, 'unit_kerja_id');
    }

    public function permohonanDiajukan(): HasMany
    {
        return $this->hasMany(PermohonanData::class, 'pemohon_id');
    }

    public function permohonanDiproses(): HasMany
    {
        return $this->hasMany(PermohonanData::class, 'petugas_id');
    }

    public function permohonanDikonfirmasi(): HasMany
    {
        return $this->hasMany(PermohonanData::class, 'pimpinan_id');
    }

    public function dokumenPermohonan(): HasMany
    {
        return $this->hasMany(DokumenPermohonan::class, 'diunggah_oleh');
    }

    public function riwayatStatus(): HasMany
    {
        return $this->hasMany(RiwayatStatus::class, 'user_id');
    }
}
