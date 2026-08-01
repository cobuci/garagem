@use(App\Enums\BannerIntensity)
@use(App\Enums\BannerJobStatus)
@use(App\Enums\BannerMood)
@use(App\Enums\BannerTheme)
@use(App\Enums\Permission)
@use(App\Models\Banner)

@php
    $isWorking = $banner->background_status === BannerJobStatus::Generating
        || $banner->export_status === BannerJobStatus::Generating;

    $backgroundSrc = $banner->background_path
        ? route('banners.background', $banner) . '?v=' . $banner->updated_at?->timestamp
        : null;

    $previewScale = 420 / $banner->format->width();
@endphp

<div @if ($isWorking) wire:poll.2s="refreshStatus" @endif>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $banner->name }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ $banner->format->label() }} — {{ __('banners.hints.workflow') }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <x-button flat icon="arrow-left" :label="__('banners.actions.back')" :href="route('banners.index')" wire:navigate />

            @can(Permission::EditBanner->value)
                <x-button primary icon="check" :label="__('banners.actions.save')" wire:click="save" />
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        <div class="space-y-6">
            <x-card :title="__('banners.sections.presets')">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">{{ __('banners.hints.presets') }}</p>
                <div class="flex flex-wrap gap-2">
                    @foreach (array_keys(Banner::presets()) as $presetKey)
                        <x-button
                            flat
                            primary
                            :label="__('banners.presets.' . $presetKey)"
                            wire:click="applyPreset('{{ $presetKey }}')"
                        />
                    @endforeach
                </div>
            </x-card>

            <x-card :title="__('banners.sections.background')">
                <div class="space-y-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('banners.hints.background') }}</p>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach (BannerTheme::cases() as $theme)
                            <button
                                type="button"
                                wire:click="selectTheme('{{ $theme->value }}')"
                                class="rounded-lg border p-3 text-left transition
                                    {{ $backgroundTheme === $theme->value
                                        ? 'border-primary-500 ring-2 ring-primary-500/40 bg-primary-50 dark:bg-primary-900/20'
                                        : 'border-gray-200 dark:border-gray-700 hover:border-primary-300 dark:hover:border-primary-700' }}"
                            >
                                <span class="block text-sm font-semibold text-gray-900 dark:text-white">{{ $theme->label() }}</span>
                                <span class="block mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $theme->description() }}</span>
                            </button>
                        @endforeach
                    </div>
                    @error('backgroundTheme')
                        <p class="text-sm text-red-600 dark:text-red-400">{{ __('banners.messages.theme_required') }}</p>
                    @enderror

                    <div>
                        <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('banners.fields.mood') }}</span>
                        <div class="flex flex-wrap gap-2">
                            @foreach (BannerMood::cases() as $mood)
                                <button
                                    type="button"
                                    wire:click="$set('backgroundMood', {{ $backgroundMood === $mood->value ? 'null' : "'{$mood->value}'" }})"
                                    class="rounded-full px-4 py-1.5 text-sm font-medium border transition
                                        {{ $backgroundMood === $mood->value
                                            ? 'border-primary-500 bg-primary-500 text-white'
                                            : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-primary-400' }}"
                                >
                                    {{ $mood->label() }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('banners.fields.intensity') }}</span>
                        <div class="flex flex-wrap gap-2">
                            @foreach (BannerIntensity::cases() as $intensity)
                                <button
                                    type="button"
                                    wire:click="$set('backgroundIntensity', {{ $backgroundIntensity === $intensity->value ? 'null' : "'{$intensity->value}'" }})"
                                    class="rounded-full px-4 py-1.5 text-sm font-medium border transition
                                        {{ $backgroundIntensity === $intensity->value
                                            ? 'border-primary-500 bg-primary-500 text-white'
                                            : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-primary-400' }}"
                                >
                                    {{ $intensity->label() }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <x-button
                            primary
                            icon="sparkles"
                            :label="__('banners.actions.generate_background')"
                            wire:click="generateBackground"
                            :disabled="$banner->background_status === BannerJobStatus::Generating"
                        />

                        @if ($banner->background_path)
                            <x-button flat negative :label="__('banners.actions.remove_background')" wire:click="removeBackground" />
                        @endif
                    </div>

                    @if ($banner->background_status === BannerJobStatus::Generating)
                        <p class="text-sm text-primary-600 dark:text-primary-400 animate-pulse">{{ __('banners.messages.background_generating') }}</p>
                    @endif

                    @if ($banner->background_status === BannerJobStatus::Failed)
                        <p class="text-sm text-red-600 dark:text-red-400">{{ __('banners.messages.background_failed') }}</p>
                    @endif
                </div>
            </x-card>

            <x-card :title="__('banners.sections.logo')">
                <div class="space-y-4">
                    <x-toggle wire:model.live="design.show_logo" :label="__('banners.fields.show_logo')" />

                    @if ($design['show_logo'])
                        <div class="grid grid-cols-2 gap-4">
                            <x-native-select wire:model.live="design.logo_position" :label="__('banners.fields.logo_position')">
                                <option value="top">{{ __('banners.logo_positions.top') }}</option>
                                <option value="bottom">{{ __('banners.logo_positions.bottom') }}</option>
                            </x-native-select>

                            <x-native-select wire:model.live="design.logo_size" :label="__('banners.fields.logo_size')">
                                <option value="small">{{ __('banners.logo_sizes.small') }}</option>
                                <option value="medium">{{ __('banners.logo_sizes.medium') }}</option>
                                <option value="large">{{ __('banners.logo_sizes.large') }}</option>
                            </x-native-select>
                        </div>
                    @endif
                </div>
            </x-card>

            <x-card :title="__('banners.sections.content')">
                <div class="space-y-4">
                    <x-input wire:model.live.debounce.400ms="design.title" :label="__('banners.fields.title')" />
                    <x-input wire:model.live.debounce.400ms="design.subtitle" :label="__('banners.fields.subtitle')" />
                    <x-input wire:model.live.debounce.400ms="design.footer" :label="__('banners.fields.footer')" />

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('banners.fields.background_color') }}</label>
                            <input type="color" wire:model.live="design.background_color" class="h-10 w-full cursor-pointer rounded-lg border border-gray-300 dark:border-gray-600 bg-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('banners.fields.accent_color') }}</label>
                            <input type="color" wire:model.live="design.accent_color" class="h-10 w-full cursor-pointer rounded-lg border border-gray-300 dark:border-gray-600 bg-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('banners.fields.text_color') }}</label>
                            <input type="color" wire:model.live="design.text_color" class="h-10 w-full cursor-pointer rounded-lg border border-gray-300 dark:border-gray-600 bg-transparent">
                        </div>
                    </div>
                </div>
            </x-card>

            <x-card :title="__('banners.sections.items')">
                <div class="space-y-3">
                    @foreach ($design['items'] as $index => $item)
                        <div class="flex items-end gap-2" wire:key="item-{{ $index }}">
                            <div class="flex-1">
                                <x-input wire:model.live.debounce.400ms="design.items.{{ $index }}.name" :label="$index === 0 ? __('banners.fields.item_name') : null" />
                            </div>
                            <div class="w-24">
                                <x-input wire:model.live.debounce.400ms="design.items.{{ $index }}.note" :label="$index === 0 ? __('banners.fields.item_note') : null" />
                            </div>
                            <div class="w-32">
                                <x-input wire:model.live.debounce.400ms="design.items.{{ $index }}.price" :label="$index === 0 ? __('banners.fields.item_price') : null" />
                            </div>
                            <x-button flat negative icon="trash" wire:click="removeItem({{ $index }})" />
                        </div>
                    @endforeach

                    <x-button flat primary icon="plus" :label="__('banners.actions.add_item')" wire:click="addItem" />
                </div>
            </x-card>

            <x-card :title="__('banners.sections.export')">
                <div class="space-y-4">
                    <x-native-select wire:model="exportScale" :label="__('banners.fields.resolution')">
                        @foreach ([1, 2, 3] as $scale)
                            <option value="{{ $scale }}">
                                {{ $banner->format->width() * $scale }} × {{ $banner->format->height() * $scale }} px ({{ $scale }}x)
                            </option>
                        @endforeach
                    </x-native-select>

                    <div class="flex flex-wrap items-center gap-2">
                        <x-button
                            primary
                            icon="arrow-down-tray"
                            :label="__('banners.actions.export')"
                            wire:click="export"
                            :disabled="$banner->export_status === BannerJobStatus::Generating"
                        />

                        @if ($banner->export_status === BannerJobStatus::Ready)
                            <x-button flat positive icon="photo" :label="__('banners.actions.download_png')" wire:click="downloadPng" />
                            <x-button flat positive icon="document" :label="__('banners.actions.download_pdf')" wire:click="downloadPdf" />
                        @endif
                    </div>

                    @if ($banner->export_status === BannerJobStatus::Generating)
                        <p class="text-sm text-primary-600 dark:text-primary-400 animate-pulse">{{ __('banners.messages.export_generating') }}</p>
                    @endif

                    @if ($banner->export_status === BannerJobStatus::Failed)
                        <p class="text-sm text-red-600 dark:text-red-400">{{ __('banners.messages.export_failed') }}</p>
                    @endif
                </div>
            </x-card>
        </div>

        <div>
            <div class="xl:sticky xl:top-8">
                <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                    {{ __('banners.sections.preview') }}
                </h2>
                <div class="inline-block rounded-xl shadow-lg ring-1 ring-black/10 overflow-hidden"
                     style="width: 420px; height: {{ (int) round($banner->format->height() * $previewScale) }}px;">
                    <div style="transform: scale({{ $previewScale }}); transform-origin: top left;">
                        @include('banners.canvas', [
                            'format'        => $banner->format,
                            'design'        => $design,
                            'backgroundSrc' => $backgroundSrc,
                            'logoSrc'       => asset(Banner::LOGO_PATH),
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
