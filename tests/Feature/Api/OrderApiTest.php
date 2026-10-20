<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Item;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('places an order from the cart and empties it', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create(['name' => 'Silk Scarf']);
    CartItem::factory()->for(Cart::factory()->for($user))->for($item)->create(['quantity' => 3]);
    Sanctum::actingAs($user);

    $this->postJson(route('api.orders.store'), ['note' => 'Gift wrap please'])
        ->assertCreated()
        ->assertJsonPath('data.user_id', $user->id)
        ->assertJsonPath('data.customer_email', $user->email)
        ->assertJsonPath('data.items.0.name', 'Silk Scarf')
        ->assertJsonPath('data.items.0.quantity', 3);

    expect(CartItem::count())->toBe(0);
});

it('refuses to place an order from an empty cart', function () {
    Sanctum::actingAs(User::factory()->create());

    $this->postJson(route('api.orders.store'))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('cart');

    expect(Order::count())->toBe(0);
});

it('lists only the customer own orders', function () {
    $user = User::factory()->create();
    $own = Order::factory()->for($user)->create();
    Order::factory()->for(User::factory())->create();
    Sanctum::actingAs($user);

    $this->getJson(route('api.orders.index'))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $own->id);
});

it('lists every order for an admin', function () {
    Order::factory()->count(2)->for(User::factory())->create();
    Sanctum::actingAs(User::factory()->admin()->create());

    $this->getJson(route('api.orders.index'))->assertOk()->assertJsonCount(2, 'data');
});

it('hides another customer order', function () {
    $order = Order::factory()->for(User::factory())->create();
    Sanctum::actingAs(User::factory()->create());

    $this->getJson(route('api.orders.show', $order))->assertNotFound();
});
