<?php

use App\Http\Controllers\PokemonController;
use Illuminate\Support\Facades\Route;

Route::get('/pokemons/search', [PokemonController::class, 'searchPokemon']);

Route::get('/pokemons/{pokemon}/moves', [PokemonController::class, 'getPokemonMoves'])->name('pokemons.moves');
