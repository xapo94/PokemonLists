<x-layout.app>
    <div class="max-w-4xl mx-auto py-12 px-4">
        <h1 class="text-2xl font-bold mb-8">Profile</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Change Password --}}
            <div class="border border-border rounded-lg p-6 space-y-4">
                <h2 class="text-lg font-semibold">Change Password</h2>

                <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="current_password" class="block text-sm mb-1">Current Password</label>
                        <input
                            id="current_password"
                            name="current_password"
                            type="password"
                            required
                            class="w-full border border-border rounded px-3 py-2 text-sm bg-background"
                        >
                        <x-form.error for="current_password" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm mb-1">New Password</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            class="w-full border border-border rounded px-3 py-2 text-sm bg-background"
                        >
                        <x-form.error for="password" />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm mb-1">Confirm New Password</label>
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            required
                            class="w-full border border-border rounded px-3 py-2 text-sm bg-background"
                        >
                        <x-form.error for="password_confirmation" />
                    </div>

                    <button type="submit" class="bg-blue-600 text-white text-sm font-medium px-4 py-2 rounded hover:bg-blue-700">
                        Update Password
                    </button>
                </form>
            </div>

            {{-- Change Favorite Pokémon --}}
            <div class="border border-border rounded-lg p-6 space-y-4">
                <h2 class="text-lg font-semibold">Change Favorite Pokémon</h2>

                <div class="flex items-center gap-4">
                    <img
                        src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/{{ auth()->user()->fav_pokemon_id }}.png"
                        class="h-16 w-16 rounded-full border-2 border-border bg-muted object-contain"
                    />
                    <p class="text-sm text-muted-foreground">Current favorite</p>
                </div>

                <form method="POST" action="{{ route('profile.pokemon') }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <x-form.pokemon-search />

                    <button type="submit" class="bg-blue-600 text-white text-sm font-medium px-4 py-2 rounded hover:bg-blue-700">
                        Update Favorite
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-layout.app>