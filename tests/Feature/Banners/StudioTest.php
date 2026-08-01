<?php

use App\Enums\BannerFont;
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
        ->assertSee(__('banners.themes.barbecue.label'))
        ->assertSeeHtml('banner-preview-frame')
        ->assertSeeHtml('aspect-ratio:')
        ->assertSeeHtml('ResizeObserver')
        ->assertSee(__('banners.hints.mood'))
        ->assertSee(__('banners.hints.intensity'));
});

it('marks the studio dirty when the design changes and clears after save', function () {
    Livewire::test(Studio::class, ['banner' => $this->banner])
        ->assertSet('isDirty', false)
        ->assertDontSee(__('banners.actions.save_changes'))
        ->set('design.title', 'OFERTAS')
        ->assertSet('isDirty', true)
        ->assertSee(__('banners.actions.save_changes'))
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('isDirty', false)
        ->assertDontSee(__('banners.actions.save_changes'));
});

it('marks the studio dirty when applying a preset', function () {
    Livewire::test(Studio::class, ['banner' => $this->banner])
        ->call('applyPreset', 'barbecue')
        ->assertSet('isDirty', true)
        ->assertSee(__('banners.actions.save_changes'));
});

it('renders tip markup for mood and intensity', function () {
    Livewire::test(Studio::class, ['banner' => $this->banner])
        ->assertSee(__('banners.hints.mood'))
        ->assertSee(__('banners.hints.intensity'))
        ->assertSeeHtml('title="' . e(__('banners.mood_tips.dark')) . '"');
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

it('can use different fonts for the title and the text', function () {
    Livewire::test(Studio::class, ['banner' => $this->banner])
        ->assertSee(__('banners.fields.title_font'))
        ->assertSee(__('banners.fields.text_font'))
        ->set('design.title_font', BannerFont::Anton->value)
        ->set('design.text_font', BannerFont::Oswald->value)
        ->assertSee("font-family: 'Anton', sans-serif")
        ->assertSee("font-family: 'Oswald', sans-serif")
        ->call('save')
        ->assertHasNoErrors();

    $design = $this->banner->fresh()->design;

    expect($design['title_font'])->toBe('anton')
        ->and($design['text_font'])->toBe('oswald');
});

it('rejects an unknown font', function () {
    Livewire::test(Studio::class, ['banner' => $this->banner])
        ->set('design.title_font', 'comic_sans')
        ->call('save')
        ->assertHasErrors(['design.title_font']);
});

it('can add a custom text with color, font and free position', function () {
    Livewire::test(Studio::class, ['banner' => $this->banner])
        ->assertSee(__('banners.sections.texts'))
        ->call('addText')
        ->assertCount('design.texts', 1)
        ->set('design.texts.0.content', 'Válido até domingo')
        ->set('design.texts.0.color', '#ff0000')
        ->set('design.texts.0.font', BannerFont::BebasNeue->value)
        ->set('design.texts.0.x', 12.5)
        ->set('design.texts.0.y', 80)
        ->assertSee('Válido até domingo')
        ->assertSee("font-family: 'Bebas Neue', sans-serif")
        ->assertSee('left: 12.5%')
        ->call('save')
        ->assertHasNoErrors();

    $text = $this->banner->fresh()->design['texts'][0];

    expect($text['content'])->toBe('Válido até domingo')
        ->and($text['color'])->toBe('#ff0000')
        ->and($text['font'])->toBe('bebas_neue')
        ->and($text['x'])->toBe(12.5)
        ->and($text['y'])->toBe(80);
});

it('can remove a custom text', function () {
    Livewire::test(Studio::class, ['banner' => $this->banner])
        ->call('addText')
        ->call('addText')
        ->assertCount('design.texts', 2)
        ->call('removeText', 0)
        ->assertCount('design.texts', 1);
});

it('keeps custom texts inside the canvas bounds', function () {
    Livewire::test(Studio::class, ['banner' => $this->banner])
        ->call('addText')
        ->set('design.texts.0.x', 150)
        ->call('save')
        ->assertHasErrors(['design.texts.0.x']);
});

it('can save logo preferences', function () {
    Livewire::test(Studio::class, ['banner' => $this->banner])
        ->set('design.show_logo', false)
        ->set('design.logo_position', 'bottom')
        ->set('design.logo_align', 'right')
        ->set('design.logo_size', 'large')
        ->call('save')
        ->assertHasNoErrors();

    $design = $this->banner->fresh()->design;

    expect($design['show_logo'])->toBeFalse()
        ->and($design['logo_position'])->toBe('bottom')
        ->and($design['logo_align'])->toBe('right')
        ->and($design['logo_size'])->toBe('large');
});

it('updates logo alignment live on the design', function () {
    Livewire::test(Studio::class, ['banner' => $this->banner])
        ->set('design.logo_align', 'left')
        ->assertSet('design.logo_align', 'left')
        ->set('design.logo_align', 'right')
        ->assertSet('design.logo_align', 'right')
        ->assertSeeHtml('justify-content: flex-end');
});

it('lists seasonal themes in the studio', function () {
    Livewire::test(Studio::class, ['banner' => $this->banner])
        ->assertSee(__('banners.themes.fathers_day.label'))
        ->assertSee(__('banners.themes.christmas.label'))
        ->assertSee(__('banners.themes.easter.label'));
});

it('rejects an invalid logo alignment', function () {
    Livewire::test(Studio::class, ['banner' => $this->banner])
        ->set('design.logo_align', 'diagonal')
        ->call('save')
        ->assertHasErrors(['design.logo_align']);
});

it('applies a brand color palette', function () {
    Livewire::test(Studio::class, ['banner' => $this->banner])
        ->call('applyPalette', 'brand_light')
        ->assertSet('design.background_color', '#f8fafc')
        ->assertSet('design.accent_color', '#2da7ef')
        ->assertSet('design.text_color', '#0f172a');
});

it('applies a campaign preset', function () {
    Livewire::test(Studio::class, ['banner' => $this->banner])
        ->call('applyPreset', 'barbecue')
        ->assertSet('design.title', 'ESPETINHOS')
        ->assertCount('design.items', 5);
});

it('reorders items by drag position', function () {
    Livewire::test(Studio::class, ['banner' => $this->banner])
        ->call('applyPreset', 'barbecue')
        ->call('sortItems', 4, 0)
        ->assertSet('design.items.0.name', 'Medalhão')
        ->assertSet('design.items.1.name', 'Bovino');
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
        ->set('design.title', 'OFERTAS')
        ->set('exportScale', 3)
        ->call('export')
        ->assertHasNoErrors()
        ->assertSet('isDirty', false);

    Queue::assertPushed(ExportBannerJob::class, fn (ExportBannerJob $job) => $job->scale === 3);

    expect($this->banner->fresh())
        ->export_status->toBe(BannerJobStatus::Generating)
        ->design->title->toBe('OFERTAS');
});
