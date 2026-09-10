@use(App\Enums\SaleStatus)
@props(['sales'])

<div
    class="flex-1 bg-white dark:bg-gray-800 rounded-xl shadow-xs border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col min-h-0 w-full max-w-full"
    x-data="{ selectedId: null }"
    @keydown.escape.window="selectedId = null"
    @click.outside="selectedId = null"
>
    <div class="overflow-x-auto flex-1 relative w-full max-w-full">
        <table class="w-full table-fixed divide-y divide-gray-100 dark:divide-gray-700 border-separate border-spacing-0">
            <colgroup>
                <col class="w-auto min-w-[260px]">
                <col class="w-32 min-w-[110px]">
                <col class="w-36 min-w-[120px]">
                <col class="w-36 min-w-[120px]">
                <col class="w-32 min-w-[110px]">
                <col class="w-24 min-w-[88px]">
            </colgroup>
            <thead class="bg-gray-50 dark:bg-gray-900/50 sticky top-0 z-10">
            <tr>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 whitespace-nowrap">
                    {{ __('sales.customer') }}
                </th>
                <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 whitespace-nowrap">
                    {{ __('sales.cost') }}
                </th>
                <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 whitespace-nowrap">
                    {{ __('sales.sale_value') }}
                </th>
                <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 whitespace-nowrap">
                    @if($this->status === SaleStatus::Pending->value)
                        {{ __('sales.estimated_profit') }}
                    @else
                        {{ __('sales.profit') }}
                    @endif
                </th>
                <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 whitespace-nowrap">
                    {{ __('sales.date') }}
                </th>
                <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 whitespace-nowrap"></th>
            </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
            @forelse ($sales as $sale)
                <x-table.row
                    wire:key="sale-{{ $sale->id }}"
                    @click="selectedId = {{ $sale->id }}"
                    x-bind:data-selected="selectedId === {{ $sale->id }}"
                >
                    {{-- Customer & Meta --}}
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-900 dark:text-white truncate" title="{{ $sale->customer->name ?? __('sales.customer_placeholder') }}">
                                {{ $sale->customer->name ?? __('sales.customer_placeholder') }}
                            </span>
                            @if($sale->is_gift)
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200 dark:bg-purple-950/40 dark:text-purple-400 dark:border-purple-800/60">
                                    <x-icon name="gift" class="w-3.5 h-3.5" />
                                    {{ __('sales.gift') }}
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                            <span class="tabular-nums font-mono text-gray-400 dark:text-gray-500">#{{ $sale->id }}</span>
                            <span>&middot;</span>
                            <span>{{ $sale->payment_method->label() }}</span>
                            <span>&middot;</span>
                            <span class="tabular-nums">{{ $sale->items->sum('quantity') }} {{ Str::lower(__('sales.quantity')) }}</span>
                            @if(!$this->status)
                                <span>&middot;</span>
                                @if($sale->status === SaleStatus::Paid)
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        {{ __('sales.paid') }}
                                    </span>
                                @elseif($sale->status === SaleStatus::Pending)
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200/60 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        {{ __('sales.pending') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[11px] font-medium bg-red-50 text-red-700 border border-red-200/60 dark:bg-red-950/40 dark:text-red-400 dark:border-red-800/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        {{ __('sales.cancelled_sales') }}
                                    </span>
                                @endif
                            @endif
                        </div>
                    </td>

                    {{-- Cost --}}
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-600 dark:text-gray-300 tabular-nums">
                        R$ {{ number_format($sale->totalCost(), 2, ',', '.') }}
                    </td>

                    {{-- Sale Value --}}
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900 dark:text-white font-medium tabular-nums">
                        R$ {{ number_format($sale->total_amount, 2, ',', '.') }}
                    </td>

                    {{-- Profit --}}
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm {{ $sale->profit() >= 0 ? ($sale->status === SaleStatus::Pending ? 'text-gray-900 dark:text-white' : 'text-emerald-600 dark:text-emerald-400') : 'text-red-600 dark:text-red-400' }} font-semibold tabular-nums">
                        R$ {{ number_format($sale->profit(), 2, ',', '.') }}
                    </td>

                    {{-- Date --}}
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white tabular-nums">
                        <div class="font-medium text-gray-900 dark:text-white">{{ $sale->created_at->format('d/m/Y') }}</div>
                        <div class="text-xs text-gray-400 dark:text-gray-500 font-normal">{{ $sale->created_at->format('H:i') }}</div>
                    </td>

                    {{-- Actions --}}
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" @click.stop>
                        <div class="flex items-center justify-end gap-1">
                            @if($sale->status === SaleStatus::Pending)
                                @can(\App\Enums\Permission::EditSale->value)
                                    <button
                                        type="button"
                                        title="{{ __('sales.mark_as_paid') }}"
                                        aria-label="{{ __('sales.mark_as_paid') }}"
                                        wire:click="confirmMarkAsPaid({{ $sale->id }})"
                                        class="p-1.5 rounded-lg text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-950/50 dark:hover:text-emerald-300 transition-colors duration-150 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/20"
                                    >
                                        <x-icon name="check" class="w-4 h-4 stroke-[2.5]" />
                                    </button>
                                @endcan
                            @endif
                            <button
                                type="button"
                                title="{{ __('sales.details') }}"
                                aria-label="{{ __('sales.details') }}"
                                wire:click="showDetails({{ $sale->id }})"
                                class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:text-gray-400 dark:hover:bg-primary-950/50 dark:hover:text-primary-400 transition-colors duration-150 focus:outline-hidden focus:ring-2 focus:ring-primary-500/20"
                            >
                                <x-icon name="eye" class="w-4 h-4" />
                            </button>
                        </div>
                    </td>
                </x-table.row>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center justify-center max-w-sm mx-auto text-center space-y-3">
                            <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-800/80 flex items-center justify-center text-gray-400 dark:text-gray-500 border border-gray-200/60 dark:border-gray-700">
                                <x-icon name="shopping-cart" class="w-6 h-6" />
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ __('sales.no_sales_found') }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ __('sales.adjust_filters_instruction') }}
                                </p>
                            </div>
                            @can(\App\Enums\Permission::CreateSale->value)
                                <div class="pt-2">
                                    <x-button
                                        primary
                                        xs
                                        icon="plus"
                                        label="{{ __('sidebar.pos') }}"
                                        href="{{ route('sales.create') }}"
                                        class="shadow-xs font-medium"
                                    />
                                </div>
                            @endcan
                        </div>
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
