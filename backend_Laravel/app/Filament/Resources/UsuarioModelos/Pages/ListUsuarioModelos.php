<?php

namespace App\Filament\Resources\UsuarioModelos\Pages;

use App\Filament\Resources\UsuarioModelos\UsuarioModeloResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUsuarioModelos extends ListRecords
{
    protected static string $resource = UsuarioModeloResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
