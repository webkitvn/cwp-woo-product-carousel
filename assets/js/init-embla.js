import EmblaCarousel from 'embla-carousel';
import Autoplay from 'embla-carousel-autoplay';
import ClassNames from 'embla-carousel-class-names';

document.addEventListener('DOMContentLoaded', () => {
    const carousels = document.querySelectorAll('.embla');
    const loop = cwpCarouselOptions.looping;
    const autoplayEnabled = cwpCarouselOptions.autoplay;

    carousels.forEach((emblaNode) => {
        const prevButton = emblaNode.querySelector('.embla__prev');
        const nextButton = emblaNode.querySelector('.embla__next');
        const viewport = emblaNode.querySelector('.embla__viewport');
        const options = {
            loop,
            align: 'start',
        };
        const embla = EmblaCarousel(viewport, options, [
            autoplayEnabled && Autoplay(),
            ClassNames(),
        ]);

        if (prevButton) {
            prevButton.addEventListener('click', () => embla.scrollPrev(), false);
        }

        if (nextButton) {
            nextButton.addEventListener('click', () => embla.scrollNext(), false);
        }
    });
});