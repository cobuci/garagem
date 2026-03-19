<div>
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">{{ __('dashboard.title') }}</h1>
            <p class="mt-1 text-gray-500 dark:text-gray-400 font-medium">{{ __('dashboard.welcome_back', ['name' => auth()->user()->name ?? auth()->user()->email]) }}</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 border border-indigo-100 dark:border-indigo-800">
                <span class="w-2 h-2 mr-2 rounded-full bg-indigo-500 animate-pulse"></span>
                {{ now()->translatedFormat('d M, Y') }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- Total Balance Card --}}
        <div class="relative overflow-hidden bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 group hover:shadow-md transition-shadow duration-300">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 dark:bg-blue-900/10 rounded-full group-hover:scale-110 transition-transform duration-500"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/40 rounded-xl">
                        <x-icon name="banknotes" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
                <h3 class="text-gray-500 dark:text-gray-400 text-sm font-semibold uppercase tracking-wider">{{ __('dashboard.total_balance') }}</h3>
                <div class="mt-2 flex items-baseline">
                    <p class="text-3xl font-bold text-gray-900 dark:text-white leading-none">R$ {{ number_format($this->totalBalance, 2, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- Daily Sales Card --}}
        <div class="relative overflow-hidden bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 group hover:shadow-md transition-shadow duration-300">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 dark:bg-emerald-900/10 rounded-full group-hover:scale-110 transition-transform duration-500"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-900/40 rounded-xl">
                        <x-icon name="shopping-cart" class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <div class="flex flex-col items-end">
                        <div @class([
                            'flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-bold',
                            'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400' => $this->dailyMetrics['percent'] >= 0,
                            'bg-red-50 text-red-700 dark:bg-red-900/40 dark:text-red-400' => $this->dailyMetrics['percent'] < 0,
                        ])>
                            <x-icon name="{{ $this->dailyMetrics['percent'] >= 0 ? 'arrow-trending-up' : 'arrow-trending-down' }}" class="w-3 h-3" />
                            {{ number_format(abs($this->dailyMetrics['percent']), 1) }}%
                        </div>
                    </div>
                </div>
                <h3 class="text-gray-500 dark:text-gray-400 text-sm font-semibold uppercase tracking-wider">{{ __('dashboard.sales_today') }}</h3>
                <div class="mt-2">
                    <p class="text-3xl font-bold text-gray-900 dark:text-white leading-none">R$ {{ number_format($this->dailyMetrics['sales'], 2, ',', '.') }}</p>
                    <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
                        <div class="flex flex-col">
                            <span class="text-gray-400 text-[10px] uppercase font-bold">{{ __('dashboard.profit') }}</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400">R$ {{ number_format($this->dailyMetrics['profit'], 2, ',', '.') }}</span>
                        </div>
                        <div class="flex flex-col border-l border-gray-100 dark:border-gray-700 pl-2">
                            <span class="text-gray-400 text-[10px] uppercase font-bold">{{ __('dashboard.previous_profit') }}</span>
                            <span class="font-bold text-gray-500">R$ {{ number_format($this->dailyMetrics['previous_profit'], 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Monthly Sales Card --}}
        <div class="relative overflow-hidden bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 group hover:shadow-md transition-shadow duration-300">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-purple-50 dark:bg-purple-900/10 rounded-full group-hover:scale-110 transition-transform duration-500"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-purple-50 dark:bg-purple-900/40 rounded-xl">
                        <x-icon name="calendar-days" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div class="flex flex-col items-end">
                        <div @class([
                            'flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-bold',
                            'bg-purple-50 text-purple-700 dark:bg-purple-900/40 dark:text-purple-400' => $this->monthlyMetrics['percent'] >= 0,
                            'bg-red-50 text-red-700 dark:bg-red-900/40 dark:text-red-400' => $this->monthlyMetrics['percent'] < 0,
                        ])>
                            <x-icon name="{{ $this->monthlyMetrics['percent'] >= 0 ? 'arrow-trending-up' : 'arrow-trending-down' }}" class="w-3 h-3" />
                            {{ number_format(abs($this->monthlyMetrics['percent']), 1) }}%
                        </div>
                    </div>
                </div>
                <h3 class="text-gray-500 dark:text-gray-400 text-sm font-semibold uppercase tracking-wider">{{ __('dashboard.sales_month') }}</h3>
                <div class="mt-2">
                    <p class="text-3xl font-bold text-gray-900 dark:text-white leading-none">R$ {{ number_format($this->monthlyMetrics['sales'], 2, ',', '.') }}</p>
                    <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
                        <div class="flex flex-col">
                            <span class="text-gray-400 text-[10px] uppercase font-bold">{{ __('dashboard.profit') }}</span>
                            <span class="font-bold text-purple-600 dark:text-purple-400">R$ {{ number_format($this->monthlyMetrics['profit'], 2, ',', '.') }}</span>
                        </div>
                        <div class="flex flex-col border-l border-gray-100 dark:border-gray-700 pl-2">
                            <span class="text-gray-400 text-[10px] uppercase font-bold">{{ __('dashboard.previous_profit') }}</span>
                            <span class="font-bold text-gray-500">R$ {{ number_format($this->monthlyMetrics['previous_profit'], 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Recent Activities --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/50">
                <div class="flex items-center gap-2">
                    <x-icon name="list-bullet" class="w-5 h-5 text-gray-400" />
                    <h3 class="font-bold text-gray-900 dark:text-white">{{ __('dashboard.recent_activities') }}</h3>
                </div>
                <button class="text-sm text-indigo-600 dark:text-indigo-400 font-bold hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors">{{ __('dashboard.view_all') }}</button>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-gray-700/50">
                @forelse($this->recentSales as $sale)
                    <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors group">
                        <div class="flex items-center space-x-4">
                            <div @class([
                                'w-12 h-12 rounded-2xl flex items-center justify-center transition-transform group-hover:scale-110',
                                'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400' => $sale->payment_method !== 'cash',
                                'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' => $sale->payment_method === 'cash',
                            ])>
                                <x-icon name="{{ $sale->payment_method === 'credit_card' ? 'credit-card' : ($sale->payment_method === 'cash' ? 'banknotes' : 'shopping-bag') }}" class="w-6 h-6" />
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $sale->customer->name ?? __('dashboard.new_order', ['id' => $sale->id]) }}
                                </p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span @class([
                                        'px-1.5 py-0.5 rounded text-[10px] font-bold uppercase',
                                        'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-400' => $sale->status->value === 'paid',
                                        'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400' => $sale->status->value === 'pending',
                                        'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400' => $sale->status->value === 'cancelled',
                                    ])>
                                        {{ $sale->status->value }}
                                    </span>
                                    <span class="text-gray-300 dark:text-gray-600">•</span>
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                        <x-icon name="clock" class="w-3 h-3" />
                                        {{ $sale->created_at->diffForHumans() }}
                                    </span>
                                    <span class="text-gray-300 dark:text-gray-600">•</span>
                                    <span class="text-xs font-semibold text-gray-400 uppercase">{{ str_replace('_', ' ', $sale->payment_method) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-extrabold text-gray-900 dark:text-white">R$ {{ number_format($sale->total_amount, 2, ',', '.') }}</p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ $sale->items->count() }} {{ __('dashboard.sales') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 dark:bg-gray-900 mb-4">
                            <x-icon name="inbox" class="w-8 h-8 text-gray-300 dark:text-gray-600" />
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 font-medium">Nenhuma venda registrada recentemente.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Quick Stats / Mini Card --}}
        <div class="space-y-6">
            <div class="bg-indigo-600 rounded-2xl p-6 shadow-lg shadow-indigo-200 dark:shadow-none relative overflow-hidden group">
                <div class="absolute right-0 bottom-0 opacity-10 transform translate-x-4 translate-y-4 transition-transform group-hover:scale-110">
                    <x-icon name="bolt" class="w-32 h-32 text-white" />
                </div>
                <div class="relative">
                    <h4 class="text-indigo-100 text-sm font-bold uppercase tracking-wider mb-1">{{ __('dashboard.today') }}</h4>
                    <p class="text-white text-2xl font-black">R$ {{ number_format($this->dailyMetrics['sales'], 2, ',', '.') }}</p>
                    <div class="mt-4 pt-4 border-t border-indigo-500/30">
                        <div class="flex items-center justify-between text-indigo-100 text-xs font-bold">
                            <span>{{ __('dashboard.yesterday') }}</span>
                            <span>R$ {{ number_format($this->dailyMetrics['previous_sales'], 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
                <h4 class="text-gray-900 dark:text-white text-sm font-bold mb-4">{{ __('dashboard.month') }}</h4>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500 font-medium">{{ __('dashboard.sales') }}</span>
                        <span class="text-xs font-bold text-gray-900 dark:text-white">R$ {{ number_format($this->monthlyMetrics['sales'], 2, ',', '.') }}</span>
                    </div>
                    <div class="w-full bg-gray-100 dark:bg-gray-700 h-2 rounded-full overflow-hidden">
                        @php
                            $max = max($this->monthlyMetrics['sales'], $this->monthlyMetrics['previous_sales'], 1);
                            $percentage = ($this->monthlyMetrics['sales'] / $max) * 100;
                        @endphp
                        <div class="bg-indigo-500 h-full rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500 font-medium">{{ __('dashboard.last_month') }}</span>
                        <span class="text-xs font-bold text-gray-900 dark:text-white">R$ {{ number_format($this->monthlyMetrics['previous_sales'], 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
