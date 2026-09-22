<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows an empty cart', function () {
    $this->get(route('cart.index'))
        ->assertOk()
        ->assertSee('Your cart is empty.');
});

it('adds a piece to the cart', function () {
    $item = Item::factory()->create(['name' => 'Gold Wristwatch']);

    $this->post(route('cart.store', $item), ['quantity' => 2])
        ->assertRedirect(route('cart.index'))
        ->assertSessionHas('status');

    expect(Cart::count())->toBe(1)
        ->and(CartItem::where('item_id', $item->id)->first()->quantity)->toBe(2);

    $this->get(route('cart.index'))->assertOk()->assertSee('Gold Wristwatch');
});

it('reuses the same cart row across requests', function () {
    $first = Item::factory()->create();
    $second = Item::factory()->create();

    $this->post(route('cart.store', $first));
    $this->post(route('cart.store', $second));

    expect(Cart::count())->toBe(1)
        ->and(Cart::first()->cartItems)->toHaveCount(2);
});

it('adds to the existing quantity when a piece is added twice', function () {
    $item = Item::factory()->create();

    $this->post(route('cart.store', $item), ['quantity' => 2]);
    $this->post(route('cart.store', $item), ['quantity' => 3]);

    expect(CartItem::count())->toBe(1)
        ->and(CartItem::first()->quantity)->toBe(5);
});

it('defaults to a quantity of one', function () {
    $item = Item::factory()->create();

    $this->post(route('cart.store', $item));

    expect(CartItem::first()->quantity)->toBe(1);
});

it('rejects an invalid quantity', function () {
    $item = Item::factory()->create();

    $this->post(route('cart.store', $item), ['quantity' => 0])
        ->assertSessionHasErrors('quantity');

    expect(CartItem::count())->toBe(0);
});

it('updates the quantity of a piece in the cart', function () {
    $item = Item::factory()->create();
    $this->post(route('cart.store', $item));

    $this->put(route('cart.update', $item), ['quantity' => 4])
        ->assertRedirect(route('cart.index'));

    expect(CartItem::first()->quantity)->toBe(4);
});

it('removes a piece from the cart', function () {
    $item = Item::factory()->create();
    $other = Item::factory()->create();
    $this->post(route('cart.store', $item));
    $this->post(route('cart.store', $other));

    $this->delete(route('cart.destroy', $item))
        ->assertRedirect(route('cart.index'));

    expect(CartItem::count())->toBe(1)
        ->and(CartItem::first()->item_id)->toBe($other->id);
});

it('empties the cart', function () {
    $item = Item::factory()->create();
    $this->post(route('cart.store', $item));

    $this->delete(route('cart.clear'))
        ->assertRedirect(route('cart.index'));

    expect(CartItem::count())->toBe(0)
        ->and(Cart::count())->toBe(1);
});

it('deletes cart rows when the piece itself is force deleted', function () {
    $item = Item::factory()->create();
    $this->post(route('cart.store', $item));

    $item->forceDelete();

    expect(CartItem::count())->toBe(0);
});

it('submits the cart as an order and empties it', function () {
    $first = Item::factory()->create(['name' => 'Linen Suit']);
    $second = Item::factory()->create(['name' => 'Silk Tie']);
    $this->post(route('cart.store', $first), ['quantity' => 2]);
    $this->post(route('cart.store', $second));

    $response = $this->post(route('orders.store'), [
        'customer_name' => 'Omar Samara',
        'customer_email' => 'omar@example.com',
        'note' => 'Please gift wrap.',
    ]);

    $order = Order::firstWhere('customer_email', 'omar@example.com');

    expect($order)->not->toBeNull()
        ->and($order->customer_name)->toBe('Omar Samara')
        ->and($order->note)->toBe('Please gift wrap.')
        ->and($order->orderItems)->toHaveCount(2)
        ->and($order->orderItems->firstWhere('item_id', $first->id)->quantity)->toBe(2)
        ->and($order->orderItems->firstWhere('item_id', $first->id)->name)->toBe('Linen Suit')
        ->and(CartItem::count())->toBe(0);

    $response->assertRedirect(route('orders.show', $order))
        ->assertSessionHas('status');
});

it('rejects an order without contact details', function () {
    $item = Item::factory()->create();
    $this->post(route('cart.store', $item));

    $this->post(route('orders.store'), ['customer_name' => '', 'customer_email' => 'nope'])
        ->assertSessionHasErrors(['customer_name', 'customer_email']);

    expect(Order::count())->toBe(0)
        ->and(CartItem::count())->toBe(1);
});

it('refuses to submit an empty cart', function () {
    $this->post(route('orders.store'), [
        'customer_name' => 'Omar Samara',
        'customer_email' => 'omar@example.com',
    ])->assertRedirect(route('cart.index'));

    expect(Order::count())->toBe(0);
});

it('shows a submitted order', function () {
    $item = Item::factory()->create(['name' => 'Leather Satchel']);
    $this->post(route('cart.store', $item), ['quantity' => 3]);

    $this->post(route('orders.store'), [
        'customer_name' => 'Omar Samara',
        'customer_email' => 'omar@example.com',
    ]);

    $this->get(route('orders.show', Order::first()))
        ->assertOk()
        ->assertSee('Leather Satchel')
        ->assertSee('Omar Samara')
        ->assertSee('3');
});
