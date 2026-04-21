@php
    $detalles = $getState() ?? [];
@endphp

<div class="space-y-1">
    @forelse($detalles as $detalle)
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center rounded-md bg-blue-50 dark:bg-blue-900/20 px-2 py-1 text-xs font-medium text-blue-700 dark:text-blue-400">
                {{ $detalle->comida->nombre }} (x{{ $detalle->cantidad }})
            </span>
        </div>
    @empty
        <span class="text-gray-500 dark:text-gray-400 text-xs">Sin comidas</span>
    @endforelse
</div>
