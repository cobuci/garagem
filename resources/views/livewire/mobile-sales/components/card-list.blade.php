@props(['mobileSales'])

<div class="flex-1 flex flex-col min-h-0 w-full max-w-full gap-3">
    @forelse ($mobileSales as $sale)
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm p-4 flex flex-col gap-3">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                    {{ $sale->customer->name ?? $sale->customer_name ?? __('mobile_sales.customer_placeholder') }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ $sale->device_created_at->format('d/m/Y H:i') }}
                </p>
            </div>

            <div class="flex items-center justify-between gap-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('mobile_sales.total') }}</p>
                    <p class="text-base font-bold text-gray-900 dark:text-white">
                        R$ {{ number_format($sale->total_amount_cents / 100, 2, ',', '.') }}
                    </p>
                </div>
                <x-button
                    xs
                    flat
                    icon="eye"
                    label="{{ __('mobile_sales.details') }}"
                    wire:click="showDetails({{ $sale->id }})"
                />
            </div>
        </div>
    @empty
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm p-10 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('mobile_sales.no_sales_found') }}</p>
        </div>
    @endforelse

    @if ($mobileSales->hasPages())
        <div class="pt-2">
            {{ $mobileSales->links() }}
        </div>
    @endif
</div>
