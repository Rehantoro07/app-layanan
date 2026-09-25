<?php

namespace App\Filament\Resources\PermohonanData\PermohonanDataResource\RelationManagers;

use App\Enums\JenisDokumen;
use App\Models\DokumenPermohonan;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class DokumenPermohonanRelationManager extends RelationManager
{
    protected static string $relationship = 'dokumenPermohonan';

    protected static ?string $title = 'Dokumen Lampiran (Format Data & Hasil)';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('jenis')
                    ->label('Jenis Dokumen')
                    ->options(JenisDokumen::class)
                    ->default(fn () => (auth()->user()?->isPemohon() ?? false) ? JenisDokumen::FormatData->value : JenisDokumen::Hasil->value)
                    ->required(),
                FileUpload::make('path')
                    ->label('File Dokumen')
                    ->disk('public')
                    ->directory('dokumen-permohonan')
                    ->required()
                    ->storeFileNamesIn('nama_file')
                    ->maxSize(51200)
                    ->helperText('Maksimal ukuran file 50 MB'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_file')
            ->columns([
                TextColumn::make('nama_file')
                    ->label('Nama File')
                    ->searchable()
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->url(fn (DokumenPermohonan $record): string => asset('storage/'.$record->path), shouldOpenInNewTab: true),
                TextColumn::make('jenis')
                    ->label('Jenis')
                    ->badge(),
                TextColumn::make('ukuran_formatted')
                    ->label('Ukuran'),
                TextColumn::make('pengunggah.nama')
                    ->label('Diunggah Oleh'),
                TextColumn::make('created_at')
                    ->label('Waktu Unggah')
                    ->dateTime('d M Y H:i'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Unggah Dokumen')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['diunggah_oleh'] = auth()->id();

                        if (! empty($data['path']) && Storage::disk('public')->exists($data['path'])) {
                            $data['ukuran'] = Storage::disk('public')->size($data['path']);
                            $data['mime_type'] = Storage::disk('public')->mimeType($data['path']);
                            if (empty($data['nama_file'])) {
                                $data['nama_file'] = basename($data['path']);
                            }
                        }

                        return $data;
                    }),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->visible(fn (DokumenPermohonan $record): bool => (auth()->user()?->isAdmin() ?? false) || auth()->id() === $record->diunggah_oleh
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->isAdmin() ?? false),
                ]),
            ]);
    }
}
