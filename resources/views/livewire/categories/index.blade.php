@use(App\Enums\Permission)

<div>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('categories.title') }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('categories.subtitle') }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            @can(Permission::DeleteCategory->value)
                <livewire:categories.delete />
            @endcan

            @can(Permission::CreateCategory->value)
                <x-button primary icon="plus" :label="__('categories.actions.create')" wire:click="create" />
            @endcan
        </div>
    </div>

    <div class="overflow-x-auto shadow ring-1 ring-black/5 sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/50">
                <tr>
                    @can(Permission::EditCategory->value)
                        <th scope="col" class="w-10 py-3.5 pl-4 pr-0 sm:pl-6">
                            <span class="sr-only">{{ __('categories.fields.sort_order') }}</span>
                        </th>
                    @endcan
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-white sm:pl-6">
                        {{ __('categories.fields.name') }}
                    </th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                        {{ __('categories.fields.products_count') }}
                    </th>
                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                        <span class="sr-only">{{ __('categories.actions.label') }}</span>
                    </th>
                </tr>
            </thead>
            <tbody
                @can(Permission::EditCategory->value) wire:sort="sort" @endcan
                class="divide-y divide-gray-200 dark:divide-gray-800 bg-white dark:bg-gray-800"
            >
                @forelse ($this->categories as $category)
                    <tr
                        wire:key="category-{{ $category->id }}"
                        @can(Permission::EditCategory->value) wire:sort:item="{{ $category->id }}" @endcan
                    >
                        @can(Permission::EditCategory->value)
                            <td class="whitespace-nowrap py-4 pl-4 pr-0 sm:pl-6">
                                <button
                                    type="button"
                                    wire:sort:handle
                                    class="cursor-grab text-gray-400 hover:text-gray-600 active:cursor-grabbing dark:hover:text-gray-300"
                                    title="{{ __('categories.actions.reorder') }}"
                                >
                                    <x-icon name="bars-3" class="h-5 w-5" />
                                </button>
                            </td>
                        @endcan
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-white sm:pl-6">
                            {{ $category->name }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                            <x-badge
                                :color="$category->products_count > 0 ? 'primary' : 'secondary'"
                                :label="(string) $category->products_count"
                            />
                        </td>
                        <td wire:sort:ignore class="relative space-x-2 whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                            @can(Permission::EditCategory->value)
                                <x-button flat primary icon="pencil" wire:click="edit({{ $category->id }})" />
                            @endcan

                            @can(Permission::DeleteCategory->value)
                                @if ($category->products_count > 0)
                                    <x-button
                                        flat
                                        negative
                                        icon="trash"
                                        disabled
                                        x-tooltip="'{{ __('categories.messages.cannot_delete_with_products') }}'"
                                    />
                                @endif

                                @if ($category->products_count === 0)
                                    <x-button
                                        flat
                                        negative
                                        icon="trash"
                                        wire:click="confirmDelete({{ $category->id }})"
                                    />
                                @endif
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td
                            colspan="{{ auth()->user()?->can(Permission::EditCategory->value) ? 4 : 3 }}"
                            class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400"
                        >
                            {{ __('categories.empty') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-drawer
        wire:model="showDrawer"
        :title="$form->category ? __('categories.actions.edit') : __('categories.actions.create')"
    >
        <div class="flex h-full flex-col">
            <div class="flex-1 overflow-y-auto">
                <x-input
                    wire:model="form.name"
                    :label="__('categories.fields.name')"
                    :placeholder="__('categories.fields.name_placeholder')"
                />
            </div>

            <div class="mt-6 flex shrink-0 justify-end gap-x-4 border-t border-gray-100 pt-6 dark:border-gray-700">
                <x-button flat :label="__('categories.actions.cancel')" wire:click="$set('showDrawer', false)" />
                <x-button primary :label="__('categories.actions.save')" wire:click="save" />
            </div>
        </div>
    </x-drawer>
</div>
