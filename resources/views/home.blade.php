<x-layout.app>
    <div class="max-w-5xl mx-auto py-12 px-4">
        <section class="mb-8">
            <h1 class="text-3xl font-bold mb-2">Pokemon Lists</h1>
            <p class="text-sm text-muted-foreground">
                Below are showcased some random pokemon images for completely no reason! Since you are here though, take a minute to check them out!
            </p>
        </section>

        <x-carousel :images="$carouselImages" />
    </div>
</x-layout.app>