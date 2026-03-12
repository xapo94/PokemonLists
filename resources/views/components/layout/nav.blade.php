<nav class="border-b border-border px-6">
    <div class="mx-auto h-16 flex items-center justify-between">
        <div>
            <a href="/" class="flex items-center gap-2">
                {{-- <img src="{{ asset('images/pokelogo.png') }}" alt="Pokemon Lists" class="h-12 w-auto"> --}}
                <span class="font-semibold">Pokemon Lists</span>
            </a>
        </div>

        <div class="flex items-center gap-x-5">
            @auth
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.outside="open = false" class="flex items-center cursor-pointer">
                        <img
                            src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/{{ auth()->user()->fav_pokemon_id }}.png"
                            class="h-10 w-10 rounded-full border-2 border-border bg-muted object-contain"
                        />
                    </button>

                    <div x-cloak x-show="open" class="absolute right-0 w-44 bg-background text-zinc-900 border border-border rounded-lg shadow-lg z-[9999] overflow-hidden">
                        <div class="px-4 py-2 border-b border-border">
                            <p class="text-xs text-muted-foreground">Signed in as</p>
                            <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                        </div>
                        <a href="{{ route('profile') }}" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-muted">
                            Profile
                        </a>
                        <a href="{{ route('teams.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-muted">
                            Teams
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2 text-sm hover:bg-muted cursor-pointer text-red-500">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            @endauth

            @guest
                <a href="{{ route('login') }}" class="text-sm font-medium hover:opacity-75 transition-opacity">
                    Sign In
                </a>
                <a href="{{ route('register') }}" class="text-sm font-medium bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Register
                </a>
            @endguest
        </div>
    </div>
</nav>