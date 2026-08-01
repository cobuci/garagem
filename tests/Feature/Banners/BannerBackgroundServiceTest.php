<?php

use App\Enums\BannerFormat;
use App\Enums\BannerIntensity;
use App\Enums\BannerMood;
use App\Enums\BannerTheme;
use App\Models\Banner;
use App\Models\User;
use App\Services\BannerBackgroundService;
use Illuminate\Support\Facades\Storage;
use Laravel\Ai\Image;

it('generates and stores a banner background from the selected theme', function () {
    Storage::fake();
    Image::fake();

    $user = User::factory()->create();
    $banner = Banner::create([
        'user_id'              => $user->id,
        'name'                 => 'Banner IA',
        'format'               => BannerFormat::Stories,
        'design'               => Banner::defaultDesign(),
        'background_theme'     => BannerTheme::Barbecue,
        'background_mood'      => BannerMood::Night,
        'background_intensity' => BannerIntensity::Soft,
    ]);

    $path = app(BannerBackgroundService::class)->generate($banner);

    expect($path)->toStartWith("banners/{$banner->id}/background-")
        ->and(Storage::exists($path))->toBeTrue()
        ->and($banner->fresh()->background_prompt)
        ->toContain('churrasco')
        ->toContain('noturno')
        ->toContain('sem nenhum texto');

    Image::assertGenerated(fn ($prompt) => str_contains($prompt->prompt, 'brasas')
        && $prompt->size === '2:3');
});
