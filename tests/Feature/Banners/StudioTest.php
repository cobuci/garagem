<?php

use App\Enums\BannerFormat;
use App\Enums\BannerJobStatus;
use App\Enums\BannerMood;
use App\Enums\BannerTheme;
use App\Enums\Permission;
use App\Jobs\ExportBannerJob;
use App\Jobs\GenerateBannerBackgroundJob;
use App\Livewire\Banners\Studio;
use App\Models\Banner;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $this->user = User::factory()->create();
    $this->user->givePermissionTo([
        Permission::ViewBanner->value,
        Permission::CreateBanner->value,
        Permission::EditBanner->value,
        Permission::DeleteBanner->value,
    ]);
    actingAs($this->user);
    config(['wireui.style.icon' => 'outline']);

    $this->banner = Banner::create([
        'user_id' => $this->user->id,
        'name'    => 'Banner Studio',
        'format'  => BannerFormat::Stories,
        'design'  => Banner::defaultDesign(),
    ]);
});

it('can render the studio page', function () {
    Livewire::test(Studio::class, ['banner' => $this->banner])
        ->assertSuccessful()
        ->assertSee('Banner Studio')
        ->assertSee(__('banners.sections.preview'))
        ->assertSee(__('banners.sections.presets'))
        ->assertSee(__('banners.themes.barbecue.label'));
});

it('can save design changes', function () {
    Livewire::test(Studio::class, ['banner' => $this->banner])
        ->set('design.title', 'OFERTAS')
        ->set('design.subtitle', 'Só hoje')
        ->call('save')
        ->assertHasNoErrors();

    expect($this->banner->fresh()->design['title'])->toBe('OFERTAS')
        ->and($this->banner->fresh()->design['subtitle'])->toBe('Só hoje');
});

it('can save logo preferences', function () {
    Livewire::test(Studio::class, ['banner' => $this->banner])
        ->set('design.show_logo', false)
        ->set('design.logo_position', 'bottom')
        ->set('design.logo_size', 'large')
        ->call('save')
        ->assertHasNoErrors();

    $design = $this->banner->fresh()->design;

    expect($design['show_logo'])->toBeFalse()
        ->and($design['logo_position'])->toBe('bottom')
        ->and($design['logo_size'])->toBe('large');
});

it('applies a campaign preset', function () {
    Livewire::test(Studio::class, ['banner' => $this->banner])
        ->call('applyPreset', 'barbecue')
        ->assertSet('design.title', 'ESPETINHOS')
        ->assertCount('design.items', 5);
});

it('can add and remove items', function () {
    Livewire::test(Studio::class, ['banner' => $this->banner])
        ->call('addItem')
        ->assertCount('design.items', 2)
        ->call('removeItem', 1)
        ->assertCount('design.items', 1);
});

it('dispatches background generation job with theme', function () {
    Queue::fake();

    Livewire::test(Studio::class, ['banner' => $this->banner])
        ->call('selectTheme', BannerTheme::Barbecue->value)
        ->set('backgroundMood', BannerMood::Night->value)
        ->call('generateBackground')
        ->assertHasNoErrors();

    Queue::assertPushed(GenerateBannerBackgroundJob::class);

    expect($this->banner->fresh())
        ->background_status->toBe(BannerJobStatus::Generating)
        ->background_theme->toBe(BannerTheme::Barbecue)
        ->background_mood->toBe(BannerMood::Night);
});

it('requires a theme to generate background', function () {
    Queue::fake();

    Livewire::test(Studio::class, ['banner' => $this->banner])
        ->call('generateBackground')
        ->assertHasErrors(['backgroundTheme' => 'required']);

    Queue::assertNothingPushed();
});

it('dispatches export job', function () {
    Queue::fake();

    Livewire::test(Studio::class, ['banner' => $this->banner])
        ->set('exportScale', 3)
        ->call('export')
        ->assertHasNoErrors();

    Queue::assertPushed(ExportBannerJob::class, fn (ExportBannerJob $job) => $job->scale === 3);

    expect($this->banner->fresh()->export_status)->toBe(BannerJobStatus::Generating);
});
