<div class="space-y-6">
    <div>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('admin.brand.title') }}</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('admin.brand.subtitle') }}</p>
    </div>

    @if ($hasLogo)
        <div class="flex items-center gap-6 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex h-24 w-24 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-900 p-2">
                <img src="{{ $logoUrl }}?v={{ now()->timestamp }}" alt="{{ __('admin.brand.title') }}" class="max-h-full max-w-full object-contain">
            </div>
            <div class="space-y-2">
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('admin.brand.current') }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('admin.brand.available_in_banners') }}</p>
                @can(\App\Enums\Permission::EditSetting->value)
                    <x-button flat negative icon="trash" :label="__('admin.brand.actions.remove')" wire:click="remove" wire:confirm="{{ __('admin.brand.messages.confirm_remove') }}" />
                @endcan
            </div>
        </div>
    @else
        <div class="rounded-xl border border-dashed border-gray-300 dark:border-gray-600 p-6 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('admin.brand.empty') }}</p>
        </div>
    @endif

    @can(\App\Enums\Permission::EditSetting->value)
        <form wire:submit="save" class="space-y-4 max-w-md">
            <x-input
                type="file"
                accept="image/png"
                wire:model="logo"
                :label="$hasLogo ? __('admin.brand.fields.replace') : __('admin.brand.fields.upload')"
            />

            @error('logo')
                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror

            @if ($logo)
                <div class="flex h-24 w-24 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-900 p-2">
                    <img src="{{ $logo->temporaryUrl() }}" alt="" class="max-h-full max-w-full object-contain">
                </div>
            @endif

            <x-button type="submit" primary icon="arrow-up-tray" :label="__('admin.brand.actions.save')" />
        </form>
    @endcan
</div>
