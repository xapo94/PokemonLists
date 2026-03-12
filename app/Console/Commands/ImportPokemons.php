<?php

namespace App\Console\Commands;

use App\Models\Pokemon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportPokemons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pokemon:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Pokémon from PokéAPI into the pokemons table';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Fetching Pokémon list from PokéAPI...');

        $response = Http::get('https://pokeapi.co/api/v2/pokemon', [
            'limit' => 1350,
            'offset' => 0,
        ]);

        if ($response->failed()) {
            $this->error('Failed to fetch data from PokéAPI');

            return Command::FAILURE;
        }

        $results = $response->json('results') ?? [];

        $this->info('Importing '.count($results).' Pokémon...');

        foreach ($results as $entry) {
            $name = $entry['name'];
            $url = $entry['url']; // e.g. "https://pokeapi.co/api/v2/pokemon/25/"

            // extract numeric ID from URL
            $trimmed = rtrim($url, '/');                  // "…/pokemon/25"
            $lastSlashPos = strrpos($trimmed, '/');
            $idString = substr($trimmed, $lastSlashPos + 1); // "25"
            $id = (int) $idString;

            Pokemon::updateOrCreate(
                ['id' => $id],
                ['name' => $name],
            );
        }

        $this->info('Done.');

        return Command::SUCCESS;
    }
}
