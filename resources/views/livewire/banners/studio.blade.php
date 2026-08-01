@use(App\Enums\BannerFont)
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

    $canvasWidth = $banner->format->width();
    $canvasHeight = $banner->format->height();
@endphp

<div
    class="pb-24 lg:pb-0"
    @if ($isWorking) wire:poll.2s="refreshStatus" @endif
    x-data
    x-init="
        const warn = (event) => {
            if (! $wire.isDirty) return
            event.preventDefault()
            event.returnValue = ''
        }
        window.addEventListener('beforeunload', warn)
    "
>
    <link href="{{ BannerFont::stylesheetUrl(...BannerFont::cases()) }}" rel="stylesheet">
    <style>
        .banner-editable { transition: outline-color 0.15s; outline: 2px dashed transparent; outline-offset: 6px; }
        .banner-editable:hover { outline-color: rgba(56, 182, 248, 0.7); cursor: text; }
        .banner-editable:focus { outline-color: rgba(56, 182, 248, 1); }
    </style>

    <div class="sticky top-16 lg:top-0 z-30 flex items-center justify-between gap-3 mb-6 -mx-4 lg:-mx-8 px-4 lg:px-8 py-3 bg-gray-50/95 dark:bg-gray-900/95 backdrop-blur">
        <div class="min-w-0">
            <div class="flex items-center gap-2 min-w-0">
                <h1 class="text-xl lg:text-2xl font-bold text-gray-900 dark:text-white truncate">{{ $banner->name }}</h1>
                <span class="inline-flex items-center gap-1.5 shrink-0 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-800 dark:bg-amber-500/15 dark:text-amber-300 {{ $isDirty ? '' : 'hidden' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                    {{ __('banners.messages.unsaved_changes') }}
                </span>
            </div>
            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400 truncate">
                {{ $banner->format->label() }}
                <span class="text-amber-600 dark:text-amber-400 {{ $isDirty ? '' : 'hidden' }}">· {{ __('banners.messages.save_to_keep') }}</span>
            </p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <x-button
                flat
                icon="arrow-left"
                :label="__('banners.actions.back')"
                :href="route('banners.index')"
                wire:navigate
                class="!hidden sm:!inline-flex"
                x-on:click="if ($wire.isDirty && ! confirm({{ \Illuminate\Support\Js::from(__('banners.messages.discard_unsaved')) }})) $event.preventDefault()"
            />
            <x-button
                flat
                icon="arrow-left"
                :href="route('banners.index')"
                wire:navigate
                class="sm:!hidden"
                x-on:click="if ($wire.isDirty && ! confirm({{ \Illuminate\Support\Js::from(__('banners.messages.discard_unsaved')) }})) $event.preventDefault()"
            />

            @can(Permission::EditBanner->value)
                <x-button
                    :color="$isDirty ? 'warning' : 'primary'"
                    icon="check"
                    :label="$isDirty ? __('banners.actions.save_changes') : __('banners.actions.save')"
                    wire:click="save"
                    class="!hidden lg:!inline-flex"
                />
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 lg:gap-8">
        <div class="lg:col-span-2 lg:order-last min-w-0">
            <div class="lg:sticky lg:top-8 min-w-0">
                <div class="flex items-baseline justify-between mb-3">
                    <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('banners.sections.preview') }}
                    </h2>
                    <span
                        class="text-xs text-gray-400 dark:text-gray-500 cursor-help border-b border-dashed border-gray-300 dark:border-gray-600"
                        title="{{ __('banners.hints.tap_to_edit') }}"
                    >{{ __('banners.hints.tap_to_edit') }}</span>
                </div>

                <div
                    class="banner-preview-frame relative w-full max-w-[480px] mx-auto min-w-0 rounded-xl shadow-lg ring-1 ring-black/10 overflow-hidden bg-gray-100 dark:bg-gray-900"
                    style="aspect-ratio: {{ $canvasWidth }} / {{ $canvasHeight }};"
                    x-data="{
                        canvasWidth: {{ $canvasWidth }},
                        scale: {{ 480 / $canvasWidth }},
                        fit() {
                            const measured = this.$el.clientWidth
                            if (measured < 1) {
                                return
                            }
                            this.scale = measured / this.canvasWidth
                        },
                    }"
                    x-init="
                        fit()
                        requestAnimationFrame(() => fit())
                        ;[50, 150, 400].forEach((ms) => setTimeout(() => fit(), ms))
                        new ResizeObserver(() => fit()).observe($el)
                        window.addEventListener('resize', () => fit())
                    "
                >
                    <div
                        class="absolute top-0 left-0"
                        wire:key="banner-preview-{{ $banner->id }}-{{ $banner->updated_at?->timestamp }}"
                        style="width: {{ $canvasWidth }}px; transform: scale({{ 480 / $canvasWidth }}); transform-origin: top left;"
                        :style="`width: ${canvasWidth}px; transform: scale(${scale}); transform-origin: top left;`"
                    >
                        @include('banners.canvas', [
                            'format'        => $banner->format,
                            'design'        => $design,
                            'backgroundSrc' => $backgroundSrc,
                            'logoSrc'       => asset(Banner::LOGO_PATH),
                            'editable'      => auth()->user()?->can(Permission::EditBanner->value) ?? false,
                        ])
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-3 space-y-4" x-data="{ active: 'presets' }">
            <x-banner.section name="presets" :title="__('banners.sections.presets')" :hint="__('banners.hints.presets')">
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
            </x-banner.section>

            <x-banner.section name="background" :title="__('banners.sections.background')" :hint="__('banners.hints.background')">
                <div class="space-y-4">
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
                        <div class="flex items-center gap-1.5 mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('banners.fields.mood') }}</span>
                            <span class="text-gray-400 cursor-help" title="{{ __('banners.hints.mood') }}">
                                <x-icon name="question-mark-circle" class="w-4 h-4" />
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach (BannerMood::cases() as $mood)
                                <button
                                    type="button"
                                    wire:click="$set('backgroundMood', {{ $backgroundMood === $mood->value ? 'null' : "'{$mood->value}'" }})"
                                    title="{{ $mood->tip() }}"
                                    class="rounded-full px-4 py-1.5 text-sm font-medium border transition
                                        {{ $backgroundMood === $mood->value
                                            ? 'border-primary-500 bg-primary-500 text-white'
                                            : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-primary-400' }}"
                                >
                                    {{ $mood->label() }}
                                </button>
                            @endforeach
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            {{ $backgroundMood ? BannerMood::from($backgroundMood)->tip() : __('banners.hints.mood') }}
                        </p>
                    </div>

                    <div>
                        <div class="flex items-center gap-1.5 mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('banners.fields.intensity') }}</span>
                            <span class="text-gray-400 cursor-help" title="{{ __('banners.hints.intensity') }}">
                                <x-icon name="question-mark-circle" class="w-4 h-4" />
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach (BannerIntensity::cases() as $intensity)
                                <button
                                    type="button"
                                    wire:click="$set('backgroundIntensity', {{ $backgroundIntensity === $intensity->value ? 'null' : "'{$intensity->value}'" }})"
                                    title="{{ $intensity->tip() }}"
                                    class="rounded-full px-4 py-1.5 text-sm font-medium border transition
                                        {{ $backgroundIntensity === $intensity->value
                                            ? 'border-primary-500 bg-primary-500 text-white'
                                            : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-primary-400' }}"
                                >
                                    {{ $intensity->label() }}
                                </button>
                            @endforeach
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            {{ $backgroundIntensity ? BannerIntensity::from($backgroundIntensity)->tip() : __('banners.hints.intensity') }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
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
            </x-banner.section>

            <x-banner.section name="logo" :title="__('banners.sections.logo')">
                <div class="space-y-4">
                    <x-toggle wire:model.live="design.show_logo" :label="__('banners.fields.show_logo')" />

                    @if ($design['show_logo'])
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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

                        <div>
                            <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('banners.fields.logo_align') }}</span>
                            <div class="inline-flex rounded-lg border border-gray-300 dark:border-gray-600 overflow-hidden">
                                @foreach (['left', 'center', 'right'] as $align)
                                    <button
                                        type="button"
                                        wire:click="$set('design.logo_align', '{{ $align }}')"
                                        class="px-4 py-2 text-sm font-medium transition
                                            {{ ($design['logo_align'] ?? 'center') === $align
                                                ? 'bg-primary-500 text-white'
                                                : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}"
                                    >
                                        {{ __('banners.logo_aligns.' . $align) }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </x-banner.section>

            <x-banner.section name="content" :title="__('banners.sections.content')" :hint="__('banners.hints.content')">
                <div class="space-y-4">
                    <x-input wire:model.live.debounce.400ms="design.title" :label="__('banners.fields.title')" />
                    <x-input wire:model.live.debounce.400ms="design.subtitle" :label="__('banners.fields.subtitle')" />
                    <x-input wire:model.live.debounce.400ms="design.footer" :label="__('banners.fields.footer')" />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-native-select wire:model.live="design.title_font" :label="__('banners.fields.title_font')">
                            @foreach (BannerFont::cases() as $font)
                                <option value="{{ $font->value }}" style="font-family: {{ $font->family() }}">{{ $font->label() }}</option>
                            @endforeach
                        </x-native-select>

                        <x-native-select wire:model.live="design.text_font" :label="__('banners.fields.text_font')">
                            @foreach (BannerFont::cases() as $font)
                                <option value="{{ $font->value }}" style="font-family: {{ $font->family() }}">{{ $font->label() }}</option>
                            @endforeach
                        </x-native-select>
                    </div>

                    <div>
                        <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('banners.fields.brand_colors') }}</span>
                        <div class="flex flex-wrap gap-2">
                            @foreach (Banner::brandPalettes() as $paletteKey => $palette)
                                <button
                                    type="button"
                                    wire:click="applyPalette('{{ $paletteKey }}')"
                                    class="flex items-center gap-2 rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:border-primary-400 transition"
                                >
                                    <span class="flex -space-x-1">
                                        <span class="w-4 h-4 rounded-full ring-1 ring-black/10" style="background: {{ $palette['background_color'] }}"></span>
                                        <span class="w-4 h-4 rounded-full ring-1 ring-black/10" style="background: {{ $palette['accent_color'] }}"></span>
                                        <span class="w-4 h-4 rounded-full ring-1 ring-black/10" style="background: {{ $palette['text_color'] }}"></span>
                                    </span>
                                    {{ __('banners.palettes.' . $paletteKey) }}
                                </button>
                            @endforeach
                        </div>
                    </div>

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
            </x-banner.section>

            <x-banner.section name="items" :title="__('banners.sections.items')" :hint="__('banners.hints.items')">
                <div class="space-y-3">
                    @foreach ($design['items'] as $index => $item)
                        <div class="flex items-end gap-2" wire:key="item-{{ $index }}">
                            <div class="flex-1 min-w-0">
                                <x-input wire:model.live.debounce.400ms="design.items.{{ $index }}.name" :label="$index === 0 ? __('banners.fields.item_name') : null" />
                            </div>
                            <div class="w-20 sm:w-24">
                                <x-input wire:model.live.debounce.400ms="design.items.{{ $index }}.note" :label="$index === 0 ? __('banners.fields.item_note') : null" />
                            </div>
                            <div class="w-28 sm:w-32">
                                <x-input wire:model.live.debounce.400ms="design.items.{{ $index }}.price" :label="$index === 0 ? __('banners.fields.item_price') : null" />
                            </div>
                            <x-button flat negative icon="trash" wire:click="removeItem({{ $index }})" />
                        </div>
                    @endforeach

                    <x-button flat primary icon="plus" :label="__('banners.actions.add_item')" wire:click="addItem" />
                </div>
            </x-banner.section>

            <x-banner.section name="texts" :title="__('banners.sections.texts')" :hint="__('banners.hints.texts')">
                <div class="space-y-3">
                    @foreach ($design['texts'] ?? [] as $index => $text)
                        <div class="space-y-3 rounded-lg border border-gray-200 dark:border-gray-700 p-3" wire:key="text-{{ $index }}">
                            <div class="flex items-end gap-2">
                                <div class="flex-1 min-w-0">
                                    <x-input wire:model.live.debounce.400ms="design.texts.{{ $index }}.content" :label="__('banners.fields.text_content')" />
                                </div>
                                <x-button flat negative icon="trash" wire:click="removeText({{ $index }})" />
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                <x-native-select wire:model.live="design.texts.{{ $index }}.font" :label="__('banners.fields.font')">
                                    @foreach (BannerFont::cases() as $font)
                                        <option value="{{ $font->value }}" style="font-family: {{ $font->family() }}">{{ $font->label() }}</option>
                                    @endforeach
                                </x-native-select>
                                <x-input type="number" min="12" max="200" wire:model.live.debounce.400ms="design.texts.{{ $index }}.size" :label="__('banners.fields.size')" />
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('banners.fields.color') }}</label>
                                    <input type="color" wire:model.live="design.texts.{{ $index }}.color" class="h-10 w-full cursor-pointer rounded-lg border border-gray-300 dark:border-gray-600 bg-transparent">
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <x-button flat primary icon="plus" :label="__('banners.actions.add_text')" wire:click="addText" />
                </div>
            </x-banner.section>

            <x-banner.section name="export" :title="__('banners.sections.export')">
                <div class="space-y-4">
                    <div class="flex items-end gap-2">
                        <div class="flex-1">
                            <x-native-select wire:model="exportScale" :label="__('banners.fields.resolution')">
                                @foreach ([1, 2, 3] as $scale)
                                    <option value="{{ $scale }}">
                                        {{ $banner->format->width() * $scale }} × {{ $banner->format->height() * $scale }} px ({{ $scale }}x)
                                    </option>
                                @endforeach
                            </x-native-select>
                        </div>
                        <span class="mb-2 text-gray-400 cursor-help" title="{{ __('banners.hints.resolution') }}">
                            <x-icon name="question-mark-circle" class="w-5 h-5" />
                        </span>
                    </div>

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
            </x-banner.section>
        </div>
    </div>

    @can(Permission::EditBanner->value)
        <div class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 dark:bg-gray-800/95 backdrop-blur border-t border-gray-200 dark:border-gray-700 p-3 space-y-2">
            <p class="text-center text-xs font-medium text-amber-700 dark:text-amber-300 {{ $isDirty ? '' : 'hidden' }}">
                {{ __('banners.messages.unsaved_changes') }} — {{ __('banners.messages.save_to_keep') }}
            </p>
            <x-button
                :color="$isDirty ? 'warning' : 'primary'"
                icon="check"
                :label="$isDirty ? __('banners.actions.save_changes') : __('banners.actions.save')"
                wire:click="save"
                class="w-full"
            />
        </div>
    @endcan
</div>
