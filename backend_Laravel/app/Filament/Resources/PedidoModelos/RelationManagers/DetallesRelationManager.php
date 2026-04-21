<?php

namespace App\Filament\Resources\PedidoModelos\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;

class DetallesRelationManager extends RelationManager
{
    protected static string $relationship = 'detalles';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('usuario_id')
                    ->required()
                    ->numeric(),
                TextInput::make('comida_id')
                    ->required()
                    ->numeric(),
                TextInput::make('cantidad')
                    ->required()
                    ->numeric(),
                TextInput::make('n_mesas')
                    ->required(),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('comida.nombre')
                    ->label('Comida'),
                TextEntry::make('comida.precio')
                    ->label('Precio'),
                TextEntry::make('cantidad')
                    ->label('Cantidad'),
                TextEntry::make('usuario.nombre')
                    ->label('Mesero'),
                TextEntry::make('n_mesas')
                    ->label('Mesas'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                TextColumn::make('comida.nombre')
                    ->label('Comida'),
                TextColumn::make('comida.precio')
                    ->label('Precio ($)')
                    ->formatStateUsing(fn ($state) => number_format($state, 2)),
                TextColumn::make('cantidad')
                    ->numeric(),
                TextColumn::make('usuario.nombre')
                    ->label('Mesero'),
                TextColumn::make('n_mesas')
                    ->label('Mesas'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
