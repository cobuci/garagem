<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('admin.users.title') }}</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('admin.users.subtitle') }}
            </p>
        </div>
        @can(\App\Enums\Permission::CreateUser->value)
            <x-button primary icon="plus" label="{{ __('admin.users.actions.create') }}" wire:click="create" />
        @endcan
    </div>

    <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/50">
                <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-white sm:pl-6">{{ __('admin.users.fields.name') }}</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('admin.users.fields.email') }}</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('admin.users.fields.roles') }}</th>
                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                        <span class="sr-only">{{ __('admin.users.actions.label') }}</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800 bg-white dark:bg-gray-800">
                @foreach($this->users as $user)
                    <tr>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-white sm:pl-6">
                            {{ $user->name }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $user->email }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex flex-wrap gap-1">
                                @foreach($user->roles as $role)
                                    <x-badge secondary label="{{ $role->name }}" />
                                @endforeach
                            </div>
                        </td>
                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6 space-x-2">
                            @can(\App\Enums\Permission::EditUser->value)
                                <x-button flat primary icon="pencil" wire:click="edit({{ $user->id }})" />
                            @endcan

                            @can(\App\Enums\Permission::DeleteUser->value)
                                @if($user->id !== auth()->id())
                                    <x-button flat negative icon="trash"
                                        wire:confirm="{{ __('admin.users.messages.confirm_delete') }}"
                                        wire:click="delete({{ $user->id }})" />
                                @endif
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <x-drawer wire:model="showDrawer" title="{{ $form->user ? __('admin.users.actions.edit') : __('admin.users.actions.create') }}">
        <div class="flex flex-col h-full">
            <div class="flex-1 overflow-y-auto">
                <div class="space-y-4">
                    <x-input wire:model="form.name" label="{{ __('admin.users.fields.name') }}" placeholder="{{ __('admin.users.fields.name_placeholder') }}" />
                    <x-input wire:model="form.email" label="{{ __('admin.users.fields.email') }}" placeholder="{{ __('admin.users.fields.email_placeholder') }}" />
                    <div class="flex items-end gap-2">
                        <div class="flex-1">
                            <x-password wire:model="form.password"
                                label="{{ __('admin.users.fields.password') }}"
                                placeholder="{{ $form->user ? __('admin.users.fields.password_placeholder_edit') : __('admin.users.fields.password_placeholder') }}"
                            />
                        </div>
                        <x-button
                            icon="key"
                            primary
                            flat
                            squared
                            class="h-10"
                            wire:click="generatePassword"
                            x-tooltip="'{{ __('admin.users.actions.generate_password') }}'"
                        />
                    </div>

                    <x-select
                        label="{{ __('admin.users.fields.roles') }}"
                        wire:model="form.roles"
                        placeholder="{{ __('admin.users.fields.roles_placeholder') }}"
                        :options="$this->roles"
                        option-label="name"
                        option-value="name"
                        multiselect
                    />
                </div>
            </div>

            <div class="flex justify-end gap-x-4 pt-6 border-t border-gray-100 dark:border-gray-700 mt-6 shrink-0">
                <x-button flat label="{{ __('admin.users.actions.cancel') }}" x-on:click="$wire.showDrawer = false" />
                <x-button primary label="{{ __('admin.users.actions.save') }}" wire:click="save" />
            </div>
        </div>
    </x-drawer>
</div>
