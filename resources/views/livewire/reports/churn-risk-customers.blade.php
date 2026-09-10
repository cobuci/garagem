<div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 lg:col-span-2">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('reports.churn_risk.title') }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                {{ trans_choice('reports.churn_risk.at_risk_count', $atRiskCount, ['count' => $atRiskCount]) }}
            </p>
        </div>
    </div>

    @if ($customers->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-14 h-14 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center mb-4">
                <x-icon name="check-circle" class="w-7 h-7 text-green-500" />
            </div>
            <p class="text-base font-semibold text-gray-700 dark:text-gray-200">{{ __('reports.churn_risk.empty') }}</p>
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">{{ __('reports.churn_risk.empty_subtitle') }}</p>
        </div>
    @endif
    @if (! $customers->isEmpty())
        <div class="overflow-x-auto" wire:loading.delay.class="opacity-60 transition-opacity duration-200">
            <table class="w-full text-sm text-left table-fixed">
                    <colgroup>
                        <col class="w-auto min-w-0">
                        <col class="w-24">
                        <col class="w-32 hidden sm:table-column">
                        <col class="w-36">
                        <col class="w-24">
                        <col class="w-32 hidden md:table-column">
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

                        <th wire:click="sort('name')"
                            wire:keydown.enter="sort('name')"
                            tabindex="0"
                            role="button"
                            aria-sort="{{ $sortAria('name') }}"
                            class="{{ $thSortable }} {{ $sortField === 'name' ? $thActive : $thInactive }}">
                            {{ __('reports.churn_risk.columns.name') }}{!! $icon('name') !!}
                        </th>
                        <th wire:click="sort('total_purchases')"
                            wire:keydown.enter="sort('total_purchases')"
                            tabindex="0"
                            role="button"
                            aria-sort="{{ $sortAria('total_purchases') }}"
                            class="{{ $thSortable }} {{ $sortField === 'total_purchases' ? $thActive : $thInactive }} text-right">
                            {{ __('reports.churn_risk.columns.purchases') }}{!! $icon('total_purchases') !!}
                        </th>
                        <th wire:click="sort('avg_interval')"
                            wire:keydown.enter="sort('avg_interval')"
                            tabindex="0"
                            role="button"
                            aria-sort="{{ $sortAria('avg_interval') }}"
                            class="{{ $thSortable }} {{ $sortField === 'avg_interval' ? $thActive : $thInactive }} text-right hidden sm:table-cell">
                            {{ __('reports.churn_risk.columns.avg_interval') }}{!! $icon('avg_interval') !!}
                        </th>
                        <th wire:click="sort('days_since_last')"
                            wire:keydown.enter="sort('days_since_last')"
                            tabindex="0"
                            role="button"
                            aria-sort="{{ $sortAria('days_since_last') }}"
                            class="{{ $thSortable }} {{ $sortField === 'days_since_last' ? $thActive : $thInactive }} text-right">
                            {{ __('reports.churn_risk.columns.days_since') }}{!! $icon('days_since_last') !!}
                        </th>
                        <th wire:click="sort('urgency')"
                            wire:keydown.enter="sort('urgency')"
                            tabindex="0"
                            role="button"
                            aria-sort="{{ $sortAria('urgency') }}"
                            class="{{ $thSortable }} {{ $sortField === 'urgency' ? $thActive : $thInactive }} text-center">
                            {{ __('reports.churn_risk.columns.urgency') }}{!! $icon('urgency') !!}
                        </th>
                        <th wire:click="sort('total_spent')"
                            wire:keydown.enter="sort('total_spent')"
                            tabindex="0"
                            role="button"
                            aria-sort="{{ $sortAria('total_spent') }}"
                            class="{{ $thSortable }} {{ $sortField === 'total_spent' ? $thActive : $thInactive }} text-right hidden md:table-cell">
                            {{ __('reports.churn_risk.columns.total_spent') }}{!! $icon('total_spent') !!}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                    @foreach ($customers as $customer)
                        @php
                            $urgency = \App\Livewire\Reports\ChurnRiskCustomers::urgencyLevel(
                                (float) $customer->days_since_last,
                                (float) $customer->avg_interval_days
                            );
                            $urgencyTextClass = match ($urgency) {
                                'critical' => 'text-red-600 dark:text-red-400',
                                'high'     => 'text-orange-500 dark:text-orange-400',
                                default    => 'text-amber-700 dark:text-amber-300',
                            };
                            $urgencyBadgeClass = match ($urgency) {
                                'critical' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                                'high'     => 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300',
                                default    => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
                            };
                        @endphp
                        <tr class="group hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="py-3 pr-4 min-w-0">
                                <a href="{{ route('customers.show', $customer->id) }}"
                                   class="block truncate font-medium text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                                   title="{{ $customer->name }}">
                                    {{ $customer->name }}
                                </a>
                            </td>
                            <td class="py-3 pr-4 text-right text-gray-600 dark:text-gray-300 tabular-nums">
                                {{ number_format($customer->total_purchases) }}
                            </td>
                            <td class="py-3 pr-4 text-right text-gray-600 dark:text-gray-300 tabular-nums hidden sm:table-cell">
                                {{ number_format($customer->avg_interval_days, 1) }}
                                <span class="text-xs text-gray-400">{{ __('reports.churn_risk.days') }}</span>
                            </td>
                            <td class="py-3 pr-4 text-right font-semibold tabular-nums {{ $urgencyTextClass }}">
                                {{ number_format($customer->days_since_last) }}
                                <span class="text-xs font-normal text-gray-400">{{ __('reports.churn_risk.days') }}</span>
                            </td>
                            <td class="py-3 pr-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $urgencyBadgeClass }}">
                                    {{ __('reports.churn_risk.urgency.' . $urgency) }}
                                </span>
                            </td>
                            <td class="py-3 text-right text-gray-600 dark:text-gray-300 tabular-nums hidden md:table-cell">
                                R$ {{ number_format($customer->total_spent / 100, 2, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($customers->hasPages())
            <div class="mt-4 border-t border-gray-100 dark:border-gray-700 pt-4">
                {{ $customers->links() }}
            </div>
        @endif
    @endif
</div>
