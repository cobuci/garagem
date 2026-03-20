@props(['sales'])

<div class="flex-1 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col min-h-0 w-full max-w-full">
    <div class="overflow-x-auto flex-1 relative w-full max-w-full">
        <table class="w-full divide-y divide-gray-100 dark:divide-gray-700 border-separate border-spacing-0">
            <thead class="bg-gray-50 dark:bg-gray-900/50 sticky top-0 z-10">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 whitespace-nowrap">{{ __('sales.customer') }}</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 whitespace-nowrap">{{ __('sales.cost') }}</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 whitespace-nowrap">{{ __('sales.sale_value') }}</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 whitespace-nowrap">
                    @if($this->status === \App\Enums\SaleStatus::Pending->value)
                        {{ __('sales.estimated_profit') }}
                    @else
                        {{ __('sales.profit') }}
                    @endif
                </th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 whitespace-nowrap">{{ __('sales.date') }}</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 whitespace-nowrap"></th>
            </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
            @forelse ($sales as $sale)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                        {{ $sale->customer->name ?? __('sales.customer_placeholder') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                        R$ {{ number_format($sale->totalCost(), 2, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-medium">
                        R$ {{ number_format($sale->total_amount, 2, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm {{ $sale->profit() >= 0 ? ($sale->status === \App\Enums\SaleStatus::Pending ? 'text-gray-900 dark:text-white' : 'text-green-600 dark:text-green-400') : 'text-red-600 dark:text-red-400' }} font-medium">
                        R$ {{ number_format($sale->profit(), 2, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                        {{ $sale->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 text-right flex justify-end gap-2">
                        @if($sale->status === \App\Enums\SaleStatus::Pending)
                            <x-button
                                xs
                                flat
                                primary
                                icon="check"
                                label="{{ __('sales.mark_as_paid') }}"
                                wire:click="confirmMarkAsPaid({{ $sale->id }})"
                            />
                        @endif
                        <x-button
                            xs
                            flat
                            icon="eye"
                            label="{{ __('sales.details') }}"
                            wire:click="showDetails({{ $sale->id }})"
                        />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                        {{ __('sales.no_sales_found') }}
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if ($sales->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
            {{ $sales->links() }}
        </div>
    @endif
</div>
