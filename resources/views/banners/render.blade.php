<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
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
    ])
</body>
</html>
