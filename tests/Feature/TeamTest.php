<?php

use App\Enums\PokemonGenderEnum;
use App\Models\Move;
use App\Models\Pokemon;
use App\Models\Team;
use App\Models\User;
use Database\Factories\TeamSlotFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// -------------------------
// Index
// -------------------------

it('shows the teams index page for authenticated users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('teams.index'))
        ->assertStatus(200);
});

it('redirects unauthenticated users away from teams index', function () {
    $this->get(route('teams.index'))
        ->assertRedirect(route('login'));
});

// -------------------------
// Create
// -------------------------

it('shows the create team page for authenticated users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('teams.create'))
        ->assertStatus(200);
});

it('redirects unauthenticated users away from create team page', function () {
    $this->get(route('teams.create'))
        ->assertRedirect(route('login'));
});

// -------------------------
// Store
// -------------------------

it('creates a team successfully', function () {
    $user = User::factory()->create();
    $pokemon = Pokemon::factory()->create();
    $name = fake()->words(2, true);

    $this->actingAs($user)
        ->post(route('teams.store'), [
            'name' => $name,
            'pokemon_slots' => [
                [
                    'pokemon_id' => $pokemon->id,
                    'slot' => 1,
                    'level' => fake()->numberBetween(1, 100),
                    'gender' => fake()->randomElement(PokemonGenderEnum::values()),
                    'moves' => [],
                ],
            ],
        ])
        ->assertRedirect(route('teams.index'));

    $this->assertDatabaseHas('teams', ['name' => $name, 'user_id' => $user->id]);
});

it('stores team slots correctly', function () {
    $user = User::factory()->create();
    $pokemon = Pokemon::factory()->create();
    $level = fake()->numberBetween(1, 100);
    $gender = fake()->randomElement(PokemonGenderEnum::values());

    $this->actingAs($user)
        ->post(route('teams.store'), [
            'name' => fake()->words(2, true),
            'pokemon_slots' => [
                [
                    'pokemon_id' => $pokemon->id,
                    'slot' => 1,
                    'level' => $level,
                    'gender' => $gender,
                    'moves' => [],
                ],
            ],
        ]);

    $this->assertDatabaseHas('team_slots', [
        'pokemon_id' => $pokemon->id,
        'slot' => 1,
        'level' => $level,
        'gender' => $gender,
    ]);
});

it('fails to create a team without a name', function () {
    $user = User::factory()->create();
    $pokemon = Pokemon::factory()->create();

    $this->actingAs($user)
        ->post(route('teams.store'), [
            'name' => '',
            'pokemon_slots' => [
                [
                    'pokemon_id' => $pokemon->id,
                    'slot' => 1,
                    'level' => fake()->numberBetween(1, 100),
                    'gender' => fake()->randomElement(PokemonGenderEnum::values()),
                    'moves' => [],
                ],
            ],
        ])
        ->assertSessionHasErrors(['name']);
});

it('fails to create a team without pokemon slots', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('teams.store'), [
            'name' => fake()->words(2, true),
            'pokemon_slots' => [],
        ])
        ->assertSessionHasErrors(['pokemon_slots']);
});

it('redirects unauthenticated users away from store team', function () {
    $this->post(route('teams.store'))
        ->assertRedirect(route('login'));
});

// -------------------------
// Edit
// -------------------------

it('shows the edit team page for the team owner', function () {
    $user = User::factory()->create();
    $team = Team::factory()->for($user)->create();
    TeamSlotFactory::forTeam($team, 1);

    $this->actingAs($user)
        ->get(route('teams.edit', $team))
        ->assertStatus(200);
});

it('prevents users from editing another users team', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $team = Team::factory()->for($otherUser)->create();

    $this->actingAs($user)
        ->get(route('teams.edit', $team))
        ->assertStatus(403);
});

it('redirects unauthenticated users away from edit team page', function () {
    $team = Team::factory()->create();

    $this->get(route('teams.edit', $team))
        ->assertRedirect(route('login'));
});

// -------------------------
// Update
// -------------------------

it('updates a team successfully', function () {
    $user = User::factory()->create();
    $team = Team::factory()->for($user)->create();
    $pokemon = Pokemon::factory()->create();
    $newName = fake()->words(2, true);
    TeamSlotFactory::forTeam($team, 1);

    $this->actingAs($user)
        ->patch(route('teams.update', $team), [
            'name' => $newName,
            'pokemon_slots' => [
                [
                    'pokemon_id' => $pokemon->id,
                    'slot' => 1,
                    'level' => fake()->numberBetween(1, 100),
                    'gender' => fake()->randomElement(PokemonGenderEnum::values()),
                    'moves' => [],
                ],
            ],
        ])
        ->assertRedirect(route('teams.index'));

    $this->assertDatabaseHas('teams', ['name' => $newName]);
});

