@use(App\Enums\Permission)
<div class="space-y-6 flex flex-col min-h-0 w-full max-w-full">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ __('bills_payable.title') }}</h1>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200/80 dark:border-gray-700 tabular-nums">
                    {{ $this->counts['pending'] }}
                </span>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('bills_payable.description') }}</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Card 1: Total Due -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('bills_payable.summary.total_due') }}</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/50 border border-amber-200/40 dark:border-amber-800/40 flex items-center justify-center text-amber-600 dark:text-amber-400">
                    <x-icon name="banknotes" class="w-4 h-4" />
                </div>
            </div>
            <div>
                <p class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white tabular-nums">
                    R$ {{ number_format($this->summary['total_due'], 2, ',', '.') }}
                </p>
                <div class="mt-2 flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-2xs font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-400 border border-amber-200/50 dark:border-amber-800/50 tabular-nums">
                        {{ $this->counts['pending'] }} {{ $this->counts['pending'] === 1 ? 'conta aberta' : 'contas abertas' }}
                    </span>
                    <span>a liquidar</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Overdue -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('bills_payable.summary.overdue') }}</span>
                <div @class([
                    'w-8 h-8 rounded-lg flex items-center justify-center border',
                    'bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 border-rose-200/40 dark:border-rose-800/40' => $this->summary['overdue'] > 0,
                    'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border-emerald-200/40 dark:border-emerald-800/40' => $this->summary['overdue'] == 0,
                ])>
                    <x-icon name="{{ $this->summary['overdue'] > 0 ? 'exclamation-triangle' : 'shield-check' }}" class="w-4 h-4" />
                </div>
            </div>
            <div>
                <p @class([
                    'text-2xl font-bold tracking-tight tabular-nums',
                    'text-rose-600 dark:text-rose-400' => $this->summary['overdue'] > 0,
                    'text-emerald-600 dark:text-emerald-400' => $this->summary['overdue'] == 0,
                ])>
                    R$ {{ number_format($this->summary['overdue'], 2, ',', '.') }}
                </p>
                <div class="mt-2 flex items-center gap-1.5 text-xs">
                    @if ($this->summary['overdue'] > 0)
                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-2xs font-semibold bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-400 border border-rose-200/50 dark:border-rose-800/50 tabular-nums">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                            {{ $this->counts['overdue'] }} {{ $this->counts['overdue'] === 1 ? 'vencida' : 'vencidas' }}
                        </span>
                        <span class="text-rose-600 dark:text-rose-400 font-medium">Requer atenção imediata</span>
                    @else
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-2xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-800/50">
                            Em dia
                        </span>
                        <span class="text-gray-400 dark:text-gray-500">Nenhum pagamento em atraso</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Card 3: Next Month -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('bills_payable.summary.next_month') }}</span>
                <div class="w-8 h-8 rounded-lg bg-sky-50 dark:bg-sky-950/50 border border-sky-200/40 dark:border-sky-800/40 flex items-center justify-center text-sky-600 dark:text-sky-400">
                    <x-icon name="calendar-days" class="w-4 h-4" />
                </div>
            </div>
            <div>
                <p class="text-2xl font-bold tracking-tight text-sky-600 dark:text-sky-400 tabular-nums">
                    R$ {{ number_format($this->summary['next_month'], 2, ',', '.') }}
                </p>
                <div class="mt-2 flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-2xs font-semibold bg-sky-50 text-sky-700 dark:bg-sky-950/50 dark:text-sky-400 border border-sky-200/50 dark:border-sky-800/50">
                        Projeção
                    </span>
                    <span>Compromissos futuros</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Container: Filter Toolbar + Data Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xs border border-gray-200/80 dark:border-gray-700/80 overflow-hidden flex flex-col min-h-0 w-full max-w-full">
        <!-- Integrated Toolbar -->
        <div class="px-5 py-3.5 border-b border-gray-200/80 dark:border-gray-700/80 bg-gray-50/50 dark:bg-gray-900/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="w-full sm:max-w-xs relative">
                <x-input
                    icon="magnifying-glass"
                    wire:model.live.debounce.300ms="search"
                    :placeholder="__('bills_payable.filters.search_placeholder')"
                    class="bg-white dark:bg-gray-800"
                />
                <div wire:loading.delay wire:target="search" class="absolute right-3 top-1/2 -translate-y-1/2">
                    <x-icon name="arrow-path" class="w-4 h-4 text-gray-400 animate-spin" />
                </div>
            </div>

            <!-- Segmented Status Tabs -->
            <div class="inline-flex items-center p-1 rounded-xl bg-gray-100 dark:bg-gray-900/60 border border-gray-200/60 dark:border-gray-700/60 gap-1 self-start sm:self-auto">
                <button
                    type="button"
                    wire:click="$set('status', 'pending')"
                    @class([
                        'inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-150 cursor-pointer',
                        'bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-2xs' => $status === 'pending',
                        'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-white/50 dark:hover:bg-gray-800/50' => $status !== 'pending',
                    ])
                >
                    <x-icon name="clock" class="w-3.5 h-3.5" />
                    <span>{{ __('bills_payable.filters.pending') }}</span>
                    <span @class([
                        'px-1.5 py-0.2 rounded-full text-2xs tabular-nums font-bold',
                        'bg-amber-100 text-amber-800 dark:bg-amber-950/70 dark:text-amber-300' => $status === 'pending',
                        'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300' => $status !== 'pending',
                    ])>
                        {{ $this->counts['pending'] }}
                    </span>
                </button>

                <button
                    type="button"
                    wire:click="$set('status', 'paid')"
                    @class([
                        'inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-150 cursor-pointer',
                        'bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-2xs' => $status === 'paid',
                        'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-white/50 dark:hover:bg-gray-800/50' => $status !== 'paid',
                    ])
                >
                    <x-icon name="check" class="w-3.5 h-3.5" />
                    <span>{{ __('bills_payable.filters.paid') }}</span>
                    <span @class([
                        'px-1.5 py-0.2 rounded-full text-2xs tabular-nums font-bold',
                        'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300' => $status === 'paid',
                        'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300' => $status !== 'paid',
                    ])>
                        {{ $this->counts['paid'] }}
                    </span>
                </button>

                <button
                    type="button"
                    wire:click="$set('status', 'all')"
                    @class([
                        'inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-150 cursor-pointer',
                        'bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-2xs' => $status === 'all',
                        'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-white/50 dark:hover:bg-gray-800/50' => $status !== 'all',
                    ])
                >
                    <x-icon name="list-bullet" class="w-3.5 h-3.5" />
                    <span>{{ __('bills_payable.filters.all') }}</span>
                    <span @class([
                        'px-1.5 py-0.2 rounded-full text-2xs tabular-nums font-bold',
                        'bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200' => $status === 'all',
                        'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300' => $status !== 'all',
                    ])>
                        {{ $this->counts['all'] }}
                    </span>
                </button>
            </div>
        </div>

        @if ($this->search)
            <div class="px-5 py-2 border-b border-gray-200/60 dark:border-gray-700/60 bg-sky-50/30 dark:bg-sky-950/20 flex items-center justify-between text-xs text-gray-600 dark:text-gray-300">
                <span>Filtrando por: <strong class="font-semibold text-gray-900 dark:text-white">"{{ $this->search }}"</strong> ({{ trans_choice('bills_payable.filters.results_count', $this->bills->count(), ['count' => $this->bills->count()]) }})</span>
                <button
                    type="button"
                    wire:click="$set('search', '')"
                    class="inline-flex items-center gap-1 font-semibold text-primary-600 hover:text-primary-700 dark:text-primary-400 hover:underline cursor-pointer"
                >
                    <x-icon name="x-mark" class="w-3.5 h-3.5" />
                    <span>{{ __('bills_payable.filters.clear') }}</span>
                </button>
            </div>
        @endif

        <!-- High-Density Data Table -->
        <div class="overflow-x-auto flex-1 relative w-full max-w-full">
            <table class="w-full divide-y divide-gray-100 dark:divide-gray-700/80 border-separate border-spacing-0">
                <thead class="bg-gray-50/90 dark:bg-gray-900/60 backdrop-blur-xs sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200/80 dark:border-gray-700/80 whitespace-nowrap">
                            {{ __('bills_payable.table.due_date') }}
                        </th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200/80 dark:border-gray-700/80 whitespace-nowrap">
                            {{ __('bills_payable.table.product') }}
                        </th>
                        <th class="px-6 py-3.5 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200/80 dark:border-gray-700/80 whitespace-nowrap">
                            {{ __('bills_payable.table.quantity') }}
                        </th>
                        <th class="px-6 py-3.5 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200/80 dark:border-gray-700/80 whitespace-nowrap">
                            {{ __('bills_payable.table.total_value') }}
                        </th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200/80 dark:border-gray-700/80 whitespace-nowrap">
                            {{ __('bills_payable.table.status') }}
                        </th>
                        <th class="px-6 py-3.5 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200/80 dark:border-gray-700/80 whitespace-nowrap"></th>
                    </tr>
                </thead>
                <tbody
                    class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700/80 transition-opacity duration-200"
                    wire:loading.class="opacity-60"
                    wire:target="search, status"
                    x-data="{ selectedId: null }"
                >
                    @forelse ($this->bills as $bill)
                        @php
                            $isOverdue = !$bill->is_paid && $bill->due_date?->isPast();
                            $isDueToday = !$bill->is_paid && $bill->due_date?->isToday();
                            $daysOverdue = $isOverdue ? (int) $bill->due_date?->diffInDays(now()) : 0;
                            $daysUntilDue = (!$bill->is_paid && !$isOverdue && !$isDueToday) ? (int) now()->diffInDays($bill->due_date) : 0;
                        @endphp
                        <x-table.row
                            wire:key="bill-{{ $bill->id }}"
                            @click="selectedId = {{ $bill->id }}"
                            x-bind:data-selected="selectedId === {{ $bill->id }}"
                        >
                            <!-- Due Date -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col gap-0.5">
                                    <div @class([
                                        'text-sm font-semibold tabular-nums',
                                        'text-rose-600 dark:text-rose-400' => $isOverdue,
                                        'text-amber-600 dark:text-amber-400' => $isDueToday,
                                        'text-gray-900 dark:text-white' => !$isOverdue && !$isDueToday,
                                    ])>
                                        {{ $bill->due_date?->format('d/m/Y') ?? '-' }}
                                    </div>
                                    <div class="text-2xs text-gray-500 dark:text-gray-400">
                                        @if ($bill->is_paid)
                                            <span class="text-emerald-600 dark:text-emerald-400 font-medium">
                                                Pago em {{ $bill->payment_date?->format('d/m/Y') }}
                                            </span>
                                        @elseif ($isOverdue)
                                            <span class="text-rose-600 dark:text-rose-400 font-semibold">
                                                Vencido há {{ $daysOverdue }} {{ $daysOverdue === 1 ? 'dia' : 'dias' }}
                                            </span>
                                        @elseif ($isDueToday)
                                            <span class="text-amber-600 dark:text-amber-400 font-semibold">
                                                {{ __('bills_payable.table.due_today') }}
                                            </span>
                                        @else
                                            <span>Em {{ $daysUntilDue }} {{ $daysUntilDue === 1 ? 'dia' : 'dias' }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Product -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700/80 border border-gray-200/80 dark:border-gray-600 flex items-center justify-center text-gray-700 dark:text-gray-200 text-xs font-bold shrink-0">
                                        {{ mb_substr($bill->product->name, 0, 2) }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                                {{ $bill->product->name }}
                                            </span>
                                            @if ($bill->product->weight)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-2xs font-bold bg-gray-100 dark:bg-gray-700/80 text-gray-600 dark:text-gray-300 border border-gray-200/80 dark:border-gray-600 tabular-nums shrink-0">
                                                    {{ $bill->product->weight }}
                                                </span>
                                            @endif
                                        </div>
                                        @if ($bill->product->brand)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">
                                                {{ $bill->product->brand }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Quantity -->
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-800 dark:text-gray-200 tabular-nums">
                                    {{ number_format($bill->quantity, 0, ',', '.') }}
                                </div>
                                <div class="text-2xs text-gray-400 dark:text-gray-500">
                                    {{ __('bills_payable.table.units') }}
                                </div>
                            </td>

                            <!-- Total Value -->
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">
                                    R$ {{ number_format($bill->total_cost, 2, ',', '.') }}
                                </div>
                                <div class="text-2xs text-gray-400 dark:text-gray-500 tabular-nums">
                                    R$ {{ number_format($bill->unit_cost, 2, ',', '.') }} / un
                                </div>
                            </td>

                            <!-- Status Badge (Strict Semaphore & Dual Mode) -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($bill->is_paid)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-800/50 tabular-nums">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        {{ __('bills_payable.table.paid') }}
                                    </span>
                                @elseif ($isOverdue)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-400 border border-rose-200/50 dark:border-rose-800/50 tabular-nums">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        {{ __('bills_payable.table.overdue') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-400 border border-amber-200/50 dark:border-amber-800/50 tabular-nums">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        {{ __('bills_payable.table.pending') }}
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right whitespace-nowrap" @click.stop>
                                <div class="flex items-center justify-end gap-2">
                                    @if (!$bill->is_paid)
                                        @can(Permission::EditProductPurchase->value)
                                            <button
                                                type="button"
                                                wire:click="confirmPayment({{ $bill->id }})"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-primary-600 hover:bg-primary-700 active:bg-primary-800 shadow-2xs hover:shadow-xs transition-all duration-150 active:scale-95 cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary-500/30"
                                            >
                                                <x-icon name="check" class="w-3.5 h-3.5 stroke-[2.5]" />
                                                <span>{{ __('bills_payable.actions.pay') }}</span>
                                            </button>
                                        @endcan

                                        @can(Permission::DeleteProductPurchase->value)
                                            <button
                                                type="button"
                                                wire:click="confirmCancellation({{ $bill->id }})"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/60 border border-gray-200/80 dark:border-gray-700 shadow-2xs transition-all duration-150 active:scale-95 cursor-pointer focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-600"
                                            >
                                                <x-icon name="trash" class="w-3.5 h-3.5 text-rose-500 dark:text-rose-400" />
                                                <span>{{ __('bills_payable.actions.cancel') }}</span>
                                            </button>
                                        @endcan
                                    @else
                                        @can(Permission::DeleteProductPurchase->value)
                                            <button
                                                type="button"
                                                wire:click="confirmCancellation({{ $bill->id }})"
                                                title="Estornar / Cancelar conta paga"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/60 border border-gray-200/80 dark:border-gray-700 shadow-2xs transition-all duration-150 active:scale-95 cursor-pointer focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-600"
                                            >
                                                <x-icon name="arrow-uturn-left" class="w-3.5 h-3.5 text-rose-500 dark:text-rose-400" />
                                                <span>{{ __('bills_payable.actions.cancel') }}</span>
                                            </button>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </x-table.row>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center max-w-sm mx-auto text-center space-y-3">
                                    <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400 dark:text-gray-500 border border-gray-200/60 dark:border-gray-700">
                                        <x-icon name="document-text" class="w-6 h-6" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            {{ __('bills_payable.table.no_records') }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ __('bills_payable.table.no_records_description') }}
                                        </p>
                                    </div>
                                    @if ($this->search || $this->status !== 'pending')
                                        <div class="pt-2">
                                            <x-button
                                                xs
                                                outline
                                                label="{{ __('bills_payable.table.reset_filters') }}"
                                                wire:click="$set('search', ''); $set('status', 'pending')"
                                                class="font-medium cursor-pointer"
                                            />
                                        </div>
                                    @endif
                                </div>
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
