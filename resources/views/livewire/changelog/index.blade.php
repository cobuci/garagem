<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('changelog.admin.title') }}</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('changelog.admin.subtitle') }}</p>
        </div>
        <x-button primary icon="plus" label="{{ __('changelog.admin.new') }}" wire:click="create" />
    </div>

    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700">
        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-3">{{ __('changelog.admin.run_seeder') }}</p>
        <div class="flex items-center gap-3" x-data="{ seeder: '' }">
            <div class="flex-1">
                <select
                    wire:model="selectedSeeder"
                    x-model="seeder"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                >
                    <option value="">{{ __('changelog.admin.seeder_placeholder') }}</option>
                    @foreach(\App\Livewire\Changelog\Index::SEEDERS as $class => $label)
                        <option value="{{ $class }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <x-button
                secondary
                icon="play"
                label="{{ __('changelog.admin.seeder_run') }}"
                wire:click="runSeeder"
                wire:confirm="{{ __('changelog.admin.seeder_confirm') }}"
                x-bind:disabled="!seeder"
            />
        </div>
    </div>

    @if($this->changelogs->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-gray-400 dark:text-gray-600">
            <x-icon name="megaphone" class="w-12 h-12 mb-3" />
            <p class="text-sm">{{ __('changelog.admin.empty') }}</p>
        </div>
    @else
        <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-white sm:pl-6">{{ __('changelog.admin.version') }}</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('changelog.admin.title_field') }}</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('changelog.admin.released_at') }}</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('changelog.admin.items') }}</th>
                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800 bg-white dark:bg-gray-800">
                    @foreach($this->changelogs as $changelog)
                        <tr wire:key="{{ $changelog->id }}">
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-white sm:pl-6">
                                <x-badge primary label="v{{ $changelog->version }}" />
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700 dark:text-gray-300">
                                {{ $changelog->title }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $changelog->released_at->format('d/m/Y') }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $changelog->items_count }}
                            </td>
                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6 space-x-2">
                                <x-button flat primary icon="pencil" wire:click="edit({{ $changelog->id }})" />
                                <x-button flat negative icon="trash"
                                    wire:confirm="{{ __('changelog.admin.delete_confirm') }}"
                                    wire:click="delete({{ $changelog->id }})" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <x-drawer wire:model="showForm" title="{{ $editingId ? __('changelog.admin.edit') : __('changelog.admin.new') }}">
        @if($showForm)
            <livewire:changelog.form wire:model="editingId" @changelog-saved="onSaved" wire:key="form-{{ $editingId ?? 'new' }}" />
        @endif
    </x-drawer>
</div>
