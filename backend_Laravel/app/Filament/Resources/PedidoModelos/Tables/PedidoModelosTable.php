<?php

namespace App\Filament\Resources\PedidoModelos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PedidoModelosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Pedido #')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('Y-m-d')
                    ->sortable(),
                TextColumn::make('total')
                    ->label('Total ($)')
                    ->numeric()
                    ->sortable(),
                BadgeColumn::make('estado')
                    ->label('Estado')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Finalizado' : 'En Proceso')
                    ->colors([
                        'success' => true,
                        'warning' => false,
                    ])
                    ->sortable(),
                ViewColumn::make('detalles')
                    ->label('Comidas')
                    ->view('filament.columns.pedido-comidas'),
                TextColumn::make('detalles.usuario.nombre')
                    ->label('Mesero')
                    ->limit(15)
                    ->searchable(),
                TextColumn::make('detalles.n_mesas')
                    ->label('Mesas')
                    ->limit(20),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Filter::make('fecha')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('fecha_desde')
                            ->label('Desde'),
                        \Filament\Forms\Components\DatePicker::make('fecha_hasta')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['fecha_desde'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha', '>=', $date),
                            )
                            ->when(
                                $data['fecha_hasta'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha', '<=', $date),
                            );
                    }),
                SelectFilter::make('detalles.usuario_id')
                    ->label('Mesera')
                    ->relationship('detalles.usuario', 'nombre')
                    ->searchable()
                    ->multiple()
                    ->preload(),
                SelectFilter::make('detalles.n_mesas')
                    ->label('Mesas')
                    ->options(function () {
                        return \App\Models\PedidoComidaUsuarioModelo::distinct()
                            ->pluck('n_mesas', 'n_mesas')
                            ->toArray();
                    })
                    ->multiple(),
                SelectFilter::make('detalles.comida_id')
                    ->label('Comida')
                    ->relationship('detalles.comida', 'nombre')
                    ->searchable()
                    ->multiple()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
