<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

it('returns 401 when logout is called without a valid token', function () {
    postJson(route('api.v1.auth.logout'))
        ->assertUnauthorized();
});

it('revokes the current access token and returns 204', function () {
    $user = User::factory()->create();
    $token = $user->createToken('mobile')->plainTextToken;

    assertDatabaseCount('personal_access_tokens', 1);

    postJson(route('api.v1.auth.logout'), [], [
        'Authorization' => 'Bearer ' . $token,
    ])->assertNoContent();

    assertDatabaseCount('personal_access_tokens', 0);
});

it('only revokes the current token and keeps other tokens of the same user', function () {
    $user = User::factory()->create();

    $currentToken = $user->createToken('android')->plainTextToken;
    $otherToken = $user->createToken('ios')->plainTextToken;

    assertDatabaseCount('personal_access_tokens', 2);

    postJson(route('api.v1.auth.logout'), [], [
        'Authorization' => 'Bearer ' . $currentToken,
    ])->assertNoContent();

    assertDatabaseCount('personal_access_tokens', 1);
    assertDatabaseMissing('personal_access_tokens', [
        'name' => 'android',
    ]);
    $this->assertDatabaseHas('personal_access_tokens', [
        'name' => 'ios',
    ]);
});
