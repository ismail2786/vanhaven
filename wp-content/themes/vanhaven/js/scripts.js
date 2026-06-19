// document.addEventListener('DOMContentLoaded', () => {

//     const menuToggle = document.querySelector('.menu-toggle');
//     const accordionWrapper = document.querySelector('.accordion-wrapper');

//     /*
//     ----------------------------
//     OPEN / CLOSE HAMBURGER
//     ----------------------------
//     */

//     menuToggle.addEventListener('click', function(e) {

//         if (
//             e.target.closest('.header-accordion') ||
//             e.target.closest('.menu')
//         ) {
//             return;
//         }

//         menuToggle.classList.toggle('active');
//         document.body.classList.toggle('menu-open');

//         if (!menuToggle.classList.contains('active')) {

//             accordionWrapper.classList.remove('submenu-open');

//             document.querySelectorAll('.menu.active').forEach(menu => {
//                 menu.classList.remove('active');
//             });

//             document.querySelectorAll('.active-parent').forEach(item => {
//                 item.classList.remove('active-parent');
//             });
//         }
//     });

//     /*
//     ----------------------------
//     MOBILE ONLY
//     ----------------------------
//     */

//     if (window.innerWidth <= 992) {

//         document.querySelectorAll('.header-accordion').forEach(accordion => {

//             const heading = accordion.querySelector('h4');
//             const submenu = accordion.querySelector('.menu');

//             if (!submenu || !heading.querySelector('a') === false) return;

//             /*
//             ----------------------------
//             CREATE BACK BUTTON
//             ----------------------------
//             */

//             const parentTitle = heading.textContent.trim();

//             submenu.insertAdjacentHTML(
//                 'afterbegin',
//                 `
//                 <li class="menu-back">Back</li>
//                 <li class="submenu-title">${parentTitle}</li>
//                 `
//             );

//             /*
//             ----------------------------
//             OPEN SUBMENU
//             ----------------------------
//             */

//             heading.addEventListener('click', function(e) {

//                 if (heading.querySelector('a')) return;

//                 e.preventDefault();

//                 accordionWrapper.classList.add('submenu-open');

//                 accordion.classList.add('active-parent');

//                 submenu.classList.add('active');
//             });
//         });

//         /*
//         ----------------------------
//         BACK BUTTON
//         ----------------------------
//         */

//         document.addEventListener('click', function(e) {

//             const backBtn = e.target.closest('.menu-back');

//             if (!backBtn) return;

//             const submenu = backBtn.closest('.menu');

//             submenu.classList.remove('active');

//             accordionWrapper.classList.remove('submenu-open');

//             document
//                 .querySelectorAll('.active-parent')
//                 .forEach(item => item.classList.remove('active-parent'));
//         });
//     }
// });


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