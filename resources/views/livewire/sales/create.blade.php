<div x-data="{ cartOpen: false }" class="h-[calc(100vh-4rem)] lg:h-screen -m-4 lg:-m-8 flex flex-col overflow-hidden bg-gray-50 dark:bg-gray-950 relative">
    <div class="flex flex-1 overflow-hidden h-full">
        <div class="flex-1 flex flex-col min-w-0 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 relative">
            <div class="p-4 border-b border-gray-200 dark:border-gray-800 space-y-3 bg-white/95 dark:bg-gray-900/95 backdrop-blur-sm sticky top-0 z-10">
                <div class="flex items-center justify-between gap-4">
                    <h1 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white hidden sm:block">{{ __('sales.title') }}</h1>
                    <div class="flex-1 max-w-xl flex gap-2">
                        <div class="relative flex-1 group">
                            <x-input
                                icon="magnifying-glass"
                                wire:model.live.debounce.300ms="search"
                                placeholder="{{ __('sales.search_placeholder') }}"
                                shadowless
                                x-on:keydown.window="if ($event.key === '/' && !['INPUT', 'TEXTAREA'].includes($event.target.tagName)) { $event.preventDefault(); $el.focus(); }"
                                class="border-gray-200 dark:border-gray-700 focus:!ring-primary-500/20"
                            />
                            <div class="absolute right-3 top-2.5 hidden sm:flex items-center gap-1 pointer-events-none">
                                <kbd class="px-1.5 py-0.5 text-xs font-mono font-medium text-gray-400 bg-gray-50 border border-gray-200 dark:border-gray-700 rounded dark:bg-gray-800 dark:text-gray-400">/</kbd>
                            </div>
                        </div>
                        <div class="w-40 sm:w-48">
                            <x-select
                                wire:model.live="selectedCategoryId"
                                placeholder="{{ __('sales.categories_placeholder') }}"
                                :options="$this->categories"
                                option-label="name"
                                option-value="id"
                                shadowless
                            />
                        </div>
                    </div>
                </div>

                <!-- Chips de Categorias -->
                <div class="flex gap-1.5 overflow-x-auto pb-1 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none] scroll-smooth">
                    <button
                        type="button"
                        wire:click="$set('selectedCategoryId', null)"
                        @class([
                            'px-3.5 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-colors border',
                            'bg-primary-600 text-white border-primary-600 shadow-sm' => is_null($selectedCategoryId),
                            'bg-gray-100 text-gray-700 border-gray-200 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700' => !is_null($selectedCategoryId),
                        ])
                    >
                        {{ __('sales.all_categories') }}
                    </button>
                    @foreach($this->categories as $category)
                        <button
                            type="button"
                            wire:click="$set('selectedCategoryId', {{ $category->id }})"
                            @class([
                                'px-3.5 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-colors border',
                                'bg-primary-600 text-white border-primary-600 shadow-sm' => $selectedCategoryId == $category->id,
                                'bg-gray-100 text-gray-700 border-gray-200 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700' => $selectedCategoryId != $category->id,
                            ])
                        >
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4 lg:p-6">
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @forelse($this->products as $product)
                        <button
                            type="button"
                            wire:click="addItem({{ $product->id }})"
                            class="group relative flex flex-col bg-white dark:bg-gray-800/90 rounded-xl border border-gray-200 dark:border-gray-700/80 hover:border-primary-500 dark:hover:border-primary-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 transition-colors duration-150 text-left overflow-hidden shadow-xs hover:shadow-md"
                        >
                            <div class="aspect-square w-full bg-gray-50 dark:bg-gray-900/60 flex items-center justify-center border-b border-gray-100 dark:border-gray-700/60 relative group-hover:bg-primary-50/40 dark:group-hover:bg-primary-950/20 transition-colors">
                                <x-icon name="shopping-bag" class="w-10 h-10 text-gray-300 dark:text-gray-600 group-hover:text-primary-400 dark:group-hover:text-primary-500 transition-colors" />

                                @if($product->stock_quantity !== null)
                                    <div @class([
                                        'absolute top-2.5 right-2.5 px-2 py-0.5 rounded-full text-xs font-semibold tabular-nums uppercase tracking-wide',
                                        'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' => $product->stock_quantity > 5,
                                        'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-400 border border-amber-200 dark:border-amber-800' => $product->stock_quantity <= 5 && $product->stock_quantity > 0,
                                        'bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-400 border border-red-200 dark:border-red-800' => $product->stock_quantity <= 0,
                                    ])>
                                        {{ $product->stock_quantity > 0 ? $product->stock_quantity . ' un' : __('sales.out_of_stock') }}
                                    </div>
                                @endif
                            </div>

                            <div class="p-3.5 flex flex-col flex-1">
                                <div class="flex items-center justify-between gap-1 mb-1">
                                    <span class="text-xs font-semibold text-primary-600 dark:text-primary-400 uppercase tracking-wider truncate">
                                        {{ $product->category->name }}
                                    </span>
                                    @if(isset($this->form->items[$product->id]))
                                        <span class="inline-flex items-center bg-primary-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-md tabular-nums shrink-0">
                                            {{ $this->form->items[$product->id]['quantity'] }}x {{ __('sales.in_cart') }}
                                        </span>
                                    @endif
                                </div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 line-clamp-2 leading-snug flex-1 mb-2">{{ $product->name }}</h3>

                                @if($product->brand || $product->weight)
                                    <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 mb-3 text-xs text-gray-500 dark:text-gray-400">
                                        @if($product->brand)
                                            <span class="truncate">{{ $product->brand }}</span>
                                        @endif
                                        @if($product->brand && $product->weight)
                                            <span class="text-gray-300 dark:text-gray-600">·</span>
                                        @endif
                                        @if($product->weight)
                                            <span class="tabular-nums">{{ $product->weight }}</span>
                                        @endif
                                    </div>
                                @endif

                                <div class="flex items-center justify-between mt-auto pt-2 border-t border-gray-100 dark:border-gray-800">
                                    <span class="text-base font-bold text-gray-900 dark:text-white tabular-nums">
                                        R$ {{ number_format($product->sale_price, 2, ',', '.') }}
                                    </span>
                                    <div class="h-8 w-8 rounded-md bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-300 group-hover:bg-primary-600 group-hover:text-white transition-colors">
                                        <x-icon name="plus" class="w-4 h-4" />
                                    </div>
                                </div>
                            </div>
                        </button>
                    @empty
                        <div class="col-span-full py-16 px-4 flex flex-col items-center justify-center text-center">
                            <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4 text-gray-400 dark:text-gray-500">
                                <x-icon name="{{ empty($search) && empty($selectedCategoryId) ? 'shopping-bag' : 'magnifying-glass' }}" class="w-8 h-8" />
                            </div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 mb-1">
                                @if(empty($search) && empty($selectedCategoryId))
                                    {{ __('sales.ready_to_sell') }}
                                @else
                                    {{ __('sales.product_not_found') }}
                                @endif
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 max-w-sm">
                                @if(empty($search) && empty($selectedCategoryId))
                                    {{ __('sales.start_selling_instruction') }}
                                @else
                                    {{ __('sales.adjust_filters_instruction') }}
                                @endif
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div
            :class="cartOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'"
            class="fixed inset-y-0 right-0 w-full sm:w-[400px] lg:relative lg:w-[400px] flex flex-col bg-gray-50 dark:bg-gray-950 shadow-2xl lg:shadow-none z-50 lg:z-20 transition-transform duration-300 ease-in-out"
        >
            <div class="p-4 border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        @click="cartOpen = false"
                        class="lg:hidden p-1.5 -ml-1.5 rounded-md text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                        aria-label="{{ __('sales.cart') }}"
                    >
                        <x-icon name="arrow-left" class="w-5 h-5" />
                    </button>
                    <div class="relative">
                        <x-icon name="shopping-cart" class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                        @if(count($form->items) > 0)
                            <span class="absolute -top-1.5 -right-2 bg-primary-600 text-white text-xs font-bold px-1.5 py-0.2 rounded-full tabular-nums leading-none ring-2 ring-white dark:ring-gray-900">
                                {{ collect($form->items)->sum('quantity') }}
                            </span>
                        @endif
                    </div>
                    <h2 class="font-bold text-gray-900 dark:text-white uppercase tracking-wider text-xs">{{ __('sales.cart') }}</h2>
                </div>
                @if(count($form->items) > 0)
                    <button
                        type="button"
                        wire:click="$set('form.items', [])"
                        class="text-xs text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition-colors font-semibold"
                    >
                        {{ __('sales.clear_cart') }}
                    </button>
                @endif
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-3">
                @forelse($form->items as $productId => $item)
                    <div class="flex gap-3 bg-white dark:bg-gray-900 p-3 rounded-xl border border-gray-200 dark:border-gray-800 transition-colors shadow-xs">
                        <div class="h-10 w-10 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center shrink-0 border border-gray-100 dark:border-gray-700 text-gray-400 dark:text-gray-500">
                            <x-icon name="cube" class="w-5 h-5" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start gap-2 mb-1">
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate flex-1">{{ $item['name'] }}</p>
                                <button
                                    type="button"
                                    wire:click="removeItem({{ $productId }})"
                                    class="p-0.5 rounded text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors shrink-0"
                                    title="{{ __('sales.cancel') }}"
                                >
                                    <x-icon name="x-mark" class="w-4 h-4" />
                                </button>
                            </div>
                            @if(!empty($item['brand']) || !empty($item['weight']))
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 mb-2 text-xs text-gray-500 dark:text-gray-400">
                                    @if(!empty($item['brand']))
                                        <span>{{ $item['brand'] }}</span>
                                    @endif
                                    @if(!empty($item['brand']) && !empty($item['weight']))
                                        <span class="text-gray-300 dark:text-gray-600">·</span>
                                    @endif
                                    @if(!empty($item['weight']))
                                        <span class="tabular-nums">{{ $item['weight'] }}</span>
                                    @endif
                                </div>
                            @endif
                            <div class="flex items-center justify-between">
                                <div class="flex items-center bg-gray-100 dark:bg-gray-800 rounded-md p-0.5 border border-gray-200 dark:border-gray-700">
                                    <button
                                        type="button"
                                        wire:click="updateQuantity({{ $productId }}, {{ $item['quantity'] - 1 }})"
                                        class="p-1 rounded hover:bg-white dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 transition-colors"
                                        aria-label="Diminuir quantidade"
                                    >
                                        <x-icon name="minus" class="w-3.5 h-3.5" />
                                    </button>
                                    <span class="text-xs font-bold w-7 text-center text-gray-900 dark:text-gray-100 tabular-nums">{{ $item['quantity'] }}</span>
                                    <button
                                        type="button"
                                        wire:click="updateQuantity({{ $productId }}, {{ $item['quantity'] + 1 }})"
                                        class="p-1 rounded hover:bg-white dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 transition-colors"
                                        aria-label="Aumentar quantidade"
                                    >
                                        <x-icon name="plus" class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                                <span class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">
                                    R$ {{ number_format(($item['unit_price'] * $item['quantity']) / 100, 2, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="h-full flex flex-col items-center justify-center text-center py-12 px-4">
                        <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-3 text-gray-400 dark:text-gray-500">
                            <x-icon name="shopping-cart" class="w-7 h-7" />
                        </div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('sales.empty_cart') }}</p>
                    </div>
                @endforelse
            </div>

            <div class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 p-4 shrink-0">
                <div class="space-y-4">
                    <div class="space-y-3">
                        <x-select
                            label="{{ __('sales.customer') }}"
                            wire:model="form.customerId"
                            placeholder="{{ __('sales.customer_placeholder') }}"
                            :options="$this->customers"
                            option-label="name"
                            option-value="id"
                            icon="user"
                            shadowless
                        />

                        <div class="grid grid-cols-2 gap-3">
                            <x-select
                                label="{{ __('sales.payment_method') }}"
                                wire:model.live="form.paymentMethod"
                                :options="[
                                    ['label' => __('sales.payments.money'), 'value' => 'money', 'icon' => 'banknotes'],
                                    ['label' => __('sales.payments.credit_card'), 'value' => 'credit_card', 'icon' => 'credit-card'],
                                    ['label' => __('sales.payments.debit_card'), 'value' => 'debit_card', 'icon' => 'credit-card'],
                                    ['label' => __('sales.payments.pix'), 'value' => 'pix', 'icon' => 'qr-code'],
                                    ['label' => __('sales.payments.others'), 'value' => 'others', 'icon' => 'ellipsis-horizontal'],
                                ]"
                                option-label="label"
                                option-value="value"
                                :clearable="false"
                                shadowless
                            />
                            <x-money-input
                                label="{{ __('sales.discount') }}"
                                wire:model.live="form.discountAmount"
                                prefix="R$"
                                shadowless
                            />
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                        <div class="flex items-center gap-3">
                            <x-select
                                label="{{ __('sales.status') }}"
                                wire:model="form.status"
                                :options="[
                                    ['label' => __('sales.paid'), 'value' => 'paid'],
                                    ['label' => __('sales.pending'), 'value' => 'pending'],
                                ]"
                                option-label="label"
                                option-value="value"
                                :clearable="false"
                                shadowless
                                sm
                                class="!w-28"
                            />
                            <div class="pt-5">
                                <x-toggle label="{{ __('sales.gift') }}" wire:model="form.isGift" sm />
                            </div>
                        </div>

                        @if(in_array($form->paymentMethod, ['credit_card', 'debit_card']))
                            <div class="flex items-center gap-2 px-2.5 py-1 bg-sky-50 dark:bg-sky-950/40 rounded-full border border-sky-200 dark:border-sky-800/60 text-xs font-semibold text-sky-700 dark:text-sky-300">
                                <span class="tabular-nums">{{ __('sales.fee') }} ({{ $this->feePercentage }}%)</span>
                                <x-toggle wire:model.live="form.passFeeToCustomer" sm />
                            </div>
                        @endif
                    </div>

                    <div class="space-y-1.5 pt-3 border-t border-gray-100 dark:border-gray-800 text-xs">
                        <div class="flex justify-between font-medium text-gray-500 dark:text-gray-400">
                            <span>{{ __('sales.subtotal') }}</span>
                            <span class="tabular-nums">R$ {{ number_format($this->subtotal / 100, 2, ',', '.') }}</span>
                        </div>
                        @if($this->discountInCents > 0)
                            <div class="flex justify-between font-medium text-red-600 dark:text-red-400">
                                <span>{{ __('sales.discount') }}</span>
                                <span class="tabular-nums">- R$ {{ number_format($this->discountInCents / 100, 2, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($this->feeAmount > 0 && $form->passFeeToCustomer)
                            <div class="flex justify-between font-medium text-sky-600 dark:text-sky-400">
                                <span>{{ __('sales.fee_addition') }}</span>
                                <span class="tabular-nums">+ R$ {{ number_format($this->feeAmount / 100, 2, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($form->isGift)
                            <div class="flex justify-between font-semibold text-emerald-600 dark:text-emerald-400">
                                <span>{{ __('sales.gift_applied') }}</span>
                                <span class="tabular-nums">- R$ {{ number_format($this->subtotal / 100, 2, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($this->feeAmount > 0 && !$form->passFeeToCustomer && !$form->isGift)
                            <div class="flex justify-between font-medium text-amber-600 dark:text-amber-400">
                                <span>{{ __('sales.fee_deduction') }} ({{ $this->feePercentage }}%)</span>
                                <span class="tabular-nums">- R$ {{ number_format($this->feeAmount / 100, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between font-semibold text-gray-700 dark:text-gray-300">
                                <span>{{ __('sales.net_amount') }}</span>
                                <span class="tabular-nums">R$ {{ number_format($this->netAmount / 100, 2, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between items-baseline pt-2 border-t border-gray-100 dark:border-gray-800">
                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('sales.total') }}</span>
                            <span class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums tracking-tight">
                                R$ {{ number_format($this->totalAmount / 100, 2, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <x-button
                        primary
                        full
                        rounded="md"
                        wire:click="save"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        class="py-2.5 font-semibold text-sm shadow-sm"
                    >
                        <span wire:loading.remove wire:target="save" class="inline-flex items-center justify-center gap-2">
                            <x-icon name="check" class="w-4 h-4" />
                            {{ __('sales.finish_sale') }}
                        </span>
                        <span wire:loading wire:target="save" class="inline-flex items-center justify-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            {{ __('sales.finish_sale') }}...
                        </span>
                    </x-button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Cart Backdrop -->
    <div
        x-show="cartOpen"
        x-transition:enter="transition-opacity ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="cartOpen = false"
        class="lg:hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40"
        x-cloak
    ></div>

    <!-- Mobile Floating Cart Trigger -->
    <button
        type="button"
        @click="cartOpen = true"
        class="lg:hidden fixed bottom-6 right-6 z-30 bg-primary-600 text-white p-3.5 rounded-full shadow-lg hover:bg-primary-700 active:scale-95 transition-transform flex items-center justify-center"
        aria-label="{{ __('sales.cart') }}"
    >
        <div class="relative">
            <x-icon name="shopping-cart" class="w-6 h-6" />
            @if(count($form->items) > 0)
                <span class="absolute -top-2 -right-2 bg-primary-800 text-white text-xs font-bold px-1.5 py-0.5 rounded-full ring-2 ring-white dark:ring-gray-900 tabular-nums">
                    {{ collect($form->items)->sum('quantity') }}
                </span>
            @endif
        </div>
    </button>
</div>
