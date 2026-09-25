<?php

namespace App\Filament\Resources\UnitKerjas;

use App\Filament\Resources\UnitKerjas\Pages\CreateUnitKerja;
use App\Filament\Resources\UnitKerjas\Pages\EditUnitKerja;
use App\Filament\Resources\UnitKerjas\Pages\ListUnitKerjas;
use App\Filament\Resources\UnitKerjas\Schemas\UnitKerjaForm;
use App\Filament\Resources\UnitKerjas\Tables\UnitKerjasTable;
use App\Models\UnitKerja;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class UnitKerjaResource extends Resource
{
    protected static ?string $model = UnitKerja::class;

    protected static ?string $navigationLabel = 'Unit Kerja';

    protected static ?string $modelLabel = 'Unit Kerja';

    protected static ?string $pluralModelLabel = 'Unit Kerja';

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return UnitKerjaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UnitKerjasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUnitKerjas::route('/'),
            'create' => CreateUnitKerja::route('/create'),
            'edit' => EditUnitKerja::route('/{record}/edit'),
        ];
    }
}
