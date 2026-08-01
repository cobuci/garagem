@use(App\Enums\Permission)
@use(App\Enums\BannerFormat)

<div>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('banners.title') }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('banners.subtitle') }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            @can(Permission::CreateBanner->value)
                <x-button primary icon="plus" :label="__('banners.actions.create')" wire:click="create" />
            @endcan
        </div>
    </div>

    <div class="overflow-x-auto shadow ring-1 ring-black/5 sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/50">
                <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-white sm:pl-6">
                        {{ __('banners.fields.name') }}
                    </th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                        {{ __('banners.fields.format') }}
                    </th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                        {{ __('banners.fields.created_by') }}
                    </th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                        {{ __('banners.fields.updated_at') }}
                    </th>
                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                        <span class="sr-only">{{ __('banners.actions.label') }}</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800 bg-white dark:bg-gray-800">
                @forelse ($this->banners as $banner)
                    <tr wire:key="banner-{{ $banner->id }}">
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-white sm:pl-6">
                            <a href="{{ route('banners.studio', $banner) }}" wire:navigate class="hover:text-primary-600 dark:hover:text-primary-400">
                                {{ $banner->name }}
                            </a>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                            <x-badge color="primary" :label="$banner->format->label()" />
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $banner->user->name }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $banner->updated_at->diffForHumans() }}
                        </td>
                        <td class="relative space-x-2 whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                            <x-button flat primary icon="paint-brush" :href="route('banners.studio', $banner)" wire:navigate />

                            @can(Permission::DeleteBanner->value)
                                <x-button
                                    flat
                                    negative
                                    icon="trash"
                                    wire:click="confirmDelete({{ $banner->id }})"
                                />
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ __('banners.empty') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-modal-card wire:model="showDeleteModal" :title="__('banners.messages.delete_title')" max-width="md">
        <div class="space-y-4">
            <div class="flex items-center gap-3 rounded-lg bg-red-50 p-4 dark:bg-red-900/20">
                <x-icon name="exclamation-triangle" class="h-6 w-6 shrink-0 text-red-600 dark:text-red-400" />
                <p class="text-sm font-medium text-red-700 dark:text-red-300">
                    {{ __('banners.messages.confirm_delete', ['banner' => $deletingBannerName]) }}
                </p>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('banners.messages.delete_description') }}
            </p>
        </div>

        <x-slot name="footer" class="flex justify-end gap-x-4">
            <x-button flat :label="__('banners.actions.cancel')" wire:click="$set('showDeleteModal', false)" />
            <x-button negative :label="__('banners.actions.delete')" wire:click="delete" spinner="delete" />
        </x-slot>
    </x-modal-card>

    <x-drawer wire:model="showDrawer" :title="__('banners.actions.create')">
        <div class="flex h-full flex-col">
            <div class="flex-1 space-y-4 overflow-y-auto">
                <x-input
                    wire:model="name"
                    :label="__('banners.fields.name')"
                    :placeholder="__('banners.fields.name_placeholder')"
                />

                <x-native-select wire:model="format" :label="__('banners.fields.format')">
                    @foreach (BannerFormat::cases() as $formatOption)
                        <option value="{{ $formatOption->value }}">{{ $formatOption->label() }}</option>
                    @endforeach
                </x-native-select>
            </div>

            <div class="mt-6 flex shrink-0 justify-end gap-x-4 border-t border-gray-100 pt-6 dark:border-gray-700">
                <x-button flat :label="__('banners.actions.cancel')" wire:click="$set('showDrawer', false)" />
                <x-button primary :label="__('banners.actions.save')" wire:click="store" />
            </div>
        </div>
    </x-drawer>
</div>
