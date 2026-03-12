<div id="team-builder" data-pokemon='@json($pokemon->map(fn($p) => ["id" => $p->id, "name" => $p->name]))'>
    {{-- Search Input --}}
    <div class="relative">
        <input
            type="text"
            id="team_pokemon_search"
            placeholder="Search for a Pokémon to add"
            class="w-full border border-border rounded px-3 py-2 text-sm bg-background"
            autocomplete="off"
        />
        <div id="team_pokemon_results" class="absolute z-50 w-full border border-border rounded mt-1 bg-white text-zinc-900 max-h-40 overflow-auto hidden"></div>
    </div>

    {{-- Slot Boxes --}}
    <div id="team_slots" class="flex flex-wrap gap-3 mt-4"></div>

    {{-- Warnings --}}
    <p id="team_max_warning" class="text-red-500 text-xs mt-2 hidden">A team can have a maximum of 6 Pokémon.</p>
    <p id="team_duplicate_warning" class="text-red-500 text-xs mt-2 hidden">This Pokémon is already in your team.</p>
</div>

@once
    @push('scripts')
        @vite('resources/js/team-builder.js')
    @endpush
@endonce