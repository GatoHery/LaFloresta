<?php

namespace App\Filament\Pages;

use App\Models\PedidoModelo;
use BackedEnum;
use Harvirsidhu\FilamentCards\Filament\Pages\CardsPage;
use Harvirsidhu\FilamentCards\CardItem;
use Harvirsidhu\FilamentCards\CardGroup;

class ListPedidosPendientes extends CardsPage
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Pedidos Pendientes';
    protected static ?int $navigationSort = 2;

    protected static string|int|array $columns = 3;

    protected static function getCards(): array
    {
        $pedidos = PedidoModelo::where('estado', false)
            ->with(['detalles.usuario', 'detalles.comida'])
            ->orderBy('fecha', 'desc')
            ->get();

        if ($pedidos->isEmpty()) {
            return [
                CardItem::make('#')
                    ->label('Sin pedidos')
                    ->description('No hay pedidos pendientes en este momento')
                    ->disabled(),
            ];
        }

        return [
            CardGroup::make('Pedidos Pendientes')
                ->columns(3)
                ->collapsible()
                ->schema(
                    $pedidos->map(function ($pedido) {
                        $detallesCount = $pedido->detalles->count();
                        $mesas = $pedido->detalles->pluck('n_mesas')->unique()->implode(', ');
                        
                        return CardItem::make(route('filament.admin.resources.pedido-modelos.edit', $pedido->id))
                            ->label('Pedido #' . $pedido->id)
                            ->badge('$' . $pedido->total)
                            ->badgeColor('success')
                            ->description($detallesCount . " comida(s) • Mesas: " . $mesas)
                            ->color('success')
                            ->icon('heroicon-o-document-text');
                    })->toArray()
                ),
        ];
    }
}
