@props(['activities'])

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/50">
        <div class="flex items-center gap-2">
            <x-icon name="list-bullet" class="w-5 h-5 text-gray-400" />
            <h3 class="font-bold text-gray-900 dark:text-white">{{ __('dashboard.recent_activities') }}</h3>
        </div>
        <a href="{{ route('recent-activities.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 font-bold hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors">
            {{ __('dashboard.view_all') }}
        </a>
    </div>
    <div class="divide-y divide-gray-50 dark:divide-gray-700/50">
        @forelse($activities as $transaction)
            <div class="px-4 sm:px-6 py-4 flex items-center justify-between hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors group">
                <div class="flex items-center space-x-3 sm:space-x-4 min-w-0 flex-1">
                    <div @class([
                        'w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl flex-none flex items-center justify-center transition-transform group-hover:scale-110',
                        'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' => $transaction->type === \App\Enums\TransactionType::Sale,
                        'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400' => in_array($transaction->type, [\App\Enums\TransactionType::Purchase, \App\Enums\TransactionType::CancelledSale]),
                        'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' => $transaction->type === \App\Enums\TransactionType::ManualAdjustment,
                    ])>
                        <x-icon name="{{ match($transaction->type) {
                            \App\Enums\TransactionType::Sale => 'shopping-bag',
                            \App\Enums\TransactionType::Purchase => 'shopping-cart',
                            \App\Enums\TransactionType::CancelledSale => 'x-circle',
                            \App\Enums\TransactionType::ManualAdjustment => 'adjustments-horizontal',
                        } }}" class="w-5 h-5 sm:w-6 sm:h-6" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold text-gray-900 dark:text-white truncate">
                            {{ $transaction->description }}
                        </p>
                        <div class="flex items-center gap-1.5 mt-0.5 overflow-hidden">
                            <span @class([
                                'px-1 sm:px-1.5 py-0.5 rounded text-[9px] sm:text-[10px] font-bold uppercase flex-none',
                                'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-400' => $transaction->type === \App\Enums\TransactionType::Sale,
                                'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400' => in_array($transaction->type, [\App\Enums\TransactionType::Purchase, \App\Enums\TransactionType::CancelledSale]),
                                'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400' => $transaction->type === \App\Enums\TransactionType::ManualAdjustment,
                            ])>
                                {{ $transaction->type->label() }}
                            </span>
                            <span class="text-gray-300 dark:text-gray-600 flex-none">•</span>
                            <span class="text-[10px] sm:text-xs font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1 truncate">
                                <x-icon name="clock" class="w-3 h-3 flex-none" />
                                <span class="truncate">{{ $transaction->transaction_date->diffForHumans() }}</span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-right flex-none ml-3">
                    <p @class([
                        'text-sm font-extrabold whitespace-nowrap',
                        'text-emerald-600 dark:text-emerald-400' => $transaction->amount > 0,
                        'text-red-600 dark:text-red-400' => $transaction->amount < 0,
                        'text-gray-900 dark:text-white' => $transaction->amount == 0,
                    ])>
                        {{ $transaction->amount > 0 ? '+' : '' }} R$ {{ number_format(abs($transaction->amount) / 100, 2, ',', '.') }}
                    </p>
                    <p class="hidden sm:block text-[10px] font-semibold text-gray-400 uppercase">
                        {{ $transaction->transaction_date->format('d/m/Y H:i') }}
                    </p>
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
</div>
