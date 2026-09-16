<?php

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lists items on the index page', function () {
    $item = Item::factory()->create(['name' => 'Gold Wristwatch']);

    $this->get(route('main.index'))
        ->assertOk()
        ->assertSee('Gold Wristwatch')
        ->assertSee('/main/'.$item->id);
});

it('shows the create form', function () {
    $this->get(route('main.create'))
        ->assertOk()
        ->assertSee('name="name"', false)
        ->assertSee('name="rate"', false)
        ->assertSee('name="caption"', false);
});

it('stores a new item and redirects to it', function () {
    $response = $this->post(route('main.store'), [
        'name' => 'Linen Suit',
        'rate' => 82,
        'caption' => 'Cut from Irish linen.',
    ]);

    $item = Item::firstWhere('name', 'Linen Suit');

    expect($item)->not->toBeNull()
        ->and($item->rate)->toBe(82)
        ->and($item->caption)->toBe('Cut from Irish linen.');

    $response->assertRedirect(route('main.show', $item))
        ->assertSessionHas('status');
});

it('rejects an invalid item on store', function () {
    $this->post(route('main.store'), ['name' => '', 'rate' => 150, 'caption' => ''])
        ->assertSessionHasErrors(['name', 'rate', 'caption']);

    expect(Item::count())->toBe(0);
});

it('shows a single item', function () {
    $item = Item::factory()->create(['name' => 'Silk Tie', 'rate' => 64]);

    $this->get(route('main.show', $item))
        ->assertOk()
        ->assertSee('Silk Tie')
        ->assertSee($item->caption)
        ->assertSee('64');
});

it('returns 404 for an item that does not exist', function () {
    $this->get('/main/99999')->assertNotFound();
});

it('shows the edit form prefilled', function () {
    $item = Item::factory()->create(['name' => 'Leather Satchel']);

    $this->get(route('main.edit', $item))
        ->assertOk()
        ->assertSee('Leather Satchel');
});

it('updates an item and redirects to it', function () {
    $item = Item::factory()->create(['name' => 'Old Name', 'rate' => 10]);

    $response = $this->put(route('main.update', $item), [
        'name' => 'New Name',
        'rate' => 90,
        'caption' => 'Revised description.',
    ]);

    $response->assertRedirect(route('main.show', $item))
        ->assertSessionHas('status');

    expect($item->fresh())
        ->name->toBe('New Name')
        ->rate->toBe(90);
});

it('rejects an invalid item on update', function () {
    $item = Item::factory()->create(['name' => 'Keep Me']);

    $this->put(route('main.update', $item), ['name' => '', 'rate' => -5, 'caption' => ''])
        ->assertSessionHasErrors(['name', 'rate', 'caption']);

    expect($item->fresh()->name)->toBe('Keep Me');
});

it('deletes an item and redirects to the index', function () {
    $item = Item::factory()->create();

    $this->delete(route('main.destroy', $item))
        ->assertRedirect(route('main.index'))
        ->assertSessionHas('status');

    expect(Item::find($item->id))->toBeNull();
});
