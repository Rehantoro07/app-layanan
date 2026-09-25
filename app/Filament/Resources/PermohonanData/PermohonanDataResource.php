<?php

namespace App\Filament\Resources\PermohonanData;

use App\Filament\Resources\PermohonanData\Pages\CreatePermohonanData;
use App\Filament\Resources\PermohonanData\Pages\EditPermohonanData;
use App\Filament\Resources\PermohonanData\Pages\ListPermohonanData;
use App\Filament\Resources\PermohonanData\Pages\ViewPermohonanData;
use App\Filament\Resources\PermohonanData\PermohonanDataResource\RelationManagers\DokumenPermohonanRelationManager;
use App\Filament\Resources\PermohonanData\PermohonanDataResource\RelationManagers\RiwayatStatusRelationManager;
use App\Filament\Resources\PermohonanData\Schemas\PermohonanDataForm;
use App\Filament\Resources\PermohonanData\Schemas\PermohonanDataInfolist;
use App\Filament\Resources\PermohonanData\Tables\PermohonanDataTable;
use App\Models\PermohonanData;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class PermohonanDataResource extends Resource
{
    protected static ?string $model = PermohonanData::class;

    protected static ?string $navigationLabel = 'Permohonan Data';

    protected static ?string $modelLabel = 'Permohonan Data';

    protected static ?string $pluralModelLabel = 'Permohonan Data';

    protected static string|UnitEnum|null $navigationGroup = 'Layanan Data';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    public static function form(Schema $schema): Schema
    {
        return PermohonanDataForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PermohonanDataInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PermohonanDataTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            DokumenPermohonanRelationManager::class,
            RiwayatStatusRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if (! $user) {
            return $query;
        }

        if ($user->isPemohon()) {
            return $query->where('pemohon_id', $user->id);
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPermohonanData::route('/'),
            'create' => CreatePermohonanData::route('/create'),
            'view' => ViewPermohonanData::route('/{record}'),
            'edit' => EditPermohonanData::route('/{record}/edit'),
        ];
    }
}
