@props([
    'totalPendingAmount' => 0,
    'pendingCount' => 0,
    'totalPaidAmount' => 0,
    'paidCount' => 0,
    'totalAllAmount' => 0,
    'totalSalesCount' => 0,
])

<div class="flex-none w-full max-w-full">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ __('sales.history_title') }}</h1>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200/80 dark:border-gray-700 tabular-nums">
                    {{ $totalSalesCount }}
                </span>
            </div>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                {{ __('sales.history_subtitle') }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            @can(\App\Enums\Permission::CreateSale->value)
                <x-button
                    primary
                    icon="plus"
                    label="{{ __('sidebar.pos') }}"
                    href="{{ route('sales.create') }}"
                    class="font-medium shadow-xs"
                />
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
        {{-- Total Pendente --}}
        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-100 dark:border-gray-700 shadow-xs">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('sales.total_pending') }}</p>
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            </div>
            <div class="mt-2 flex items-baseline justify-between">
                <p class="text-xl font-bold text-amber-600 dark:text-amber-400 tabular-nums">
                    R$ {{ number_format($totalPendingAmount, 2, ',', '.') }}
                </p>
                <span class="text-xs text-gray-500 dark:text-gray-400 tabular-nums">
                    {{ $pendingCount }} {{ Str::lower(__('sales.pending_sales')) }}
                </span>
            </div>
        </div>

        {{-- Total Pago --}}
        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-100 dark:border-gray-700 shadow-xs">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('sales.paid_sales') }}</p>
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            </div>
            <div class="mt-2 flex items-baseline justify-between">
                <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400 tabular-nums">
                    R$ {{ number_format($totalPaidAmount, 2, ',', '.') }}
                </p>
                <span class="text-xs text-gray-500 dark:text-gray-400 tabular-nums">
                    {{ $paidCount }} {{ Str::lower(__('sales.paid_sales')) }}
                </span>
            </div>
        </div>

        {{-- Total Geral --}}
        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-100 dark:border-gray-700 shadow-xs">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('sales.all_sales') }}</p>
                <span class="w-2 h-2 rounded-full bg-sky-500"></span>
            </div>
            <div class="mt-2 flex items-baseline justify-between">
                <p class="text-xl font-bold text-gray-900 dark:text-white tabular-nums">
                    R$ {{ number_format($totalAllAmount, 2, ',', '.') }}
                </p>
                <span class="text-xs text-gray-500 dark:text-gray-400 tabular-nums">
                    {{ $totalSalesCount }} {{ Str::lower(__('sales.history_title')) }}
                </span>
            </div>
        </div>
    </div>
</div>
