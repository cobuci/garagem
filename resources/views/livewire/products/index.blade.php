@php
    use Illuminate\Contracts\Pagination\Paginator;
    $categories = $this->categories;
@endphp
<div x-data="{ showPrices: true }" class="space-y-6 flex flex-col min-h-0 w-full max-w-full">
    <div class="flex-none bg-gray-50 dark:bg-gray-900 pb-2 w-full max-w-full space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ __('products.title') }}</h1>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200/80 dark:border-gray-700 tabular-nums">
                        {{ $selectedCategoryId === 0 ? __('products.latest_products') : ($categories->firstWhere('id', $selectedCategoryId)?->name ?? __('products.title')) }}
                    </span>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('products.subtitle') }}</p>
            </div>

            <div class="grid grid-cols-1 sm:flex sm:flex-wrap sm:items-center gap-2 sm:justify-end w-full sm:w-auto">
                <div class="grid grid-cols-1 sm:flex sm:items-center gap-2 w-full sm:w-auto">
                    @can(\App\Enums\Permission::CreateProduct->value)
                        <div class="w-full sm:w-auto">
                            <livewire:products.create :categories="$categories"/>
                        </div>
                    @endcan

                    <div class="grid grid-cols-2 sm:flex sm:items-center gap-2 w-full sm:w-auto">
                        @can(\App\Enums\Permission::CreateProductPurchase->value)
                            <div class="w-full sm:w-auto">
                                <livewire:products.purchase :categories="$categories"/>
                            </div>
                        @endcan

                        <x-button
                            outline
                            x-on:click="showPrices = !showPrices"
                            class="w-full sm:w-auto justify-center bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/60 hover:text-gray-900 dark:hover:text-white font-medium shadow-xs transition-all duration-150 active:scale-[0.98] flex items-center gap-2 whitespace-nowrap"
                        >
                            <x-icon x-show="showPrices" name="eye-slash" class="w-4 h-4 text-gray-500 dark:text-gray-400 shrink-0"/>
                            <x-icon x-show="!showPrices" name="eye" class="w-4 h-4 text-gray-500 dark:text-gray-400 shrink-0"/>
                            <span
                                x-text="showPrices ? '{{ __('products.hide_prices') }}' : '{{ __('products.show_prices') }}'"></span>
                        </x-button>
                    </div>
                </div>

                @can(\App\Enums\Permission::EditProduct->value)
                    <livewire:products.edit :categories="$categories"/>
                @endcan

                @can(\App\Enums\Permission::DeleteProduct->value)
                    <livewire:products.delete/>
                @endcan
            </div>
        </div>

        @php
            $stats = $this->stats;
            $marginPercent = $stats['total_sale'] > 0 ? round(($stats['total_profit'] / $stats['total_sale']) * 100, 1) : 0;
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs overflow-hidden grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-100 dark:divide-gray-700/80">
            <div class="p-4 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('products.total_cost_value') }}</span>
                    <span class="text-xs font-medium text-gray-400 dark:text-gray-500">{{ __('products.capital_invested') }}</span>
                </div>
                <p class="text-xl font-bold tracking-tight text-gray-900 dark:text-white tabular-nums transition-all duration-200"
                   :class="showPrices ? '' : 'blur-sm select-none'">
                    {{ __('products.currency_symbol') }} {{ number_format($stats['total_cost'], 2, ',', '.') }}
                </p>
            </div>

            <div class="p-4 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('products.total_sale_value') }}</span>
                    <span class="text-xs font-medium text-gray-400 dark:text-gray-500">{{ __('products.projected_revenue') }}</span>
                </div>
                <p class="text-xl font-bold tracking-tight text-gray-900 dark:text-white tabular-nums transition-all duration-200"
                   :class="showPrices ? '' : 'blur-sm select-none'">
                    {{ __('products.currency_symbol') }} {{ number_format($stats['total_sale'], 2, ',', '.') }}
                </p>
            </div>

            <div class="p-4 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('products.total_profit') }}</span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-bold tabular-nums {{ $stats['total_profit'] >= 0 ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-800/50' : 'bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-400 border border-red-200/50 dark:border-red-800/50' }}">
                        {{ $stats['total_profit'] >= 0 ? '+' : '' }}{{ number_format($marginPercent, 1, ',', '.') }}% {{ __('products.margin') }}
                    </span>
                </div>
                <p class="text-xl font-bold tracking-tight tabular-nums transition-all duration-200 {{ $stats['total_profit'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}"
                   :class="showPrices ? '' : 'blur-sm select-none'">
                    {{ __('products.currency_symbol') }} {{ number_format($stats['total_profit'], 2, ',', '.') }}
                </p>
            </div>
        </div>

        <div class="bg-gray-100/80 dark:bg-gray-900/60 p-1.5 rounded-xl border border-gray-200/60 dark:border-gray-800 flex items-center gap-1.5 overflow-x-auto w-full max-w-full">
            <button
                type="button"
                wire:click="selectCategory(0)"
                class="px-3.5 py-2 rounded-lg text-xs font-medium whitespace-nowrap transition-all duration-150 flex items-center gap-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 {{ $selectedCategoryId === 0 ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-xs font-semibold border border-gray-200/70 dark:border-gray-700/80' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-white/50 dark:hover:bg-gray-800/40' }}"
            >
                <x-icon name="clock" class="w-4 h-4 {{ $selectedCategoryId === 0 ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 dark:text-gray-500' }}"/>
                <span>{{ __('products.latest_products') }}</span>
            </button>
            @foreach($categories as $category)
                <button
                    type="button"
                    wire:click="selectCategory({{ $category->id }})"
                    class="px-3.5 py-2 rounded-lg text-xs font-medium whitespace-nowrap transition-all duration-150 flex items-center gap-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 {{ $selectedCategoryId === $category->id ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-xs font-semibold border border-gray-200/70 dark:border-gray-700/80' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-white/50 dark:hover:bg-gray-800/40' }}"
                >
                    @if($category->icon && view()->exists("components.wireui.icons.outline.{$category->icon}"))
                        <x-icon :name="$category->icon" class="w-4 h-4 {{ $selectedCategoryId === $category->id ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 dark:text-gray-500' }}"/>
                    @endif
                    <span>{{ $category->name }}</span>

                    @if(in_array($category->id, $this->skippedCategories))
                        <span class="inline-flex items-center text-gray-400 dark:text-gray-500" title="{{ __('products.skipped') }}">
                            <x-icon name="eye-slash" class="w-3.5 h-3.5"/>
                        </span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    <div
        class="flex-1 bg-white dark:bg-gray-800 rounded-xl shadow-xs border border-gray-200/80 dark:border-gray-700/80 overflow-hidden flex flex-col min-h-0 w-full max-w-full">
        <div class="overflow-x-auto flex-1 relative w-full max-w-full">
            <table class="w-full divide-y divide-gray-100 dark:divide-gray-700/80 border-separate border-spacing-0">
                <thead class="bg-gray-50/90 dark:bg-gray-900/80 sticky top-0 z-10">
                <tr>
                    <th class="w-14 px-4 py-3.5 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200/80 dark:border-gray-700/80 whitespace-nowrap">{{ __('products.id') }}</th>
                    <th class="min-w-60 px-6 py-3.5 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200/80 dark:border-gray-700/80 whitespace-nowrap">{{ __('products.name') }}</th>
                    <th class="w-28 px-4 py-3.5 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200/80 dark:border-gray-700/80 whitespace-nowrap">{{ __('products.specification') }}</th>
                    <th class="w-32 px-4 py-3.5 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200/80 dark:border-gray-700/80 whitespace-nowrap">{{ __('products.cost') }}</th>
                    <th class="w-40 px-6 py-3.5 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200/80 dark:border-gray-700/80 whitespace-nowrap">{{ __('products.pricing') }}</th>
                    <th class="w-36 px-6 py-3.5 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200/80 dark:border-gray-700/80 whitespace-nowrap">{{ __('products.stock') }}</th>
                    <th class="w-20 px-4 py-3.5 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200/80 dark:border-gray-700/80 whitespace-nowrap"></th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700/80 transition-opacity duration-200"
                       wire:loading.class="opacity-60"
                       x-data="{ selectedId: null }">
                @forelse ($this->products as $product)
                    @php
                        $unitCost = (float) $product->unit_cost;
                        $salePrice = (float) $product->sale_price;
                        $unitProfit = $salePrice - $unitCost;
                        $unitMargin = $salePrice > 0 ? round(($unitProfit / $salePrice) * 100) : 0;
                    @endphp
                    <x-table.row
                        wire:key="product-{{ $product->id }}"
                        @click="selectedId = {{ $product->id }}"
                        x-bind:data-selected="selectedId === {{ $product->id }}"
                        :variant="$product->stock_quantity <= 0 ? 'danger' : 'default'"
                    >
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="text-xs font-mono tabular-nums text-gray-400 dark:text-gray-500">#{{ $product->id }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div
                                class="text-sm font-semibold text-gray-900 dark:text-white truncate max-w-xs" title="{{ $product->name }}">{{ $product->name }}</div>
                            @if($product->brand)
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700/60 text-gray-600 dark:text-gray-300">
                                        {{ $product->brand }}
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div
                                class="text-sm text-gray-700 dark:text-gray-300 tabular-nums">{{ $product->weight }}</div>
                        </td>
                        <td class="px-4 py-4 text-right whitespace-nowrap">
                            <span class="text-sm font-medium tabular-nums text-gray-600 dark:text-gray-300 transition-all duration-200"
                                  :class="showPrices ? '' : 'blur-sm select-none'">
                                {{ __('products.currency_symbol') }} {{ number_format($unitCost, 2, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="text-sm font-bold tabular-nums text-gray-900 dark:text-white transition-all duration-200"
                                 :class="showPrices ? '' : 'blur-sm select-none'">
                                {{ __('products.currency_symbol') }} {{ number_format($salePrice, 2, ',', '.') }}
                            </div>
                            @if ($salePrice > 0 && $unitCost > 0)
                                <div class="text-xs font-semibold tabular-nums mt-0.5 transition-all duration-200 {{ $unitProfit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}"
                                     :class="showPrices ? '' : 'blur-sm select-none'">
                                    {{ $unitProfit >= 0 ? '+' : '' }}{{ __('products.currency_symbol') }} {{ number_format($unitProfit, 2, ',', '.') }}
                                    <span class="font-normal opacity-80">({{ $unitMargin }}%)</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            @if ($product->stock_quantity > 0)
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/70 dark:bg-emerald-950/30 dark:text-emerald-400 dark:border-emerald-800/40 tabular-nums">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>{{ $product->stock_quantity }} {{ $product->stock_quantity === 1 ? __('products.unit_single') : __('products.unit_plural') }}</span>
                                </div>
                            @else
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200/70 dark:bg-red-950/30 dark:text-red-400 dark:border-red-800/40 tabular-nums">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    <span>{{ __('products.out_of_stock') }}</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-right whitespace-nowrap" @click.stop>
                            <div class="flex items-center justify-end gap-1">
                                @can(\App\Enums\Permission::EditProduct->value)
                                    <button
                                        type="button"
                                        title="{{ __('products.edit') }}"
                                        x-on:click="$dispatch('product:edit', { product: {{ $product->id }} })"
                                        class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-950/50 dark:hover:text-primary-400 transition-colors duration-150 focus:outline-hidden focus:ring-2 focus:ring-primary-500/20"
                                    >
                                        <x-icon name="pencil" class="w-4 h-4"/>
                                    </button>
                                @endcan

                                @can(\App\Enums\Permission::DeleteProduct->value)
                                    <button
                                        type="button"
                                        title="{{ __('products.delete') }}"
                                        x-on:click="$dispatch('product:delete', { product: {{ $product->id }} })"
                                        class="group p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-950/50 transition-colors duration-150 focus:outline-hidden focus:ring-2 focus:ring-red-500/20"
                                    >
                                        <x-icon name="trash" class="w-4 h-4 text-gray-400 group-hover:text-red-700 dark:group-hover:text-red-300"/>
                                    </button>
                                @endcan
                            </div>
                        </td>
                    </x-table.row>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-14 text-center">
                            <div class="flex flex-col items-center justify-center max-w-xs mx-auto text-center space-y-3">
                                <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400 dark:text-gray-500 border border-gray-200/60 dark:border-gray-700">
                                    <x-icon name="archive-box" class="w-6 h-6"/>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        {{ __('products.empty') }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ __('products.empty_description') }}
                                    </p>
                                </div>
                                @can(\App\Enums\Permission::CreateProduct->value)
                                    <div class="pt-2">
                                        <x-button
                                            sm
                                            primary
                                            icon="plus"
                                            label="{{ __('products.new') }}"
                                            class="font-semibold shadow-xs hover:shadow transition-all duration-150 active:scale-[0.98]"
                                            x-on:click="$dispatch('product:open-create')"
                                        />
                                    </div>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if ($this->selectedCategoryId !== 0 && $this->products instanceof Paginator && $this->products->hasPages())
            <div class="px-6 py-4 border-t border-gray-200/80 dark:border-gray-700/80 bg-gray-50/50 dark:bg-gray-900/40">
                {{ $this->products->links() }}
            </div>
        @endif
    </div>
</div>

