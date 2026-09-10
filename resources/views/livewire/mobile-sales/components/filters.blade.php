@props([
    'status',
    'search'       => '',
    'totalSales'   => 0,
    'totalPending' => 0,
    'totalSynced'  => 0,
    'totalFailed'  => 0,
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
            <span>{{ __('mobile_sales.pending_sales') }}</span>
            @if($status === 'pending')
                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-semibold tabular-nums bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                    {{ $totalPending }}
                </span>
            @else
                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-semibold tabular-nums bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    {{ $totalPending }}
                </span>
            @endif
        </button>

        {{-- Sincronizadas --}}
        <button
            type="button"
            wire:click="filterByStatus('synced')"
            class="group py-3 px-3 border-b-2 font-medium text-sm whitespace-nowrap transition-colors duration-150 flex items-center gap-2 {{ $status === 'synced' ? 'border-primary-600 text-primary-600 dark:text-primary-400 dark:border-primary-500' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
        >
            <x-icon name="check-circle" class="w-4 h-4 {{ $status === 'synced' ? 'text-emerald-500' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-500' }}"/>
            <span>{{ __('mobile_sales.synced_sales') }}</span>
            @if($status === 'synced')
                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-semibold tabular-nums bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                    {{ $totalSynced }}
                </span>
            @else
                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-semibold tabular-nums bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    {{ $totalSynced }}
                </span>
            @endif
        </button>

        {{-- Com Falha (se houver ou se selecionado) --}}
        @if($totalFailed > 0 || $status === 'failed')
            <button
                type="button"
                wire:click="filterByStatus('failed')"
                class="group py-3 px-3 border-b-2 font-medium text-sm whitespace-nowrap transition-colors duration-150 flex items-center gap-2 {{ $status === 'failed' ? 'border-primary-600 text-primary-600 dark:text-primary-400 dark:border-primary-500' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
            >
                <x-icon name="exclamation-circle" class="w-4 h-4 {{ $status === 'failed' ? 'text-red-500' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-500' }}"/>
                <span>{{ __('mobile_sales.failed_sales') }}</span>
                @if($status === 'failed')
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-semibold tabular-nums bg-red-100 text-red-800 dark:bg-red-950/60 dark:text-red-300">
                        {{ $totalFailed }}
                    </span>
                @else
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-semibold tabular-nums bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                        {{ $totalFailed }}
                    </span>
                @endif
            </button>
        @endif

        {{-- Todas as Vendas --}}
        <button
            type="button"
            wire:click="filterByStatus(null)"
            class="group py-3 px-3 border-b-2 font-medium text-sm whitespace-nowrap transition-colors duration-150 flex items-center gap-2 {{ $status === null ? 'border-primary-600 text-primary-600 dark:text-primary-400 dark:border-primary-500' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
        >
            <x-icon name="list-bullet" class="w-4 h-4 {{ $status === null ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-500' }}"/>
            <span>{{ __('mobile_sales.all_sales') }}</span>
            @if($status === null)
                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-semibold tabular-nums bg-sky-100 text-sky-800 dark:bg-sky-950/60 dark:text-sky-300">
                    {{ $totalSales }}
                </span>
            @else
                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-semibold tabular-nums bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    {{ $totalSales }}
                </span>
            @endif
        </button>
    </div>

    {{-- Campo de Busca --}}
    <div class="w-full sm:w-72 pb-2 sm:pb-2">
        <x-input
            icon="magnifying-glass"
            wire:model.live.debounce.300ms="search"
            placeholder="{{ __('mobile_sales.search_placeholder') }}"
            clearable
        />
    </div>
</div>
