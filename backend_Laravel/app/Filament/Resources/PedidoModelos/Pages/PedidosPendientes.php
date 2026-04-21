<?php

namespace App\Filament\Resources\PedidoModelos\Pages;

use App\Filament\Resources\PedidoModelos\PedidoModeloResource;
use App\Models\PedidoModelo;
use Filament\Resources\Pages\Page;

class PedidosPendientes extends Page
{
    protected static string $resource = PedidoModeloResource::class;

    protected static ?string $title = 'Pedidos Pendientes';

    public function getPedidosPendientes()
    {
        return PedidoModelo::where('estado', false)
            ->with(['detalles.usuario', 'detalles.comida'])
            ->orderBy('fecha', 'desc')
            ->get();
    }
}
