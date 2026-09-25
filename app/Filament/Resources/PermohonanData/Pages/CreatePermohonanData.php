<?php

namespace App\Filament\Resources\PermohonanData\Pages;

use App\Enums\StatusPermohonan;
use App\Filament\Resources\PermohonanData\PermohonanDataResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePermohonanData extends CreateRecord
{
    protected static string $resource = PermohonanDataResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        if ($user && $user->isPemohon()) {
            $data['pemohon_id'] = $user->id;
            $data['unit_kerja_id'] = $user->unit_kerja_id;
            $data['status'] = StatusPermohonan::Draft->value;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->catatRiwayat(
            ke: StatusPermohonan::Draft,
            user: auth()->user(),
            catatan: 'Permohonan dibuat dalam status draft'
        );
    }
}
