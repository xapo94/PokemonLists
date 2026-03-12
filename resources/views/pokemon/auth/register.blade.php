<x-layout.app>
    <div class="max-w-md mx-auto py-12 px-4">
        <h1 class="text-2xl font-bold mb-6">Create an account</h1>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm mb-1">Name</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    class="w-full border border-border rounded px-3 py-2 text-sm bg-background"
                >
                <x-form.error for="name" />
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm mb-1">Email</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    class="w-full border border-border rounded px-3 py-2 text-sm bg-background"
                >
                <x-form.error for="email" />
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm mb-1">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    class="w-full border border-border rounded px-3 py-2 text-sm bg-background"
                >
                <x-form.error for="password" />
            </div>

            {{-- Confirm Password --}}
            <div>
                <label for="password_confirmation" class="block text-sm mb-1">Confirm Password</label>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    class="w-full border border-border rounded px-3 py-2 text-sm bg-background"
                >
                <x-form.error for="password_confirmation" />
            </div>

            {{-- Favorite Pokémon search --}}
            <div>
                <x-form.pokemon-search />
            </div>

            <button
                type="submit"
                class="w-full mt-4 bg-blue-600 text-white text-sm font-medium py-2 rounded hover:bg-blue-700"
            >
                Register
            </button>
        </form>
    </div>
</x-layout.app>