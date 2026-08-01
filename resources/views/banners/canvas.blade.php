@php
    $width = $format->width();
    $height = $format->height();
    $items = collect($design['items'] ?? [])->filter(fn ($item) => filled($item['name'] ?? null));
@endphp
<div style="position: relative; width: {{ $width }}px; height: {{ $height }}px; background-color: {{ $design['background_color'] }}; overflow: hidden; font-family: 'Instrument Sans', ui-sans-serif, sans-serif; color: {{ $design['text_color'] }};">
    @if ($backgroundSrc)
        <img src="{{ $backgroundSrc }}" alt="" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
    @endif

    <div style="position: relative; display: flex; flex-direction: column; width: 100%; height: 100%; padding: 88px 84px; box-sizing: border-box;">
        <div style="text-align: center;">
            <div style="font-size: 76px; font-weight: 800; letter-spacing: 2px; line-height: 1.1;">{{ $design['title'] }}</div>
            @if (filled($design['subtitle']))
                <div style="margin-top: 20px; font-size: 36px; font-weight: 700; color: {{ $design['accent_color'] }};">{{ $design['subtitle'] }}</div>
            @endif
        </div>

        <div style="display: flex; flex-direction: column; justify-content: center; gap: 30px; flex: 1; margin-top: 56px;">
            @foreach ($items as $item)
                <div style="display: flex; align-items: baseline; gap: 16px; font-size: 36px;">
                    <span style="font-weight: 600;">{{ $item['name'] }}</span>
                    @if (filled($item['note'] ?? null))
                        <span style="font-size: 24px; font-weight: 700; color: {{ $design['accent_color'] }};">{{ $item['note'] }}</span>
                    @endif
                    <span style="flex: 1; border-bottom: 3px dotted {{ $design['text_color'] }}55; margin: 0 8px;"></span>
                    <span style="font-weight: 700; white-space: nowrap;">{{ $item['price'] }}</span>
                </div>
            @endforeach
        </div>

        @if (filled($design['footer']))
            <div style="text-align: center; font-size: 26px; font-weight: 600; opacity: 0.85; margin-top: 40px;">{{ $design['footer'] }}</div>
        @endif
    </div>
</div>
