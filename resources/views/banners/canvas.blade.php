@php
    $editable = $editable ?? false;
    $width = $format->width();
    $height = $format->height();
    $titleFont = App\Enums\BannerFont::fromDesign($design, 'title_font');
    $textFont = App\Enums\BannerFont::fromDesign($design, 'text_font');
    $items = collect($design['items'] ?? []);
    $items = $editable ? $items : $items->filter(fn ($item) => filled($item['name'] ?? null));
    $showLogo = ($design['show_logo'] ?? false) && filled($logoSrc ?? null);
    $logoWidth = match ($design['logo_size'] ?? 'medium') {
        'small' => 140,
        'large' => 280,
        default => 200,
    };
    $logoPosition = $design['logo_position'] ?? 'top';
    $logoAlign = match ($design['logo_align'] ?? 'center') {
        'left' => 'flex-start',
        'right' => 'flex-end',
        default => 'center',
    };
    $editableAttrs = 'contenteditable="true" spellcheck="false" class="banner-editable"';
    $formatPrice = fn ($price): string => is_numeric($price)
        ? 'R$ ' . number_format((float) $price, 2, ',', '.')
        : (string) $price;
@endphp
<div style="position: relative; width: {{ $width }}px; height: {{ $height }}px; background-color: {{ $design['background_color'] }}; overflow: hidden; font-family: {{ $textFont->family() }}; color: {{ $design['text_color'] }};">
    @if ($backgroundSrc)
        <img src="{{ $backgroundSrc }}" alt="" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
    @endif

    <div style="position: relative; display: flex; flex-direction: column; width: 100%; height: 100%; padding: 72px 84px; box-sizing: border-box;">
        @if ($showLogo && $logoPosition === 'top')
            <div style="display: flex; justify-content: {{ $logoAlign }}; margin-bottom: 32px;">
                <img src="{{ $logoSrc }}" alt="" style="width: {{ $logoWidth }}px; height: auto; display: block;">
            </div>
        @endif

        <div style="text-align: center;">
            <div
                @if ($editable) {!! $editableAttrs !!} x-on:keydown.enter.prevent="$event.target.blur()" x-on:blur="$wire.set('design.title', $event.target.innerText.trim())" @endif
                style="font-family: {{ $titleFont->family() }}; font-size: 76px; font-weight: 800; letter-spacing: 2px; line-height: 1.1;"
            >{{ $design['title'] }}</div>
            @if ($editable || filled($design['subtitle']))
                <div
                    @if ($editable) {!! $editableAttrs !!} x-on:keydown.enter.prevent="$event.target.blur()" x-on:blur="$wire.set('design.subtitle', $event.target.innerText.trim())" @endif
                    style="font-family: {{ $titleFont->family() }}; margin-top: 20px; font-size: 36px; font-weight: 700; color: {{ $design['accent_color'] }}; min-height: 40px;"
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
                        @if ($editable) {!! $editableAttrs !!} x-on:keydown.enter.prevent="$event.target.blur()" x-on:blur="$wire.set('design.items.{{ $index }}.price', $event.target.innerText.replace(/[^0-9,]/g, '').replace(',', '.'))" @endif
                        style="font-weight: 700; white-space: nowrap; min-width: 60px;"
                    >{{ $formatPrice($item['price'] ?? '') }}</span>
                </div>
            @endforeach
        </div>

        @if ($showLogo && $logoPosition === 'bottom')
            <div style="display: flex; justify-content: {{ $logoAlign }}; margin-top: 40px;">
                <img src="{{ $logoSrc }}" alt="" style="width: {{ $logoWidth }}px; height: auto; display: block;">
            </div>
        @endif

        @if ($editable || filled($design['footer']))
            <div
                @if ($editable) {!! $editableAttrs !!} x-on:keydown.enter.prevent="$event.target.blur()" x-on:blur="$wire.set('design.footer', $event.target.innerText.trim())" @endif
                style="text-align: center; font-size: 26px; font-weight: 600; opacity: 0.85; margin-top: 32px; min-height: 30px;"
            >{{ $design['footer'] }}</div>
        @endif
    </div>

    @foreach ($design['texts'] ?? [] as $index => $text)
        <div
            @if ($editable)
                wire:key="canvas-text-{{ $index }}"
                x-data
                x-on:pointerdown.prevent="
                    const el = $el;
                    const canvas = el.offsetParent.getBoundingClientRect();
                    const rect = el.getBoundingClientRect();
                    const offsetX = $event.clientX - rect.left;
                    const offsetY = $event.clientY - rect.top;
                    const move = (e) => {
                        const x = Math.min(100, Math.max(0, (e.clientX - offsetX - canvas.left) * 100 / canvas.width));
                        const y = Math.min(100, Math.max(0, (e.clientY - offsetY - canvas.top) * 100 / canvas.height));
                        el.style.left = Math.round(x * 10) / 10 + '%';
                        el.style.top = Math.round(y * 10) / 10 + '%';
                    };
                    const stop = () => {
                        window.removeEventListener('pointermove', move);
                        window.removeEventListener('pointerup', stop);
                        $wire.set('design.texts.{{ $index }}.x', parseFloat(el.style.left), false);
                        $wire.set('design.texts.{{ $index }}.y', parseFloat(el.style.top));
                    };
                    window.addEventListener('pointermove', move);
                    window.addEventListener('pointerup', stop);
                "
            @endif
            style="position: absolute; left: {{ $text['x'] }}%; top: {{ $text['y'] }}%; font-family: {{ App\Enums\BannerFont::fromDesign($text, 'font')->family() }}; font-size: {{ $text['size'] }}px; font-weight: 700; color: {{ $text['color'] }}; white-space: nowrap; line-height: 1.2;{{ $editable ? ' cursor: move; touch-action: none; user-select: none;' : '' }}"
        >{{ $text['content'] }}</div>
    @endforeach
</div>
