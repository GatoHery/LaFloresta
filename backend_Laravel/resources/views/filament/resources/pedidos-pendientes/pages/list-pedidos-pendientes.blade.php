<x-filament-widgets::widget>
    <x-slot name="heading">
        Pedidos Pendientes
    </x-slot>

    @if($this->pedidos->isEmpty())
        <div class="rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 p-8 text-center">
            <p class="text-gray-500 dark:text-gray-400 text-lg">No hay pedidos pendientes</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
            @foreach($this->pedidos as $pedido)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg hover:shadow-xl transition-shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4 text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-xl font-bold">Pedido #{{ $pedido->id }}</h3>
                                <p class="text-blue-100 text-sm">{{ $pedido->fecha->format('d/m/Y') }}</p>
                            </div>
                            <span class="inline-block bg-orange-400 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                En Proceso
                            </span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-4 space-y-4">
                        <!-- Total -->
                        <div class="pb-4 border-b border-gray-200 dark:border-gray-700">
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Total</p>
                            <p class="text-3xl font-bold text-green-600 dark:text-green-400">
                                ${{ number_format($pedido->total, 2) }}
                            </p>
                        </div>

                        <!-- Comidas -->
                        <div>
                            <p class="text-gray-600 dark:text-gray-400 text-sm font-semibold mb-2">
                                Comidas ({{ $pedido->detalles->count() }})
                            </p>
                            <div class="space-y-2 max-h-40 overflow-y-auto">
                                @foreach($pedido->detalles as $detalle)
                                    <div class="flex justify-between text-sm bg-gray-50 dark:bg-gray-700 p-2 rounded">
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-900 dark:text-white">
                                                {{ $detalle->comida->nombre }}
                                            </p>
                                            <p class="text-gray-500 dark:text-gray-400 text-xs">
                                                {{ $detalle->usuario->nombre }} | x{{ $detalle->cantidad }}
                                            </p>
                                        </div>
                                        <p class="font-semibold text-gray-700 dark:text-gray-300">
                                            ${{ number_format($detalle->comida->precio * $detalle->cantidad, 2) }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Mesas -->
                        <div class="pb-4 border-b border-gray-200 dark:border-gray-700">
                            <p class="text-gray-600 dark:text-gray-400 text-sm">
                                <strong>Mesas:</strong> {{ $pedido->detalles->pluck('n_mesas')->unique()->implode(', ') }}
                            </p>
                        </div>
                    </div>

                    <!-- Footer - Actions -->
                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('filament.admin.resources.pedido-modelos.edit', $pedido->id) }}" 
                           class="inline-block w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded text-center text-sm transition">
                            📝 Editar Pedido
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-filament-widgets::widget>
