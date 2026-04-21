<?php

namespace App\Filament\Resources\UsuarioModelos\Pages;

use App\Filament\Resources\UsuarioModelos\UsuarioModeloResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUsuarioModelo extends EditRecord
{
    protected static string $resource = UsuarioModeloResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
