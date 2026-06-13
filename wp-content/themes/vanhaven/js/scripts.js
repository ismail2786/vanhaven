const menuToggle = document.querySelector('.menu-toggle');

menuToggle.addEventListener('click', (e) => {

    const header = e.target.closest('.header-accordion h4');

    if (header) {
        const currentAccordion = header.closest('.header-accordion');

        document.querySelectorAll('.header-accordion').forEach(accordion => {
            if (accordion !== currentAccordion) {
                accordion.classList.remove('active');
            }
        });

        currentAccordion.classList.toggle('active');
        return;
    }

    if (e.target.closest('a')) {
        return;
    }

    menuToggle.classList.toggle('active');
    document.body.classList.toggle('menu-open');

    // Close accordions when menu closes
    if (!menuToggle.classList.contains('active')) {
        document.querySelectorAll('.header-accordion').forEach(accordion => {
            accordion.classList.remove('active');
        });
    }
});

// accordion

document.querySelectorAll('.vh-question').forEach(button => {

    button.addEventListener('click', function () {

        const current = this.closest('.vh-item');

        document.querySelectorAll('.vh-item').forEach(item => {

            if (item !== current) {
                item.classList.remove('active');
            }

        });

        current.classList.toggle('active');
    });

});

/* Gallery */

document.addEventListener('DOMContentLoaded', function () {

    if (typeof Swiper === 'undefined') {
        return;
    }

    if (window.innerWidth > 767) {

        new Swiper('.vh-gallery-slider', {

            slidesPerView: 1,
            spaceBetween: 20,
            loop: true,

            navigation: {
                nextEl: '.vh-next',
                prevEl: '.vh-prev'
            }

        });

    } else {

        new Swiper('.vh-gallery-mobile', {

            slidesPerView: 1.15,
            spaceBetween: 20,
            loop: true,

            navigation: {
                nextEl: '.vh-next',
                prevEl: '.vh-prev'
            }

        });

    }

});