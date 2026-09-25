<?php

namespace App\Filament\Resources\PermohonanData\Pages;

use App\Filament\Resources\PermohonanData\PermohonanDataResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPermohonanData extends ListRecords
{
    protected static string $resource = PermohonanDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
