@use(App\Enums\BannerFont)
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="{{ BannerFont::stylesheetUrl(...BannerFont::usedIn($banner->design)) }}" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { width: {{ $banner->format->width() }}px; height: {{ $banner->format->height() }}px; }
    </style>
</head>
<body>
    @include('banners.canvas', [
        'format'        => $banner->format,
        'design'        => $banner->design,
        'backgroundSrc' => $backgroundSrc,
        'logoSrc'       => $logoSrc,
    ])
</body>
</html>
