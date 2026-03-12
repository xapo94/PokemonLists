<?php

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeamRequest extends FormRequest
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
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'pokemon_slots' => ['required', 'array', 'min:1', 'max:6'],
            'pokemon_slots.*.pokemon_id' => ['required', 'integer', 'exists:pokemons,id'],
            'pokemon_slots.*.slot' => ['required', 'integer', 'min:1', 'max:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'pokemon_slots.required' => 'Please add at least one Pokémon to your team.',
            'pokemon_slots.max' => 'A team can have a maximum of 6 Pokémon.',
            'pokemon_slots.*.pokemon_id.exists' => 'One or more selected Pokémon are invalid.',
        ];
    }
}
