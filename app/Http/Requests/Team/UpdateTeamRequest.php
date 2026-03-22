<?php

namespace App\Http\Requests\Team;

use App\Enums\PokemonGenderEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
     * Runs before validation ~ filter out empty slots
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'pokemon_slots' => collect($this->pokemon_slots)
                ->filter(fn ($slot) => ! empty($slot['pokemon_id']))
                ->map(fn ($slot) => array_merge($slot, [
                    'moves' => collect($slot['moves'] ?? [])
                        ->filter(fn ($move) => ! empty($move))
                        ->values()
                        ->toArray(),
                ]))
                ->values()
                ->toArray(),
        ]);
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
            'pokemon_slots.*.pokemon_id' => ['required', 'integer', 'exists:pokemons,id', 'distinct'],
            'pokemon_slots.*.slot' => ['required', 'integer', 'min:1', 'max:6'],
            'pokemon_slots.*.level' => ['nullable', 'integer', 'min:1', 'max:100'],
            'pokemon_slots.*.gender' => ['nullable', Rule::in(PokemonGenderEnum::values())],
            'pokemon_slots.*.moves' => ['nullable', 'array', 'max:4'],
            'pokemon_slots.*.moves.*' => ['integer', 'exists:moves,id', 'distinct'],
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
