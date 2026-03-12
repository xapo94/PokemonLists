<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UpdatePokemonRequest;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        return view('pokemon.profile');
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $validated = $request->validated();

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $request->session()->regenerate();

        return back()->with('success', 'Password updated successfully.');
    }

    public function updatePokemon(UpdatePokemonRequest $request)
    {
        $validated = $request->validated();

        $request->user()->update([
            'fav_pokemon_id' => $validated['fav_pokemon_id'],
        ]);

        return back()->with('success', 'Favorite Pokémon updated successfully.');
    }
}
