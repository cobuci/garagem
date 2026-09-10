<div class="space-y-6 flex flex-col min-h-0 w-full max-w-full">
    {{-- Header da Página --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ __('finance.title') }}</h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200/80 dark:border-gray-700 tabular-nums">
                    {{ $this->typeCounts['all'] }}
                </span>
            </div>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                {{ __('finance.description') }}
            </p>
        </div>
    </div>

    {{-- Cards de Resumo Financeiro com Ações Integradas --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Saldo Atual com Ações de Adicionar/Remover --}}
        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('finance.summary.current_balance') }}</span>
                    <div @class([
                        'p-2 rounded-lg flex items-center justify-center',
                        'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400' => $this->summary['current_balance'] >= 0,
                        'bg-red-50 text-red-600 dark:text-red-400 dark:bg-red-950/40' => $this->summary['current_balance'] < 0,
                    ])>
                        <x-icon name="banknotes" class="w-4 h-4" />
                    </div>
                </div>
                <p @class([
                    'text-2xl font-bold tracking-tight tabular-nums',
                    'text-emerald-600 dark:text-emerald-400' => $this->summary['current_balance'] >= 0,
                    'text-red-600 dark:text-red-400' => $this->summary['current_balance'] < 0,
                ])>
                    R$ {{ number_format($this->summary['current_balance'] / 100, 2, ',', '.') }}
                </p>
            </div>

            @can(\App\Enums\Permission::CreateFinancialTransaction->value)
                <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700/60 grid grid-cols-2 gap-2">
                    <button
                        type="button"
                        wire:click="openAdjustmentModal('add')"
                        class="inline-flex items-center justify-center gap-1.5 px-2.5 py-2 sm:py-1.5 min-h-[38px] sm:min-h-[34px] text-xs font-semibold rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 border border-emerald-200/80 dark:border-emerald-800/50 transition-all duration-150 active:scale-[0.98] shadow-2xs"
                    >
                        <x-icon name="plus" class="w-3.5 h-3.5 shrink-0 text-emerald-600 dark:text-emerald-400" />
                        <span class="truncate">{{ __('finance.actions.add_balance') }}</span>
                    </button>
                    <button
                        type="button"
                        wire:click="openAdjustmentModal('remove')"
                        class="inline-flex items-center justify-center gap-1.5 px-2.5 py-2 sm:py-1.5 min-h-[38px] sm:min-h-[34px] text-xs font-semibold rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 hover:bg-rose-100 dark:hover:bg-rose-900/50 border border-rose-200/80 dark:border-rose-800/50 transition-all duration-150 active:scale-[0.98] shadow-2xs"
                    >
                        <x-icon name="minus" class="w-3.5 h-3.5 shrink-0 text-rose-500 dark:text-rose-400" />
                        <span class="truncate">{{ __('finance.actions.remove_balance') }}</span>
                    </button>
                </div>
            @endcan
        </div>

        {{-- Total Devido --}}
        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('finance.summary.total_due') }}</span>
                    <div class="p-2 rounded-lg bg-red-50 text-red-600 dark:bg-red-950/40 dark:text-red-400 flex items-center justify-center">
                        <x-icon name="arrow-trending-down" class="w-4 h-4" />
                    </div>
                </div>
                <p class="text-2xl font-bold tracking-tight text-red-600 dark:text-red-400 tabular-nums">
                    R$ {{ number_format($this->summary['total_due'] / 100, 2, ',', '.') }}
                </p>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('bills_payable.title') }}</span>
                <a
                    href="{{ route('bills-payable.index') }}"
                    class="inline-flex items-center gap-1 text-xs font-semibold text-sky-600 dark:text-sky-400 hover:text-sky-700 dark:hover:text-sky-300 transition-colors"
                >
                    <span>{{ __('dashboard.view_all') }}</span>
                    <x-icon name="arrow-right" class="w-3 h-3" />
                </a>
            </div>
        </div>

        {{-- A Receber --}}
        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('finance.summary.to_receive') }}</span>
                    <div class="p-2 rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400 flex items-center justify-center">
                        <x-icon name="arrow-trending-up" class="w-4 h-4" />
                    </div>
                </div>
                <p class="text-2xl font-bold tracking-tight text-amber-600 dark:text-amber-400 tabular-nums">
                    R$ {{ number_format($this->summary['to_receive'] / 100, 2, ',', '.') }}
                </p>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('sales.pending') }}</span>
                <a
                    href="{{ route('sales.index') }}"
                    class="inline-flex items-center gap-1 text-xs font-semibold text-sky-600 dark:text-sky-400 hover:text-sky-700 dark:hover:text-sky-300 transition-colors"
                >
                    <span>{{ __('dashboard.view_all') }}</span>
                    <x-icon name="arrow-right" class="w-3 h-3" />
                </a>
            </div>
        </div>
    </div>

    {{-- Seção de Filtros (Abas de Tipos + Barra de Busca e Período) --}}
    <div class="space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200 dark:border-gray-700 pb-2 sm:pb-0">
            <div class="flex items-center overflow-x-auto gap-1 sm:gap-2 -mb-px scrollbar-hide">
                {{-- Todas --}}
                <button
                    type="button"
                    wire:click="$set('type', 'all')"
                    class="group py-3 px-3 border-b-2 font-medium text-sm whitespace-nowrap transition-colors duration-150 flex items-center gap-2 {{ $type === 'all' ? 'border-primary-600 text-primary-600 dark:text-primary-400 dark:border-primary-500' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
                >
                    <x-icon name="list-bullet" class="w-4 h-4 {{ $type === 'all' ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-500' }}"/>
                    <span>{{ __('finance.filters.all') }}</span>
                    <span @class([
                        'inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-semibold tabular-nums',
                        'bg-sky-100 text-sky-800 dark:bg-sky-950/60 dark:text-sky-300' => $type === 'all',
                        'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' => $type !== 'all',
                    ])>
                        {{ $this->typeCounts['all'] }}
                    </span>
                </button>

                {{-- Vendas --}}
                <button
                    type="button"
                    wire:click="$set('type', 'sale')"
                    class="group py-3 px-3 border-b-2 font-medium text-sm whitespace-nowrap transition-colors duration-150 flex items-center gap-2 {{ $type === 'sale' ? 'border-primary-600 text-primary-600 dark:text-primary-400 dark:border-primary-500' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
                >
                    <x-icon name="shopping-bag" class="w-4 h-4 {{ $type === 'sale' ? 'text-emerald-500' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-500' }}"/>
                    <span>{{ __('finance.filters.sale') }}</span>
                    <span @class([
                        'inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-semibold tabular-nums',
                        'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' => $type === 'sale',
                        'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' => $type !== 'sale',
                    ])>
                        {{ $this->typeCounts['sale'] }}
                    </span>
                </button>

                {{-- Compras --}}
                <button
                    type="button"
                    wire:click="$set('type', 'purchase')"
                    class="group py-3 px-3 border-b-2 font-medium text-sm whitespace-nowrap transition-colors duration-150 flex items-center gap-2 {{ $type === 'purchase' ? 'border-primary-600 text-primary-600 dark:text-primary-400 dark:border-primary-500' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
                >
                    <x-icon name="shopping-cart" class="w-4 h-4 {{ $type === 'purchase' ? 'text-red-500' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-500' }}"/>
                    <span>{{ __('finance.filters.purchase') }}</span>
                    <span @class([
                        'inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-semibold tabular-nums',
                        'bg-red-100 text-red-800 dark:bg-red-950/60 dark:text-red-300' => $type === 'purchase',
                        'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' => $type !== 'purchase',
                    ])>
                        {{ $this->typeCounts['purchase'] }}
                    </span>
                </button>

                {{-- Cancelamentos --}}
                <button
                    type="button"
                    wire:click="$set('type', 'cancelled_sale')"
                    class="group py-3 px-3 border-b-2 font-medium text-sm whitespace-nowrap transition-colors duration-150 flex items-center gap-2 {{ $type === 'cancelled_sale' ? 'border-primary-600 text-primary-600 dark:text-primary-400 dark:border-primary-500' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
                >
                    <x-icon name="x-circle" class="w-4 h-4 {{ $type === 'cancelled_sale' ? 'text-red-500' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-500' }}"/>
                    <span>{{ __('finance.filters.cancelled_sale') }}</span>
                    <span @class([
                        'inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-semibold tabular-nums',
                        'bg-red-100 text-red-800 dark:bg-red-950/60 dark:text-red-300' => $type === 'cancelled_sale',
                        'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' => $type !== 'cancelled_sale',
                    ])>
                        {{ $this->typeCounts['cancelled_sale'] }}
                    </span>
                </button>

                {{-- Ajustes Manuais --}}
                <button
                    type="button"
                    wire:click="$set('type', 'manual_adjustment')"
                    class="group py-3 px-3 border-b-2 font-medium text-sm whitespace-nowrap transition-colors duration-150 flex items-center gap-2 {{ $type === 'manual_adjustment' ? 'border-primary-600 text-primary-600 dark:text-primary-400 dark:border-primary-500' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
                >
                    <x-icon name="adjustments-horizontal" class="w-4 h-4 {{ $type === 'manual_adjustment' ? 'text-sky-500' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-500' }}"/>
                    <span>{{ __('finance.filters.manual_adjustment') }}</span>
                    <span @class([
                        'inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-semibold tabular-nums',
                        'bg-sky-100 text-sky-800 dark:bg-sky-950/60 dark:text-sky-300' => $type === 'manual_adjustment',
                        'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' => $type !== 'manual_adjustment',
                    ])>
                        {{ $this->typeCounts['manual_adjustment'] }}
                    </span>
                </button>
            </div>
        </div>

        {{-- Controles Secundários: Busca + Filtro de Período + Limpar Filtros --}}
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-1">
            <div class="w-full sm:w-80">
                <x-input
                    icon="magnifying-glass"
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('finance.search_placeholder') }}"
                    clearable
                />
            </div>

            <div class="flex items-center justify-between sm:justify-end gap-2 flex-wrap sm:flex-nowrap">
                <div class="inline-flex w-full sm:w-auto overflow-x-auto rounded-lg border border-gray-200/80 dark:border-gray-700/80 bg-white dark:bg-gray-800 p-0.5 shadow-xs scrollbar-hide">
                    <button
                        type="button"
                        wire:click="$set('period', 'all')"
                        @class([
                            'flex-1 sm:flex-none text-center px-2.5 py-1.5 sm:py-1 text-xs font-semibold rounded-md transition-colors whitespace-nowrap',
                            'bg-primary-50 text-primary-700 dark:bg-primary-950/60 dark:text-primary-300' => $period === 'all',
                            'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' => $period !== 'all',
                        ])
                    >
                        {{ __('finance.periods.all') }}
                    </button>
                    <button
                        type="button"
                        wire:click="$set('period', 'today')"
                        @class([
                            'flex-1 sm:flex-none text-center px-2.5 py-1.5 sm:py-1 text-xs font-semibold rounded-md transition-colors whitespace-nowrap',
                            'bg-primary-50 text-primary-700 dark:bg-primary-950/60 dark:text-primary-300' => $period === 'today',
                            'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' => $period !== 'today',
                        ])
                    >
                        {{ __('finance.periods.today') }}
                    </button>
                    <button
                        type="button"
                        wire:click="$set('period', '7_days')"
                        @class([
                            'flex-1 sm:flex-none text-center px-2.5 py-1.5 sm:py-1 text-xs font-semibold rounded-md transition-colors whitespace-nowrap',
                            'bg-primary-50 text-primary-700 dark:bg-primary-950/60 dark:text-primary-300' => $period === '7_days',
                            'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' => $period !== '7_days',
                        ])
                    >
                        {{ __('finance.periods.7_days') }}
                    </button>
                    <button
                        type="button"
                        wire:click="$set('period', 'this_month')"
                        @class([
                            'flex-1 sm:flex-none text-center px-2.5 py-1.5 sm:py-1 text-xs font-semibold rounded-md transition-colors whitespace-nowrap',
                            'bg-primary-50 text-primary-700 dark:bg-primary-950/60 dark:text-primary-300' => $period === 'this_month',
                            'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' => $period !== 'this_month',
                        ])
                    >
                        {{ __('finance.periods.this_month') }}
                    </button>
                </div>

                @if($this->hasActiveFilters)
                    <x-button
                        xs
                        flat
                        icon="x-circle"
                        label="{{ __('finance.clear_filters') }}"
                        wire:click="resetFilters"
                        class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 shrink-0 w-full sm:w-auto justify-center"
                    />
                @endif
            </div>
        </div>
    </div>

    <div class="flex-1 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col min-h-0 w-full max-w-full">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/50">
            <div class="flex items-center gap-2">
                <x-icon name="list-bullet" class="w-5 h-5 text-gray-400 dark:text-gray-500" />
                <h3 class="font-bold text-gray-900 dark:text-white">{{ __('finance.table.title') }}</h3>
                @if($this->transactions->total() > 0)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-700/60 text-gray-600 dark:text-gray-300 tabular-nums">
                        {{ $this->transactions->total() }}
                    </span>
                @endif
            </div>
        </div>

        <div class="divide-y divide-gray-100 dark:divide-gray-700/50 overflow-y-auto flex-1">
            @forelse ($this->transactions as $transaction)
                <div class="px-4 sm:px-6 py-4 flex items-center justify-between hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors group">
                    <div class="flex items-center space-x-3 sm:space-x-4 min-w-0 flex-1">
                        <div @class([
                            'w-10 h-10 rounded-xl flex-none flex items-center justify-center transition-transform group-hover:scale-105',
                            'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400' => $transaction->type === \App\Enums\TransactionType::Sale,
                            'bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400' => in_array($transaction->type, [\App\Enums\TransactionType::Purchase, \App\Enums\TransactionType::CancelledSale]),
                            'bg-sky-50 dark:bg-sky-950/40 text-sky-600 dark:text-sky-400' => $transaction->type === \App\Enums\TransactionType::ManualAdjustment,
                        ])>
                            <x-icon name="{{ match($transaction->type) {
                                \App\Enums\TransactionType::Sale => 'shopping-bag',
                                \App\Enums\TransactionType::Purchase => 'shopping-cart',
                                \App\Enums\TransactionType::CancelledSale => 'x-circle',
                                \App\Enums\TransactionType::ManualAdjustment => 'adjustments-horizontal',
                            } }}" class="w-5 h-5" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                {{ $transaction->description }}
                            </p>
                            <div class="flex items-center gap-2 mt-0.5 overflow-hidden">
                                <span @class([
                                    'px-2 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider flex-none',
                                    'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400' => $transaction->type === \App\Enums\TransactionType::Sale,
                                    'bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-400' => in_array($transaction->type, [\App\Enums\TransactionType::Purchase, \App\Enums\TransactionType::CancelledSale]),
                                    'bg-sky-50 text-sky-700 dark:bg-sky-950/50 dark:text-sky-400' => $transaction->type === \App\Enums\TransactionType::ManualAdjustment,
                                ])>
                                    {{ $transaction->type->label() }}
                                </span>
                                <span class="text-gray-300 dark:text-gray-600 flex-none">•</span>
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1 truncate">
                                    <x-icon name="clock" class="w-3.5 h-3.5 flex-none text-gray-400 dark:text-gray-500" />
                                    <span class="truncate">{{ $transaction->transaction_date->diffForHumans() }}</span>
                                </span>
                                <span class="hidden sm:inline text-gray-300 dark:text-gray-600 flex-none">•</span>
                                <span class="hidden sm:inline text-xs font-medium text-gray-400 dark:text-gray-500 tabular-nums flex-none">
                                    {{ $transaction->transaction_date->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 sm:gap-4 ml-4 flex-none">
                        <div class="text-right flex-none">
                            <p @class([
                                'text-sm sm:text-base font-bold whitespace-nowrap tabular-nums',
                                'text-emerald-600 dark:text-emerald-400' => $transaction->amount > 0,
                                'text-red-600 dark:text-red-400' => $transaction->amount < 0,
                                'text-gray-900 dark:text-white' => $transaction->amount == 0,
                            ])>
                                {{ $transaction->amount > 0 ? '+' : '' }} R$ {{ number_format(abs($transaction->amount) / 100, 2, ',', '.') }}
                            </p>
                        </div>
                        @if($transaction->type === \App\Enums\TransactionType::ManualAdjustment)
                            @can(\App\Enums\Permission::DeleteFinancialTransaction->value)
                                <x-button
                                    xs
                                    icon="trash"
                                    negative
                                    flat
                                    wire:click="confirmCancelAdjustment({{ $transaction->id }})"
                                    title="{{ __('finance.adjustment_modal.cancel') }}"
                                    class="p-1.5"
                                />
                            @endcan
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-800 mb-4 transition-transform hover:scale-105 border border-gray-200/60 dark:border-gray-700">
                        <x-icon name="inbox" class="w-7 h-7 text-gray-400 dark:text-gray-500" />
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 font-medium text-sm">
                        {{ $this->hasActiveFilters ? __('finance.table.no_records_filtered') : __('finance.table.no_records') }}
                    </p>
                    @if($this->hasActiveFilters)
                        <div class="mt-3">
                            <button
                                type="button"
                                wire:click="resetFilters"
                                class="inline-flex items-center text-xs font-semibold text-sky-600 dark:text-sky-400 hover:text-sky-700 dark:hover:text-sky-300 underline underline-offset-2"
                            >
                                {{ __('finance.clear_filters') }}
                            </button>
                        </div>
                    @endif
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
