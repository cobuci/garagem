@use(App\Enums\Permission)
@use(App\Enums\SaleStatus)
<div class="space-y-6 flex flex-col min-h-0 w-full max-w-full">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a
                href="{{ route('customers.index') }}"
                wire:navigate
                class="p-2 bg-white dark:bg-gray-800 rounded-lg border border-gray-200/80 dark:border-gray-700/80 hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-xs"
            >
                <x-icon name="arrow-left" class="w-5 h-5 text-gray-600 dark:text-gray-400" />
            </a>
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $customer->name }}</h1>
                    @if ($customer->gender)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200/80 dark:border-gray-700">
                            {{ $customer->gender === \App\Enums\Gender::Male ? __('customers.male') : __('customers.female') }}
                        </span>
                    @endif
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('customers.profile_subtitle') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2.5">
            @can(Permission::EditCustomer->value)
                <x-button
                    outline
                    class="flex-1 sm:flex-none justify-center bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/60 font-medium shadow-xs transition-all duration-150 active:scale-[0.98]"
                    icon="pencil"
                    :label="__('customers.edit')"
                    wire:click="$dispatch('edit:customer', { customer: {{ $customer->id }} })"
                />
            @endcan

            @can(Permission::DeleteCustomer->value)
                <x-button
                    negative
                    outline
                    class="flex-1 sm:flex-none justify-center font-medium shadow-xs transition-all duration-150 active:scale-[0.98]"
                    icon="trash"
                    :label="__('customers.delete')"
                    @click="$wire.set('showDeleteModal', true)"
                />
            @endcan
        </div>
    </div>

    <!-- Customer Dossier / Metadata Strip -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs p-4 sm:p-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="flex items-start gap-3">
                <div class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 shrink-0">
                    <x-icon name="phone" class="w-4 h-4" />
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 block">{{ __('customers.phone') }}</span>
                    @if ($customer->phone)
                        <a
                            href="tel:{{ preg_replace('/\D/', '', $customer->phone) }}"
                            class="text-sm font-semibold text-gray-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400 tabular-nums truncate block"
                        >
                            {{ $customer->phone }}
                        </a>
                    @else
                        <span class="text-sm text-gray-400 dark:text-gray-500">-</span>
                    @endif
                </div>
            </div>

            <div class="flex items-start gap-3">
                <div class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 shrink-0">
                    <x-icon name="envelope" class="w-4 h-4" />
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 block">{{ __('customers.email') }}</span>
                    @if ($customer->email)
                        <a
                            href="mailto:{{ $customer->email }}"
                            class="text-sm font-semibold text-gray-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400 truncate block"
                            title="{{ $customer->email }}"
                        >
                            {{ $customer->email }}
                        </a>
                    @else
                        <span class="text-sm text-gray-400 dark:text-gray-500">-</span>
                    @endif
                </div>
            </div>

            <div class="flex items-start gap-3">
                <div class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 shrink-0">
                    <x-icon name="map-pin" class="w-4 h-4" />
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 block">{{ __('customers.location') }}</span>
                    @php
                        $addressParts = array_filter([$customer->street, $customer->neighborhood, $customer->address]);
                    @endphp
                    @if (! empty($addressParts))
                        <span class="text-sm font-semibold text-gray-900 dark:text-white truncate block" title="{{ implode(', ', $addressParts) }}">
                            {{ implode(', ', $addressParts) }}
                        </span>
                        @if ($customer->zip_code)
                            <span class="text-xs text-gray-400 dark:text-gray-500 tabular-nums block">
                                CEP: {{ $customer->zip_code }}
                            </span>
                        @endif
                    @else
                        <span class="text-sm text-gray-400 dark:text-gray-500">-</span>
                    @endif
                </div>
            </div>

            <div class="flex items-start gap-3">
                <div class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 shrink-0">
                    <x-icon name="calendar" class="w-4 h-4" />
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 block">Registro</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white tabular-nums block">
                        {{ $customer->created_at?->format('d/m/Y') ?? '-' }}
                    </span>
                    <span class="text-xs text-gray-400 dark:text-gray-500 block">
                        {{ $customer->created_at?->diffForHumans() ?? '' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat Cards Strip -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xs border border-gray-200/80 dark:border-gray-700/80">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-emerald-50 dark:bg-emerald-950/40 rounded-lg">
                    <x-icon name="banknotes" class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                </div>
                <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/30 px-2 py-0.5 rounded-full border border-emerald-200/50 dark:border-emerald-800/50">
                    Confirmado
                </span>
            </div>
            <h3 class="text-gray-500 dark:text-gray-400 text-xs font-bold uppercase tracking-wider">{{ __('customers.total_spent') }}</h3>
            <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums tracking-tight mt-1">
                R$ {{ number_format($this->totalSpent, 2, ',', '.') }}
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xs border border-gray-200/80 dark:border-gray-700/80">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 {{ $this->totalDue > 0 ? 'bg-red-50 dark:bg-red-900/30' : 'bg-gray-100 dark:bg-gray-800' }} rounded-lg">
                    <x-icon name="credit-card" class="w-6 h-6 {{ $this->totalDue > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-400 dark:text-gray-500' }}" />
                </div>
                @if ($this->totalDue > 0)
                    <span class="text-xs font-semibold text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-950/30 px-2 py-0.5 rounded-full border border-red-200/50 dark:border-red-800/50">
                        Pendente
                    </span>
                @else
                    <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/30 px-2 py-0.5 rounded-full border border-emerald-200/50 dark:border-emerald-800/50">
                        {{ __('customers.up_to_date') }}
                    </span>
                @endif
            </div>
            <h3 class="text-gray-500 dark:text-gray-400 text-xs font-bold uppercase tracking-wider">{{ __('customers.total_due') }}</h3>
            <p class="text-2xl font-bold {{ $this->totalDue > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }} tabular-nums tracking-tight mt-1">
                R$ {{ number_format($this->totalDue, 2, ',', '.') }}
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xs border border-gray-200/80 dark:border-gray-700/80">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-primary-50 dark:bg-primary-950/40 rounded-lg">
                    <x-icon name="shopping-bag" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                </div>
                <span class="text-xs font-semibold text-primary-700 dark:text-primary-300 bg-primary-50 dark:bg-primary-950/30 px-2 py-0.5 rounded-full border border-primary-200/50 dark:border-primary-800/50">
                    Histórico
                </span>
            </div>
            <h3 class="text-gray-500 dark:text-gray-400 text-xs font-bold uppercase tracking-wider">{{ __('customers.total_orders') }}</h3>
            <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums tracking-tight mt-1">
                {{ $this->orders->total() }}
            </p>
        </div>
    </div>

    <!-- Orders Table Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xs border border-gray-200/80 dark:border-gray-700/80 overflow-hidden flex flex-col min-h-0 w-full max-w-full">
        <div class="px-6 py-4 border-b border-gray-200/80 dark:border-gray-700/80 bg-gray-50/70 dark:bg-gray-900/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2.5">
                <h3 class="font-bold text-base text-gray-900 dark:text-white tracking-tight">{{ __('orders.history') }}</h3>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200/80 dark:border-gray-700 tabular-nums">
                    {{ $this->orders->total() }}
                </span>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                @can(Permission::EditSale->value)
                    <x-button
                        primary
                        outline
                        xs
                        icon="check"
                        :label="__('customers.mark_all_as_paid')"
                        wire:click="confirmMarkAllAsPaid"
                        :disabled="$this->totalDue <= 0"
                        class="font-medium shadow-xs"
                    />
                @endcan

                <div class="flex bg-gray-100 dark:bg-gray-900/80 p-1 rounded-lg self-start sm:self-auto border border-gray-200/60 dark:border-gray-800">
                    <button
                        type="button"
                        wire:click="filterByStatus(null)"
                        class="px-3 py-1 text-xs font-medium rounded-md transition-all duration-150 focus:outline-none {{ is_null($status) ? 'bg-white dark:bg-gray-800 shadow-xs font-semibold text-gray-900 dark:text-white border border-gray-200/70 dark:border-gray-700/80' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}"
                    >
                        {{ __('sales.all_sales') }}
                    </button>
                    <button
                        type="button"
                        wire:click="filterByStatus('paid')"
                        class="px-3 py-1 text-xs font-medium rounded-md transition-all duration-150 focus:outline-none {{ $status === 'paid' ? 'bg-white dark:bg-gray-800 shadow-xs font-semibold text-gray-900 dark:text-white border border-gray-200/70 dark:border-gray-700/80' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}"
                    >
                        {{ __('sales.paid_sales') }}
                    </button>
                    <button
                        type="button"
                        wire:click="filterByStatus('pending')"
                        class="px-3 py-1 text-xs font-medium rounded-md transition-all duration-150 focus:outline-none {{ $status === 'pending' ? 'bg-white dark:bg-gray-800 shadow-xs font-semibold text-gray-900 dark:text-white border border-gray-200/70 dark:border-gray-700/80' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}"
                    >
                        {{ __('sales.pending_sales') }}
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto flex-1 relative w-full max-w-full">
            <table class="w-full table-fixed divide-y divide-gray-100 dark:divide-gray-700/80 border-separate border-spacing-0">
                <colgroup>
                    <col class="w-auto min-w-[180px]">
                    <col class="w-48 min-w-[140px]">
                    <col class="w-44 min-w-[130px]">
                    <col class="w-44 min-w-[130px]">
                </colgroup>
                <thead class="bg-gray-50 dark:bg-gray-900/50 sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200/80 dark:border-gray-700/80 whitespace-nowrap">{{ __('orders.date') }}</th>
                        <th class="w-48 px-6 py-3.5 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200/80 dark:border-gray-700/80 whitespace-nowrap">{{ __('orders.total') }}</th>
                        <th class="w-44 px-6 py-3.5 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200/80 dark:border-gray-700/80 whitespace-nowrap">{{ __('orders.status') }}</th>
                        <th class="w-44 px-6 py-3.5 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200/80 dark:border-gray-700/80 whitespace-nowrap">{{ __('orders.actions') }}</th>
                    </tr>
                </thead>
                <tbody
                    class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700/80"
                    x-data="{ selectedId: null }"
                >
                    @forelse ($this->orders as $order)
                        <x-table.row
                            wire:key="order-{{ $order->id }}"
                            @click="selectedId = {{ $order->id }}"
                            x-bind:data-selected="selectedId === {{ $order->id }}"
                            x-on:click="if (!$event.target.closest('button, a')) { $wire.showDetails({{ $order->id }}) }"
                        >
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white tabular-nums">
                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                </div>
                                <span class="text-xs font-mono text-gray-400 dark:text-gray-500 tabular-nums">
                                    #{{ $order->id }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">
                                    R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($order->status === SaleStatus::Paid)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/70 dark:bg-emerald-950/30 dark:text-emerald-400 dark:border-emerald-800/40">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        {{ __('sales.paid') }}
                                    </span>
                                @elseif ($order->status === SaleStatus::Pending)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200/70 dark:bg-amber-950/30 dark:text-amber-400 dark:border-amber-800/40">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        {{ __('sales.pending') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200/70 dark:bg-red-950/30 dark:text-red-400 dark:border-red-800/40">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        {{ __('sales.cancel') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" @click.stop>
                                <div class="flex items-center justify-end gap-1.5">
                                    @if ($order->status === SaleStatus::Pending)
                                        @can(Permission::EditSale->value)
                                            <x-button
                                                xs
                                                flat
                                                primary
                                                icon="check"
                                                :label="__('sales.mark_as_paid')"
                                                wire:click="confirmMarkAsPaid({{ $order->id }})"
                                            />
                                        @endcan
                                    @endif

                                    <button
                                        type="button"
                                        title="{{ __('sales.details') }}"
                                        wire:click="showDetails({{ $order->id }})"
                                        class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-950/50 dark:hover:text-primary-400 transition-colors duration-150 focus:outline-hidden focus:ring-2 focus:ring-primary-500/20"
                                    >
                                        <x-icon name="eye" class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </x-table.row>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-14 text-center">
                                <div class="flex flex-col items-center justify-center max-w-xs mx-auto text-center space-y-2">
                                    <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400 dark:text-gray-500 border border-gray-200/60 dark:border-gray-700">
                                        <x-icon name="receipt-percent" class="w-5 h-5" />
                                    </div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        {{ __('orders.empty') }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($this->orders->hasPages())
            <div class="px-6 py-4 border-t border-gray-200/80 dark:border-gray-700/80 bg-gray-50/50 dark:bg-gray-900/40">
                {{ $this->orders->links() }}
            </div>
        @endif
    </div>

    @include('livewire.sales.components.details-modal', ['selectedSale' => $this->selectedSale])
    @include('livewire.sales.components.confirm-payment-modal', ['selectedSale' => $this->selectedSale])
    @include('livewire.sales.components.confirm-cancel-modal', ['selectedSale' => $this->selectedSale])
    @include('livewire.customers.components.confirm-mark-all-as-paid-modal')

    <x-modal-card wire:model="showDeleteModal" :title="__('customers.delete')">
        <div class="space-y-4">
            <div class="flex items-center space-x-3 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200/60 dark:border-red-800/40">
                <x-icon name="exclamation-triangle" class="w-6 h-6 text-red-600 dark:text-red-400 shrink-0" />
                <p class="text-sm text-red-700 dark:text-red-300 font-medium">
                    {{ __('customers.delete_confirm') }}
                </p>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('customers.delete_subtitle') }}
            </p>
        </div>

        <x-slot name="footer" class="flex justify-end gap-x-4">
            <x-button flat :label="__('customers.cancel')" x-on:click="close" />
            <x-button negative :label="__('customers.delete')" wire:click="delete" spinner="delete" />
        </x-slot>
    </x-modal-card>

    <div>
        <livewire:customers.edit />
    </div>
</div>
