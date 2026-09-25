<?php

namespace App\Filament\Resources\PermohonanData\PermohonanDataResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RiwayatStatusRelationManager extends RelationManager
{
    protected static string $relationship = 'riwayatStatus';

    protected static ?string $title = 'Riwayat Status & Jejak Audit';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('status_ke')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu Perubahan')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable(),
                TextColumn::make('status_dari')
                    ->label('Status Semula')
                    ->badge()
                    ->placeholder('(Awal)'),
                TextColumn::make('status_ke')
                    ->label('Status Menjadi')
                    ->badge(),
                TextColumn::make('user.nama')
                    ->label('Dilakukan Oleh')
                    ->placeholder('Sistem'),
                TextColumn::make('catatan')
                    ->label('Catatan / Keterangan')
                    ->wrap()
                    ->placeholder('-'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