it('prevents users from updating another users team', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $team = Team::factory()->for($otherUser)->create();
    $pokemon = Pokemon::factory()->create();

    $this->actingAs($user)
        ->patch(route('teams.update', $team), [
            'name' => fake()->words(2, true),
            'pokemon_slots' => [
                [
                    'pokemon_id' => $pokemon->id,
                    'slot' => 1,
                    'level' => fake()->numberBetween(1, 100),
                    'gender' => fake()->randomElement(PokemonGenderEnum::values()),
                    'moves' => [],
                ],
            ],
        ])
        ->assertStatus(403);
});

it('fails to update a team with invalid data', function () {
    $user = User::factory()->create();
    $team = Team::factory()->for($user)->create();

    $this->actingAs($user)
        ->patch(route('teams.update', $team), [
            'name' => 'ab',
            'pokemon_slots' => [],
        ])
        ->assertSessionHasErrors(['name', 'pokemon_slots']);
});

it('redirects unauthenticated users away from update team', function () {
    $team = Team::factory()->create();

    $this->patch(route('teams.update', $team))
        ->assertRedirect(route('login'));
});

// -------------------------
// Destroy
// -------------------------

it('deletes a team successfully', function () {
    $user = User::factory()->create();
    $team = Team::factory()->for($user)->create();

    $this->actingAs($user)
        ->delete(route('teams.destroy', $team))
        ->assertRedirect(route('teams.index'));

    $this->assertDatabaseMissing('teams', ['id' => $team->id]);
});

it('prevents users from deleting another users team', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $team = Team::factory()->for($otherUser)->create();

    $this->actingAs($user)
        ->delete(route('teams.destroy', $team))
        ->assertStatus(403);
});

it('redirects unauthenticated users away from delete team', function () {
    $team = Team::factory()->create();

    $this->delete(route('teams.destroy', $team))
        ->assertRedirect(route('login'));
});

it('fails to create a team with duplicate pokemon', function () {
    $user = User::factory()->create();
    $pokemon = Pokemon::factory()->create();

    $this->actingAs($user)
        ->post(route('teams.store'), [
            'name' => fake()->words(2, true),
            'pokemon_slots' => [
                [
                    'pokemon_id' => $pokemon->id,
                    'slot' => 1,
                    'level' => fake()->numberBetween(1, 100),
                    'gender' => fake()->randomElement(PokemonGenderEnum::values()),
                    'moves' => [],
                ],
                [
                    'pokemon_id' => $pokemon->id,
                    'slot' => 2,
                    'level' => fake()->numberBetween(1, 100),
                    'gender' => fake()->randomElement(PokemonGenderEnum::values()),
                    'moves' => [],
                ],
            ],
        ])
        ->assertSessionHasErrors(['pokemon_slots.0.pokemon_id']);
});

it('fails to update a team with duplicate pokemon', function () {
    $user = User::factory()->create();
    $team = Team::factory()->for($user)->create();
    $pokemon = Pokemon::factory()->create();

    $this->actingAs($user)
        ->patch(route('teams.update', $team), [
            'name' => fake()->words(2, true),
            'pokemon_slots' => [
                [
                    'pokemon_id' => $pokemon->id,
                    'slot' => 1,
                    'level' => fake()->numberBetween(1, 100),
                    'gender' => fake()->randomElement(PokemonGenderEnum::values()),
                    'moves' => [],
                ],
                [
                    'pokemon_id' => $pokemon->id,
                    'slot' => 2,
                    'level' => fake()->numberBetween(1, 100),
                    'gender' => fake()->randomElement(PokemonGenderEnum::values()),
                    'moves' => [],
                ],
            ],
        ])
        ->assertSessionHasErrors(['pokemon_slots.0.pokemon_id']);
});

it('fails to create a team with duplicate moves in a slot', function () {
    $user = User::factory()->create();
    $pokemon = Pokemon::factory()->create();
    $move = Move::factory()->create();
    $pokemon->moves()->attach($move->id);

    $this->actingAs($user)
        ->post(route('teams.store'), [
            'name' => fake()->words(2, true),
            'pokemon_slots' => [
                [
                    'pokemon_id' => $pokemon->id,
                    'slot' => 1,
                    'level' => fake()->numberBetween(1, 100),
                    'gender' => fake()->randomElement(PokemonGenderEnum::values()),
                    'moves' => [$move->id, $move->id],
                ],
            ],
        ])
        ->assertSessionHasErrors(['pokemon_slots.0.moves.0']);
});
