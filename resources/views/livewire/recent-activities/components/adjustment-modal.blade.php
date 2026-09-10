<x-modal-card
    name="adjustmentModal"
    wire:model="showAdjustmentModal"
    title="{{ $adjustmentType === 'add' ? __('finance.adjustment_modal.title_add') : __('finance.adjustment_modal.title_remove') }}"
    max-width="lg"
>
    <div>
        {{-- Seletor de Tipo de Movimentação --}}
        <div class="grid grid-cols-2 gap-2 p-1 bg-gray-100 dark:bg-gray-800 rounded-xl border border-gray-200/80 dark:border-gray-700/80 mb-5">
            <button
                type="button"
                wire:click="$set('adjustmentType', 'add')"
                @class([
                    'flex items-center justify-center gap-2 py-2 px-3 rounded-lg text-xs font-bold uppercase tracking-wider transition-all duration-150',
                    'bg-white dark:bg-gray-700 text-emerald-600 dark:text-emerald-400 shadow-xs border border-gray-200/60 dark:border-gray-600' => $adjustmentType === 'add',
                    'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' => $adjustmentType !== 'add',
                ])
            >
                <x-icon name="plus-circle" class="w-4 h-4" />
                <span>{{ __('finance.adjustment_modal.type_credit') }}</span>
            </button>
            <button
                type="button"
                wire:click="$set('adjustmentType', 'remove')"
                @class([
                    'flex items-center justify-center gap-2 py-2 px-3 rounded-lg text-xs font-bold uppercase tracking-wider transition-all duration-150',
                    'bg-white dark:bg-gray-700 text-rose-600 dark:text-rose-400 shadow-xs border border-gray-200/60 dark:border-gray-600' => $adjustmentType === 'remove',
                    'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' => $adjustmentType !== 'remove',
                ])
            >
                <x-icon name="minus-circle" class="w-4 h-4" />
                <span>{{ __('finance.adjustment_modal.type_debit') }}</span>
            </button>
        </div>

        {{-- Contexto do Saldo Atual --}}
        <div class="flex items-center justify-between px-4 py-3 rounded-xl bg-gray-50/80 dark:bg-gray-800/80 border border-gray-200/60 dark:border-gray-700/60 mb-5">
            <div class="flex items-center gap-2.5">
                <div class="p-1.5 rounded-lg bg-gray-200/60 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                    <x-icon name="banknotes" class="w-4 h-4" />
                </div>
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('finance.adjustment_modal.current_balance_label') }}</span>
            </div>
            <span @class([
                'text-sm font-bold tabular-nums',
                'text-emerald-600 dark:text-emerald-400' => $this->summary['current_balance'] >= 0,
                'text-red-600 dark:text-red-400' => $this->summary['current_balance'] < 0,
            ])>
                R$ {{ number_format($this->summary['current_balance'] / 100, 2, ',', '.') }}
            </span>
        </div>

        {{-- Campos do Formulário --}}
        <div class="space-y-4">
            <div>
                <div class="flex items-center justify-between mb-1">
                    <x-label :label="__('finance.adjustment_modal.amount')" />
                    @if($adjustmentType === 'remove' && $this->summary['current_balance'] > 0)
                        <button
                            type="button"
                            wire:click="fillAllBalance"
                            class="inline-flex items-center gap-1 text-xs font-semibold text-rose-600 dark:text-rose-400 hover:text-rose-700 dark:hover:text-rose-300 hover:underline transition-colors focus:outline-hidden"
                        >
                            <x-icon name="arrow-down-tray" class="w-3 h-3" />
                            <span>{{ __('finance.adjustment_modal.fill_all') }}</span>
                        </button>
                    @endif
                </div>
                <x-money-input
                    placeholder="0,00"
                    wire:model="amount"
                    prefix="R$"
                />
            </div>
            <div>
                <x-textarea
                    label="{{ __('finance.adjustment_modal.description') }}"
                    placeholder="{{ __('finance.adjustment_modal.description_placeholder') }}"
                    wire:model="description"
                    rows="3"
                />
            </div>
        </div>
    </div>

    <x-slot name="footer">
        <div class="flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-2 sm:gap-3 w-full">
            <x-button flat label="{{ __('finance.adjustment_modal.cancel') }}" x-on:click="close" class="w-full sm:w-auto justify-center" />
            @if($adjustmentType === 'add')
                <x-button
                    positive
                    icon="plus"
                    label="{{ __('finance.adjustment_modal.confirm') }}"
                    wire:click="saveAdjustment"
                    spinner="saveAdjustment"
                    class="font-semibold shadow-xs w-full sm:w-auto justify-center"
                />
            @else
                <x-button
                    negative
                    icon="minus"
                    label="{{ __('finance.adjustment_modal.confirm') }}"
                    wire:click="saveAdjustment"
                    spinner="saveAdjustment"
                    class="font-semibold shadow-xs w-full sm:w-auto justify-center"
                />
            @endif
        </div>
    </x-slot>
</x-modal-card>
