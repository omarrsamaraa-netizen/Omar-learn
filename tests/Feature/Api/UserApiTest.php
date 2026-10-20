<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('lists users for an admin without exposing passwords', function () {
    User::factory()->count(2)->create();
    Sanctum::actingAs(User::factory()->admin()->create());

    $this->getJson(route('api.users.index'))
        ->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonMissingPath('data.0.password');
});

it('filters users by search term', function () {
    User::factory()->create(['name' => 'Layla Haddad']);
    User::factory()->create(['name' => 'Omar Samara']);
    Sanctum::actingAs(User::factory()->admin()->create(['name' => 'Admin']));

    $this->getJson(route('api.users.index', ['search' => 'Layla']))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Layla Haddad');
});

it('forbids customers from listing users', function () {
    Sanctum::actingAs(User::factory()->create());

    $this->getJson(route('api.users.index'))->assertForbidden();
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
