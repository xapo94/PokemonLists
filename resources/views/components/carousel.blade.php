@props(['images' => []])

<section class="w-full">
    <div class="swiper mySwiper rounded-lg border border-border overflow-hidden">
        <div class="swiper-wrapper">
            @foreach ($images as $image)
                <div class="swiper-slide">
                    <img src="{{ $image }}" alt="Pokemon background" class="w-full h-full object-cover">
                </div>
            @endforeach
        </div>

        {{-- Pagination dots --}}
        <div class="swiper-pagination"></div>

        {{-- Optional: navigation arrows --}}
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>
</section>