<?php

namespace App\Services;

use App\Models\Pokemon;

class PokemonSearchService
{
    /**
     * Search Pokémon by name.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function search(?string $term)
    {
        return Pokemon::query()
            ->when($term, fn ($q) => $q->where('name', 'like', "%{$term}%"))
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name']);
    }

    /**
     * Get all learnable moves for a Pokémon.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getMoves(Pokemon $pokemon)
    {
        return $pokemon->moves()
            ->orderBy('name')
            ->get(['moves.id', 'moves.name']);
    }
}
