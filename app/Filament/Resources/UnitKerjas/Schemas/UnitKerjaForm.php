<?php

namespace App\Filament\Resources\UnitKerjas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UnitKerjaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode')
                    ->label('Kode Unit Kerja')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50)
                    ->placeholder('Contoh: PUSDATIN'),
                TextInput::make('nama')
                    ->label('Nama Unit Kerja')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Contoh: Pusat Data dan Informasi HAYATI'),
            ]);
    }
}
