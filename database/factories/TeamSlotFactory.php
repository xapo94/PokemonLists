<?php

namespace Database\Factories;

use App\Enums\PokemonGenderEnum;
use App\Models\Pokemon;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamSlotFactory extends Factory
{
    /**
     * Define the model's default state.
     * Usage: TeamSlot::factory()->create()
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'pokemon_id' => Pokemon::factory(),
            'slot' => fake()->numberBetween(1, 6),
            'level' => fake()->numberBetween(1, 100),
            'gender' => fake()->randomElement(PokemonGenderEnum::values()),
        ];
    }

    /**
     * Attach a given number of Pokémon slots to a team with sequential slot numbers.
     * Usage: TeamSlotFactory::forTeam($team, 3) — attaches 3 Pokémon to the team in slots 1, 2, 3.
     *
     * @param  Team  $team  The team to attach slots to
     * @param  int  $count  The number of slots to create (default: 6)
     */
    public static function forTeam(Team $team, int $count = 6): void
    {
        $pokemons = Pokemon::factory()->count($count)->create();

        $pokemons->each(function ($pokemon, $index) use ($team) {
            $team->pokemon()->attach($pokemon->id, [
                'slot' => $index + 1,
                'level' => fake()->numberBetween(1, 100),
                'gender' => fake()->randomElement(PokemonGenderEnum::values()),
            ]);
        });
    }
}
