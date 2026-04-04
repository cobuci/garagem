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
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <th class="pb-3 pr-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('reports.churn_risk.columns.name') }}</th>
                        <th class="pb-3 pr-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide text-right">{{ __('reports.churn_risk.columns.purchases') }}</th>
                        <th class="pb-3 pr-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide text-right hidden sm:table-cell">{{ __('reports.churn_risk.columns.avg_interval') }}</th>
                        <th class="pb-3 pr-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide text-right">{{ __('reports.churn_risk.columns.days_since') }}</th>
                        <th class="pb-3 pr-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide text-center">{{ __('reports.churn_risk.columns.urgency') }}</th>
                        <th class="pb-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide text-right hidden md:table-cell">{{ __('reports.churn_risk.columns.total_spent') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                    @foreach ($customers as $customer)
                        @php
                            $urgency = \App\Livewire\Reports\ChurnRiskCustomers::urgencyLevel(
                                (float) $customer->days_since_last,
                                (float) $customer->avg_interval_days
                            );
                        @endphp
                        <tr class="group hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="py-3 pr-4">
                                <a href="{{ route('customers.show', $customer->id) }}"
                                   class="font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
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
                            <td class="py-3 pr-4 text-right font-semibold tabular-nums
                                @if ($urgency === 'critical') text-red-600 dark:text-red-400
                                @elseif ($urgency === 'high') text-orange-500 dark:text-orange-400
                                @else text-yellow-600 dark:text-yellow-400
                                @endif">
                                {{ number_format($customer->days_since_last) }}
                                <span class="text-xs font-normal text-gray-400">{{ __('reports.churn_risk.days') }}</span>
                            </td>
                            <td class="py-3 pr-4 text-center">
                                @if ($urgency === 'critical')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300">
                                        {{ __('reports.churn_risk.urgency.critical') }}
                                    </span>
                                @elseif ($urgency === 'high')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300">
                                        {{ __('reports.churn_risk.urgency.high') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300">
                                        {{ __('reports.churn_risk.urgency.medium') }}
                                    </span>
                                @endif
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
