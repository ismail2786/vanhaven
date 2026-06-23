/* header */
document.addEventListener('DOMContentLoaded', () => {

    const menuToggle = document.querySelector('.menu-toggle');
    const accordionWrapper = document.querySelector('.accordion-wrapper');

    /*
    ============================
    HAMBURGER OPEN / CLOSE
    ============================
    */

    menuToggle.addEventListener('click', function(e) {

        // Ignore clicks inside menu content
        if (
            e.target.closest('.header-accordion') ||
            e.target.closest('.menu')
        ) {
            return;
        }

        menuToggle.classList.toggle('active');
        document.body.classList.toggle('menu-open');

        // Reset everything when closing menu
        if (!menuToggle.classList.contains('active')) {

            accordionWrapper.classList.remove('submenu-open');

            document.querySelectorAll('.menu.active').forEach(menu => {
                menu.classList.remove('active');
            });

            document.querySelectorAll('.header-accordion').forEach(acc => {
                acc.classList.remove('active');
                acc.classList.remove('active-parent');
            });
        }
    });

    /*
    ============================
    MENU ITEMS
    ============================
    */

    document.querySelectorAll('.header-accordion').forEach(accordion => {

        const heading = accordion.querySelector('h4');
        const submenu = accordion.querySelector('.menu');

        // Skip items without submenu
        if (!submenu) return;

        /*
        ============================
        CREATE BACK BUTTON ON MOBILE
        ============================
        */

        if (window.innerWidth <= 992) {

            const parentTitle = heading.textContent.trim();

            if (!submenu.querySelector('.menu-back')) {

                submenu.insertAdjacentHTML(
                    'afterbegin',
                    `
                    <li class="menu-back">Back</li>
                    <li class="submenu-title">${parentTitle}</li>
                    `
                );
            }
        }

        /*
        ============================
        CLICK MENU TITLE
        ============================
        */

        heading.addEventListener('click', function(e) {

            // Allow normal links (HOME, CONTACT etc)
            if (heading.querySelector('a')) {
                return;
            }

            e.preventDefault();

            /*
            ============================
            MOBILE
            ============================
            */

            if (window.innerWidth <= 992) {

                accordionWrapper.classList.add('submenu-open');

                document.querySelectorAll('.active-parent').forEach(item => {
                    item.classList.remove('active-parent');
                });

                document.querySelectorAll('.menu.active').forEach(menu => {
                    menu.classList.remove('active');
                });

                accordion.classList.add('active-parent');
                submenu.classList.add('active');

                return;
            }

            /*
            ============================
            DESKTOP
            ============================
            */

            document.querySelectorAll('.header-accordion').forEach(acc => {

                if (acc !== accordion) {
                    acc.classList.remove('active');
                }
            });

            accordion.classList.toggle('active');
        });
    });

    /*
    ============================
    MOBILE BACK BUTTON
    ============================
    */

    document.addEventListener('click', function(e) {

        const backBtn = e.target.closest('.menu-back');

        if (!backBtn) return;

        const submenu = backBtn.closest('.menu');

        submenu.classList.remove('active');

        accordionWrapper.classList.remove('submenu-open');

        document.querySelectorAll('.active-parent').forEach(item => {
            item.classList.remove('active-parent');
        });
    });

});

/* footer */
jQuery(function ($) {

    function initFooterAccordion() {
        if ($(window).width() <= 768) {

            $('.footer-column ul, .footer-column .menu').hide();

            $('.footer-column h4')
                .off('click.footerAccordion')
                .on('click.footerAccordion', function () {

                    const menu = $(this).next('ul, .menu');

                    if (menu.is(':visible')) {
                        menu.slideUp();
                    } else {
                        $('.footer-column ul, .footer-column .menu').slideUp();
                        menu.slideDown();
                    }
                });

        } else {
            $('.footer-column h4').off('click.footerAccordion');
            $('.footer-column ul, .footer-column .menu').show();
        }
    }

    initFooterAccordion();

    $(window).on('resize', function () {
        initFooterAccordion();
    });

});