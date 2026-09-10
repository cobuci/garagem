@props([
    'status',
    'pendingCount' => 0,
    'paidCount' => 0,
    'cancelledCount' => 0,
    'allCount' => 0,
])

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200 dark:border-gray-700 pb-2 sm:pb-0">
    <div class="flex items-center overflow-x-auto gap-1 sm:gap-2 -mb-px">
        {{-- Pendentes --}}
        <button
            type="button"
            wire:click="filterByStatus('pending')"
            class="group py-3 px-3 border-b-2 font-medium text-sm whitespace-nowrap transition-colors duration-150 flex items-center gap-2 {{ $status === 'pending' ? 'border-primary-600 text-primary-600 dark:text-primary-400 dark:border-primary-500' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
        >
            <x-icon name="clock" class="w-4 h-4 {{ $status === 'pending' ? 'text-amber-500' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-500' }}"/>
            <span>{{ __('sales.pending_sales') }}</span>
            @if($status === 'pending')
                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-semibold tabular-nums bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                    {{ $pendingCount }}
                </span>
            @else
                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-semibold tabular-nums bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    {{ $pendingCount }}
                </span>
            @endif
        </button>

        {{-- Pagas --}}
        <button
            type="button"
            wire:click="filterByStatus('paid')"
            class="group py-3 px-3 border-b-2 font-medium text-sm whitespace-nowrap transition-colors duration-150 flex items-center gap-2 {{ $status === 'paid' ? 'border-primary-600 text-primary-600 dark:text-primary-400 dark:border-primary-500' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
        >
            <x-icon name="check-circle" class="w-4 h-4 {{ $status === 'paid' ? 'text-emerald-500' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-500' }}"/>
            <span>{{ __('sales.paid_sales') }}</span>
            @if($status === 'paid')
                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-semibold tabular-nums bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                    {{ $paidCount }}
                </span>
            @else
                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-semibold tabular-nums bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    {{ $paidCount }}
                </span>
            @endif
        </button>

        {{-- Canceladas --}}
        <button
            type="button"
            wire:click="filterByStatus('cancelled')"
            class="group py-3 px-3 border-b-2 font-medium text-sm whitespace-nowrap transition-colors duration-150 flex items-center gap-2 {{ $status === 'cancelled' ? 'border-primary-600 text-primary-600 dark:text-primary-400 dark:border-primary-500' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
        >
            <x-icon name="x-circle" class="w-4 h-4 {{ $status === 'cancelled' ? 'text-red-500' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-500' }}"/>
            <span>{{ __('sales.cancelled_sales') }}</span>
            @if($status === 'cancelled')
                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-semibold tabular-nums bg-red-100 text-red-800 dark:bg-red-950/60 dark:text-red-300">
                    {{ $cancelledCount }}
                </span>
            @else
                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-semibold tabular-nums bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    {{ $cancelledCount }}
                </span>
            @endif
        </button>

        {{-- Todas as Vendas --}}
        <button
            type="button"
            wire:click="filterByStatus(null)"
            class="group py-3 px-3 border-b-2 font-medium text-sm whitespace-nowrap transition-colors duration-150 flex items-center gap-2 {{ $status === null ? 'border-primary-600 text-primary-600 dark:text-primary-400 dark:border-primary-500' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
        >
            <x-icon name="list-bullet" class="w-4 h-4 {{ $status === null ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-500' }}"/>
            <span>{{ __('sales.all_sales') }}</span>
            @if($status === null)
                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-semibold tabular-nums bg-sky-100 text-sky-800 dark:bg-sky-950/60 dark:text-sky-300">
                    {{ $allCount }}
                </span>
            @else
                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-semibold tabular-nums bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    {{ $allCount }}
                </span>
            @endif
        </button>
    </div>

    {{-- Campo de Busca --}}
    <div class="w-full sm:w-64 pb-2 sm:pb-2">
        <x-input
            icon="magnifying-glass"
            wire:model.live.debounce.300ms="search"
            placeholder="{{ __('sales.search_placeholder_sales') }}"
            clearable
        />
    </div>
</div>
