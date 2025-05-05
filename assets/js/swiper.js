import Swiper from 'swiper/bundle';
import 'swiper/css/bundle';

// Initialiser après chargement du DOM
document.addEventListener('DOMContentLoaded', () => {
    console.log('testt')
    const swiper = new Swiper('.swiper', {

        // Optional parameters
        direction: 'vertical',
        loop: true,

        // If we need pagination
        pagination: {
            el: '.swiper-pagination',
        },

        // Navigation arrows
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },

        // And if we need scrollbar
        scrollbar: {
            el: '.swiper-scrollbar',
        },
    });

});
