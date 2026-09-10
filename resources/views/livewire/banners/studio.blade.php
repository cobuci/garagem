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

    $exportIsOutdated = $banner->export_status === BannerJobStatus::Ready
        && $banner->export_design != [...$design, 'format' => $banner->format->value];
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
        .banner-editable { transition: outline-color 0.15s, box-shadow 0.15s; outline: 2px dashed transparent; outline-offset: 4px; border-radius: 6px; }
        .banner-editable:hover { outline-color: rgba(2, 132, 199, 0.7); cursor: text; }
        .banner-editable:focus { outline-color: #0284c7; }
        .banner-draggable { transition: outline-color 0.15s; outline: 2px dashed transparent; outline-offset: 4px; border-radius: 6px; }
        .banner-draggable:hover { outline-color: rgba(2, 132, 199, 0.7); }
    </style>

    <div
        class="sticky top-16 lg:top-0 z-30 flex items-center justify-between gap-3 mb-6 -mx-4 lg:-mx-8 px-4 lg:px-8 py-3 backdrop-blur transition-[background-color,box-shadow] duration-200"
        x-data="{ stuck: false }"
        x-init="stuck = $el.getBoundingClientRect().top <= (window.innerWidth >= 1024 ? 1 : 65)"
        x-on:scroll.window="stuck = $el.getBoundingClientRect().top <= (window.innerWidth >= 1024 ? 1 : 65)"
        :class="stuck
            ? 'bg-white/95 dark:bg-gray-800/95 shadow-md shadow-black/5 dark:shadow-black/30 border-b border-gray-200/80 dark:border-gray-700/60'
            : 'bg-gray-50/95 dark:bg-gray-900/95 border-b border-transparent'"
    >
        <div class="min-w-0">
            <div class="flex items-center gap-2.5 min-w-0">
                <h1 class="text-xl lg:text-2xl font-bold text-gray-900 dark:text-white truncate">{{ $banner->name }}</h1>
                <span class="inline-flex items-center gap-1.5 shrink-0 rounded-full bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 px-2.5 py-0.5 text-xs font-semibold text-amber-700 dark:text-amber-300 {{ $isDirty ? '' : 'hidden' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                    {{ __('banners.messages.unsaved_changes') }}
                </span>
            </div>
            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400 truncate">
                {{ $banner->format->label() }}
                <span class="text-amber-600 dark:text-amber-400 font-medium {{ $isDirty ? '' : 'hidden' }}">· {{ __('banners.messages.save_to_keep') }}</span>
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
                    spinner="save"
                    class="!hidden lg:!inline-flex"
                />
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 lg:gap-8">
        <div class="lg:col-span-2 lg:order-last min-w-0">
            <div class="lg:sticky lg:top-20 min-w-0">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('banners.sections.preview') }}
                    </h2>
                    @can(Permission::EditBanner->value)
                        <select
                            wire:model.live="format"
                            aria-label="{{ __('banners.fields.format') }}"
                            class="rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-xs font-medium text-gray-700 dark:text-gray-300 py-1.5 pl-2.5 pr-8 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 shadow-2xs transition-colors"
                        >
                            @foreach (App\Enums\BannerFormat::cases() as $formatOption)
                                <option value="{{ $formatOption->value }}">{{ $formatOption->label() }} ({{ $formatOption->width() }} × {{ $formatOption->height() }})</option>
                            @endforeach
                        </select>
                    @endcan
                </div>

                <div
                    wire:key="preview-frame-{{ $banner->format->value }}"
                    class="banner-preview-frame relative w-full max-w-[480px] mx-auto min-w-0 rounded-xl shadow-md border border-gray-200/80 dark:border-gray-700 ring-1 ring-black/5 dark:ring-white/5 overflow-hidden bg-gray-100 dark:bg-gray-900"
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
                            'logoSrc'       => Banner::logoUrl(),
                            'editable'      => auth()->user()?->can(Permission::EditBanner->value) ?? false,
                        ])
                    </div>
                </div>

                <p class="mt-2.5 text-center text-xs text-gray-400 dark:text-gray-500 flex items-center justify-center gap-1">
                    <x-icon name="cursor-arrow-rays" class="w-3.5 h-3.5 inline-block shrink-0" />
                    {{ __('banners.hints.tap_to_edit') }}
                </p>
            </div>
        </div>

        <div class="lg:col-span-3 space-y-4" x-data="{ active: 'presets' }">
            <x-banner.section name="presets" :title="__('banners.sections.presets')" :hint="__('banners.hints.presets')">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                    @foreach (array_keys(Banner::presets()) as $presetKey)
                        <button
                            type="button"
                            wire:click="applyPreset('{{ $presetKey }}')"
                            class="flex items-center justify-center gap-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/80 px-3.5 py-2.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 hover:border-sky-500 hover:bg-sky-50/50 dark:hover:bg-sky-950/30 hover:text-sky-700 dark:hover:text-sky-300 transition-all duration-150 shadow-2xs focus:outline-none focus:ring-2 focus:ring-sky-500"
                        >
                            <x-icon name="sparkles" class="w-4 h-4 text-sky-600 dark:text-sky-400 shrink-0" />
                            <span>{{ __('banners.presets.' . $presetKey) }}</span>
                        </button>
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
                                class="relative rounded-lg border p-3 text-left transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-sky-500
                                    {{ $backgroundTheme === $theme->value
                                        ? 'border-sky-500 ring-2 ring-sky-500/20 bg-sky-50/80 dark:bg-sky-950/30 shadow-2xs'
                                        : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/60 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50/80 dark:hover:bg-gray-700/30' }}"
                            >
                                <div class="flex items-start justify-between gap-1">
                                    <span class="block text-sm font-semibold text-gray-900 dark:text-white">{{ $theme->label() }}</span>
                                    @if ($backgroundTheme === $theme->value)
                                        <x-icon name="check-circle" class="w-4 h-4 text-sky-600 dark:text-sky-400 shrink-0 mt-0.5" />
                                    @endif
                                </div>
                                <span class="block mt-1 text-xs text-gray-500 dark:text-gray-400 leading-relaxed">{{ $theme->description() }}</span>
                            </button>
                        @endforeach
                    </div>
                    @error('backgroundTheme')
                        <p class="text-sm text-red-600 dark:text-red-400">{{ __('banners.messages.theme_required') }}</p>
                    @enderror

                    <div>
                        <div class="flex items-center gap-1.5 mb-2">
                            <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('banners.fields.mood') }}</span>
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
                                    class="rounded-full px-4 py-1.5 text-xs font-semibold border transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-sky-500
                                        {{ $backgroundMood === $mood->value
                                            ? 'border-sky-600 bg-sky-600 text-white shadow-xs'
                                            : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:border-sky-400 hover:text-sky-600 dark:hover:text-sky-400' }}"
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
                            <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('banners.fields.intensity') }}</span>
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
                                    class="rounded-full px-4 py-1.5 text-xs font-semibold border transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-sky-500
                                        {{ $backgroundIntensity === $intensity->value
                                            ? 'border-sky-600 bg-sky-600 text-white shadow-xs'
                                            : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:border-sky-400 hover:text-sky-600 dark:hover:text-sky-400' }}"
                                >
                                    {{ $intensity->label() }}
                                </button>
                            @endforeach
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            {{ $backgroundIntensity ? BannerIntensity::from($backgroundIntensity)->tip() : __('banners.hints.intensity') }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5 pt-1">
                        <x-button
                            primary
                            icon="sparkles"
                            :label="__('banners.actions.generate_background')"
                            wire:click="generateBackground"
                            spinner="generateBackground"
                            :disabled="$banner->background_status === BannerJobStatus::Generating"
                        />

                        @if ($banner->background_path)
                            <x-button flat negative icon="trash" :label="__('banners.actions.remove_background')" wire:click="removeBackground" />
                        @endif
                    </div>

                    @if ($banner->background_status === BannerJobStatus::Generating)
                        <div class="flex items-center gap-2.5 rounded-lg bg-sky-50 dark:bg-sky-950/30 border border-sky-200 dark:border-sky-800/50 p-3 text-sky-700 dark:text-sky-300">
                            <svg class="h-4 w-4 animate-spin text-sky-600 dark:text-sky-400 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <p class="text-xs font-medium">{{ __('banners.messages.background_generating') }}</p>
                        </div>
                    @endif

                    @if ($banner->background_status === BannerJobStatus::Failed)
                        <div class="flex items-center gap-2.5 rounded-lg bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800/50 p-3 text-red-700 dark:text-red-300">
                            <x-icon name="exclamation-circle" class="w-4 h-4 shrink-0 text-red-600 dark:text-red-400" />
                            <p class="text-xs font-medium">{{ __('banners.messages.background_failed') }}</p>
                        </div>
                    @endif
                </div>
            </x-banner.section>

            <x-banner.section name="logo" :title="__('banners.sections.logo')">
                @if (Banner::hasLogo())
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
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('banners.messages.logo_missing') }}
                        @can(Permission::ViewAdmin->value)
                            <a href="{{ route('admin.index', ['t' => 'brand']) }}" wire:navigate class="text-primary-600 dark:text-primary-400 hover:underline">{{ __('banners.actions.upload_logo') }}</a>
                        @endcan
                    </p>
                @endif
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

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1.5">{{ __('banners.fields.background_color') }}</label>
                            <div class="flex items-center gap-2">
                                <input type="color" wire:model.live="design.background_color" class="h-9 w-12 cursor-pointer rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 p-0.5 shadow-2xs">
                                <span class="text-xs font-mono font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">{{ $design['background_color'] ?? '' }}</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1.5">{{ __('banners.fields.accent_color') }}</label>
                            <div class="flex items-center gap-2">
                                <input type="color" wire:model.live="design.accent_color" class="h-9 w-12 cursor-pointer rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 p-0.5 shadow-2xs">
                                <span class="text-xs font-mono font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">{{ $design['accent_color'] ?? '' }}</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1.5">{{ __('banners.fields.text_color') }}</label>
                            <div class="flex items-center gap-2">
                                <input type="color" wire:model.live="design.text_color" class="h-9 w-12 cursor-pointer rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 p-0.5 shadow-2xs">
                                <span class="text-xs font-mono font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">{{ $design['text_color'] ?? '' }}</span>
                            </div>
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
                                <x-input wire:model.live.debounce.400ms="design.items.{{ $index }}.note" :label="$index === 0 ? __('banners.fields.item_note') : null" :placeholder="__('banners.fields.item_note_placeholder')" />
                            </div>
                            <div class="w-28 sm:w-32">
                                <x-money-input prefix="R$" wire:model.live.debounce.400ms="design.items.{{ $index }}.price" :label="$index === 0 ? __('banners.fields.item_price') : null" />
                            </div>
                            <x-button flat negative icon="trash" wire:click="removeItem({{ $index }})" aria-label="{{ __('banners.actions.delete') }}" title="{{ __('banners.actions.delete') }}" />
                        </div>
                    @endforeach

                    <x-button flat primary icon="plus" :label="__('banners.actions.add_item')" wire:click="addItem" />
                </div>
            </x-banner.section>

            <x-banner.section name="texts" :title="__('banners.sections.texts')" :hint="__('banners.hints.texts')">
                <div class="space-y-3">
                    @foreach ($design['texts'] ?? [] as $index => $text)
                        <div class="space-y-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/40 p-3" wire:key="text-{{ $index }}">
                            <div class="flex items-end gap-2">
                                <div class="flex-1 min-w-0">
                                    <x-input wire:model.live.debounce.400ms="design.texts.{{ $index }}.content" :label="__('banners.fields.text_content')" />
                                </div>
                                <x-button flat negative icon="trash" wire:click="removeText({{ $index }})" aria-label="{{ __('banners.actions.delete') }}" title="{{ __('banners.actions.delete') }}" />
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                <x-native-select wire:model.live="design.texts.{{ $index }}.font" :label="__('banners.fields.font')">
                                    @foreach (BannerFont::cases() as $font)
                                        <option value="{{ $font->value }}" style="font-family: {{ $font->family() }}">{{ $font->label() }}</option>
                                    @endforeach
                                </x-native-select>
                                <x-input type="number" min="12" max="200" wire:model.live.debounce.400ms="design.texts.{{ $index }}.size" :label="__('banners.fields.size')" />
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">{{ __('banners.fields.color') }}</label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" wire:model.live="design.texts.{{ $index }}.color" class="h-9 w-10 cursor-pointer rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 p-0.5">
                                        <span class="text-xs font-mono font-medium text-gray-600 dark:text-gray-400 uppercase">{{ $design['texts'][$index]['color'] ?? '' }}</span>
                                    </div>
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

                    <div class="flex flex-wrap items-center gap-2.5 pt-1">
                        <x-button
                            primary
                            icon="arrow-down-tray"
                            :label="__('banners.actions.export')"
                            wire:click="export"
                            spinner="export"
                            :disabled="$banner->export_status === BannerJobStatus::Generating"
                        />

                        @if ($banner->export_status === BannerJobStatus::Ready)
                            <x-button flat positive icon="photo" :label="__('banners.actions.download_png')" wire:click="downloadPng" />
                            <x-button flat positive icon="document" :label="__('banners.actions.download_pdf')" wire:click="downloadPdf" />
                        @endif
                    </div>

                    @if ($exportIsOutdated)
                        <div class="flex items-start gap-2.5 rounded-lg bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50 p-3 text-amber-800 dark:text-amber-300 text-xs">
                            <x-icon name="exclamation-triangle" class="w-4 h-4 mt-0.5 shrink-0 text-amber-600 dark:text-amber-400" />
                            <p>{{ __('banners.messages.export_outdated') }}</p>
                        </div>
                    @endif

                    @if ($banner->export_status === BannerJobStatus::Generating)
                        <div class="flex items-center gap-2.5 rounded-lg bg-sky-50 dark:bg-sky-950/30 border border-sky-200 dark:border-sky-800/50 p-3 text-sky-700 dark:text-sky-300">
                            <svg class="h-4 w-4 animate-spin text-sky-600 dark:text-sky-400 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <p class="text-xs font-medium">{{ __('banners.messages.export_generating') }}</p>
                        </div>
                    @endif

                    @if ($banner->export_status === BannerJobStatus::Failed)
                        <div class="flex items-center gap-2.5 rounded-lg bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800/50 p-3 text-red-700 dark:text-red-300">
                            <x-icon name="exclamation-circle" class="w-4 h-4 shrink-0 text-red-600 dark:text-red-400" />
                            <p class="text-xs font-medium">{{ __('banners.messages.export_failed') }}</p>
                        </div>
                    @endif
                </div>
            </x-banner.section>
        </div>
    </div>

    @can(Permission::EditBanner->value)
        @if ($isDirty)
            <div class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 dark:bg-gray-800/95 backdrop-blur border-t border-gray-200 dark:border-gray-700 p-3 space-y-2">
                <p class="text-center text-xs font-medium text-amber-700 dark:text-amber-300">
                    {{ __('banners.messages.unsaved_changes') }} — {{ __('banners.messages.save_to_keep') }}
                </p>
                <x-button
                    warning
                    icon="check"
                    :label="__('banners.actions.save_changes')"
                    wire:click="save"
                    spinner="save"
                    class="w-full"
                />
            </div>
        @endif
    @endcan
</div>
