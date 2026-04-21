<?php

namespace App\Filament\Resources\PedidoModelos\Pages;

use App\Filament\Resources\PedidoModelos\PedidoModeloResource;
use App\Models\Comidas;
use App\Models\PedidoModelo;
use Filament\Resources\Pages\CreateRecord;

class CreatePedidoModelo extends CreateRecord
{
    protected static string $resource = PedidoModeloResource::class;

    protected function afterCreate(): void
    {
        // Después de guardar el pedido y sus detalles, recalcular el total
        $pedido = $this->record;
        $subtotal = 0;
        
        if ($pedido->detalles) {
            foreach ($pedido->detalles as $detalle) {
                if ($detalle->comida) {
                    $subtotal += (float)$detalle->comida->precio * (float)$detalle->cantidad;
                }
            }
        }
        
        $servicio = $subtotal * 0.10;
        $total = round($subtotal + $servicio, 2);
        
        \Log::info('CreatePedidoModelo - afterCreate', ['pedido_id' => $pedido->id, 'subtotal' => $subtotal, 'servicio' => $servicio, 'total' => $total]);
        
        // Actualizar el total en la base de datos
        $pedido->update(['total' => $total]);
    }
}
