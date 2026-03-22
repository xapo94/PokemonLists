<div id="team-builder" data-pokemon='@json($slots)'>
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
    <div id="team_slots" class="flex flex-col gap-3 mt-4">
        @for($i = 0; $i < 6; $i++)
            <div class="slot-box hidden flex flex-col md:flex-row md:items-center gap-4 border border-border rounded-lg p-3" data-index="{{ $i }}">

                {{-- Top on mobile / Left on desktop: Image + Name + Level + Gender --}}
                <div class="flex items-center gap-4 flex-1">
                    <img src="" alt="" class="slot-image h-16 w-16 object-contain flex-shrink-0" />
                    <div class="flex flex-col gap-2 flex-1">
                        <p class="slot-name text-sm font-medium capitalize"></p>
                        <div class="flex gap-4">
                            <div class="flex flex-col gap-1">
                                <label class="text-xs text-zinc-500">Level</label>
                                <input
                                    type="number"
                                    name="pokemon_slots[{{ $i }}][level]"
                                    min="1"
                                    max="100"
                                    value="100"
                                    placeholder="1-100"
                                    class="slot-level w-20 border border-border rounded px-2 py-1 text-sm bg-background"
                                />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-xs text-zinc-500">Gender</label>
                                <select name="pokemon_slots[{{ $i }}][gender]" class="slot-gender border border-border rounded px-2 py-1 text-sm bg-background">
                                    @foreach(\App\Enums\PokemonGenderEnum::cases() as $gender)
                                        <option value="{{ $gender->value }}" {{ $gender === \App\Enums\PokemonGenderEnum::default() ? 'selected' : '' }}>
                                            {{ $gender->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bottom on mobile / Right on desktop: Moves --}}
                <div class="flex flex-col gap-2 md:w-40">
                    <p class="text-xs text-zinc-500">Moves</p>
                    <div class="grid grid-cols-2 gap-1">
                        @for($m = 0; $m < 4; $m++)
                            <div class="relative">
                                <div class="slot-move-box cursor-pointer border border-dashed border-zinc-300 rounded px-2 py-1 text-xs text-zinc-400 hover:border-zinc-500" data-move-index="{{ $m }}">
                                    + Add Move
                                </div>
                                <input type="hidden" name="pokemon_slots[{{ $i }}][moves][{{ $m }}]" class="slot-move-id" value="" />
                            </div>
                        @endfor
                    </div>
                    <div class="slot-move-results hidden absolute z-50 w-48 border border-border rounded mt-1 bg-white text-zinc-900 max-h-40 overflow-auto"></div>
                </div>

                {{-- Hidden inputs --}}
                <input type="hidden" name="pokemon_slots[{{ $i }}][pokemon_id]" class="slot-pokemon-id" value="" />
                <input type="hidden" name="pokemon_slots[{{ $i }}][slot]" class="slot-number" value="{{ $i + 1 }}" />

                {{-- Remove button --}}
                <button type="button" class="slot-remove md:ml-auto text-red-500 hover:text-red-700 text-lg font-bold flex-shrink-0">×</button>
            </div>
        @endfor
    </div>

    {{-- Warnings --}}
    <p id="team_max_warning" class="text-red-500 text-xs mt-2 hidden">A team can have a maximum of 6 Pokémon.</p>
    <p id="team_duplicate_warning" class="text-red-500 text-xs mt-2 hidden">This Pokémon is already in your team.</p>
</div>

@once
    @push('scripts')
        @vite('resources/js/team-builder.js')
    @endpush
@endonce