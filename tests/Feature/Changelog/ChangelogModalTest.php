<?php

use App\Livewire\Changelog\Modal;
use App\Models\Changelog;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $this->user = User::factory()->create();
    actingAs($this->user);
});

it('does not open modal when there are no changelogs', function () {
    Livewire::test(Modal::class)
        ->assertSet('isOpen', false)
        ->assertSet('totalSteps', 0);
});

it('opens modal when user has unseen changelogs', function () {
    $changelog = Changelog::factory()->create();
    $changelog->items()->createMany([
        ['title' => 'Feature A', 'description' => 'Desc A', 'sort_order' => 0],
        ['title' => 'Feature B', 'description' => 'Desc B', 'sort_order' => 1],
    ]);

    Livewire::test(Modal::class)
        ->assertSet('isOpen', true)
        ->assertSet('totalSteps', 2);
});

it('does not open modal when all changelogs are already seen', function () {
    $changelog = Changelog::factory()->create();
    $changelog->items()->create(['title' => 'Feature A', 'description' => 'Desc A', 'sort_order' => 0]);
    $this->user->seenChangelogs()->attach($changelog->id, ['seen_at' => now()]);

    Livewire::test(Modal::class)
        ->assertSet('isOpen', false);
});

it('navigates to next item', function () {
    $changelog = Changelog::factory()->create();
    $changelog->items()->createMany([
        ['title' => 'Item 1', 'description' => 'Desc 1', 'sort_order' => 0],
        ['title' => 'Item 2', 'description' => 'Desc 2', 'sort_order' => 1],
    ]);

    Livewire::test(Modal::class)
        ->assertSet('currentStep', 0)
        ->call('next')
        ->assertSet('currentStep', 1);
});

it('navigates to previous item', function () {
    $changelog = Changelog::factory()->create();
    $changelog->items()->createMany([
        ['title' => 'Item 1', 'description' => 'Desc 1', 'sort_order' => 0],
        ['title' => 'Item 2', 'description' => 'Desc 2', 'sort_order' => 1],
    ]);

    Livewire::test(Modal::class)
        ->call('next')
        ->assertSet('currentStep', 1)
        ->call('previous')
        ->assertSet('currentStep', 0);
});

it('does not go below step 0 on previous', function () {
    $changelog = Changelog::factory()->create();
    $changelog->items()->create(['title' => 'Item 1', 'description' => 'Desc 1', 'sort_order' => 0]);

    Livewire::test(Modal::class)
        ->assertSet('currentStep', 0)
        ->call('previous')
        ->assertSet('currentStep', 0);
});

it('marks changelog as seen and closes modal on dismiss', function () {
    $changelog = Changelog::factory()->create();
    $changelog->items()->create(['title' => 'Feature A', 'description' => 'Desc A', 'sort_order' => 0]);

    Livewire::test(Modal::class)
        ->assertSet('isOpen', true)
        ->call('dismiss')
        ->assertSet('isOpen', false);

    expect(
        $this->user->seenChangelogs()->where('changelogs.id', $changelog->id)->exists(),
    )->toBeTrue();
});

it('marks as seen when finishing the last step via next', function () {
    $changelog = Changelog::factory()->create();
    $changelog->items()->create(['title' => 'Only item', 'description' => 'Desc', 'sort_order' => 0]);

    Livewire::test(Modal::class)
        ->assertSet('isOpen', true)
        ->assertSet('totalSteps', 1)
        ->call('next')
        ->assertSet('isOpen', false);

    expect(
        $this->user->seenChangelogs()->where('changelogs.id', $changelog->id)->exists(),
    )->toBeTrue();
});

it('only shows unseen changelogs when user has seen some', function () {
    $seenChangelog = Changelog::factory()->create();
    $seenChangelog->items()->create(['title' => 'Seen', 'description' => 'Desc', 'sort_order' => 0]);
    $this->user->seenChangelogs()->attach($seenChangelog->id, ['seen_at' => now()]);

    $unseenChangelog = Changelog::factory()->create();
    $unseenChangelog->items()->createMany([
        ['title' => 'Unseen A', 'description' => 'Desc A', 'sort_order' => 0],
        ['title' => 'Unseen B', 'description' => 'Desc B', 'sort_order' => 1],
    ]);

    Livewire::test(Modal::class)
        ->assertSet('isOpen', true)
        ->assertSet('totalSteps', 2);
});
