<x-layout.app>
    <div class="max-w-md mx-auto py-12 px-4">
        <h1 class="text-2xl font-bold mb-6">Sign in</h1>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

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

            <button
                type="submit"
                class="w-full mt-4 bg-blue-600 text-white text-sm font-medium py-2 rounded hover:bg-blue-700"
            >
                Sign In
            </button>
        </form>
    </div>
</x-layout.app>