<x-layout.app>
    <div class="max-w-4xl mx-auto py-12 px-4">
        <h1 class="text-2xl font-bold mb-8">Edit Team</h1>

        <form method="POST" action="{{ route('teams.update', $team) }}" class="space-y-6">
            @csrf
            @method('PATCH')

            @if($errors->any())
                @foreach($errors->all() as $error)
                    <p class="text-red-500 text-sm">{{ $error }}</p>
                @endforeach
            @endif

            <div>
                <label for="name" class="block text-sm mb-1">Team Name</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name', $team->name) }}"
                    required
                    class="w-full border border-border rounded px-3 py-2 text-sm bg-background"
                >
                <x-form.error for="name" />
            </div>

            <div>
                <label class="block text-sm mb-1">Pokémon</label>
                <x-form.team-builder :pokemon="$team->pokemon" />
                <x-form.error for="pokemon_slots" />
            </div>

            <button type="submit" class="bg-blue-600 text-white text-sm font-medium px-4 py-2 rounded hover:bg-blue-700">
                Update Team
            </button>
        </form>
    </div>
</x-layout.app>