<div>
    <label for="pokemon_search" class="block text-sm mb-1">Favorite Pokémon</label>

    <input
        type="text"
        id="pokemon_search"
        placeholder="Type your favorite Pokémon"
        class="w-full border border-border rounded px-3 py-2 text-sm bg-background"
        required
    >

    <input type="hidden" name="fav_pokemon_id" id="fav_pokemon_id">

    <x-form.error for="fav_pokemon_id" />
</div>

@once
    @push('scripts')
        @vite('resources/js/pokemon-search.js')
    @endpush
@endonce