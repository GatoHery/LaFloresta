<?php

namespace App\Filament\Resources\PedidoModelos\Pages;

use App\Filament\Resources\PedidoModelos\PedidoModeloResource;
use App\Models\Comidas;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPedidoModelo extends EditRecord
{
    protected static string $resource = PedidoModeloResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
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
        
        \Log::info('EditPedidoModelo - afterSave', ['pedido_id' => $pedido->id, 'subtotal' => $subtotal, 'servicio' => $servicio, 'total' => $total]);
        
        // Actualizar el total en la base de datos
        $pedido->update(['total' => $total]);
    }
}
