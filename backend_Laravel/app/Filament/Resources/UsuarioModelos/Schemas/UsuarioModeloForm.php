<?php

namespace App\Filament\Resources\UsuarioModelos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UsuarioModeloForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required(),
                TextInput::make('rol')
                    ->required(),
                TextInput::make('contrasena')
                    ->required(),
            ]);
    }
}
