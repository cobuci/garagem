<div class="space-y-6 flex flex-col min-h-0 w-full max-w-full">
    <div class="flex-none bg-gray-50 dark:bg-gray-900 pb-2 w-full max-w-full">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('finance.title') }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('finance.description') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <x-button positive icon="plus" label="{{ __('finance.actions.add_balance') }}" wire:click="openAdjustmentModal('add')" />
                <x-button negative icon="minus" label="{{ __('finance.actions.remove_balance') }}" wire:click="openAdjustmentModal('remove')" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div @class([
                'bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm border-l-4',
                'border-l-green-500' => $this->summary['current_balance'] >= 0,
                'border-l-red-500' => $this->summary['current_balance'] < 0,
            ])>
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('finance.summary.current_balance') }}</p>
                <p @class([
                    'text-lg font-bold',
                    'text-green-600 dark:text-green-400' => $this->summary['current_balance'] >= 0,
                    'text-red-600 dark:text-red-400' => $this->summary['current_balance'] < 0,
                ])>
                    R$ {{ number_format($this->summary['current_balance'] / 100, 2, ',', '.') }}
                </p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm border-l-4 border-l-red-500">
                <p class="text-xs font-semibold text-red-500 dark:text-red-400 uppercase tracking-wider mb-1">{{ __('finance.summary.total_due') }}</p>
                <p class="text-lg font-bold text-red-600 dark:text-red-400">
                    R$ {{ number_format($this->summary['total_due'] / 100, 2, ',', '.') }}
                </p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm border-l-4 border-l-blue-500">
                <p class="text-xs font-semibold text-blue-500 dark:text-blue-400 uppercase tracking-wider mb-1">{{ __('finance.summary.to_receive') }}</p>
                <p class="text-lg font-bold text-blue-600 dark:text-blue-400">
                    R$ {{ number_format($this->summary['to_receive'] / 100, 2, ',', '.') }}
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 mb-4">
            <x-button sm :outline="$type !== 'all'" :primary="$type === 'all'" wire:click="$set('type', 'all')" label="{{ __('finance.filters.all') }}" />
            <x-button sm :outline="$type !== 'sale'" :primary="$type === 'sale'" wire:click="$set('type', 'sale')" label="{{ __('finance.filters.sale') }}" />
            <x-button sm :outline="$type !== 'purchase'" :primary="$type === 'purchase'" wire:click="$set('type', 'purchase')" label="{{ __('finance.filters.purchase') }}" />
            <x-button sm :outline="$type !== 'cancelled_sale'" :primary="$type === 'cancelled_sale'" wire:click="$set('type', 'cancelled_sale')" label="{{ __('finance.filters.cancelled_sale') }}" />
            <x-button sm :outline="$type !== 'manual_adjustment'" :primary="$type === 'manual_adjustment'" wire:click="$set('type', 'manual_adjustment')" label="{{ __('finance.filters.manual_adjustment') }}" />
        </div>
    </div>

    <div class="flex-1 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col min-h-0 w-full max-w-full">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/50">
            <div class="flex items-center gap-2">
                <x-icon name="list-bullet" class="w-5 h-5 text-gray-400" />
                <h3 class="font-bold text-gray-900 dark:text-white">{{ __('finance.table.title') }}</h3>
            </div>
        </div>

        <div class="divide-y divide-gray-50 dark:divide-gray-700/50 overflow-y-auto flex-1">
            @forelse ($this->transactions as $transaction)
                <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors group">
                    <div class="flex items-center space-x-4">
                        <div @class([
                            'w-12 h-12 rounded-2xl flex items-center justify-center transition-transform group-hover:scale-110',
                            'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' => $transaction->type === \App\Enums\TransactionType::Sale,
                            'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400' => in_array($transaction->type, [\App\Enums\TransactionType::Purchase, \App\Enums\TransactionType::CancelledSale]),
                            'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' => $transaction->type === \App\Enums\TransactionType::ManualAdjustment,
                        ])>
                            <x-icon name="{{ match($transaction->type) {
                                \App\Enums\TransactionType::Sale => 'shopping-bag',
                                \App\Enums\TransactionType::Purchase => 'shopping-cart',
                                \App\Enums\TransactionType::CancelledSale => 'x-circle',
                                \App\Enums\TransactionType::ManualAdjustment => 'adjustments-horizontal',
                            } }}" class="w-6 h-6" />
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">
                                {{ $transaction->description }}
                            </p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span @class([
                                    'px-1.5 py-0.5 rounded text-[10px] font-bold uppercase',
                                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-400' => $transaction->type === \App\Enums\TransactionType::Sale,
                                    'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400' => in_array($transaction->type, [\App\Enums\TransactionType::Purchase, \App\Enums\TransactionType::CancelledSale]),
                                    'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400' => $transaction->type === \App\Enums\TransactionType::ManualAdjustment,
                                ])>
                                    {{ $transaction->type->label() }}
                                </span>
                                <span class="text-gray-300 dark:text-gray-600">•</span>
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                    <x-icon name="clock" class="w-3 h-3" />
                                    {{ $transaction->transaction_date->diffForHumans() }}
                                </span>
                                <span class="text-gray-300 dark:text-gray-600">•</span>
                                <span class="text-[10px] font-semibold text-gray-400 uppercase">
                                    {{ $transaction->transaction_date->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <p @class([
                                'text-sm font-extrabold',
                                'text-emerald-600 dark:text-emerald-400' => $transaction->amount > 0,
                                'text-red-600 dark:text-red-400' => $transaction->amount < 0,
                                'text-gray-900 dark:text-white' => $transaction->amount == 0,
                            ])>
                                {{ $transaction->amount > 0 ? '+' : '' }} R$ {{ number_format(abs($transaction->amount) / 100, 2, ',', '.') }}
                            </p>
                        </div>
                        @if($transaction->type === \App\Enums\TransactionType::ManualAdjustment)
                            <x-button
                                xs
                                icon="trash"
                                negative
                                wire:click="confirmCancelAdjustment({{ $transaction->id }})"
                                title="{{ __('finance.adjustment_modal.cancel') }}"
                            />
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 dark:bg-gray-900 mb-4 transition-transform hover:scale-110">
                        <x-icon name="inbox" class="w-8 h-8 text-gray-300 dark:text-gray-600" />
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 font-medium">{{ __('finance.table.no_records') }}</p>
                </div>
            @endforelse
        </div>
        @if($this->transactions->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                {{ $this->transactions->links() }}
            </div>
        @endif
    </div>

    @include('livewire.recent-activities.components.adjustment-modal')
    @include('livewire.recent-activities.components.confirm-cancel-modal')
</div>
