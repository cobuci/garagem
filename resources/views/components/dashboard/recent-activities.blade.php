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
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <p @class([
                        'text-sm font-extrabold',
                        'text-emerald-600 dark:text-emerald-400' => $transaction->amount > 0,
                        'text-red-600 dark:text-red-400' => $transaction->amount < 0,
                        'text-gray-900 dark:text-white' => $transaction->amount == 0,
                    ])>
                        {{ $transaction->amount > 0 ? '+' : '' }} R$ {{ number_format(abs($transaction->amount) / 100, 2, ',', '.') }}
                    </p>
                    <p class="text-[10px] font-semibold text-gray-400 uppercase">
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
