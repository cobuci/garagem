@props(['selectedSale'])

<x-modal-card
    wire:model="showDetailsModal"
    title="{{ $selectedSale ? __('sales.sale_details') . ' #' . $selectedSale->id : __('sales.sale_details') }}"
    max-width="2xl"
>
    @if($selectedSale)
        <div class="space-y-5">
            {{-- Status & Timestamp Bar --}}
            <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        {{ __('sales.status') }}:
                    </span>
                    @if($selectedSale->status === \App\Enums\SaleStatus::Paid)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            {{ __('sales.paid') }}
                        </span>
                    @elseif($selectedSale->status === \App\Enums\SaleStatus::Pending)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            {{ __('sales.pending') }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200 dark:bg-red-950/40 dark:text-red-400 dark:border-red-800/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                            {{ __('sales.cancelled_sales') }}
                        </span>
                    @endif

                    @if($selectedSale->is_gift)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200 dark:bg-purple-950/40 dark:text-purple-400 dark:border-purple-800/60">
                            <x-icon name="gift" class="w-3.5 h-3.5" />
                            {{ __('sales.gift') }}
                        </span>
                    @endif
                </div>

                @if($selectedSale->created_at)
                    <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 tabular-nums">
                        <x-icon name="calendar" class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500" />
                        <span>{{ $selectedSale->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                @endif
            </div>

            {{-- Key Information Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 rounded-xl bg-gray-50/75 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-800">
                <div class="col-span-2">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        {{ __('sales.customer') }}
                    </p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white mt-1 truncate" title="{{ $selectedSale->customer->name ?? __('sales.customer_placeholder') }}">
                        {{ $selectedSale->customer->name ?? __('sales.customer_placeholder') }}
                    </p>
                </div>

                <div class="col-span-1">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        {{ __('sales.payment_method') }}
                    </p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                        {{ $selectedSale->payment_method->label() }}
                    </p>
                </div>

                <div class="col-span-1">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        {{ $selectedSale->status === \App\Enums\SaleStatus::Pending ? __('sales.estimated_profit') : __('sales.profit') }}
                    </p>
                    <p class="text-sm font-semibold tabular-nums mt-1 {{ $selectedSale->profit() >= 0 ? ($selectedSale->status === \App\Enums\SaleStatus::Pending ? 'text-gray-900 dark:text-white' : 'text-emerald-600 dark:text-emerald-400') : 'text-red-600 dark:text-red-400' }}">
                        R$ {{ number_format($selectedSale->profit(), 2, ',', '.') }}
                    </p>
                </div>
            </div>

            {{-- Items Table --}}
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        {{ __('sales.items') }} ({{ $selectedSale->items->count() }})
                    </p>
                    <span class="text-xs text-gray-400 dark:text-gray-500 tabular-nums">
                        {{ $selectedSale->items->sum('quantity') }} {{ Str::lower(__('sales.quantity')) }}
                    </span>
                </div>

                <div class="rounded-lg border border-gray-100 dark:border-gray-700 overflow-hidden overflow-x-auto">
                    <table class="w-full text-sm min-w-[500px] sm:min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th scope="col" class="px-3.5 py-2.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ __('sales.product') }}
                                </th>
                                <th scope="col" class="px-3.5 py-2.5 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ __('sales.quantity') }}
                                </th>
                                <th scope="col" class="px-3.5 py-2.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ __('sales.unit_price') }}
                                </th>
                                <th scope="col" class="px-3.5 py-2.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    {{ __('sales.subtotal') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            @foreach($selectedSale->items as $item)
                                <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-3.5 py-2.5 text-gray-900 dark:text-white">
                                        <p class="font-medium text-gray-900 dark:text-white">
                                            {{ $item->product?->name ?? __('sales.product_not_found') }}
                                        </p>
                                        @if($item->product && ($item->product->brand || $item->product->weight))
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                {{ $item->product->brand }}
                                                @if($item->product->brand && $item->product->weight) &middot; @endif
                                                {{ $item->product->weight }}
                                            </p>
                                        @endif
                                    </td>
                                    <td class="px-3.5 py-2.5 text-center text-gray-700 dark:text-gray-300 tabular-nums">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="px-3.5 py-2.5 text-right text-gray-700 dark:text-gray-300 tabular-nums">
                                        R$ {{ number_format($item->unit_price, 2, ',', '.') }}
                                    </td>
                                    <td class="px-3.5 py-2.5 text-right font-medium text-gray-900 dark:text-white tabular-nums">
                                        R$ {{ number_format($item->subtotal, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Summary Breakdown --}}
            <div class="pt-2 border-t border-gray-100 dark:border-gray-700 space-y-2">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    {{ __('sales.summary') }}
                </p>

                <div class="bg-gray-50/60 dark:bg-gray-900/30 rounded-lg p-3.5 border border-gray-100 dark:border-gray-800 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">{{ __('sales.subtotal') }}</span>
                        <span class="text-gray-900 dark:text-white font-medium tabular-nums">
                            R$ {{ number_format($selectedSale->items->sum('subtotal'), 2, ',', '.') }}
                        </span>
                    </div>

                    @if($selectedSale->discount_amount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">{{ __('sales.discount') }}</span>
                            <span class="text-red-600 dark:text-red-400 font-medium tabular-nums">
                                - R$ {{ number_format($selectedSale->discount_amount, 2, ',', '.') }}
                            </span>
                        </div>
                    @endif

                    @if($selectedSale->fee_amount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">
                                {{ $selectedSale->pass_fee_to_customer ? __('sales.fee_addition') : __('sales.fee_deduction') }} ({{ $selectedSale->fee_percentage }}%)
                            </span>
                            <span class="tabular-nums font-medium {{ $selectedSale->pass_fee_to_customer ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $selectedSale->pass_fee_to_customer ? '+' : '-' }} R$ {{ number_format($selectedSale->fee_amount, 2, ',', '.') }}
                            </span>
                        </div>
                    @endif

                    <div class="flex justify-between items-baseline pt-2.5 border-t border-gray-200/70 dark:border-gray-700/80">
                        <span class="text-base font-semibold text-gray-900 dark:text-white">{{ __('sales.total') }}</span>
                        <span class="text-xl font-bold text-sky-600 dark:text-sky-400 tabular-nums">
                            R$ {{ number_format($selectedSale->total_amount, 2, ',', '.') }}
                        </span>
                    </div>

                    @if($selectedSale->fee_amount > 0 && ! $selectedSale->pass_fee_to_customer)
                        <div class="flex justify-between text-xs pt-1 text-gray-500 dark:text-gray-400">
                            <span>{{ __('sales.net_amount') }}</span>
                            <span class="tabular-nums font-medium text-gray-700 dark:text-gray-300">
                                R$ {{ number_format($selectedSale->net_amount, 2, ',', '.') }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <x-slot name="footer">
        <div class="flex flex-col-reverse sm:flex-row justify-between items-stretch sm:items-center w-full gap-3">
            {{-- Destructive Action (Left) --}}
            <div class="flex items-center justify-start">
                @if($selectedSale && $selectedSale->status !== \App\Enums\SaleStatus::Cancelled)
                    @can(\App\Enums\Permission::EditSale->value)
                        <x-button
                            flat
                            negative
                            icon="x-circle"
                            label="{{ __('sales.cancel_sale') }}"
                            wire:click="confirmCancelSale({{ $selectedSale->id }})"
                            spinner="confirmCancelSale"
                            class="w-full sm:w-auto text-xs font-medium"
                        />
                    @endcan
                @endif
            </div>

            {{-- Operational Actions (Right) --}}
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                @if($selectedSale)
                    @if($selectedSale->invoice_status === 'generating')
                        <x-button
                            outline
                            disabled
                            icon="arrow-path"
                            label="{{ __('sales.generating_invoice') }}"
                            class="w-full sm:w-auto bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 font-medium shadow-xs opacity-75"
                        />
                    @elseif($selectedSale->invoice_status === 'failed')
                        <x-button
                            negative
                            outline
                            icon="exclamation-triangle"
                            spinner="downloadInvoicePng"
                            label="{{ __('sales.invoice_failed_retry') }}"
                            wire:click="downloadInvoicePng({{ $selectedSale->id }})"
                            class="w-full sm:w-auto font-medium shadow-xs"
                        />
                    @else
                        <x-button
                            outline
                            icon="receipt-percent"
                            spinner="downloadInvoicePng"
                            label="{{ $selectedSale->invoice_status === 'ready' ? __('sales.download_invoice_png') : __('sales.download_invoice') }}"
                            wire:click="downloadInvoicePng({{ $selectedSale->id }})"
                            class="w-full sm:w-auto bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/60 font-medium shadow-xs transition-all duration-150 active:scale-[0.98]"
                        />
                    @endif
                @endif

                @if($selectedSale && $selectedSale->status === \App\Enums\SaleStatus::Pending)
                    @can(\App\Enums\Permission::EditSale->value)
                        <x-button
                            positive
                            icon="check"
                            label="{{ __('sales.mark_as_paid') }}"
                            wire:click="confirmMarkAsPaid({{ $selectedSale->id }})"
                            spinner="confirmMarkAsPaid"
                            class="w-full sm:w-auto font-semibold shadow-xs hover:shadow transition-all duration-150 active:scale-[0.98]"
                        />
                    @endcan
                @endif
            </div>
        </div>
    </x-slot>
</x-modal-card>
