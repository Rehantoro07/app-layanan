<?php

namespace App\Filament\Resources\PermohonanData\Tables;

use App\Enums\StatusPermohonan;
use App\Models\PermohonanData;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PermohonanDataTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor_permohonan')
                    ->label('No. Permohonan')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('medium'),
                TextColumn::make('nama_data')
                    ->label('Nama Data')
                    ->searchable()
                    ->sortable()
                    ->limit(35)
                    ->tooltip(fn ($record): string => $record->nama_data),
                TextColumn::make('pemohon.nama')
                    ->label('Pemohon')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('unitKerja.nama')
                    ->label('Unit Kerja')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('tanggal_permohonan')
                    ->label('Tgl Permohonan')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('batas_sla')
                    ->label('Batas SLA')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('-'),
                IconColumn::make('melebihi_sla')
                    ->label('Lewat SLA')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Permohonan')
                    ->options(StatusPermohonan::class),
                SelectFilter::make('unit_kerja_id')
                    ->label('Unit Kerja')
                    ->relationship('unitKerja', 'nama'),
                TernaryFilter::make('melebihi_sla')
                    ->label('Lewat Batas SLA'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (PermohonanData $record): bool => $record->status === StatusPermohonan::Draft ||
                        (auth()->user()?->isAdmin() ?? false) ||
                        (auth()->user()?->isPetugas() ?? false)
                    ),
                Action::make('kirim')
                    ->label('Kirim ke Pusat Data')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Kirim Permohonan ke Pusat Data')
                    ->modalDescription('Apakah Anda yakin ingin mengirim permohonan ini ke Pusat Data? Batas waktu penyelesaian SLA (3 hari) akan mulai dihitung.')
                    ->visible(fn (PermohonanData $record): bool => $record->status === StatusPermohonan::Draft &&
                        ((auth()->user()?->isAdmin() ?? false) || auth()->id() === $record->pemohon_id)
                    )
                    ->action(function (PermohonanData $record): void {
                        $record->status = StatusPermohonan::Diajukan;
                        $record->dikirim_pada = now();
                        $record->batas_sla = now()->addDays(3);
                        $record->save();
                        $record->catatRiwayat(StatusPermohonan::Diajukan, auth()->user(), 'Permohonan dikirim ke Pusat Data');

                        Notification::make()
                            ->title('Permohonan Berhasil Dikirim')
                            ->body("Permohonan {$record->nomor_permohonan} telah dikirim ke Pusat Data.")
                            ->success()
                            ->send();
                    }),
                Action::make('proses')
                    ->label('Mulai Proses')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Mulai Proses Permohonan')
                    ->modalDescription('Anda akan ditugaskan sebagai petugas yang memproses permohonan data ini.')
                    ->visible(fn (PermohonanData $record): bool => $record->status === StatusPermohonan::Diajukan &&
                        ((auth()->user()?->isAdmin() ?? false) || (auth()->user()?->isPetugas() ?? false))
                    )
                    ->action(function (PermohonanData $record): void {
                        $record->status = StatusPermohonan::Diproses;
                        $record->petugas_id = auth()->id();
                        $record->save();
                        $record->catatRiwayat(StatusPermohonan::Diproses, auth()->user(), 'Permohonan diterima dan mulai diproses petugas');

                        Notification::make()
                            ->title('Status Permohonan: Diproses')
                            ->body("Permohonan {$record->nomor_permohonan} sedang diproses.")
                            ->warning()
                            ->send();
                    }),
                Action::make('kirim_pimpinan')
                    ->label('Kirim ke Pimpinan')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    ->visible(fn (PermohonanData $record): bool => $record->status === StatusPermohonan::Diproses &&
                        ((auth()->user()?->isAdmin() ?? false) || auth()->id() === $record->petugas_id || (auth()->user()?->isPetugas() ?? false))
                    )
                    ->form([
                        Textarea::make('catatan_hasil')
                            ->label('Catatan Hasil Pengolahan Data')
                            ->required()
                            ->rows(4)
                            ->placeholder('Jelaskan ringkasan data yang diolah atau dokumen hasil yang telah diunggah.'),
                    ])
                    ->action(function (PermohonanData $record, array $data): void {
                        $record->status = StatusPermohonan::MenungguKonfirmasi;
                        $record->catatan_hasil = $data['catatan_hasil'];
                        $record->save();
                        $record->catatRiwayat(StatusPermohonan::MenungguKonfirmasi, auth()->user(), $data['catatan_hasil']);

                        Notification::make()
                            ->title('Permohonan Dikirim ke Pimpinan')
                            ->body("Permohonan {$record->nomor_permohonan} menunggu konfirmasi Pimpinan.")
                            ->info()
                            ->send();
                    }),
                Action::make('konfirmasi')
                    ->label('Konfirmasi Selesai')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (PermohonanData $record): bool => $record->status === StatusPermohonan::MenungguKonfirmasi &&
                        ((auth()->user()?->isAdmin() ?? false) || (auth()->user()?->isPimpinan() ?? false))
                    )
                    ->form([
                        Textarea::make('catatan')
                            ->label('Catatan Pimpinan (Opsional)')
                            ->placeholder('Catatan atau persetujuan pimpinan terkait hasil pengolahan data'),
                    ])
                    ->action(function (PermohonanData $record, array $data): void {
                        $record->status = StatusPermohonan::Selesai;
                        $record->pimpinan_id = auth()->id();
                        $record->dikonfirmasi_pada = now();
                        $record->selesai_pada = now();
                        $record->save();
                        $record->catatRiwayat(
                            StatusPermohonan::Selesai,
                            auth()->user(),
                            $data['catatan'] ?? 'Permohonan data telah dikonfirmasi selesai oleh Pimpinan'
                        );

                        Notification::make()
                            ->title('Permohonan Telah Selesai')
                            ->body("Permohonan {$record->nomor_permohonan} telah dikonfirmasi dan selesai.")
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->isAdmin() ?? false),
                ]),
            ]);
    }
}
