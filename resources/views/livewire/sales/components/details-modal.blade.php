@props(['selectedSale'])

<x-modal-card wire:model="showDetailsModal" title="{{ __('sales.sale_details') }}" max-width="2xl">
    @if($selectedSale)
        <div class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase">{{ __('sales.customer') }}</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $selectedSale->customer->name ?? __('sales.customer_placeholder') }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase">{{ __('sales.date') }}</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $selectedSale->created_at->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase">{{ __('sales.payment_method') }}</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $selectedSale->payment_method->label() }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase">{{ __('sales.net_amount') }}</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">R$ {{ number_format($selectedSale->net_amount, 2, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase">{{ __('sales.profit') }}</p>
                    <p class="text-sm font-medium {{ $selectedSale->profit() >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">R$ {{ number_format($selectedSale->profit(), 2, ',', '.') }}</p>
                </div>
            </div>

            <div class="border-t border-gray-100 dark:border-gray-700 pt-4 overflow-x-auto">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">{{ __('sales.items') }}</p>
                <table class="w-full text-sm min-w-[500px] sm:min-w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-2 py-1 text-left">{{ __('sales.product') }}</th>
                            <th class="px-2 py-1 text-center">{{ __('sales.quantity') }}</th>
                            <th class="px-2 py-1 text-right">{{ __('sales.unit_price') }}</th>
                            <th class="px-2 py-1 text-right">{{ __('sales.subtotal') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($selectedSale->items as $item)
                            <tr>
                                <td class="px-2 py-2">{{ $item->product->name }}</td>
                                <td class="px-2 py-2 text-center">{{ $item['quantity'] }}</td>
                                <td class="px-2 py-2 text-right">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                <td class="px-2 py-2 text-right">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-100 dark:border-gray-700 pt-4 space-y-1">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">{{ __('sales.summary') }}</p>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">{{ __('sales.subtotal') }}</span>
                    <span class="text-gray-900 dark:text-white font-medium">R$ {{ number_format($selectedSale->items->sum('subtotal'), 2, ',', '.') }}</span>
                </div>
                @if($selectedSale->discount_amount > 0)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">{{ __('sales.discount') }}</span>
                        <span class="text-red-600">- R$ {{ number_format($selectedSale->discount_amount, 2, ',', '.') }}</span>
                    </div>
                @endif
                @if($selectedSale->fee_amount > 0)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">{{ __('sales.fee') }} ({{ $selectedSale->fee_percentage }}%)</span>
                        <span class="{{ $selectedSale->pass_fee_to_customer ? 'text-green-600' : 'text-red-600' }}">
                            {{ $selectedSale->pass_fee_to_customer ? '+' : '-' }} R$ {{ number_format($selectedSale->fee_amount, 2, ',', '.') }}
                        </span>
                    </div>
                @endif
                <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-50 dark:border-gray-800">
                    <span class="text-gray-900 dark:text-white">{{ __('sales.total') }}</span>
                    <span class="text-primary-600">R$ {{ number_format($selectedSale->total_amount, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>
    @endif

    <x-slot name="footer">
        <div class="flex flex-col sm:flex-row justify-between items-center w-full gap-y-4">
            <div class="w-full sm:w-auto flex justify-center sm:justify-start">
                @if($selectedSale && $selectedSale->status !== \App\Enums\SaleStatus::Cancelled)
                    @can(\App\Enums\Permission::EditSale->value)
                        <x-button negative outline label="{{ __('sales.cancel_sale') }}" wire:click="confirmCancelSale({{ $selectedSale->id }})" class="w-full sm:w-auto" />
                    @endcan
                @endif
            </div>
            <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                <x-button flat label="{{ __('sales.close') }}" x-on:click="close" class="order-last sm:order-first" />
                @if($selectedSale)
                    <div class="flex flex-col sm:flex-row gap-4">
                        @if($selectedSale->invoice_status === 'generating')
                            <x-button secondary outline spinner="downloadInvoice" icon="arrow-path" label="{{ __('sales.generating_invoice') }}" class="w-full sm:w-auto" />
                        @elseif($selectedSale->invoice_status === 'failed')
                            <x-button negative outline
                                      icon="exclamation-triangle"
                                      label="{{ __('sales.invoice_failed_retry') }}"
                                      wire:click="downloadInvoice({{ $selectedSale->id }})"
                                      class="w-full sm:w-auto" />
                        @else
                            <div class="relative inline-flex w-full sm:w-auto" x-data="{ open: false }">
                                <x-button secondary outline
                                          icon="arrow-down-tray"
                                          label="{{ $selectedSale->invoice_status === 'ready' ? __('sales.download_invoice_ready') : __('sales.download_invoice') }}"
                                          wire:click="downloadInvoice({{ $selectedSale->id }})"
                                          class="w-full sm:w-auto rounded-r-none border-r-0" />
                                <button
                                    @click="open = !open"
                                    type="button"
                                    class="inline-flex items-center px-2 border border-secondary-300 dark:border-secondary-600 rounded-r-md bg-white dark:bg-secondary-800 text-secondary-700 dark:text-secondary-300 hover:bg-secondary-50 dark:hover:bg-secondary-700 focus:outline-none transition"
                                    aria-haspopup="true"
                                    :aria-expanded="open"
                                >
                                    <x-heroicons::outline.chevron-down class="w-4 h-4" />
                                </button>
                                <div
                                    x-show="open"
                                    @click.outside="open = false"
                                    x-transition
                                    class="absolute right-0 bottom-full mb-1 w-44 rounded-md shadow-lg bg-white dark:bg-secondary-800 border border-secondary-200 dark:border-secondary-600 z-50"
                                >
                                    <button
                                        type="button"
                                        wire:click="downloadInvoice({{ $selectedSale->id }})"
                                        @click="open = false"
                                        class="flex items-center gap-2 w-full px-4 py-2 text-sm text-secondary-700 dark:text-secondary-300 hover:bg-secondary-50 dark:hover:bg-secondary-700 rounded-t-md"
                                    >
                                        <x-heroicons::outline.document class="w-4 h-4" />
                                        {{ __('sales.download_invoice_pdf') }}
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="downloadInvoicePng({{ $selectedSale->id }})"
                                        @click="open = false"
                                        class="flex items-center gap-2 w-full px-4 py-2 text-sm text-secondary-700 dark:text-secondary-300 hover:bg-secondary-50 dark:hover:bg-secondary-700 rounded-b-md"
                                    >
                                        <x-heroicons::outline.photo class="w-4 h-4" />
                                        {{ __('sales.download_invoice_png') }}
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
                @if($selectedSale && $selectedSale->status === \App\Enums\SaleStatus::Pending)
                    @can(\App\Enums\Permission::EditSale->value)
                        <x-button primary label="{{ __('sales.mark_as_paid') }}" wire:click="confirmMarkAsPaid({{ $selectedSale->id }})" class="w-full sm:w-auto" />
                    @endcan
                @endif
            </div>
        </div>
    </x-slot>
</x-modal-card>
