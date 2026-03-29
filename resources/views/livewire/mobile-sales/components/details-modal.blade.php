@props(['selectedMobileSale'])

<x-modal-card wire:model="showDetailsModal" title="{{ __('mobile_sales.sale_details') }}" max-width="2xl">
    @if($selectedMobileSale)
        <div class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase">{{ __('mobile_sales.customer') }}</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ $selectedMobileSale->customer->name ?? $selectedMobileSale->customer_name ?? __('mobile_sales.customer_placeholder') }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase">{{ __('mobile_sales.device_date') }}</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ $selectedMobileSale->device_created_at->format('d/m/Y H:i') }}
                    </p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-xs font-semibold text-gray-500 uppercase">{{ __('mobile_sales.local_id') }}</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white font-mono truncate" title="{{ $selectedMobileSale->local_id }}">
                        {{ $selectedMobileSale->local_id }}
                    </p>
                </div>
            </div>

            <div class="border-t border-gray-100 dark:border-gray-700 pt-4 overflow-x-auto">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">{{ __('mobile_sales.items') }}</p>
                <table class="w-full text-sm min-w-120 sm:min-w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-2 py-1 text-left">{{ __('mobile_sales.product') }}</th>
                            <th class="px-2 py-1 text-center">{{ __('mobile_sales.quantity') }}</th>
                            <th class="px-2 py-1 text-right">{{ __('mobile_sales.unit_price') }}</th>
                            <th class="px-2 py-1 text-right">{{ __('mobile_sales.subtotal') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($selectedMobileSale->items as $item)
                            <tr>
                                <td class="px-2 py-2">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $item->product->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        {{ $item->product->brand }}
                                        @if($item->product->weight)
                                            &middot; {{ $item->product->weight }}
                                        @endif
                                    </p>
                                </td>
                                <td class="px-2 py-2 text-center">{{ $item->quantity }}</td>
                                <td class="px-2 py-2 text-right">R$ {{ number_format($item->unit_price_cents / 100, 2, ',', '.') }}</td>
                                <td class="px-2 py-2 text-right">R$ {{ number_format($item->subtotal_cents / 100, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-100 dark:border-gray-700 pt-4">
                <div class="flex justify-between text-lg font-bold">
                    <span class="text-gray-900 dark:text-white">{{ __('mobile_sales.total') }}</span>
                    <span class="text-primary-600">R$ {{ number_format($selectedMobileSale->total_amount_cents / 100, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>
    @endif

    <x-slot name="footer">
        <div class="flex justify-end w-full">
            <x-button flat label="{{ __('mobile_sales.close') }}" x-on:click="close" />
        </div>
    </x-slot>
</x-modal-card>
