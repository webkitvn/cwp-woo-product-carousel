document.addEventListener('DOMContentLoaded', function () {
    var carousels = document.querySelectorAll('.embla');
    carousels.forEach(function (emblaNode) {
        EmblaCarousel(emblaNode, { loop: true });
    });
});