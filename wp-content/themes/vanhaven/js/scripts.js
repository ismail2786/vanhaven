const menuToggle = document.querySelector('.menu-toggle');

menuToggle.addEventListener('click', () => {
    menuToggle.classList.toggle('active');
});

// document.addEventListener('DOMContentLoaded', () => {

//     const menuToggle = document.querySelector('.menu-toggle');

//     // Hamburger click
//     menuToggle.addEventListener('click', function(e) {

//         // Ignore clicks on menu links
//         if (e.target.closest('.menu a')) {
//             return;
//         }

//         this.classList.toggle('active');

//     });

//     // Accordion
//     document.querySelectorAll('.menu-item-has-children > a').forEach(link => {

//         link.addEventListener('click', function(e) {

//             e.preventDefault();
//             e.stopPropagation();

//             const parent = this.parentElement;

//             // Close other accordions
//             document.querySelectorAll('.menu-item-has-children').forEach(item => {
//                 if (item !== parent) {
//                     item.classList.remove('active');
//                 }
//             });

//             parent.classList.toggle('active');

//         });

//     });

// });


// document.querySelectorAll('.menu-item-has-children > a').forEach(link => {

//     link.addEventListener('click', function(e) {

//         e.preventDefault();
//         e.stopPropagation();

//         document.querySelectorAll('.menu-item-has-children').forEach(item => {
//             item.classList.remove('active');
//         });

//         this.parentElement.classList.add('active');
//     });

// });


document.querySelectorAll('.menu-item-has-children > a').forEach(link => {

    link.addEventListener('click', function(e) {

        e.preventDefault();
        e.stopPropagation();

        document.querySelectorAll('.menu-item-has-children').forEach(item => {
            item.classList.remove('active');
        });

        this.parentElement.classList.add('active');

    });

});