<?php

namespace App\Filament\Resources\UsuarioModelos;

use App\Filament\Resources\UsuarioModelos\Pages\CreateUsuarioModelo;
use App\Filament\Resources\UsuarioModelos\Pages\EditUsuarioModelo;
use App\Filament\Resources\UsuarioModelos\Pages\ListUsuarioModelos;
use App\Filament\Resources\UsuarioModelos\Schemas\UsuarioModeloForm;
use App\Filament\Resources\UsuarioModelos\Tables\UsuarioModelosTable;
use App\Models\UsuarioModelo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UsuarioModeloResource extends Resource
{
    protected static ?string $model = UsuarioModelo::class;

    protected static ?string $modelLabel = 'Usuario';
    protected static ?string $pluralModelLabel = 'Usuarios';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return UsuarioModeloForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsuarioModelosTable::configure($table);
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
            'index' => ListUsuarioModelos::route('/'),
            'create' => CreateUsuarioModelo::route('/create'),
            'edit' => EditUsuarioModelo::route('/{record}/edit'),
        ];
    }
}
