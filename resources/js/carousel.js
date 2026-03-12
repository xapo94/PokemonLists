import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

export function initCarousels() {
    const carousels = document.querySelectorAll('.mySwiper');
    if (carousels.length === 0) return;

    Swiper.use([Navigation, Pagination, Autoplay]);

    carousels.forEach((carousel) => {
        new Swiper(carousel, {
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: carousel.querySelector('.swiper-pagination'),
                clickable: true,
            },
            navigation: {
                nextEl: carousel.querySelector('.swiper-button-next'),
                prevEl: carousel.querySelector('.swiper-button-prev'),
            },
        });
    });
}