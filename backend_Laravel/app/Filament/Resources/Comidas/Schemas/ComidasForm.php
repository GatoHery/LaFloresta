<?php

namespace App\Filament\Resources\Comidas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ComidasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required(),
                TextInput::make('precio')
                    ->required()
                    ->numeric(),
                TextInput::make('cantidad')
                    ->required()
                    ->numeric(),
            ]);
    }
}
