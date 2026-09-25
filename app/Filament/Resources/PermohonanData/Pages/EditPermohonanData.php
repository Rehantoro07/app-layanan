<?php

namespace App\Filament\Resources\PermohonanData\Pages;

use App\Filament\Resources\PermohonanData\PermohonanDataResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPermohonanData extends EditRecord
{
    protected static string $resource = PermohonanDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
