@php
    $editable = $editable ?? false;
    $width = $format->width();
    $height = $format->height();
    $items = collect($design['items'] ?? []);
    $items = $editable ? $items : $items->filter(fn ($item) => filled($item['name'] ?? null));
    $showLogo = ($design['show_logo'] ?? false) && filled($logoSrc ?? null);
    $logoWidth = match ($design['logo_size'] ?? 'medium') {
        'small' => 140,
        'large' => 280,
        default => 200,
    };
    $logoPosition = $design['logo_position'] ?? 'top';
    $logoAlign = $design['logo_align'] ?? 'center';
    $editableAttrs = 'contenteditable="true" spellcheck="false" class="banner-editable"';
@endphp
<div style="position: relative; width: {{ $width }}px; height: {{ $height }}px; background-color: {{ $design['background_color'] }}; overflow: hidden; font-family: 'Instrument Sans', ui-sans-serif, sans-serif; color: {{ $design['text_color'] }};">
    @if ($backgroundSrc)
        <img src="{{ $backgroundSrc }}" alt="" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
    @endif

    <div style="position: relative; display: flex; flex-direction: column; width: 100%; height: 100%; padding: 72px 84px; box-sizing: border-box;">
        @if ($showLogo && $logoPosition === 'top')
            <div style="text-align: {{ $logoAlign }}; margin-bottom: 32px;">
                <img src="{{ $logoSrc }}" alt="" style="width: {{ $logoWidth }}px; height: auto;">
            </div>
        @endif

        <div style="text-align: center;">
            <div
                @if ($editable) {!! $editableAttrs !!} x-on:keydown.enter.prevent="$event.target.blur()" x-on:blur="$wire.set('design.title', $event.target.innerText.trim())" @endif
                style="font-size: 76px; font-weight: 800; letter-spacing: 2px; line-height: 1.1;"
            >{{ $design['title'] }}</div>
            @if ($editable || filled($design['subtitle']))
                <div
                    @if ($editable) {!! $editableAttrs !!} x-on:keydown.enter.prevent="$event.target.blur()" x-on:blur="$wire.set('design.subtitle', $event.target.innerText.trim())" @endif
                    style="margin-top: 20px; font-size: 36px; font-weight: 700; color: {{ $design['accent_color'] }}; min-height: 40px;"
                >{{ $design['subtitle'] }}</div>
            @endif
        </div>

        <div @if ($editable) wire:sort="sortItems" @endif style="display: flex; flex-direction: column; justify-content: center; gap: 30px; flex: 1; margin-top: 48px;">
            @foreach ($items as $index => $item)
                <div
                    @if ($editable) wire:sort:item="{{ $index }}" wire:key="canvas-item-{{ $index }}" @endif
                    style="display: flex; align-items: baseline; gap: 16px; font-size: 36px;"
                >
                    @if ($editable)
                        <span wire:sort:handle title="{{ __('banners.hints.drag_to_reorder') }}" style="cursor: grab; opacity: 0.35; font-size: 30px; user-select: none; line-height: 1;">⠿</span>
                    @endif
                    <span
                        @if ($editable) {!! $editableAttrs !!} x-on:keydown.enter.prevent="$event.target.blur()" x-on:blur="$wire.set('design.items.{{ $index }}.name', $event.target.innerText.trim())" @endif
                        style="font-weight: 600; min-width: 60px;"
                    >{{ $item['name'] }}</span>
                    @if ($editable || filled($item['note'] ?? null))
                        <span
                            @if ($editable) {!! $editableAttrs !!} x-on:keydown.enter.prevent="$event.target.blur()" x-on:blur="$wire.set('design.items.{{ $index }}.note', $event.target.innerText.trim())" @endif
                            style="font-size: 24px; font-weight: 700; color: {{ $design['accent_color'] }}; min-width: 40px;"
                        >{{ $item['note'] ?? '' }}</span>
                    @endif
                    <span style="flex: 1; border-bottom: 3px dotted {{ $design['text_color'] }}55; margin: 0 8px;"></span>
                    <span
                        @if ($editable) {!! $editableAttrs !!} x-on:keydown.enter.prevent="$event.target.blur()" x-on:blur="$wire.set('design.items.{{ $index }}.price', $event.target.innerText.trim())" @endif
                        style="font-weight: 700; white-space: nowrap; min-width: 60px;"
                    >{{ $item['price'] }}</span>
                </div>
            @endforeach
        </div>

        @if ($showLogo && $logoPosition === 'bottom')
            <div style="text-align: {{ $logoAlign }}; margin-top: 40px;">
                <img src="{{ $logoSrc }}" alt="" style="width: {{ $logoWidth }}px; height: auto;">
            </div>
        @endif

        @if ($editable || filled($design['footer']))
            <div
                @if ($editable) {!! $editableAttrs !!} x-on:keydown.enter.prevent="$event.target.blur()" x-on:blur="$wire.set('design.footer', $event.target.innerText.trim())" @endif
                style="text-align: center; font-size: 26px; font-weight: 600; opacity: 0.85; margin-top: 32px; min-height: 30px;"
            >{{ $design['footer'] }}</div>
        @endif
    </div>
</div>
