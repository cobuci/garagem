<div class="space-y-6 flex flex-col min-h-0 w-full max-w-full">
    <div class="flex-none bg-gray-50 dark:bg-gray-900 pb-2 w-full max-w-full">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('bills_payable.title') }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('bills_payable.description') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('bills_payable.summary.total_due') }}</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white">
                    R$ {{ number_format($this->summary['total_due'], 2, ',', '.') }}
                </p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm border-l-4 border-l-red-500">
                <p class="text-xs font-semibold text-red-500 dark:text-red-400 uppercase tracking-wider mb-1">{{ __('bills_payable.summary.overdue') }}</p>
                <p class="text-lg font-bold text-red-600 dark:text-red-400">
                    R$ {{ number_format($this->summary['overdue'], 2, ',', '.') }}
                </p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm border-l-4 border-l-blue-500">
                <p class="text-xs font-semibold text-blue-500 dark:text-blue-400 uppercase tracking-wider mb-1">{{ __('bills_payable.summary.next_month') }}</p>
                <p class="text-lg font-bold text-blue-600 dark:text-blue-400">
                    R$ {{ number_format($this->summary['next_month'], 2, ',', '.') }}
                </p>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 items-center justify-between mb-4">
            <div class="w-full sm:w-64">
                <x-input icon="magnifying-glass" wire:model.live.debounce.300ms="search" placeholder="{{ __('bills_payable.filters.search_placeholder') }}" />
            </div>
            <div class="flex items-center gap-2">
                <x-button sm :outline="$status !== 'pending'" :primary="$status === 'pending'" wire:click="$set('status', 'pending')" title="{{ __('bills_payable.filters.pending') }}" icon="clock" />
                <x-button sm :outline="$status !== 'paid'" :primary="$status === 'paid'" wire:click="$set('status', 'paid')" title="{{ __('bills_payable.filters.paid') }}" icon="check" />
                <x-button sm :outline="$status !== 'all'" :primary="$status === 'all'" wire:click="$set('status', 'all')" title="{{ __('bills_payable.filters.all') }}" icon="list-bullet" />
            </div>
        </div>
    </div>

    <div class="flex-1 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col min-h-0 w-full max-w-full">
        <div class="overflow-x-auto flex-1 relative w-full max-w-full">
            <table class="w-full divide-y divide-gray-100 dark:divide-gray-700 border-separate border-spacing-0">
                <thead class="bg-gray-50 dark:bg-gray-900/50 sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 whitespace-nowrap">{{ __('bills_payable.table.due_date') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 whitespace-nowrap">{{ __('bills_payable.table.product') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 whitespace-nowrap text-right">{{ __('bills_payable.table.quantity') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 whitespace-nowrap text-right">{{ __('bills_payable.table.total_value') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 whitespace-nowrap">{{ __('bills_payable.table.status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 whitespace-nowrap"></th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700" x-data="{ selectedId: null }">
                    @forelse ($this->bills as $bill)
                        <x-table.row
                            wire:key="bill-{{ $bill->id }}"
                            @click="selectedId = {{ $bill->id }}"
                            x-bind:data-selected="selectedId === {{ $bill->id }}"
                        >
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div @class([
                                    'text-sm',
                                    'text-red-600 font-bold' => !$bill->is_paid && $bill->due_date?->isPast(),
                                    'text-gray-900 dark:text-white' => $bill->is_paid || !$bill->due_date?->isPast(),
                                ])>
                                    {{ $bill->due_date?->format('d/m/Y') ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $bill->product->name }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $bill->product->brand }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ $bill->quantity }}
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                                R$ {{ number_format($bill->total_cost, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if ($bill->is_paid)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ __('bills_payable.table.paid_at', ['date' => $bill->payment_date?->format('d/m/Y')]) }}
                                    </span>
                                @else
                                    <span @class([
                                        'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                        'bg-red-100 text-red-800' => $bill->due_date?->isPast(),
                                        'bg-yellow-100 text-yellow-800' => !$bill->due_date?->isPast(),
                                    ])>
                                        {{ $bill->due_date?->isPast() ? __('bills_payable.table.overdue') : __('bills_payable.table.pending') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap" @click.stop>
                                <div class="flex justify-end gap-2">
                                    @can(\App\Enums\Permission::DeleteProductPurchase->value)
                                        <x-button
                                            xs
                                            outline
                                            negative
                                            label="{{ __('bills_payable.actions.cancel') }}"
                                            wire:click="confirmCancellation({{ $bill->id }})"
                                        />
                                    @endcan

                                    @if (!$bill->is_paid)
                                        @can(\App\Enums\Permission::EditProductPurchase->value)
                                            <x-button
                                                xs
                                                primary
                                                label="{{ __('bills_payable.actions.pay') }}"
                                                wire:click="confirmPayment({{ $bill->id }})"
                                            />
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </x-table.row>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                {{ __('bills_payable.table.no_records') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @include('livewire.bills-payable.components.confirm-payment-modal')
    @include('livewire.bills-payable.components.confirm-cancel-modal')
</div>
