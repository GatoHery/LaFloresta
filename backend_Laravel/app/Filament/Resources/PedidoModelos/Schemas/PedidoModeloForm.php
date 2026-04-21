<?php

namespace App\Filament\Resources\PedidoModelos\Schemas;

use App\Models\Comidas;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PedidoModeloForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('fecha')
                    ->required()
                    ->label('Fecha del Pedido'),
                Toggle::make('estado')
                    ->label('Pedido Finalizado')
                    ->default(false),
                Repeater::make('detalles')
                    ->label('Comidas del Pedido')
                    ->relationship('detalles')
                    ->schema([
                        Select::make('tipo_comida')
                            ->label('Categoría')
                            ->options(function () {
                                return Comidas::distinct()
                                    ->pluck('tipo', 'tipo')
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->live(),
                        Select::make('comida_id')
                            ->label('Comida')
                            ->options(function ($get) {
                                $tipo = $get('tipo_comida');
                                if (!$tipo) {
                                    return Comidas::pluck('nombre', 'id')->toArray();
                                }
                                return Comidas::where('tipo', $tipo)
                                    ->pluck('nombre', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('cantidad')
                            ->label('Cantidad')
                            ->numeric()
                            ->required()
                            ->default(1),
                        Select::make('usuario_id')
                            ->label('Mesera')
                            ->relationship('usuario', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('n_mesas')
                            ->label('Mesas')
                            ->required(),
                    ])
                    ->addActionLabel('Agregar Comida')
                    ->collapsible()
                    ->collapsed()
                    ->live(),
                Placeholder::make('subtotal_display')
                    ->label('Subtotal ($)')
                    ->content(function ($get) {
                        $subtotal = static::calcularSubtotal($get('detalles') ?? []);
                        return number_format($subtotal, 2);
                    }),
                Placeholder::make('servicio_display')
                    ->label('Servicio 10% ($)')
                    ->content(function ($get) {
                        $subtotal = static::calcularSubtotal($get('detalles') ?? []);
                        $servicio = $subtotal * 0.10;
                        return number_format($servicio, 2);
                    }),
                Placeholder::make('total_display')
                    ->label('Total ($)')
                    ->content(function ($get) {
                        $subtotal = static::calcularSubtotal($get('detalles') ?? []);
                        $servicio = $subtotal * 0.10;
                        $total = $subtotal + $servicio;
                        return number_format($total, 2);
                    }),
                Hidden::make('total')
                    ->default(0)
                    ->dehydrated(),
            ]);
    }

    private static function calcularSubtotal($detalles)
    {
        $subtotal = 0;
        foreach ($detalles as $detalle) {
            if (isset($detalle['comida_id'])) {
                $comida = Comidas::find($detalle['comida_id']);
                if ($comida) {
                    $cantidad = $detalle['cantidad'] ?? 1;
                    $subtotal += $comida->precio * $cantidad;
                }
            }
        }
        return $subtotal;
    }
}
