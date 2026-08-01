<?php

use App\Enums\Permission;
use App\Livewire\Admin\BrandLogo;
use App\Models\Banner;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    Storage::fake('public');

    $this->admin = User::factory()->create();
    $this->admin->givePermissionTo([
        Permission::ViewAdmin->value,
        Permission::EditSetting->value,
    ]);
    actingAs($this->admin);
    config(['wireui.style.icon' => 'outline']);
});

it('can upload the brand logo', function () {
    Livewire::test(BrandLogo::class)
        ->set('logo', UploadedFile::fake()->image('logo.png', 200, 200))
        ->call('save')
        ->assertHasNoErrors();

    expect(Banner::hasLogo())->toBeTrue()
        ->and(Storage::disk('public')->exists(Banner::LOGO_STORAGE_PATH))->toBeTrue();
});

it('can remove the brand logo', function () {
    Storage::disk('public')->put(Banner::LOGO_STORAGE_PATH, 'png');

    Livewire::test(BrandLogo::class)
        ->call('remove')
        ->assertHasNoErrors();

    expect(Banner::hasLogo())->toBeFalse();
});

it('rejects non-png uploads', function () {
    Livewire::test(BrandLogo::class)
        ->set('logo', UploadedFile::fake()->image('logo.jpg', 200, 200))
        ->call('save')
        ->assertHasErrors(['logo']);
});

it('forbids logo changes without edit setting permission', function () {
    $viewer = User::factory()->create();
    $viewer->givePermissionTo(Permission::ViewAdmin->value);
    actingAs($viewer);

    Livewire::test(BrandLogo::class)
        ->set('logo', UploadedFile::fake()->image('logo.png', 200, 200))
        ->call('save')
        ->assertForbidden();
});
