<?php

use App\Models\Cart;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('requires a token', function () {
    $this->getJson(route('api.cart.show'))->assertUnauthorized();
});

it('returns an empty cart for a new user', function () {
    Sanctum::actingAs(User::factory()->create());

    $this->getJson(route('api.cart.show'))
        ->assertOk()
        ->assertJsonPath('data.total_quantity', 0)
        ->assertJsonCount(0, 'data.items');
});

it('adds, updates and removes items in the user cart', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create(['name' => 'Gold Wristwatch']);
    Sanctum::actingAs($user);

    $this->postJson(route('api.cart.store', $item), ['quantity' => 2])
        ->assertOk()
        ->assertJsonPath('data.items.0.name', 'Gold Wristwatch')
        ->assertJsonPath('data.total_quantity', 2);

    $this->putJson(route('api.cart.update', $item), ['quantity' => 5])
        ->assertOk()
        ->assertJsonPath('data.items.0.quantity', 5);

    $this->deleteJson(route('api.cart.destroy', $item))
        ->assertOk()
        ->assertJsonCount(0, 'data.items');

    expect(Cart::where('user_id', $user->id)->count())->toBe(1);
});

it('rejects an invalid quantity', function () {
    Sanctum::actingAs(User::factory()->create());

    $this->postJson(route('api.cart.store', Item::factory()->create()), ['quantity' => 0])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('quantity');
});

it('returns 404 when updating an item that is not in the cart', function () {
    Sanctum::actingAs(User::factory()->create());

    $this->putJson(route('api.cart.update', Item::factory()->create()), ['quantity' => 2])
        ->assertNotFound();
});

it('keeps each user cart separate', function () {
    $item = Item::factory()->create();

    Sanctum::actingAs(User::factory()->create());
    $this->postJson(route('api.cart.store', $item));

    Sanctum::actingAs(User::factory()->create());
    $this->getJson(route('api.cart.show'))->assertJsonCount(0, 'data.items');
});
