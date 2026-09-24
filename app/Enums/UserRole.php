<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum UserRole: string implements HasColor, HasLabel
{
    case Admin = 'admin';
    case Petugas = 'petugas';
    case Pimpinan = 'pimpinan';
    case Pemohon = 'pemohon';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Admin => 'Admin (Staf TI)',
            self::Petugas => 'Petugas (Pusat Data)',
            self::Pimpinan => 'Pimpinan (Kepala Pusat Data)',
            self::Pemohon => 'Pemohon (Pegawai BAPETEN)',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Admin => 'danger',
            self::Petugas => 'warning',
            self::Pimpinan => 'info',
            self::Pemohon => 'success',
        };
    }
}
