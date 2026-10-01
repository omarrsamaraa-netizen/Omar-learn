<?php

use App\Models\Item;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the login form', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('name="email"', false)
        ->assertSee('name="password"', false);
});

it('logs a user in with the right credentials', function () {
    $user = User::factory()->create(['email' => 'omar@example.com']);

    $this->post(route('login.store'), [
        'email' => 'omar@example.com',
        'password' => 'password',
    ])->assertRedirect(route('main.index'));

    $this->assertAuthenticatedAs($user);
});

it('rejects a wrong password', function () {
    User::factory()->create(['email' => 'omar@example.com']);

    $this->post(route('login.store'), [
        'email' => 'omar@example.com',
        'password' => 'not-the-password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('rejects an email that is not registered', function () {
    $this->post(route('login.store'), [
        'email' => 'nobody@example.com',
        'password' => 'password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('logs a user out', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('logout'))
        ->assertRedirect(route('main.index'));

    $this->assertGuest();
});

it('lets anyone browse the collection', function () {
    $item = Item::factory()->create();

    $this->get(route('main.index'))->assertOk();
    $this->get(route('main.show', $item))->assertOk();
});

it('keeps guests out of the management pages', function () {
    $item = Item::factory()->create();

    $this->get(route('main.create'))->assertForbidden();
    $this->get(route('main.trash'))->assertForbidden();
    $this->get(route('main.edit', $item))->assertForbidden();
    $this->post(route('main.store'), [])->assertForbidden();
    $this->delete(route('main.destroy', $item))->assertForbidden();

    expect(Item::find($item->id))->not->toBeNull();
});

it('keeps customers out of the management pages', function () {
    $item = Item::factory()->create();

    $this->actingAs(User::factory()->create());

    $this->get(route('main.create'))->assertForbidden();
    $this->delete(route('main.destroy', $item))->assertForbidden();

    expect(Item::find($item->id))->not->toBeNull();
});

it('lets an admin reach the management pages', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('main.create'))
        ->assertOk();
});

it('recognises an admin role whatever its capitalisation', function () {
    $role = Role::create(['name' => 'Admin']);

    $this->actingAs(User::factory()->create(['role_id' => $role->id]))
        ->get(route('main.create'))
        ->assertOk();
});

it('links a registered user to the customer role', function () {
    $this->post(route('register.store'), [
        'name' => 'Omar Samara',
        'email' => 'omar@example.com',
        'password' => 'correct-horse-battery',
        'password_confirmation' => 'correct-horse-battery',
    ]);

    $user = User::firstWhere('email', 'omar@example.com');

    expect($user->role_id)->not->toBeNull()
        ->and($user->role->name)->toBe(Role::CUSTOMER);
});

it('hides the admin links from guests', function () {
    $item = Item::factory()->create();

    $this->get(route('main.index'))
        ->assertOk()
        ->assertDontSee('Add piece')
        ->assertDontSee('Trash');

    $this->get(route('main.show', $item))
        ->assertOk()
        ->assertDontSee('Edit piece')
        ->assertDontSee('Remove piece');
});

it('hides the admin links from customers', function () {
    $item = Item::factory()->create();
    $this->actingAs(User::factory()->create());

    $this->get(route('main.index'))
        ->assertOk()
        ->assertDontSee('Add piece')
        ->assertDontSee('Trash');

    $this->get(route('main.show', $item))
        ->assertOk()
        ->assertSee('Add to cart')
        ->assertDontSee('Edit piece')
        ->assertDontSee('Remove piece');
});

it('shows the admin links to an admin', function () {
    $item = Item::factory()->create();
    $this->actingAs(User::factory()->admin()->create());

    $this->get(route('main.index'))
        ->assertOk()
        ->assertSee('Add piece')
        ->assertSee('Trash');

    $this->get(route('main.show', $item))
        ->assertOk()
        ->assertSee('Edit piece')
        ->assertSee('Remove piece');
});

it('registers customers, not admins', function () {
    $this->post(route('register.store'), [
        'name' => 'Omar Samara',
        'email' => 'omar@example.com',
        'password' => 'correct-horse-battery',
        'password_confirmation' => 'correct-horse-battery',
    ]);

    expect(User::firstWhere('email', 'omar@example.com')->isAdmin())->toBeFalse();

    $this->get(route('main.create'))->assertForbidden();
});
