<?php

namespace App\Filament\Resources\PermohonanData\Pages;

use App\Filament\Resources\PermohonanData\PermohonanDataResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPermohonanData extends ViewRecord
{
    protected static string $resource = PermohonanDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
