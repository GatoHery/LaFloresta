@php
    $pedidos = $this->getPedidosPendientes();
@endphp

<div class="space-y-6">
    @if($pedidos->isEmpty())
        <div class="rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 p-8 text-center">
            <p class="text-gray-500 dark:text-gray-400 text-lg">No hay pedidos pendientes</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($pedidos as $pedido)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg hover:shadow-xl transition-shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4 text-white">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h2 class="text-2xl font-bold">Pedido #{{ $pedido->id }}</h2>
                                <p class="text-blue-100 text-sm">{{ $pedido->fecha->format('d/m/Y') }}</p>
                            </div>
                            <span class="inline-block bg-orange-400 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                En Proceso
                            </span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <!-- Total -->
                        <div class="mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">Total</p>
                            <p class="text-3xl font-bold text-green-600 dark:text-green-400">
                                ${{ number_format($pedido->total, 2) }}
                            </p>
                        </div>

                        <!-- Comidas -->
                        <div class="mb-4">
                            <p class="text-gray-600 dark:text-gray-400 text-sm font-semibold mb-2">Comidas ({{ $pedido->detalles->count() }})</p>
                            <div class="space-y-2 max-h-40 overflow-y-auto">
                                @foreach($pedido->detalles as $detalle)
                                    <div class="flex justify-between items-center text-sm bg-gray-50 dark:bg-gray-700 p-2 rounded">
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ $detalle->comida->nombre }}</p>
                                            <p class="text-gray-500 dark:text-gray-400 text-xs">
                                                Cant: {{ $detalle->cantidad }} | 
                                                Mesera: {{ $detalle->usuario->nombre }}
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
                        <div class="mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                            <p class="text-gray-600 dark:text-gray-400 text-sm">Mesas: 
                                <span class="font-semibold text-gray-900 dark:text-white">
                                    {{ $pedido->detalles->pluck('n_mesas')->unique()->implode(', ') }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Footer - Actions -->
                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-700 flex gap-2">
                        <a href="{{ route('filament.admin.resources.pedido-modelos.edit', $pedido->id) }}" 
                           class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded text-center text-sm transition">
                            Editar
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

