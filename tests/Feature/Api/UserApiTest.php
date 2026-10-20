<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('returns the authenticated user info without the password', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->getJson(route('api.user.show'))
        ->assertOk()
        ->assertJsonPath('data.id', $user->id)
        ->assertJsonPath('data.email', $user->email)
        ->assertJsonPath('data.role', 'customer')
        ->assertJsonMissingPath('data.password')
        ->assertJsonMissingPath('data.remember_token');
});

it('requires a token for user info', function () {
    $this->getJson(route('api.user.show'))->assertUnauthorized();
});

it('issues a token for valid credentials', function () {
    $user = User::factory()->create();

    $this->postJson(route('api.login'), ['email' => $user->email, 'password' => 'password'])
        ->assertOk()
        ->assertJsonStructure(['token', 'user' => ['id', 'email', 'role']]);
});

it('rejects invalid credentials', function () {
    $user = User::factory()->create();

    $this->postJson(route('api.login'), ['email' => $user->email, 'password' => 'wrong'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('email');
});
