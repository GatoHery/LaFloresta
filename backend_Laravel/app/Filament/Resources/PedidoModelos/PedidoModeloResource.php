<?php

namespace App\Filament\Resources\PedidoModelos;

use App\Filament\Resources\PedidoModelos\Pages\CreatePedidoModelo;
use App\Filament\Resources\PedidoModelos\Pages\EditPedidoModelo;
use App\Filament\Resources\PedidoModelos\Pages\ListPedidoModelos;
use App\Filament\Resources\PedidoModelos\Pages\PedidosPendientes;
use App\Filament\Resources\PedidoModelos\Schemas\PedidoModeloForm;
use App\Filament\Resources\PedidoModelos\Tables\PedidoModelosTable;
use App\Models\PedidoModelo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PedidoModeloResource extends Resource
{
    protected static ?string $model = PedidoModelo::class;

    protected static ?string $modelLabel = 'Pedido';
    protected static ?string $pluralModelLabel = 'Pedidos';
    protected static ?string $recordTitleAttribute = 'id';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PedidoModeloForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PedidoModelosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPedidoModelos::route('/'),
            'pendientes' => PedidosPendientes::route('/pendientes'),
            'create' => CreatePedidoModelo::route('/create'),
            'edit' => EditPedidoModelo::route('/{record}/edit'),
        ];
    }
}
