@props(['mobileSales', 'hasFilters' => false])

<div class="flex-1 flex flex-col min-h-0 w-full max-w-full">
    @if($mobileSales->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach ($mobileSales as $sale)
                <div
                    wire:key="mobile-sale-{{ $sale->id }}"
                    class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700/80 shadow-xs hover:border-gray-300 dark:hover:border-gray-600 transition-all duration-150 p-5 flex flex-col justify-between group"
                >
                    <div>
                        {{-- Header Row: Status & Device Date --}}
                        <div class="flex items-center justify-between gap-2">
                            @if($sale->status === \App\Enums\MobileSaleStatus::Pending)
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200/60 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800/60">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    {{ __('mobile_sales.status_pending') }}
                                </span>
                            @elseif($sale->status === \App\Enums\MobileSaleStatus::Synced)
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800/60">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    {{ __('mobile_sales.status_synced') }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-200/60 dark:bg-red-950/40 dark:text-red-400 dark:border-red-800/60">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    {{ __('mobile_sales.status_failed') }}
                                </span>
                            @endif

                            <span class="text-xs text-gray-500 dark:text-gray-400 tabular-nums flex items-center gap-1">
                                <x-icon name="calendar" class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500" />
                                {{ $sale->device_created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>

                        {{-- Customer Info --}}
                        <div class="mt-3">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white truncate" title="{{ $sale->customer->name ?? $sale->customer_name ?? __('mobile_sales.customer_placeholder') }}">
                                {{ $sale->customer->name ?? $sale->customer_name ?? __('mobile_sales.customer_placeholder') }}
                            </h3>
                            <div class="flex items-center justify-between gap-2 mt-1">
                                @if($sale->customer_id)
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-sky-700 dark:text-sky-300 truncate">
                                        <x-icon name="user" class="w-3.5 h-3.5 text-sky-500 shrink-0" />
                                        <span class="truncate">{{ __('mobile_sales.registered_customer') }}</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400 truncate">
                                        <x-icon name="user" class="w-3.5 h-3.5 text-gray-400 shrink-0" />
                                        <span class="truncate">{{ __('mobile_sales.walk_in_customer') }}</span>
                                    </span>
                                @endif
                                <span class="text-xs font-mono text-gray-400 dark:text-gray-500 shrink-0 tabular-nums bg-gray-50 dark:bg-gray-900/60 px-1.5 py-0.5 rounded border border-gray-100 dark:border-gray-800" title="{{ $sale->local_id }}">
                                    #{{ Str::limit($sale->local_id, 8, '') }}
                                </span>
                            </div>
                        </div>

                        {{-- Items Preview --}}
                        <div class="mt-3 pt-3 border-t border-gray-50 dark:border-gray-700/50">
                            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-1.5 font-medium">
                                <span>{{ __('mobile_sales.items_count', ['count' => $sale->items->count()]) }}</span>
                                <span class="tabular-nums">{{ __('mobile_sales.units_count', ['count' => $sale->items->sum('quantity')]) }}</span>
                            </div>

                            <div class="space-y-1">
                                @foreach($sale->items->take(2) as $item)
                                    <div class="flex items-center justify-between gap-2 text-xs text-gray-600 dark:text-gray-300">
                                        <span class="truncate">
                                            {{ $item->quantity }}x {{ $item->product->name ?? 'Produto' }}
                                        </span>
                                        <span class="tabular-nums text-gray-400 dark:text-gray-500 text-xs shrink-0 whitespace-nowrap">
                                            R$ {{ number_format($item->subtotal_cents / 100, 2, ',', '.') }}
                                        </span>
                                    </div>
                                @endforeach

                                @if($sale->items->count() > 2)
                                    <p class="text-xs text-gray-400 dark:text-gray-500 italic">
                                        +{{ $sale->items->count() - 2 }} {{ Str::lower(__('mobile_sales.items')) }}...
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Footer: Total & Actions --}}
                    <div class="pt-3 mt-4 border-t border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-baseline justify-between sm:block">
                            <p class="text-xs uppercase tracking-wider font-semibold text-gray-400 dark:text-gray-500">{{ __('mobile_sales.total') }}</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white tabular-nums whitespace-nowrap">
                                R$ {{ number_format($sale->total_amount_cents / 100, 2, ',', '.') }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <x-button
                                xs
                                flat
                                icon="eye"
                                label="{{ __('mobile_sales.details') }}"
                                wire:click="showDetails({{ $sale->id }})"
                                class="flex-1 sm:flex-initial justify-center font-medium whitespace-nowrap"
                            />
                            @if($sale->status === \App\Enums\MobileSaleStatus::Pending)
                                <x-button
                                    xs
                                    primary
                                    icon="shopping-cart"
                                    label="{{ __('mobile_sales.open_in_pos') }}"
                                    href="{{ route('sales.create', ['mobileSaleId' => $sale->id]) }}"
                                    class="flex-1 sm:flex-initial justify-center font-medium shadow-xs whitespace-nowrap"
                                />
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-xs p-12 text-center flex flex-col items-center justify-center">
            <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700/60 flex items-center justify-center mb-3">
                <x-icon name="device-phone-mobile" class="w-6 h-6 text-gray-400 dark:text-gray-500" />
            </div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                {{ __('mobile_sales.no_sales_found') }}
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-sm">
                {{ $hasFilters ? __('mobile_sales.no_sales_filter_subtitle') : __('mobile_sales.no_sales_empty_subtitle') }}
            </p>
            @if($hasFilters)
                <x-button
                    xs
                    outline
                    label="{{ __('mobile_sales.clear_filters') }}"
                    wire:click="clearFilters"
                    class="mt-4"
                />
            @endif
        </div>
    @endif

    @if ($mobileSales->hasPages())
        <div class="pt-4">
            {{ $mobileSales->links() }}
        </div>
    @endif
</div>

