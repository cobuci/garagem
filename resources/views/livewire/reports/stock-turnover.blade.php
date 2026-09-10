<div wire:key="stock-turnover" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 lg:col-span-2">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('reports.stock_turnover.title') }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                {{ trans_choice('reports.stock_turnover.count_label', $alertCount, ['count' => $alertCount]) }}
            </p>
        </div>
    </div>

    @if ($items->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-14 h-14 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center mb-4">
                <x-icon name="check-circle" class="w-7 h-7 text-green-500" />
            </div>
            <p class="text-base font-semibold text-gray-700 dark:text-gray-200">{{ __('reports.stock_turnover.empty') }}</p>
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">{{ __('reports.stock_turnover.empty_subtitle') }}</p>
        </div>
    @else
        <div class="overflow-x-auto" wire:loading.delay.class="opacity-60 transition-opacity duration-200">
            <table class="w-full text-sm text-left table-fixed">
                <colgroup>
                    <col class="w-auto min-w-0">
                    <col class="w-32">
                    <col class="w-28">
                    <col class="w-28">
                    <col class="w-32">
                    <col class="w-28">
                </colgroup>
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        @php
                            $thBase = 'pb-3 pr-4 text-xs font-semibold uppercase tracking-wide select-none whitespace-nowrap';
                            $thSortable = $thBase . ' cursor-pointer hover:text-gray-700 dark:hover:text-gray-200 transition-colors group/th focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1 dark:focus-visible:ring-offset-gray-800 rounded';
                            $thActive = 'text-gray-800 dark:text-gray-100';
                            $thInactive = 'text-gray-500 dark:text-gray-400';

                            $icon = function (string $field) use ($sortField, $sortDirection): string {
                                if ($sortField !== $field) {
                                    return '<svg class="inline w-3 h-3 ml-0.5 opacity-30 group-hover/th:opacity-60 transition-opacity" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 15l5 5 5-5M7 9l5-5 5 5"/></svg>';
                                }
                                if ($sortDirection === 'asc') {
                                    return '<svg class="inline w-3 h-3 ml-0.5 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M7 9l5-5 5 5"/></svg>';
                                }
                                return '<svg class="inline w-3 h-3 ml-0.5 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M7 15l5 5 5-5"/></svg>';
                            };

                            $sortAria = function (string $field) use ($sortField, $sortDirection): string {
                                if ($sortField !== $field) return 'none';
                                return $sortDirection === 'asc' ? 'ascending' : 'descending';
                            };
                        @endphp

                        <th wire:click="sort('product')"
                            wire:keydown.enter="sort('product')"
                            tabindex="0"
                            role="button"
                            aria-sort="{{ $sortAria('product') }}"
                            class="{{ $thSortable }} {{ $sortField === 'product' ? $thActive : $thInactive }}">
                            {{ __('reports.stock_turnover.columns.product') }}{!! $icon('product') !!}
                        </th>
                        <th wire:click="sort('category')"
                            wire:keydown.enter="sort('category')"
                            tabindex="0"
                            role="button"
                            aria-sort="{{ $sortAria('category') }}"
                            class="{{ $thSortable }} {{ $sortField === 'category' ? $thActive : $thInactive }}">
                            {{ __('reports.stock_turnover.columns.category') }}{!! $icon('category') !!}
                        </th>
                        <th wire:click="sort('stock')"
                            wire:keydown.enter="sort('stock')"
                            tabindex="0"
                            role="button"
                            aria-sort="{{ $sortAria('stock') }}"
                            class="{{ $thSortable }} {{ $sortField === 'stock' ? $thActive : $thInactive }} text-right">
                            {{ __('reports.stock_turnover.columns.stock') }}{!! $icon('stock') !!}
                        </th>
                        <th wire:click="sort('units_per_day')"
                            wire:keydown.enter="sort('units_per_day')"
                            tabindex="0"
                            role="button"
                            aria-sort="{{ $sortAria('units_per_day') }}"
                            class="{{ $thSortable }} {{ $sortField === 'units_per_day' ? $thActive : $thInactive }} text-right">
                            {{ __('reports.stock_turnover.columns.units_per_day') }}{!! $icon('units_per_day') !!}
                        </th>
                        <th wire:click="sort('days_remaining')"
                            wire:keydown.enter="sort('days_remaining')"
                            tabindex="0"
                            role="button"
                            aria-sort="{{ $sortAria('days_remaining') }}"
                            class="{{ $thSortable }} {{ $sortField === 'days_remaining' ? $thActive : $thInactive }} text-right">
                            {{ __('reports.stock_turnover.columns.days_remaining') }}{!! $icon('days_remaining') !!}
                        </th>
                        <th class="{{ $thBase }} {{ $thInactive }} text-center">
                            {{ __('reports.stock_turnover.columns.status') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                    @foreach ($items as $item)
                        @php
                            $status = \App\Livewire\Reports\StockTurnover::stockStatus((float) $item->days_remaining);
                            $statusBadgeClass = match ($status) {
                                'critical' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                                'low'      => 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300',
                                default    => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                            };
                        @endphp
                        <tr class="group hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="py-3 pr-4 min-w-0">
                                <span class="block font-medium text-gray-900 dark:text-white" title="{{ $item->name }}">
                                    {{ $item->name }}
                                </span>
                                @if($item->brand || $item->weight)
                                    <span class="text-xs text-gray-400 dark:text-gray-500 font-medium uppercase tracking-wider">
                                        {{ $item->brand }}{{ ($item->brand && $item->weight) ? ' • ' : '' }}{{ $item->weight }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 pr-4 text-gray-600 dark:text-gray-300 truncate">
                                {{ $item->category_name ?? '—' }}
                            </td>
                            <td class="py-3 pr-4 text-right text-gray-600 dark:text-gray-300 tabular-nums">
                                {{ number_format($item->stock_quantity) }}
                            </td>
                            <td class="py-3 pr-4 text-right text-gray-600 dark:text-gray-300 tabular-nums">
                                {{ number_format($item->units_per_day, 2) }}
                            </td>
                            <td class="py-3 pr-4 text-right font-semibold tabular-nums text-gray-900 dark:text-white">
                                {{ number_format($item->days_remaining, 1) }}
                                <span class="text-xs font-normal text-gray-400">{{ __('reports.stock_turnover.days_label') }}</span>
                            </td>
                            <td class="py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusBadgeClass }}">
                                    {{ __('reports.stock_turnover.status.' . $status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($items->hasPages())
            <div class="mt-4 border-t border-gray-100 dark:border-gray-700 pt-4">
                {{ $items->links() }}
            </div>
        @endif
    @endif
</div>
