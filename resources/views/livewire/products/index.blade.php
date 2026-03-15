@php use Illuminate\Contracts\Pagination\Paginator; @endphp
<div x-data="{ showPrices: true }" class="space-y-6 flex flex-col min-h-0 w-full max-w-full">
    <div class="flex-none bg-gray-50 dark:bg-gray-900 pb-2 w-full max-w-full">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('products.title') }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('products.subtitle') }}</p>
            </div>

            <div class="flex items-center gap-2 sm:justify-end">
                <div>
                    <livewire:products.create/>
                </div>

                <x-button
                    sm
                    outline
                    x-on:click="showPrices = !showPrices"
                    class="flex items-center gap-2 whitespace-nowrap"
                >
                    <x-icon x-show="showPrices" name="eye-slash" class="w-4 h-4"/>
                    <x-icon x-show="!showPrices" name="eye" class="w-4 h-4"/>
                    <span
                        x-text="showPrices ? '{{ __('products.hide_prices') }}' : '{{ __('products.show_prices') }}'"></span>
                </x-button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('products.total_cost_value') }}</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white"
                   :class="showPrices ? '' : 'blur-sm select-none'">
                    {{ __('products.currency_symbol') }} {{ number_format($this->stats['total_cost'], 2, ',', '.') }}
                </p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('products.total_sale_value') }}</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white"
                   :class="showPrices ? '' : 'blur-sm select-none'">
                    {{ __('products.currency_symbol') }} {{ number_format($this->stats['total_sale'], 2, ',', '.') }}
                </p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('products.total_profit') }}</p>
                <p class="text-lg font-bold text-primary-600 dark:text-primary-400"
                   :class="showPrices ? '' : 'blur-sm select-none'">
                    {{ __('products.currency_symbol') }} {{ number_format($this->stats['total_profit'], 2, ',', '.') }}
                </p>
            </div>
        </div>

        <div class="flex items-center border-b border-gray-200 dark:border-gray-700 overflow-x-auto w-full max-w-full">
            <div class="flex gap-8">
                <button
                    wire:click="selectCategory(0)"
                    class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors duration-200 flex items-center gap-2 {{ $selectedCategoryId === 0 ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
                >
                    <x-icon name="clock" class="w-4 h-4"/>
                    {{ __('products.latest_products') }}
                </button>
                @foreach($this->categories as $category)
                    <button
                        wire:click="selectCategory({{ $category->id }})"
                        class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors duration-200 flex items-center gap-2 {{ $selectedCategoryId === $category->id ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
                    >
                        @if($category->icon && view()->exists("components.wireui.icons.outline.{$category->icon}"))
                            <x-icon :name="$category->icon" class="w-4 h-4"/>
                        @endif
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <div
        class="flex-1 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col min-h-0 w-full max-w-full">
        <div class="overflow-x-auto flex-1 relative w-full max-w-full">
            <table class="w-full divide-y divide-gray-100 dark:divide-gray-700 border-separate border-spacing-0">
                <thead class="bg-gray-50 dark:bg-gray-900/50 sticky top-0 z-10">
                <tr>
                    <th class="w-16 px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 whitespace-nowrap">{{ __('products.id') }}</th>
                    <th class="min-w-50 px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 whitespace-nowrap">{{ __('products.name') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 whitespace-nowrap">{{ __('products.brand') }}</th>
                    <th class="w-24 px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 whitespace-nowrap">{{ __('products.weight') }}</th>
                    <th class="w-32 px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 whitespace-nowrap">{{ __('products.cost') }}</th>
                    <th class="w-32 px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 whitespace-nowrap">{{ __('products.sale') }}</th>
                    <th class="w-24 px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 whitespace-nowrap">{{ __('products.stock') }}</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($this->products as $product)
                    <tr class="transition {{ $product->stock_quantity <= 0 ? 'bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30' : 'hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 dark:text-white">{{ $product->id }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div
                                class="text-sm font-medium text-gray-900 dark:text-white break-words">{{ $product->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div
                                class="text-sm text-gray-600 dark:text-gray-300 break-words">{{ $product->brand }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div
                                class="text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ $product->weight }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                <span :class="showPrices ? '' : 'blur-sm select-none'">
                                    {{ __('products.currency_symbol') }} {{ number_format($product->unit_cost, 2, ',', '.') }}
                                </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                <span :class="showPrices ? '' : 'blur-sm select-none'">
                                    {{ __('products.currency_symbol') }} {{ number_format($product->sale_price, 2, ',', '.') }}
                                </span>
                        </td>
                        <td class="px-6 py-4">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->stock_quantity > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $product->stock_quantity }}
                                </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ __('products.empty') }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if ($this->selectedCategoryId !== 0 && $this->products instanceof Paginator && $this->products->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                {{ $this->products->links() }}
            </div>
        @endif
    </div>
</div>
