<nav class="relative z-[9999] border-b border-border px-6">
    <div class="mx-auto h-16 flex items-center justify-between">
        <div>
            <a href="/" class="flex items-center gap-2">
                <span class="font-semibold">Pokemon Lists</span>
            </a>
        </div>

        <div class="flex items-center gap-x-5">
            @auth
                {{-- Notification Bell --}}
                <div class="relative" x-data="{ open: false, count: {{ $unreadNotifications->count() }} }">
                    <button @click="open = !open" @click.outside="open = false" class="relative cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span x-cloak x-show="count > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center" x-text="count"></span>
                    </button>

                    <div x-cloak x-show="open" class="absolute right-0 w-72 bg-white text-zinc-900 border border-border rounded-lg shadow-lg z-[9999] overflow-hidden">
                        <div class="px-4 py-2 border-b border-border flex items-center justify-between">
                            <p class="text-sm font-medium">Notifications</p>
                            <button
                                x-show="count > 0"
                                @click="fetch('{{ route('notifications.markAllAsRead') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' } }).then(() => { count = 0 })"
                                class="text-xs text-blue-500 hover:text-blue-700">
                                Mark all as read
                            </button>
                        </div>

                        @forelse($unreadNotifications as $notification)
                            <div class="px-4 py-3 text-sm border-b border-border hover:bg-zinc-50">
                                {{ $notification->data['message'] }}
                            </div>
                        @empty
                            <div class="px-4 py-3 text-sm text-zinc-400">
                                No new notifications
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- User Dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.outside="open = false" class="flex items-center cursor-pointer">
                        <img
                            src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/{{ auth()->user()->fav_pokemon_id }}.png"
                            class="h-10 w-10 rounded-full border-2 border-border bg-muted object-contain"
                        />
                    </button>

                    <div x-cloak x-show="open" class="absolute right-0 w-44 bg-white text-zinc-900 border border-border rounded-lg shadow-lg z-[9999] overflow-hidden">
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