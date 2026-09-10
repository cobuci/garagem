@props([
    'totalSales'         => 0,
    'totalPending'       => 0,
    'totalSynced'        => 0,
    'totalFailed'        => 0,
    'totalPendingAmount' => 0,
    'totalAllAmount'     => 0,
])

<div class="flex-none w-full max-w-full">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ __('mobile_sales.title') }}</h1>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200/80 dark:border-gray-700 tabular-nums">
                    {{ $totalSales }}
                </span>
            </div>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                {{ __('mobile_sales.subtitle') }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            @can(\App\Enums\Permission::CreateSale->value)
                <x-button
                    primary
                    icon="shopping-cart"
                    label="{{ __('mobile_sales.pos_button') }}"
                    href="{{ route('sales.create') }}"
                    class="font-medium shadow-xs"
                />
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
        {{-- Pendentes de Sincronização --}}
        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-100 dark:border-gray-700 shadow-xs">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('mobile_sales.total_pending') }}</p>
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            </div>
            <div class="mt-2 flex items-baseline justify-between">
                <p class="text-xl font-bold text-amber-600 dark:text-amber-400 tabular-nums">
                    R$ {{ number_format($totalPendingAmount, 2, ',', '.') }}
                </p>
                <span class="text-xs text-gray-500 dark:text-gray-400 tabular-nums">
                    {{ __('mobile_sales.pending_sales_count', ['count' => $totalPending]) }}
                </span>
            </div>
        </div>

        {{-- Sincronizadas no PDV --}}
        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-100 dark:border-gray-700 shadow-xs">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('mobile_sales.total_synced') }}</p>
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            </div>
            <div class="mt-2 flex items-baseline justify-between">
                <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">
                    {{ $totalSynced }}
                </p>
                <span class="text-xs text-gray-500 dark:text-gray-400 tabular-nums">
                    {{ __('mobile_sales.synced_sales_count', ['count' => $totalSynced]) }}
                </span>
            </div>
        </div>

        {{-- Volume Total Mobile --}}
        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-100 dark:border-gray-700 shadow-xs">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('mobile_sales.total_amount') }}</p>
                <span class="w-2 h-2 rounded-full bg-sky-500"></span>
            </div>
            <div class="mt-2 flex items-baseline justify-between">
                <p class="text-xl font-bold text-gray-900 dark:text-white tabular-nums">
                    R$ {{ number_format($totalAllAmount, 2, ',', '.') }}
                </p>
                <span class="text-xs text-gray-500 dark:text-gray-400 tabular-nums">
                    {{ __('mobile_sales.registered_sales', ['count' => $totalSales]) }}
                </span>
            </div>
        </div>
    </div>
</div>

