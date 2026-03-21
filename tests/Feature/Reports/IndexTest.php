<?php

namespace Tests\Feature\Reports;

use App\Livewire\Reports\Index;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

test('it renders the reports page', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('reports.index'))
        ->assertOk()
        ->assertSeeLivewire(Index::class);
});

test('it displays the report title and subtitle', function () {
    $user = User::factory()->create();

    actingAs($user);

    Livewire::test(Index::class)
        ->assertSee(__('reports.title'))
        ->assertSee(__('reports.subtitle'));
});
