<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePokemonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fav_pokemon_id' => ['required', 'integer', 'exists:pokemons,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'fav_pokemon_id.required' => 'Please select a favorite Pokémon.',
            'fav_pokemon_id.exists' => 'The selected Pokémon is invalid.',
        ];
    }
}
