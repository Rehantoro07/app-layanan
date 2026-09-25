<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Contoh: Budi Santoso, S.Kom'),
                TextInput::make('nip')
                    ->label('NIP')
                    ->unique(ignoreRecord: true)
                    ->maxLength(50)
                    ->placeholder('Contoh: 199203152018011002'),
                TextInput::make('email')
                    ->label('Alamat Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->placeholder('user@bapeten.go.id'),
                Select::make('unit_kerja_id')
                    ->label('Unit Kerja')
                    ->relationship('unitKerja', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('role')
                    ->label('Peran (Role)')
                    ->options(UserRole::class)
                    ->default(UserRole::Pemohon->value)
                    ->required(),
                TextInput::make('password')
                    ->label('Kata Sandi')
                    ->password()
                    ->revealable()
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->placeholder('Biarkan kosong jika tidak ingin mengubah sandi'),
                Toggle::make('is_active')
                    ->label('Akun Aktif')
                    ->helperText('Jika dinonaktifkan, pengguna tidak dapat masuk ke sistem.')
                    ->default(true)
                    ->required(),
            ]);
    }
}
