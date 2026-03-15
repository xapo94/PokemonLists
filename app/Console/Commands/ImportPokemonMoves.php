<?php

namespace App\Console\Commands;

use App\Models\Move;
use App\Models\Pokemon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportPokemonMoves extends Command
{
    protected $signature = 'pokemon:import-moves';

    protected $description = 'Import all Pokémon moves and their learnable Pokémon from PokéAPI';

    public function handle(): int
    {
        $this->info('Fetching moves from PokéAPI...');

        $allMoves = [];
        $offset = 0;
        $limit = 100;

        do {
            $response = Http::get('https://pokeapi.co/api/v2/move', [
                'limit' => $limit,
                'offset' => $offset,
            ]);

            if ($response->failed()) {
                $this->error('Failed to fetch moves from PokéAPI');

                return Command::FAILURE;
            }

            $allMoves = array_merge($allMoves, $response->json('results') ?? []);
            $offset += $limit;

        } while ($response->json('next'));

        $this->info('Importing '.count($allMoves).' moves...');

        $bar = $this->output->createProgressBar(count($allMoves));
        $bar->start();

        foreach ($allMoves as $entry) {
            $name = $entry['name'];
            $moveUrl = $entry['url'];

            $trimmed = rtrim($moveUrl, '/');
            $id = (int) substr($trimmed, strrpos($trimmed, '/') + 1);

            $move = Move::updateOrCreate(
                ['id' => $id],
                ['name' => $name],
            );

            $moveResponse = Http::get($moveUrl);

            if ($moveResponse->ok()) {
                $learnablePokemon = $moveResponse->json('learned_by_pokemon') ?? [];

                $pokemonIds = collect($learnablePokemon)->map(function ($p) {
                    $trimmed = rtrim($p['url'], '/');

                    return (int) substr($trimmed, strrpos($trimmed, '/') + 1);
                })->filter(fn ($id) => Pokemon::where('id', $id)->exists())->toArray();

                $move->pokemon()->sync($pokemonIds);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Done.');

        return Command::SUCCESS;
    }
}
