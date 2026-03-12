<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokemon Lists</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col">

    <x-layout.nav />

    <div class="flex-1 flex" style="background-image: url('{{ asset('images/background/body_bg.png') }}'); background-repeat: repeat; background-color: #424242;">
        <main class="w-full max-w-6xl mx-auto bg-white text-zinc-900 shadow-2xl my-12">
            {{ $slot }}
        </main>
    </div>

    <x-layout.footer />

    @if(session('success'))
        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 3000)"
            x-show="show"
            x-transition
            class="fixed bottom-4 right-4 z-[9999] bg-green-500 text-white text-sm font-medium px-4 py-3 rounded-lg shadow-lg"
        >
            {{ session('success') }}
        </div>
    @endif

    @stack('scripts')
</body>
</html>