<x-layout.app>
    <div class="max-w-4xl mx-auto py-12 px-4">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl font-bold">My Teams</h1>
            <a href="{{ route('teams.create') }}" class="bg-blue-600 text-white text-sm font-medium px-4 py-2 rounded hover:bg-blue-700">
                Create Team
            </a>
        </div>

        @if($teams->isEmpty())
            <p class="text-sm text-zinc-500">You have no teams yet.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($teams as $team)
                    <div class="border border-border rounded-lg p-6 space-y-4">
                        {{-- Team Name --}}
                        <h2 class="text-lg font-semibold">{{ $team->name }}</h2>

                        {{-- Pokemon Slots --}}
                        <div class="flex items-center gap-2">
                            @for($i = 1; $i <= 6; $i++)
                                @php
                                    $pokemon = $team->pokemon->firstWhere('pivot.slot', $i);
                                @endphp

                                @if($pokemon)
                                    <img
                                        src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/{{ $pokemon->id }}.png"
                                        class="h-12 w-12 object-contain"
                                        title="{{ $pokemon->name }}"
                                    />
                                @else
                                    <div class="h-12 w-12 rounded-full border-2 border-dashed border-zinc-300 flex items-center justify-center">
                                        <span class="text-zinc-300 text-xs">?</span>
                                    </div>
                                @endif
                            @endfor
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 pt-2">
                            <a href="{{ route('teams.edit', $team) }}" class="text-sm font-medium bg-zinc-100 text-zinc-800 px-3 py-1.5 rounded hover:bg-zinc-200">
                                Edit
                            </a>

                            <form method="POST" action="{{ route('teams.destroy', $team) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-medium bg-red-100 text-red-600 px-3 py-1.5 rounded hover:bg-red-200">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layout.app>