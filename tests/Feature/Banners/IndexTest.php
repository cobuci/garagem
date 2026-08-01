<?php

use App\Enums\BannerFormat;
use App\Enums\Permission;
use App\Livewire\Banners\Index;
use App\Models\Banner;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

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
});

it('can render the banners page', function () {
    Livewire::test(Index::class)
        ->assertSuccessful()
        ->assertSee(__('banners.title'));
});

it('can list banners', function () {
    Banner::create([
        'user_id' => $this->user->id,
        'name'    => 'Promoção Agosto',
        'format'  => BannerFormat::Stories,
        'design'  => Banner::defaultDesign(),
    ]);

    Livewire::test(Index::class)
        ->assertSee('Promoção Agosto');
});

it('can create a banner and redirect to studio', function () {
    Livewire::test(Index::class)
        ->call('create')
        ->set('name', 'Banner Teste')
        ->set('format', BannerFormat::Square->value)
        ->call('store')
        ->assertHasNoErrors()
        ->assertRedirect();

    assertDatabaseHas('banners', [
        'name'    => 'Banner Teste',
        'format'  => BannerFormat::Square->value,
        'user_id' => $this->user->id,
    ]);
});

it('validates required fields when creating', function () {
    Livewire::test(Index::class)
        ->call('create')
        ->set('name', '')
        ->call('store')
        ->assertHasErrors(['name' => 'required']);
});

it('can delete a banner', function () {
    $banner = Banner::create([
        'user_id' => $this->user->id,
        'name'    => 'Para Excluir',
        'format'  => BannerFormat::Post,
        'design'  => Banner::defaultDesign(),
    ]);

    Livewire::test(Index::class)
        ->call('confirmDelete', $banner->id)
        ->assertSet('showDeleteModal', true)
        ->assertSet('deletingBannerName', 'Para Excluir')
        ->assertSee(__('banners.messages.confirm_delete', ['banner' => 'Para Excluir']))
        ->call('delete')
        ->assertHasNoErrors()
        ->assertSet('showDeleteModal', false);

    assertDatabaseMissing('banners', ['id' => $banner->id]);
});
