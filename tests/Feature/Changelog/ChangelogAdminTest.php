<?php

use App\Livewire\Changelog\Form;
use App\Livewire\Changelog\Index;
use App\Models\Changelog;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');
    actingAs($this->admin);
});

it('can render changelog index', function () {
    Livewire::test(Index::class)
        ->assertStatus(200)
        ->assertSee(__('changelog.admin.title'));
});

it('lists changelogs', function () {
    Changelog::factory()->create(['title' => 'My Release', 'version' => '1.0.0']);

    Livewire::test(Index::class)
        ->assertSee('My Release')
        ->assertSee('1.0.0');
});

it('shows empty state when no changelogs', function () {
    Livewire::test(Index::class)
        ->assertSee(__('changelog.admin.empty'));
});

it('can create a changelog', function () {
    Livewire::test(Form::class)
        ->set('version', '2.0.0')
        ->set('title', 'Big update')
        ->set('releasedAt', now()->format('Y-m-d'))
        ->set('items.0.title', 'New dashboard')
        ->set('items.0.description', 'Completely redesigned dashboard')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('changelog-saved');

    expect(Changelog::where('version', '2.0.0')->exists())->toBeTrue();
});

it('validates required fields on create', function () {
    Livewire::test(Form::class)
        ->call('save')
        ->assertHasErrors([
            'version',
            'title',
            'releasedAt',
            'items.0.title',
            'items.0.description',
        ]);
});

it('can edit a changelog', function () {
    $changelog = Changelog::factory()->create(['title' => 'Old Title', 'version' => '1.0.0']);
    $changelog->items()->create(['title' => 'Old item', 'description' => 'Old desc', 'sort_order' => 0]);

    Livewire::test(Form::class, ['changelogId' => $changelog->id])
        ->assertSet('title', 'Old Title')
        ->set('title', 'New Title')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('changelog-saved');

    expect($changelog->fresh()->title)->toBe('New Title');
});

it('can delete a changelog', function () {
    $changelog = Changelog::factory()->create();

    Livewire::test(Index::class)
        ->call('delete', $changelog->id)
        ->assertHasNoErrors();

    expect(Changelog::find($changelog->id))->toBeNull();
});

it('denies access to non-admin users on index', function () {
    $user = User::factory()->create();
    actingAs($user);

    Livewire::test(Index::class)
        ->assertForbidden();
});

it('denies access to non-admin users on form', function () {
    $user = User::factory()->create();
    actingAs($user);

    Livewire::test(Form::class)
        ->assertForbidden();
});
