<?php

namespace App\Filament\Resources\Comidas\Pages;

use App\Filament\Resources\Comidas\ComidasResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditComidas extends EditRecord
{
    protected static string $resource = ComidasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
