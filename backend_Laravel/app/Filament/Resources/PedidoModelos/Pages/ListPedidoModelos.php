<?php

namespace App\Filament\Resources\PedidoModelos\Pages;

use App\Filament\Resources\PedidoModelos\PedidoModeloResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPedidoModelos extends ListRecords
{
    protected static string $resource = PedidoModeloResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
