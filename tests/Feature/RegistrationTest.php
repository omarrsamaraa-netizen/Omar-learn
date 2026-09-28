<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('shows the registration form', function () {
    $this->get(route('register'))
        ->assertOk()
        ->assertSee('name="name"', false)
        ->assertSee('name="email"', false)
        ->assertSee('name="password"', false)
        ->assertSee('name="password_confirmation"', false);
});

it('registers a new user and logs them in', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Omar Samara',
        'email' => 'omar@example.com',
        'password' => 'correct-horse-battery',
        'password_confirmation' => 'correct-horse-battery',
    ]);

    $user = User::firstWhere('email', 'omar@example.com');

    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('Omar Samara')
        ->and($user->role)->toBe('customer');

    $this->assertAuthenticatedAs($user);

    $response->assertRedirect(route('main.index'))
        ->assertSessionHas('status');
});

it('stores the password hashed', function () {
    $this->post(route('register.store'), [
        'name' => 'Omar Samara',
        'email' => 'omar@example.com',
        'password' => 'correct-horse-battery',
        'password_confirmation' => 'correct-horse-battery',
    ]);

    $user = User::firstWhere('email', 'omar@example.com');

    expect($user->password)->not->toBe('correct-horse-battery')
        ->and(Hash::check('correct-horse-battery', $user->password))->toBeTrue();
});

it('requires every field', function () {
    $this->post(route('register.store'), [])
        ->assertSessionHasErrors(['name', 'email', 'password']);

    expect(User::count())->toBe(0);
});

it('rejects an email that is already registered', function () {
    User::factory()->create(['email' => 'omar@example.com']);

    $this->post(route('register.store'), [
        'name' => 'Omar Samara',
        'email' => 'omar@example.com',
        'password' => 'correct-horse-battery',
        'password_confirmation' => 'correct-horse-battery',
    ])->assertSessionHasErrors('email');

    expect(User::count())->toBe(1);
    $this->assertGuest();
});

it('rejects a password that is not confirmed', function () {
    $this->post(route('register.store'), [
        'name' => 'Omar Samara',
        'email' => 'omar@example.com',
        'password' => 'correct-horse-battery',
        'password_confirmation' => 'something-else',
    ])->assertSessionHasErrors('password');

    expect(User::count())->toBe(0);
});

it('rejects a password shorter than eight characters', function () {
    $this->post(route('register.store'), [
        'name' => 'Omar Samara',
        'email' => 'omar@example.com',
        'password' => 'short',
        'password_confirmation' => 'short',
    ])->assertSessionHasErrors('password');

    expect(User::count())->toBe(0);
});

it('does not let the form choose its own role', function () {
    $this->post(route('register.store'), [
        'name' => 'Omar Samara',
        'email' => 'omar@example.com',
        'password' => 'correct-horse-battery',
        'password_confirmation' => 'correct-horse-battery',
        'role' => 'admin',
    ]);

    expect(User::firstWhere('email', 'omar@example.com')->role)->toBe('customer');
});
