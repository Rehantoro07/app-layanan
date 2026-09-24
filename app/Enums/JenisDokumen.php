<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum JenisDokumen: string implements HasColor, HasIcon, HasLabel
{
    case FormatData = 'format_data';
    case Hasil = 'hasil';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::FormatData => 'Dokumen Format Data',
            self::Hasil => 'Dokumen Hasil Pengolahan',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::FormatData => 'info',
            self::Hasil => 'success',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::FormatData => 'heroicon-o-document-text',
            self::Hasil => 'heroicon-o-document-check',
        };
    }
}
