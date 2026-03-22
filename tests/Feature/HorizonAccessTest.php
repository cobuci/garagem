<?php

use App\Models\User;

it('allows access to horizon for cobuci80@gmail.com', function () {
    $user = User::factory()->create(['email' => 'cobuci80@gmail.com']);

    $this->actingAs($user)
        ->get('/horizon')
        ->assertStatus(200);
});

it('denies access to horizon for other users', function () {
    $user = User::factory()->create(['email' => 'other@example.com']);

    $this->actingAs($user)
        ->get('/horizon')
        ->assertStatus(403);
});

it('denies access to horizon for guests', function () {
    $this->get('/horizon')
        ->assertStatus(403);
});
