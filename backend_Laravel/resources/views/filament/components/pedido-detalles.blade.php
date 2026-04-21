@php
    $detalles = $getState();
    $total = 0;
@endphp

<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-900">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Comida</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Precio</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cantidad</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Subtotal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Mesero</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Mesas</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-950 divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($detalles as $detalle)
                @php
                    $subtotal = $detalle->comida->precio * $detalle->cantidad;
                    $total += $subtotal;
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-900">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                        {{ $detalle->comida->nombre }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        ${{ number_format($detalle->comida->precio, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $detalle->cantidad }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        ${{ number_format($subtotal, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $detalle->usuario->nombre }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $detalle->n_mesas }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="bg-gray-50 dark:bg-gray-900">
            <tr>
                <td colspan="3" class="px-6 py-4 text-right text-sm font-bold text-gray-900 dark:text-white">Total:</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                    ${{ number_format($total, 2) }}
                </td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
</div>

@if(empty($detalles) || count($detalles) === 0)
    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
        No hay comidas asociadas a este pedido
    </div>
@endif
