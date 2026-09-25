<?php

namespace App\Filament\Resources\PermohonanData\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PermohonanDataInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Permohonan')
                    ->schema([
                        TextEntry::make('nomor_permohonan')
                            ->label('Nomor Permohonan')
                            ->weight('bold')
                            ->copyable(),
                        TextEntry::make('status')
                            ->label('Status Permohonan')
                            ->badge(),
                        TextEntry::make('pemohon.nama')
                            ->label('Pemohon'),
                        TextEntry::make('unitKerja.nama')
                            ->label('Unit Kerja Pemohon'),
                        TextEntry::make('tanggal_permohonan')
                            ->label('Tanggal Permohonan')
                            ->date('d F Y'),
                        TextEntry::make('dikirim_pada')
                            ->label('Waktu Dikirim ke Pusat Data')
                            ->dateTime('d F Y, H:i')
                            ->placeholder('Belum dikirim'),
                    ])
                    ->columns(2),

                Section::make('Data yang Dimohon')
                    ->schema([
                        TextEntry::make('nama_data')
                            ->label('Nama Data')
                            ->columnSpanFull(),
                        TextEntry::make('sumber_data')
                            ->label('Sumber Data / Aplikasi'),
                        TextEntry::make('periode_data')
                            ->label('Periode Data'),
                    ])
                    ->columns(2),

                Section::make('Tindak Lanjut Pusat Data & Status SLA')
                    ->schema([
                        TextEntry::make('petugas.nama')
                            ->label('Petugas Pemroses')
                            ->placeholder('Belum ditugaskan'),
                        TextEntry::make('pimpinan.nama')
                            ->label('Pimpinan yang Mengonfirmasi')
                            ->placeholder('Belum dikonfirmasi'),
                        TextEntry::make('batas_sla')
                            ->label('Batas Waktu SLA (3 Hari)')
                            ->dateTime('d F Y, H:i')
                            ->placeholder('-'),
                        IconEntry::make('melebihi_sla')
                            ->label('Status Batas SLA')
                            ->boolean()
                            ->trueIcon('heroicon-o-exclamation-triangle')
                            ->falseIcon('heroicon-o-check-circle')
                            ->trueColor('danger')
                            ->falseColor('success'),
                        TextEntry::make('catatan_hasil')
                            ->label('Catatan Hasil Pengolahan Data')
                            ->placeholder('Belum ada catatan')
                            ->columnSpanFull(),
                        TextEntry::make('dikonfirmasi_pada')
                            ->label('Waktu Dikonfirmasi')
                            ->dateTime('d F Y, H:i')
                            ->placeholder('-'),
                        TextEntry::make('selesai_pada')
                            ->label('Waktu Selesai')
                            ->dateTime('d F Y, H:i')
                            ->placeholder('-'),
                    ])
                    ->columns(2),
            ]);
    }
}
