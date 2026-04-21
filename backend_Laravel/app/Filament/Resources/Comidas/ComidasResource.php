<?php

namespace App\Filament\Resources\Comidas;

use App\Filament\Resources\Comidas\Pages\CreateComidas;
use App\Filament\Resources\Comidas\Pages\EditComidas;
use App\Filament\Resources\Comidas\Pages\ListComidas;
use App\Filament\Resources\Comidas\Schemas\ComidasForm;
use App\Filament\Resources\Comidas\Tables\ComidasTable;
use App\Models\Comidas;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ComidasResource extends Resource
{
    protected static ?string $model = Comidas::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Comidas';

    public static function form(Schema $schema): Schema
    {
        return ComidasForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ComidasTable::configure($table);
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
            'index' => ListComidas::route('/'),
            'create' => CreateComidas::route('/create'),
            'edit' => EditComidas::route('/{record}/edit'),
        ];
    }
}
