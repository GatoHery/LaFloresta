<?php

namespace App\Filament\Resources\Comidas\Pages;

use App\Filament\Resources\Comidas\ComidasResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListComidas extends ListRecords
{
    protected static string $resource = ComidasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
