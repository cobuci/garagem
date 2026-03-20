<div x-data="{ cartOpen: false }" class="h-[calc(100vh-4rem)] lg:h-screen -m-4 lg:-m-8 flex flex-col overflow-hidden bg-gray-50 dark:bg-gray-950 shadow-inner relative">
    <div class="flex flex-1 overflow-hidden h-full">
        <div class="flex-1 flex flex-col min-w-0 bg-white dark:bg-gray-900 shadow-sm border-r border-gray-200 dark:border-gray-800 relative">
            <div class="p-4 border-b border-gray-100 dark:border-gray-800 space-y-4 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md sticky top-0 z-10">
                <div class="flex items-center justify-between gap-4">
                    <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100 hidden sm:block">{{ __('sales.title') }}</h1>
                    <div class="flex-1 max-w-xl flex gap-2">
                        <div class="relative flex-1 group">
                            <x-input
                                icon="magnifying-glass"
                                wire:model.live.debounce.300ms="search"
                                placeholder="{{ __('sales.search_placeholder') }}"
                                shadowless
                                x-on:keydown.window.prevent.slash="$el.focus()"
                                class="!rounded-xl border-gray-200 focus:!ring-primary-500/20"
                            />
                            <div class="absolute right-3 top-2 hidden sm:flex items-center gap-1 pointer-events-none">
                                <kbd class="px-1.5 py-0.5 text-[10px] font-sans font-semibold text-gray-400 bg-gray-50 border border-gray-200 rounded-md dark:bg-gray-800 dark:border-gray-700">/</kbd>
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
                                class="!rounded-xl"
                            />
                        </div>
                    </div>
                </div>

                <!-- Chips de Categorias (Opcional, se quiser algo mais visual) -->
                <div class="flex gap-2 overflow-x-auto pb-1 no-scrollbar scroll-smooth">
                    <button
                        wire:click="$set('selectedCategoryId', null)"
                        @class([
                            'px-4 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-all border',
                            'bg-primary-500 text-white border-primary-500 shadow-sm' => is_null($selectedCategoryId),
                            'bg-gray-100 text-gray-600 border-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-800' => !is_null($selectedCategoryId),
                        ])
                    >
                        {{ __('sales.all_categories') }}
                    </button>
                    @foreach($this->categories as $category)
                        <button
                            wire:click="$set('selectedCategoryId', {{ $category->id }})"
                            @class([
                                'px-4 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-all border',
                                'bg-primary-500 text-white border-primary-500 shadow-sm' => $selectedCategoryId == $category->id,
                                'bg-gray-100 text-gray-600 border-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-800' => $selectedCategoryId != $category->id,
                            ])
                        >
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4 lg:p-6 custom-scrollbar">
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-5">
                    @forelse($this->products as $product)
                        <button
                            wire:click="addItem({{ $product->id }})"
                            class="group relative flex flex-col bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 hover:border-primary-500 dark:hover:border-primary-400 hover:ring-4 hover:ring-primary-500/10 transition-all duration-200 text-left overflow-hidden shadow-sm hover:shadow-xl"
                        >
                            <div class="aspect-square w-full bg-gray-50 dark:bg-gray-900 flex items-center justify-center border-b border-gray-100 dark:border-gray-700 relative group-hover:bg-primary-50 dark:group-hover:bg-primary-900/10 transition-colors">
                                <x-icon name="shopping-bag" class="w-10 h-10 text-gray-200 dark:text-gray-700 group-hover:text-primary-200 dark:group-hover:text-primary-800" />

                                @if($product->stock_quantity !== null)
                                    <div @class([
                                        'absolute top-2 right-2 px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-tight',
                                        'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400 border border-green-200 dark:border-green-800' => $product->stock_quantity > 5,
                                        'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400 border border-amber-200 dark:border-amber-800' => $product->stock_quantity <= 5 && $product->stock_quantity > 0,
                                        'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400 border border-red-200 dark:border-red-800' => $product->stock_quantity <= 0,
                                    ])>
                                        {{ $product->stock_quantity > 0 ? $product->stock_quantity . ' un' : __('sales.out_of_stock') }}
                                    </div>
                                @endif
                            </div>

                            <div class="p-3 flex flex-col flex-1">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[10px] font-bold text-primary-500 dark:text-primary-400 uppercase tracking-widest">{{ $product->category->name }}</span>
                                    @if(isset($this->form->items[$product->id]))
                                        <span class="bg-primary-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-md shadow-sm">
                                            {{ $this->form->items[$product->id]['quantity'] }}x {{ __('sales.in_cart') }}
                                        </span>
                                    @endif
                                </div>
                                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 line-clamp-2 leading-snug flex-1 mb-2">{{ $product->name }}</h3>

                                <div class="flex flex-wrap gap-x-3 gap-y-1 mb-3 text-[10px] text-gray-500 dark:text-gray-400 font-medium">
                                    @if($product->brand)
                                        <div class="flex items-center gap-1">
                                            <span class="text-gray-400 dark:text-gray-500 font-bold uppercase tracking-tighter">{{ __('sales.brand') }}:</span>
                                            <span>{{ $product->brand }}</span>
                                        </div>
                                    @endif
                                    @if($product->weight)
                                        <div class="flex items-center gap-1">
                                            <span class="text-gray-400 dark:text-gray-500 font-bold uppercase tracking-tighter">{{ __('sales.weight') }}:</span>
                                            <span>{{ $product->weight }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between mt-auto">
                                    <span class="text-base font-black text-gray-900 dark:text-white">
                                        R$ {{ number_format($product->sale_price, 2, ',', '.') }}
                                    </span>
                                    <div class="h-8 w-8 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center group-hover:bg-primary-500 group-hover:text-white transition-all transform group-active:scale-95 sm:group-active:scale-90 shadow-sm group-hover:shadow-md">
                                        <x-icon name="plus" class="w-5 h-5" />
                                    </div>
                                </div>
                            </div>
                        </button>
                    @empty
                        <div class="col-span-full py-20 flex flex-col items-center justify-center text-center">
                            <div class="relative mb-6">
                                <div class="w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center">
                                    <x-icon name="magnifying-glass" class="w-12 h-12 text-gray-300 dark:text-gray-600" />
                                </div>
                                <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-white dark:bg-gray-900 rounded-full border-4 border-gray-50 dark:border-gray-950 flex items-center justify-center">
                                    <x-icon name="question-mark-circle" class="w-5 h-5 text-gray-400" />
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-gray-700 dark:text-gray-300 mb-2">
                                @if(empty($search) && empty($selectedCategoryId))
                                    {{ __('sales.ready_to_sell') }}
                                @else
                                    {{ __('sales.product_not_found') }}
                                @endif
                            </h3>
                            <p class="text-gray-400 max-w-xs mx-auto text-sm">
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
            <div class="p-4 border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="cartOpen = false" class="lg:hidden p-2 -ml-2 text-gray-400 hover:text-gray-600">
                        <x-icon name="arrow-left" class="w-5 h-5" />
                    </button>
                    <div class="relative">
                        <x-icon name="shopping-cart" class="w-6 h-6 text-gray-400" />
                        @if(count($form->items) > 0)
                            <span class="absolute -top-2 -right-2 bg-primary-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full ring-2 ring-white dark:ring-gray-900 animate-bounce">
                                {{ count($form->items) }}
                            </span>
                        @endif
                    </div>
                    <h2 class="font-bold text-gray-800 dark:text-gray-100 uppercase tracking-wide text-sm">{{ __('sales.cart') }}</h2>
                </div>
                @if(count($form->items) > 0)
                    <button wire:click="$set('form.items', [])" class="text-xs text-gray-400 hover:text-red-500 transition-colors font-medium">{{ __('sales.clear_cart') }}</button>
                @endif
            </div>

            <div class="flex-1 overflow-y-auto p-4 custom-scrollbar">
                @forelse($form->items as $productId => $item)
                    <div class="flex gap-3 mb-4 group bg-white dark:bg-gray-900 p-3 rounded-2xl border border-transparent hover:border-gray-200 dark:hover:border-gray-700 transition-all shadow-sm hover:shadow-md">
                        <div class="h-12 w-12 rounded-xl bg-gray-50 dark:bg-gray-800 flex items-center justify-center shrink-0 border border-gray-100 dark:border-gray-700">
                            <x-icon name="cube" class="w-6 h-6 text-gray-300" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start mb-1">
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-100 truncate flex-1 pr-2">{{ $item['name'] }}</p>
                                <button wire:click="removeItem({{ $productId }})" class="text-gray-300 hover:text-red-500 transition-colors shrink-0">
                                    <x-icon name="x-mark" class="w-4 h-4" />
                                </button>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-800 rounded-lg p-0.5 border border-gray-200 dark:border-gray-700">
                                    <button
                                        wire:click="updateQuantity({{ $productId }}, {{ $item['quantity'] - 1 }})"
                                        class="p-1 rounded-md hover:bg-white dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400 transition-all active:scale-90"
                                    >
                                        <x-icon name="minus" class="w-3 h-3" />
                                    </button>
                                    <span class="text-xs font-black w-6 text-center text-gray-700 dark:text-gray-200">{{ $item['quantity'] }}</span>
                                    <button
                                        wire:click="updateQuantity({{ $productId }}, {{ $item['quantity'] + 1 }})"
                                        class="p-1 rounded-md hover:bg-white dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400 transition-all active:scale-90"
                                    >
                                        <x-icon name="plus" class="w-3 h-3" />
                                    </button>
                                </div>
                                <span class="text-sm font-bold text-primary-600 dark:text-primary-400">
                                    R$ {{ number_format(($item['unit_price'] * $item['quantity']) / 100, 2, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="h-full flex flex-col items-center justify-center text-center py-10 opacity-40">
                        <div class="w-20 h-20 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-full flex items-center justify-center mb-4">
                            <x-icon name="shopping-cart" class="w-10 h-10 text-gray-300" />
                        </div>
                        <p class="text-sm font-medium text-gray-500">{{ __('sales.empty_cart') }}</p>
                    </div>
                @endforelse
            </div>

            <div class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 p-4 shadow-[0_-10px_30px_rgba(0,0,0,0.05)] rounded-t-3xl">
                <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-4">
                        <x-select
                            label="{{ __('sales.customer') }}"
                            wire:model="form.customerId"
                            placeholder="{{ __('sales.customer_placeholder') }}"
                            :options="$this->customers"
                            option-label="name"
                            option-value="id"
                            icon="user"
                            shadowless
                            class="!rounded-xl"
                        />

                        <div class="grid grid-cols-2 gap-4">
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
                                class="!rounded-xl"
                            />
                            <x-money-input
                                label="{{ __('sales.discount') }}"
                                wire:model.live="form.discountAmount"
                                prefix="R$"
                                shadowless
                                class="!rounded-xl"
                            />
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-gray-100 dark:border-gray-800">
                         <div class="flex items-center gap-4">
                             <x-toggle label="{{ __('sales.gift') }}" wire:model="form.isGift" sm />
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
                                class="!rounded-lg !w-28"
                            />
                         </div>

                         @if(in_array($form->paymentMethod, ['credit_card', 'debit_card']))
                            <div class="flex items-center gap-2 px-3 py-1 bg-blue-50 dark:bg-blue-900/30 rounded-full border border-blue-100 dark:border-blue-800">
                                <span class="text-[10px] font-bold text-blue-700 dark:text-blue-400">{{ __('sales.fee') }} ({{ $this->feePercentage }}%)</span>
                                <x-toggle wire:model.live="form.passFeeToCustomer" sm />
                            </div>
                        @endif
                    </div>

                    <div class="space-y-1 pt-2">
                        <div class="flex justify-between text-xs font-medium text-gray-500">
                            <span>{{ __('sales.subtotal') }}</span>
                            <span>R$ {{ number_format($this->subtotal / 100, 2, ',', '.') }}</span>
                        </div>
                        @if($this->discountInCents > 0)
                            <div class="flex justify-between text-xs font-medium text-red-500">
                                <span>{{ __('sales.discount') }}</span>
                                <span>- R$ {{ number_format($this->discountInCents / 100, 2, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($this->feeAmount > 0 && $form->passFeeToCustomer)
                            <div class="flex justify-between text-xs font-medium text-blue-500">
                                <span>{{ __('sales.fee_addition') }}</span>
                                <span>+ R$ {{ number_format($this->feeAmount / 100, 2, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($form->isGift)
                            <div class="flex justify-between text-xs font-bold text-green-600 dark:text-green-400 animate-pulse">
                                <span>{{ __('sales.gift_applied') }}</span>
                                <span>- R$ {{ number_format($this->subtotal / 100, 2, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($this->feeAmount > 0 && !$form->passFeeToCustomer && !$form->isGift)
                            <div class="flex justify-between text-xs font-medium text-amber-600 dark:text-amber-400">
                                <span>{{ __('sales.fee_deduction') }} ({{ $this->feePercentage }}%)</span>
                                <span>- R$ {{ number_format($this->feeAmount / 100, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-xs font-bold text-gray-700 dark:text-gray-300">
                                <span>{{ __('sales.net_amount') }}</span>
                                <span>R$ {{ number_format($this->netAmount / 100, 2, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between items-end pt-2 border-t border-gray-100 dark:border-gray-800">
                            <span class="text-sm font-bold text-gray-800 dark:text-gray-100 uppercase tracking-wider">{{ __('sales.total') }}</span>
                            <span class="text-3xl font-black text-primary-600 dark:text-primary-500 tracking-tighter">
                                R$ {{ number_format($this->totalAmount / 100, 2, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <x-button
                        xl
                        primary
                        full
                        rounded="2xl"
                        label="{{ __('sales.finish_sale') }}"
                        icon="check"
                        wire:click="save"
                        wire:loading.attr="disabled"
                        class="!shadow-xl !shadow-primary-500/20 active:scale-95 transition-all py-6"
                    />
                </div>
            </div>
        </div>
    </div>
    <button
        @click="cartOpen = true"
        class="lg:hidden fixed bottom-6 right-6 z-40 bg-primary-600 text-white p-4 rounded-full shadow-2xl flex items-center justify-center animate-bounce hover:bg-primary-700 active:scale-95 transition-all"
    >
        <div class="relative">
            <x-icon name="shopping-cart" class="w-6 h-6" />
            @if(count($form->items) > 0)
                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full ring-2 ring-white">
                    {{ count($form->items) }}
                </span>
            @endif
        </div>
    </button>
</div>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; }
</style>
