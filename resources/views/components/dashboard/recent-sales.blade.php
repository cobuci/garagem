@props(['sales'])

<div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/50">
        <div class="flex items-center gap-2">
            <x-icon name="list-bullet" class="w-5 h-5 text-gray-400" />
            <h3 class="font-bold text-gray-900 dark:text-white">{{ __('dashboard.recent_activities') }}</h3>
        </div>
        <button class="text-sm text-indigo-600 dark:text-indigo-400 font-bold hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors">{{ __('dashboard.view_all') }}</button>
    </div>
    <div class="divide-y divide-gray-50 dark:divide-gray-700/50">
        @forelse($sales as $sale)
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
