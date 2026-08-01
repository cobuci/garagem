@use(App\Enums\BannerJobStatus)
@use(App\Enums\Permission)

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
                {{ $banner->format->label() }}
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

            <x-card :title="__('banners.sections.background')">
                <div class="space-y-4">
                    <x-textarea
                        wire:model="backgroundPrompt"
                        :label="__('banners.fields.background_prompt')"
                        :placeholder="__('banners.fields.background_prompt_placeholder')"
                        rows="3"
                    />

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
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
