@use(App\Enums\Permission)
<div class="space-y-6 flex flex-col min-h-0 w-full max-w-full">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ __('customers.title') }}</h1>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200/80 dark:border-gray-700 tabular-nums">
                    {{ $this->customers->total() }}
                </span>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('customers.subtitle') }}</p>
        </div>
        @can(Permission::CreateCustomer->value)
            <div class="w-full sm:w-auto">
                <x-button
                    primary
                    icon="plus"
                    :label="__('customers.new_customer')"
                    x-on:click="$dispatch('open-drawer', { component: 'customers.create' })"
                    class="w-full sm:w-auto font-medium shadow-xs transition-all duration-150 active:scale-[0.98]"
                />
            </div>
        @endcan
    </div>

    <livewire:customers.create />
    <livewire:customers.edit />

    @php
        $stats = $this->stats;
    @endphp
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs overflow-hidden grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-100 dark:divide-gray-700/80">
        <div class="p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('customers.total_customers') }}</span>
                <x-icon name="users" class="w-4 h-4 text-gray-400 dark:text-gray-500" />
            </div>
            <p class="text-xl font-bold tracking-tight text-gray-900 dark:text-white tabular-nums">
                {{ $stats['total_customers'] }}
            </p>
        </div>

        <div class="p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('customers.customers_in_debt') }}</span>
                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-bold tabular-nums {{ $stats['customers_in_debt'] > 0 ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-400 border border-amber-200/50 dark:border-amber-800/50' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-800/50' }}">
                    {{ $stats['customers_in_debt'] > 0 ? $stats['customers_in_debt'] . ' pendentes' : __('customers.up_to_date') }}
                </span>
            </div>
            <p class="text-xl font-bold tracking-tight text-gray-900 dark:text-white tabular-nums">
                {{ $stats['customers_in_debt'] }}
            </p>
        </div>

        <div class="p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('customers.total_debt') }}</span>
                <span class="text-xs font-medium text-gray-400 dark:text-gray-500">{{ __('sales.pending') }}</span>
            </div>
            <p class="text-xl font-bold tracking-tight tabular-nums {{ $stats['total_due'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                R$ {{ number_format($stats['total_due'], 2, ',', '.') }}
            </p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xs border border-gray-200/80 dark:border-gray-700/80 overflow-hidden flex flex-col min-h-0 w-full max-w-full">
        <!-- Table Toolbar with Integrated Search -->
        <div class="px-5 py-3.5 border-b border-gray-200/80 dark:border-gray-700/80 bg-gray-50/50 dark:bg-gray-900/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="w-full sm:max-w-sm relative">
                <x-input
                    wire:model.live.debounce.300ms="search"
                    icon="magnifying-glass"
                    :placeholder="__('customers.search_placeholder')"
                    class="bg-white dark:bg-gray-800"
                />
                <div wire:loading.delay wire:target="search" class="absolute right-3 top-1/2 -translate-y-1/2">
                    <x-icon name="arrow-path" class="w-4 h-4 text-gray-400 animate-spin" />
                </div>
            </div>

            @if ($this->search)
                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                    <span>{{ $this->customers->total() }} {{ trans_choice('customers.title', $this->customers->total()) }} encontrados</span>
                    <button
                        type="button"
                        wire:click="$set('search', '')"
                        class="inline-flex items-center gap-1 font-semibold text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 ml-1"
                    >
                        <x-icon name="x-mark" class="w-3.5 h-3.5" />
                        <span>Limpar</span>
                    </button>
                </div>
            @endif
        </div>

        <div class="overflow-x-auto flex-1 relative w-full max-w-full">
            <table class="w-full table-fixed divide-y divide-gray-100 dark:divide-gray-700/80 border-separate border-spacing-0">
                <colgroup>
                    <col class="w-auto min-w-[280px]">
                    <col class="w-72 min-w-[200px]">
                    <col class="w-48 min-w-[160px]">
                    <col class="w-24 min-w-[96px]">
                </colgroup>
                <thead class="bg-gray-50/90 dark:bg-gray-900/60 sticky top-0 z-10">
                    <tr>
                        <th
                            wire:key="th-name"
                            class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider border-b border-gray-200/80 dark:border-gray-700/80 whitespace-nowrap"
                        >
                            <button
                                type="button"
                                wire:click="sort('name')"
                                wire:loading.attr="disabled"
                                wire:target="sort"
                                class="inline-flex items-center gap-1.5 focus:outline-none focus-visible:ring-1 focus-visible:ring-primary-500 rounded cursor-pointer select-none transition-colors group/th {{ $sortField === 'name' ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}"
                            >
                                <span>{{ __('customers.name') }}</span>
                                <span class="inline-flex items-center">
                                    @if ($sortField === 'name')
                                        <x-icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-3.5 h-3.5 text-primary-600 dark:text-primary-400" />
                                    @else
                                        <x-icon name="chevron-up-down" class="w-3.5 h-3.5 opacity-30 group-hover/th:opacity-70 transition-opacity" />
                                    @endif
                                </span>
                            </button>
                        </th>
                        <th wire:key="th-location" class="w-72 px-6 py-3.5 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200/80 dark:border-gray-700/80 whitespace-nowrap">{{ __('customers.location') }}</th>
                        <th
                            wire:key="th-total-due"
                            class="w-48 px-6 py-3.5 text-right text-xs font-bold uppercase tracking-wider border-b border-gray-200/80 dark:border-gray-700/80 whitespace-nowrap"
                        >
                            <div class="flex justify-end">
                                <button
                                    type="button"
                                    wire:click="sort('total_due')"
                                    wire:loading.attr="disabled"
                                    wire:target="sort"
                                    class="inline-flex items-center justify-end gap-1.5 focus:outline-none focus-visible:ring-1 focus-visible:ring-primary-500 rounded cursor-pointer select-none transition-colors group/th {{ $sortField === 'total_due' ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}"
                                >
                                    <span>{{ __('customers.total_due') }}</span>
                                    <span class="inline-flex items-center">
                                        @if ($sortField === 'total_due')
                                            <x-icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-3.5 h-3.5 text-primary-600 dark:text-primary-400" />
                                        @else
                                            <x-icon name="chevron-up-down" class="w-3.5 h-3.5 opacity-30 group-hover/th:opacity-70 transition-opacity" />
                                        @endif
                                    </span>
                                </button>
                            </div>
                        </th>
                        <th wire:key="th-actions" class="w-24 px-6 py-3.5 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200/80 dark:border-gray-700/80 whitespace-nowrap">{{ __('customers.actions') }}</th>
                    </tr>
                </thead>
                <tbody
                    class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700/80 transition-opacity duration-200"
                    wire:loading.class="opacity-60"
                    wire:target="search, sort"
                    x-data="{ selectedId: null }"
                >
                    @forelse ($this->customers as $customer)
                        @php
                            $hasDue = $customer->sales_sum_total_amount > 0;
                            $dueAmount = (float) ($customer->sales_sum_total_amount / 100);
                        @endphp
                        <x-table.row
                            wire:key="customer-{{ $customer->id }}"
                            x-bind:data-selected="selectedId === {{ $customer->id }}"
                            x-on:click="selectedId = {{ $customer->id }}; if (!$event.target.closest('button, a')) { Livewire.navigate('{{ route('customers.show', $customer) }}'); }"
                        >
                            <td class="px-6 py-4 truncate">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-8 h-8 rounded-full bg-primary-50 dark:bg-primary-950/60 border border-primary-100 dark:border-primary-900/50 flex items-center justify-center text-primary-700 dark:text-primary-300 text-xs font-bold shrink-0">
                                        {{ mb_substr($customer->name, 0, 2) }}
                                    </div>
                                    <div class="truncate min-w-0 flex-1">
                                        <a
                                            href="{{ route('customers.show', $customer) }}"
                                            wire:navigate
                                            class="text-sm font-semibold text-gray-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400 transition-colors truncate block"
                                            title="{{ $customer->name }}"
                                        >
                                            {{ $customer->name }}
                                        </a>
                                        @if ($customer->phone || $customer->email)
                                            <div class="text-xs text-gray-400 dark:text-gray-500 truncate mt-0.5">
                                                {{ $customer->phone ?? $customer->email }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 truncate">
                                @php
                                    $locationText = $customer->street
                                        ? ($customer->neighborhood ? $customer->street . ' - ' . $customer->neighborhood : $customer->street)
                                        : ($customer->neighborhood ?? null);
                                @endphp
                                @if ($locationText)
                                    <div class="text-sm text-gray-600 dark:text-gray-300 truncate" title="{{ $locationText }}">
                                        {{ $locationText }}
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 dark:text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm font-bold tabular-nums {{ $hasDue ? 'text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-400' }}">
                                    R$ {{ number_format($dueAmount, 2, ',', '.') }}
                                </div>
                                @if (! $hasDue)
                                    <span class="inline-flex items-center text-xs font-semibold text-emerald-600 dark:text-emerald-400 mt-0.5">
                                        {{ __('customers.up_to_date') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right" @click.stop>
                                <div class="flex items-center justify-end gap-1">
                                    <a
                                        href="{{ route('customers.show', $customer) }}"
                                        wire:navigate
                                        title="{{ __('customers.view_profile') }}"
                                        class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-950/50 dark:hover:text-primary-400 transition-colors duration-150 focus:outline-hidden focus:ring-2 focus:ring-primary-500/20"
                                    >
                                        <x-icon name="eye" class="w-4 h-4" />
                                    </a>

                                    @can(Permission::EditCustomer->value)
                                        <button
                                            type="button"
                                            title="{{ __('customers.edit') }}"
                                            x-on:click="$dispatch('edit:customer', { customer: {{ $customer->id }} })"
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-950/50 dark:hover:text-primary-400 transition-colors duration-150 focus:outline-hidden focus:ring-2 focus:ring-primary-500/20"
                                        >
                                            <x-icon name="pencil" class="w-4 h-4" />
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </x-table.row>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-14 text-center">
                                <div class="flex flex-col items-center justify-center max-w-xs mx-auto text-center space-y-3">
                                    <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400 dark:text-gray-500 border border-gray-200/60 dark:border-gray-700">
                                        <x-icon name="users" class="w-6 h-6" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            {{ __('customers.empty') }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ __('customers.empty_description') }}
                                        </p>
                                    </div>
                                    @can(Permission::CreateCustomer->value)
                                        <div class="pt-2">
                                            <x-button
                                                sm
                                                primary
                                                icon="plus"
                                                :label="__('customers.new_customer')"
                                                class="font-semibold shadow-xs hover:shadow transition-all duration-150 active:scale-[0.98]"
                                                x-on:click="$dispatch('open-drawer', { component: 'customers.create' })"
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

        @if ($this->customers->hasPages())
            <div class="px-6 py-4 border-t border-gray-200/80 dark:border-gray-700/80 bg-gray-50/50 dark:bg-gray-900/40">
                {{ $this->customers->links() }}
            </div>
        @endif
    </div>
</div>
