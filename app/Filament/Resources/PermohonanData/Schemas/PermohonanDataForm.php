<?php

namespace App\Filament\Resources\PermohonanData\Schemas;

use App\Enums\StatusPermohonan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PermohonanDataForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Permohonan')
                    ->description('Rincian pemohon dan tanggal pengajuan')
                    ->schema([
                        TextInput::make('nomor_permohonan')
                            ->label('Nomor Permohonan')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Dibuat otomatis oleh sistem'),
                        DatePicker::make('tanggal_permohonan')
                            ->label('Tanggal Permohonan')
                            ->default(now())
                            ->required(),
                        Select::make('pemohon_id')
                            ->label('Nama Pemohon')
                            ->relationship('pemohon', 'nama')
                            ->searchable()
                            ->preload()
                            ->default(fn () => auth()->id())
                            ->disabled(fn () => ! (auth()->user()?->isAdmin() ?? false))
                            ->dehydrated()
                            ->required(),
                        Select::make('unit_kerja_id')
                            ->label('Unit Kerja Pemohon')
                            ->relationship('unitKerja', 'nama')
                            ->searchable()
                            ->preload()
                            ->default(fn () => auth()->user()?->unit_kerja_id)
                            ->disabled(fn () => ! (auth()->user()?->isAdmin() ?? false))
                            ->dehydrated()
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Data yang Dimohon')
                    ->description('Spesifikasi kebutuhan data yang belum dapat dihasilkan oleh aplikasi')
                    ->schema([
                        TextInput::make('nama_data')
                            ->label('Nama Data')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Data Rekapitulasi Dosis Radiasi Pekerja 2025')
                            ->columnSpanFull(),
                        TextInput::make('sumber_data')
                            ->label('Sumber Data / Nama Aplikasi')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Aplikasi B@LIS / Database Perizinan'),
                        TextInput::make('periode_data')
                            ->label('Periode Data')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Januari 2025 - Desember 2025'),
                    ])
                    ->columns(2),

                Section::make('Tindak Lanjut & Hasil (Pusat Data)')
                    ->description('Diisi oleh petugas dan pimpinan Pusat Data')
                    ->schema([
                        Select::make('petugas_id')
                            ->label('Petugas Pemroses')
                            ->relationship('petugas', 'nama')
                            ->disabled()
                            ->placeholder('Belum ditugaskan'),
                        Select::make('pimpinan_id')
                            ->label('Pimpinan yang Mengonfirmasi')
                            ->relationship('pimpinan', 'nama')
                            ->disabled()
                            ->placeholder('Belum dikonfirmasi'),
                        Select::make('status')
                            ->label('Status Permohonan')
                            ->options(StatusPermohonan::class)
                            ->default(StatusPermohonan::Draft->value)
                            ->disabled(fn () => ! (auth()->user()?->isAdmin() ?? false))
                            ->required(),
                        DateTimePicker::make('batas_sla')
                            ->label('Batas Waktu SLA (3 Hari)')
                            ->disabled()
                            ->placeholder('-'),
                        Textarea::make('catatan_hasil')
                            ->label('Catatan Hasil Tindak Lanjut')
                            ->columnSpanFull()
                            ->placeholder('Catatan atau keterangan hasil tindak lanjut pengolahan data')
                            ->disabled(fn () => auth()->user()?->isPemohon() ?? false),
                    ])
                    ->columns(2)
                    ->collapsed(fn (string $operation) => $operation === 'create'),
            ]);
    }
}
