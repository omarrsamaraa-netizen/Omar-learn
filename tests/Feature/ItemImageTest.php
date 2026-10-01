<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Fake images are built with an explicit mime type rather than ->image(),
 * which needs the GD extension that this PHP build does not load.
 */
uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->admin()->create());
});

/**
 * @return array<string, mixed>
 */
function itemPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Gold Wristwatch',
        'rate' => 80,
        'caption' => 'A fine piece.',
        'category_id' => Category::factory()->create()->id,
    ], $overrides);
}

it('stores an uploaded picture with a new item', function () {
    $this->post(route('main.store'), itemPayload([
        'image' => UploadedFile::fake()->create('watch.jpg', 120, 'image/jpeg'),
    ]));

    $item = Item::firstWhere('name', 'Gold Wristwatch');

    expect($item->image)->not->toBeNull();
    Storage::disk('public')->assertExists($item->image);
});

it('accepts an item without a picture', function () {
    $this->post(route('main.store'), itemPayload());

    expect(Item::firstWhere('name', 'Gold Wristwatch')->image)->toBeNull();
});

it('rejects a file that is not an image', function () {
    $this->post(route('main.store'), itemPayload([
        'image' => UploadedFile::fake()->create('invoice.pdf', 100, 'application/pdf'),
    ]))->assertSessionHasErrors('image');

    expect(Item::count())->toBe(0);
});

it('rejects an image over two megabytes', function () {
    $this->post(route('main.store'), itemPayload([
        'image' => UploadedFile::fake()->create('huge.jpg', 3000, 'image/jpeg'),
    ]))->assertSessionHasErrors('image');

    expect(Item::count())->toBe(0);
});

it('replaces the picture on update', function () {
    $item = Item::factory()->create(['image' => 'items/old.jpg']);

    $this->put(route('main.update', $item), itemPayload([
        'name' => $item->name,
        'category_id' => $item->category_id,
        'image' => UploadedFile::fake()->create('new.jpg', 120, 'image/jpeg'),
    ]));

    expect($item->fresh()->image)->not->toBe('items/old.jpg');
    Storage::disk('public')->assertExists($item->fresh()->image);
});

it('keeps the current picture when update sends no file', function () {
    $item = Item::factory()->create(['image' => 'items/keep.jpg']);

    $this->put(route('main.update', $item), itemPayload([
        'name' => 'Renamed Piece',
        'category_id' => $item->category_id,
    ]));

    expect($item->fresh())
        ->name->toBe('Renamed Piece')
        ->image->toBe('items/keep.jpg');
});

it('shows the picture on the item page', function () {
    $item = Item::factory()->create(['image' => 'items/shown.jpg']);

    $this->get(route('main.show', $item))
        ->assertOk()
        ->assertSee('storage/items/shown.jpg', false);
});
