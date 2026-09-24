<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum StatusPermohonan: string implements HasColor, HasIcon, HasLabel
{
    case Draft = 'draft';
    case Diajukan = 'diajukan';
    case Diproses = 'diproses';
    case MenungguKonfirmasi = 'menunggu_konfirmasi';
    case Selesai = 'selesai';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Diajukan => 'Diajukan',
            self::Diproses => 'Diproses',
            self::MenungguKonfirmasi => 'Menunggu Konfirmasi',
            self::Selesai => 'Selesai',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Diajukan => 'info',
            self::Diproses => 'warning',
            self::MenungguKonfirmasi => 'primary',
            self::Selesai => 'success',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Draft => 'heroicon-o-pencil-square',
            self::Diajukan => 'heroicon-o-paper-airplane',
            self::Diproses => 'heroicon-o-arrow-path',
            self::MenungguKonfirmasi => 'heroicon-o-clock',
            self::Selesai => 'heroicon-o-check-badge',
        };
    }
}
