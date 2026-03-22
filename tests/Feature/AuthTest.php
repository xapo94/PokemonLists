<?php

use App\Models\Pokemon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// -------------------------
// Register
// -------------------------

it('shows the register page', function () {
    $this->get(route('register'))
        ->assertStatus(200);
});

it('registers a user successfully', function () {
    $pokemon = Pokemon::factory()->create();
    $email = fake()->unique()->safeEmail();

    $this->post(route('register'), [
        'name' => fake()->name(),
        'email' => $email,
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'fav_pokemon_id' => $pokemon->id,
    ])
        ->assertRedirect(route('home'));

    $this->assertDatabaseHas('users', ['email' => $email]);
});

it('fails registration with short name', function () {
    $pokemon = Pokemon::factory()->create();

    $this->post(route('register'), [
        'name' => 'Jo',
        'email' => fake()->unique()->safeEmail(),
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'fav_pokemon_id' => $pokemon->id,
    ])
        ->assertSessionHasErrors(['name']);
});

it('fails registration with invalid email', function () {
    $pokemon = Pokemon::factory()->create();

    $this->post(route('register'), [
        'name' => fake()->name(),
        'email' => 'not-an-email',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'fav_pokemon_id' => $pokemon->id,
    ])
        ->assertSessionHasErrors(['email']);
});

it('fails registration with weak password', function () {
    $pokemon = Pokemon::factory()->create();

    $this->post(route('register'), [
        'name' => fake()->name(),
        'email' => fake()->unique()->safeEmail(),
        'password' => 'short',
        'password_confirmation' => 'short',
        'fav_pokemon_id' => $pokemon->id,
    ])
        ->assertSessionHasErrors(['password']);
});

it('fails registration with mismatched passwords', function () {
    $pokemon = Pokemon::factory()->create();

    $this->post(route('register'), [
        'name' => fake()->name(),
        'email' => fake()->unique()->safeEmail(),
        'password' => 'password123',
        'password_confirmation' => 'different123',
        'fav_pokemon_id' => $pokemon->id,
    ])
        ->assertSessionHasErrors(['password']);
});

it('fails registration with invalid pokemon', function () {
    $this->post(route('register'), [
        'name' => fake()->name(),
        'email' => fake()->unique()->safeEmail(),
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'fav_pokemon_id' => 99999,
    ])
        ->assertSessionHasErrors(['fav_pokemon_id']);
});

it('fails registration with duplicate email', function () {
    $pokemon = Pokemon::factory()->create();
    $email = fake()->unique()->safeEmail();
    User::factory()->create(['email' => $email]);

    $this->post(route('register'), [
        'name' => fake()->name(),
        'email' => $email,
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'fav_pokemon_id' => $pokemon->id,
    ])
        ->assertSessionHasErrors(['email']);
});

it('redirects authenticated users away from register page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('register'))
        ->assertRedirect(route('home'));
});

// -------------------------
// Login
// -------------------------

it('shows the login page', function () {
    $this->get(route('login'))
        ->assertStatus(200);
});

it('logs in a user successfully', function () {
    $email = fake()->unique()->safeEmail();

    $user = User::factory()->create([
        'email' => $email,
        'password' => 'password123',
    ]);

    $this->post(route('login'), [
        'email' => $email,
        'password' => 'password123',
    ])
        ->assertRedirect(route('home'));

    $this->assertAuthenticatedAs($user);
});

it('fails login with wrong password', function () {
    $email = fake()->unique()->safeEmail();

    User::factory()->create([
        'email' => $email,
        'password' => 'password123',
    ]);

    $this->post(route('login'), [
        'email' => $email,
        'password' => 'wrongpassword',
    ])
        ->assertSessionHasErrors(['email']);
});

it('fails login with non existent email', function () {
    $this->post(route('login'), [
        'email' => fake()->unique()->safeEmail(),
        'password' => 'password123',
    ])
        ->assertSessionHasErrors(['email']);
});

it('redirects authenticated users away from login page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('login'))
        ->assertRedirect(route('home'));
});

// -------------------------
// Logout
// -------------------------

it('logs out a user successfully', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect(route('home'));

    $this->assertGuest();
});
