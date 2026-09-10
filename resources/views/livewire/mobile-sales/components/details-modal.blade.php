@props(['selectedMobileSale'])

<x-modal-card wire:model="showDetailsModal" title="{{ __('mobile_sales.sale_details') }}" max-width="2xl">
    @if($selectedMobileSale)
        <div class="space-y-6">
            {{-- Header Metadata Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50 dark:bg-gray-900/40 p-4 rounded-xl border border-gray-100 dark:border-gray-700/80">
                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('mobile_sales.customer') }}</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white mt-0.5">
                        {{ $selectedMobileSale->customer->name ?? $selectedMobileSale->customer_name ?? __('mobile_sales.customer_placeholder') }}
                    </p>
                    <span class="inline-flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        @if($selectedMobileSale->customer_id)
                            <x-icon name="user" class="w-3.5 h-3.5 text-sky-500" />
                            {{ __('mobile_sales.registered_customer') }}
                        @else
                            <x-icon name="user" class="w-3.5 h-3.5 text-gray-400" />
                            {{ __('mobile_sales.walk_in_customer') }}
                        @endif
                    </span>
                </div>

                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('sales.status') ?? 'Status' }}</p>
                    @if($selectedMobileSale->status === \App\Enums\MobileSaleStatus::Pending)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200/60 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            {{ __('mobile_sales.status_pending') }}
                        </span>
                    @elseif($selectedMobileSale->status === \App\Enums\MobileSaleStatus::Synced)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            {{ __('mobile_sales.status_synced') }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-200/60 dark:bg-red-950/40 dark:text-red-400 dark:border-red-800/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                            {{ __('mobile_sales.status_failed') }}
                        </span>
                    @endif
                </div>

                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('mobile_sales.device_date') }}</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mt-0.5 tabular-nums flex items-center gap-1.5">
                        <x-icon name="calendar" class="w-3.5 h-3.5 text-gray-400" />
                        {{ $selectedMobileSale->device_created_at->format('d/m/Y H:i:s') }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('mobile_sales.local_id') }}</p>
                    <p class="text-xs font-mono text-gray-700 dark:text-gray-300 mt-0.5 truncate bg-white dark:bg-gray-800 px-2 py-1 rounded border border-gray-200 dark:border-gray-700" title="{{ $selectedMobileSale->local_id }}">
                        {{ $selectedMobileSale->local_id }}
                    </p>
                </div>
            </div>

            {{-- Items Container: Mobile Cards List (< sm) and Desktop Table (>= sm) --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('mobile_sales.items') }}</p>
                    <span class="text-xs text-gray-500 dark:text-gray-400 tabular-nums">
                        {{ __('mobile_sales.items_count', ['count' => $selectedMobileSale->items->count()]) }} &middot; {{ __('mobile_sales.units_count', ['count' => $selectedMobileSale->items->sum('quantity')]) }}
                    </span>
                </div>

                {{-- Mobile View (< sm): Sleek Card List --}}
                <div class="block sm:hidden border border-gray-100 dark:border-gray-700/80 rounded-xl divide-y divide-gray-100 dark:divide-gray-700/80 bg-white dark:bg-gray-800 overflow-hidden">
                    @foreach($selectedMobileSale->items as $item)
                        <div class="p-3 flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <p class="font-medium text-sm text-gray-900 dark:text-white truncate">
                                    {{ $item->product->name }}
                                </p>
                                @if($item->product->brand || $item->product->weight)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        {{ $item->product->brand }}
                                        @if($item->product->weight)
                                            &middot; {{ $item->product->weight }}
                                        @endif
                                    </p>
                                @endif
                                <p class="text-xs text-gray-400 dark:text-gray-500 tabular-nums mt-1 font-medium">
                                    {{ $item->quantity }}x R$ {{ number_format($item->unit_price_cents / 100, 2, ',', '.') }}
                                </p>
                            </div>
                            <div class="text-right shrink-0 pt-0.5">
                                <p class="text-sm font-bold text-gray-900 dark:text-white tabular-nums whitespace-nowrap">
                                    R$ {{ number_format($item->subtotal_cents / 100, 2, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Desktop View (>= sm): Full Data Table --}}
                <div class="hidden sm:block border border-gray-100 dark:border-gray-700 rounded-xl overflow-hidden">
                    <table class="w-full text-sm divide-y divide-gray-100 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/60">
                            <tr>
                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('mobile_sales.product') }}</th>
                                <th class="px-3 py-2.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-20">{{ __('mobile_sales.quantity') }}</th>
                                <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-28">{{ __('mobile_sales.unit_price') }}</th>
                                <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-28">{{ __('mobile_sales.subtotal') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            @foreach($selectedMobileSale->items as $item)
                                <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-3 py-2.5">
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $item->product->name }}</p>
                                        @if($item->product->brand || $item->product->weight)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                {{ $item->product->brand }}
                                                @if($item->product->weight)
                                                    &middot; {{ $item->product->weight }}
                                                @endif
                                            </p>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2.5 text-center tabular-nums text-gray-700 dark:text-gray-300 font-medium">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="px-3 py-2.5 text-right tabular-nums text-gray-600 dark:text-gray-400 text-xs">
                                        R$ {{ number_format($item->unit_price_cents / 100, 2, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-2.5 text-right tabular-nums text-gray-900 dark:text-white font-semibold">
                                        R$ {{ number_format($item->subtotal_cents / 100, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Total Amount Row --}}
            <div class="p-4 bg-gray-50 dark:bg-gray-900/40 rounded-xl border border-gray-100 dark:border-gray-700/80 flex items-center justify-between gap-2">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('mobile_sales.total') }}</span>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 tabular-nums">
                        {{ __('mobile_sales.items_count', ['count' => $selectedMobileSale->items->count()]) }} &middot; {{ __('mobile_sales.units_count', ['count' => $selectedMobileSale->items->sum('quantity')]) }}
                    </p>
                </div>
                <span class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white tabular-nums whitespace-nowrap">
                    R$ {{ number_format($selectedMobileSale->total_amount_cents / 100, 2, ',', '.') }}
                </span>
            </div>
        </div>
    @endif

    <x-slot name="footer">
        <div class="w-full flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            @if($selectedMobileSale)
                @if($selectedMobileSale->status === \App\Enums\MobileSaleStatus::Pending)
                    <x-button
                        primary
                        icon="shopping-cart"
                        label="{{ __('mobile_sales.open_in_pos') }}"
                        href="{{ route('sales.create', ['mobileSaleId' => $selectedMobileSale->id]) }}"
                        class="w-full sm:w-auto justify-center shadow-xs font-medium whitespace-nowrap sm:order-1"
                    />
                @elseif($selectedMobileSale->status === \App\Enums\MobileSaleStatus::Synced)
                    <span class="inline-flex items-center justify-center gap-1.5 text-xs font-medium text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-3 py-2 rounded-lg border border-emerald-200/60 dark:border-emerald-800/60 w-full sm:w-auto sm:order-1">
                        <x-icon name="check-circle" class="w-4 h-4 text-emerald-500 shrink-0" />
                        <span class="truncate">{{ __('mobile_sales.synced_notice') }}</span>
                    </span>
                @endif

                <div class="flex items-center justify-between sm:justify-start gap-2 w-full sm:w-auto sm:order-2">
                    @can(\App\Enums\Permission::DeleteSale->value)
                        @if($selectedMobileSale->status !== \App\Enums\MobileSaleStatus::Synced)
                            <x-button
                                flat
                                negative
                                icon="trash"
                                label="{{ __('mobile_sales.delete') }}"
                                wire:confirm="{{ __('mobile_sales.delete_confirm') }}"
                                wire:click="delete({{ $selectedMobileSale->id }})"
                                class="flex-1 sm:flex-initial justify-center"
                            />
                        @endif
                    @endcan
                    <x-button flat label="{{ __('mobile_sales.close') }}" x-on:click="close" class="flex-1 sm:flex-initial justify-center sm:ml-auto" />
                </div>
            @else
                <x-button flat label="{{ __('mobile_sales.close') }}" x-on:click="close" class="w-full sm:w-auto justify-center" />
            @endif
        </div>
    </x-slot>
</x-modal-card>
