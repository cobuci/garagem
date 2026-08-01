<?php

use App\Enums\BannerFormat;
use App\Models\Banner;
use App\Models\User;
use App\Services\BannerBackgroundService;
use Illuminate\Support\Facades\Storage;
use Laravel\Ai\Image;

it('generates and stores a banner background with laravel ai', function () {
    Storage::fake();
    Image::fake();

    $user = User::factory()->create();
    $banner = Banner::create([
        'user_id'           => $user->id,
        'name'              => 'Banner IA',
        'format'            => BannerFormat::Stories,
        'design'            => Banner::defaultDesign(),
        'background_prompt' => 'fundo azul de oficina',
    ]);

    $path = app(BannerBackgroundService::class)->generate($banner);

    expect($path)->toStartWith("banners/{$banner->id}/background-")
        ->and(Storage::exists($path))->toBeTrue();

    Image::assertGenerated(fn ($prompt) => str_contains($prompt->prompt, 'fundo azul de oficina')
        && $prompt->size === '9:16');
});
