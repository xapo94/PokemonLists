<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    // Show the registration form
    public function create()
    {
        return view('pokemon.auth.register');
    }

    // Handle the form submission
    public function store(RegisterUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'fav_pokemon_id' => $validated['fav_pokemon_id'],
        ]);

        Auth::login($user);

        return redirect()->route('home');
    }
}
