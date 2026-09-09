@use(App\Enums\Permission)

<div class="space-y-6 flex flex-col min-h-0 w-full max-w-full">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ __('categories.title') }}</h1>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200/80 dark:border-gray-700 tabular-nums">
                    {{ $this->categories->count() }}
                </span>
            </div>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                {{ __('categories.subtitle') }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            @can(Permission::CreateCategory->value)
                <x-button
                    primary
                    icon="plus"
                    :label="__('categories.actions.create')"
                    wire:click="create"
                    class="font-medium shadow-xs"
                />
            @endcan
        </div>
    </div>

    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow-xs border border-gray-200/80 dark:border-gray-700/80 overflow-hidden flex flex-col min-h-0 w-full max-w-full"
        x-data="{ selectedId: null }"
        @keydown.escape.window="selectedId = null"
        @click.outside="selectedId = null"
    >
        <div class="overflow-x-auto flex-1 relative w-full max-w-full">
            <table class="w-full divide-y divide-gray-100 dark:divide-gray-700 border-separate border-spacing-0">
                <thead class="bg-gray-50 dark:bg-gray-900/50 sticky top-0 z-10">
                    <tr>
                        @can(Permission::EditCategory->value)
                            <th scope="col" class="w-12 py-3.5 pl-4 pr-0 sm:pl-6 text-center border-b border-gray-100 dark:border-gray-700">
                                <span class="sr-only">{{ __('categories.fields.sort_order') }}</span>
                            </th>
                        @endcan
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 sm:pl-6">
                            {{ __('categories.fields.name') }}
                        </th>
                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                            {{ __('categories.fields.products_count') }}
                        </th>
                        <th scope="col" class="py-3.5 pl-3 pr-4 sm:pr-6 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                            <span class="sr-only">{{ __('categories.actions.label') }}</span>
                        </th>
                    </tr>
                </thead>
                <tbody
                    @can(Permission::EditCategory->value) wire:sort="sort" @endcan
                    class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700"
                >
                    @forelse ($this->categories as $category)
                        <tr
                            wire:key="category-{{ $category->id }}"
                            @can(Permission::EditCategory->value) wire:sort:item="{{ $category->id }}" @endcan
                            @click="selectedId = {{ $category->id }}"
                            x-bind:data-selected="selectedId === {{ $category->id }}"
                            class="table-row table-row--interactive"
                        >
                            @can(Permission::EditCategory->value)
                                <td class="whitespace-nowrap py-3.5 pl-4 pr-0 sm:pl-6 w-12 text-center" @click.stop>
                                    <button
                                        type="button"
                                        wire:sort:handle
                                        class="p-1.5 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:text-gray-200 dark:hover:bg-gray-700/50 transition-colors cursor-grab active:cursor-grabbing focus:outline-hidden focus:ring-2 focus:ring-primary-500/20"
                                        title="{{ __('categories.actions.reorder') }}"
                                        aria-label="{{ __('categories.actions.reorder') }}"
                                    >
                                        <x-icon name="bars-3" class="h-4 w-4" />
                                    </button>
                                </td>
                            @endcan
                            <td class="whitespace-nowrap py-3.5 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-white sm:pl-6">
                                {{ $category->name }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-3.5 text-sm">
                                @if ($category->products_count > 0)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-200/70 dark:bg-sky-950/30 dark:text-sky-300 dark:border-sky-800/40 tabular-nums">
                                        <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                        <span>{{ $category->products_count }} {{ trans_choice('categories.fields.products_badge', $category->products_count) }}</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500 border border-gray-200/70 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700 tabular-nums">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 dark:bg-gray-500"></span>
                                        <span>0 {{ trans_choice('categories.fields.products_badge', 0) }}</span>
                                    </span>
                                @endif
                            </td>
                            <td wire:sort:ignore class="relative whitespace-nowrap py-3.5 pl-3 pr-4 text-right text-sm font-medium sm:pr-6" @click.stop>
                                <div class="flex items-center justify-end gap-1">
                                    @can(Permission::EditCategory->value)
                                        <button
                                            type="button"
                                            title="{{ __('categories.actions.edit') }}"
                                            wire:click="edit({{ $category->id }})"
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-950/50 dark:hover:text-primary-400 transition-colors duration-150 focus:outline-hidden focus:ring-2 focus:ring-primary-500/20"
                                        >
                                            <x-icon name="pencil" class="w-4 h-4" />
                                        </button>
                                    @endcan

                                    @can(Permission::DeleteCategory->value)
                                        @if ($category->products_count > 0)
                                            <span
                                                class="inline-block"
                                                x-tooltip="'{{ __('categories.messages.cannot_delete_with_products') }}'"
                                            >
                                                <button
                                                    type="button"
                                                    disabled
                                                    class="p-1.5 rounded-lg text-gray-300 dark:text-gray-600 cursor-not-allowed opacity-50"
                                                >
                                                    <x-icon name="trash" class="w-4 h-4" />
                                                </button>
                                            </span>
                                        @else
                                            <button
                                                type="button"
                                                title="{{ __('categories.actions.delete') }}"
                                                wire:click="confirmDelete({{ $category->id }})"
                                                class="group p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-950/50 transition-colors duration-150 focus:outline-hidden focus:ring-2 focus:ring-red-500/20"
                                            >
                                                <x-icon name="trash" class="w-4 h-4 text-gray-400 group-hover:text-red-700 dark:group-hover:text-red-300" />
                                            </button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="{{ auth()->user()?->can(Permission::EditCategory->value) ? 4 : 3 }}"
                                class="px-6 py-14 text-center"
                            >
                                <div class="flex flex-col items-center justify-center max-w-xs mx-auto text-center space-y-3">
                                    <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400 dark:text-gray-500 border border-gray-200/60 dark:border-gray-700">
                                        <x-icon name="tag" class="w-6 h-6" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            {{ __('categories.empty') }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ __('categories.empty_description') }}
                                        </p>
                                    </div>
                                    @can(Permission::CreateCategory->value)
                                        <div class="pt-2">
                                            <x-button
                                                primary
                                                xs
                                                icon="plus"
                                                :label="__('categories.actions.create')"
                                                wire:click="create"
                                                class="shadow-xs font-medium"
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
    </div>

    @can(Permission::DeleteCategory->value)
        <livewire:categories.delete />
    @endcan

    <x-drawer
        wire:model="showDrawer"
        :title="$form->category ? __('categories.actions.edit') : __('categories.actions.create')"
    >
        <form wire:submit.prevent="save" class="flex h-full flex-col">
            <div class="flex-1 overflow-y-auto space-y-4 pt-2">
                <x-input
                    wire:model="form.name"
                    :label="__('categories.fields.name')"
                    :placeholder="__('categories.fields.name_placeholder')"
                    autofocus
                    autocomplete="off"
                />
            </div>

            <div class="mt-6 flex shrink-0 justify-end gap-x-3 border-t border-gray-100 dark:border-gray-700/80 pt-4">
                <x-button flat :label="__('categories.actions.cancel')" x-on:click="$wire.showDrawer = false" />
                <x-button
                    primary
                    :label="__('categories.actions.save')"
                    wire:click="save"
                    spinner="save"
                    class="font-medium shadow-xs hover:shadow transition-all duration-150 active:scale-[0.98] min-w-20"
                />
            </div>
        </form>
    </x-drawer>
</div>
