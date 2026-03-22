<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use App\Services\PokemonSearchService;
use Illuminate\Http\Request;

class PokemonController extends Controller
{
    public function searchPokemon(Request $request)
    {
        $term = $request->input('search');

        return PokemonSearchService::search($term);
    }

    public function getPokemonMoves(Pokemon $pokemon)
    {
        return PokemonSearchService::getMoves($pokemon);
    }
}
