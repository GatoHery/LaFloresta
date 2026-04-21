<x-filament::page>
    <div class="space-y-6">
        @if(count($this->pedidos) === 0)
            <div class="rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">No hay pedidos pendientes</p>
                <p class="text-gray-400 dark:text-gray-500 text-sm mt-2">Todos los pedidos han sido finalizados</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($this->pedidos as $pedido)
                    <div class="group relative bg-white dark:bg-slate-800 rounded-xl shadow-md hover:shadow-xl transition-all duration-200 overflow-hidden border border-gray-100 dark:border-slate-700">
                        <!-- Header con gradiente -->
                        <div class="relative h-24 bg-gradient-to-br from-blue-500 to-blue-700 p-4 text-white overflow-hidden">
                            <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-white opacity-5 rounded-full"></div>
                            <div class="relative z-10">
                                <h3 class="text-2xl font-bold">Pedido #{{ $pedido->id }}</h3>
                                <p class="text-blue-100 text-sm">{{ $pedido->fecha->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        <!-- Body -->
                        <div class="p-5 space-y-4">
                            <!-- Total destacado -->
                            <div class="flex items-center justify-between bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-lg p-4 border border-green-100 dark:border-green-800/30">
                                <span class="text-gray-600 dark:text-gray-300 font-medium">Total</span>
                                <span class="text-2xl font-bold text-green-600 dark:text-green-400">${{ number_format($pedido->total, 2) }}</span>
                            </div>

                            <!-- Comidas -->
                            <div>
                                <p class="text-gray-700 dark:text-gray-300 font-semibold text-sm mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.5 1.5H3.75A2.25 2.25 0 001.5 3.75v12.5A2.25 2.25 0 003.75 18.5h12.5a2.25 2.25 0 002.25-2.25V9.5m-15-4h12m-12 4v8m12-8v4"></path>
                                    </svg>
                                    Comidas ({{ $pedido->detalles->count() }})
                                </p>
                                <div class="space-y-2 max-h-48 overflow-y-auto pr-2">
                                    @foreach($pedido->detalles as $detalle)
                                        <div class="flex justify-between items-start p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg border border-gray-100 dark:border-slate-600/50 hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                                            <div class="flex-1">
                                                <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ $detalle->comida->nombre }}</p>
                                                <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">
                                                    {{ $detalle->usuario->nombre }} • x{{ $detalle->cantidad }}
                                                </p>
                                            </div>
                                            <p class="font-bold text-gray-900 dark:text-white ml-2">
                                                ${{ number_format($detalle->comida->precio * $detalle->cantidad, 2) }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Mesas -->
                            <div class="flex items-center pt-2 border-t border-gray-200 dark:border-slate-700">
                                <svg class="w-4 h-4 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM15 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2h-2zM5 13a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5z"></path>
                                </svg>
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    <strong>Mesas:</strong> {{ $pedido->detalles->pluck('n_mesas')->unique()->implode(', ') }}
                                </span>
                            </div>
                        </div>

                        <!-- Footer con acciones -->
                        <div class="px-5 py-3 bg-gray-50 dark:bg-slate-700/50 border-t border-gray-100 dark:border-slate-700 flex gap-2">
                            <a href="{{ route('filament.admin.resources.pedido-modelos.edit', $pedido->id) }}" 
                               class="flex-1 inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-3 rounded-lg transition duration-200 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Editar
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-filament::page>
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
    </div>
</x-filament::page>
