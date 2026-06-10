/* Header Toggle */

// const menuToggle = document.querySelector('.menu-toggle');

// menuToggle.addEventListener('click', (e) => {

//     const header = e.target.closest('.header-accordion h4');

//     if (header) {
//         const currentAccordion = header.closest('.header-accordion');

//         document.querySelectorAll('.header-accordion').forEach(accordion => {
//             if (accordion !== currentAccordion) {
//                 accordion.classList.remove('active');
//             }
//         });

//         currentAccordion.classList.toggle('active');
//         return;
//     }

//     if (e.target.closest('a')) {
//         return;
//     }

//     menuToggle.classList.toggle('active');
//     document.body.classList.toggle('menu-open');
// });


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