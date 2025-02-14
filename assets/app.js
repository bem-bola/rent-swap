import Routing from "fos-router"
require('bootstrap');
import './js/home.js';

const $ = require('jquery');
// document.addEventListener('DOMContentLoaded', () => {
    //
    // // Carousel
    // const images = document.querySelectorAll('.carousel-image');
    // const thumbnails = document.querySelectorAll('.thumbnail');
    // const prevBtn = document.getElementById('prev-btn');
    // const nextBtn = document.getElementById('next-btn');
    //
    // let currentIndex = 0;
    //
    // const updateCarousel = (index) => {
    //     images.forEach((img, idx) => {
    //         img.classList.toggle('img-active', idx === index);
    //         img.classList.toggle('img-hide', idx !== index);
    //     });
    //     thumbnails.forEach((thumbnail, idx) => {
    //         thumbnail.classList.toggle('active', idx === index);
    //     });
    // };
    //
    // prevBtn.addEventListener('click', () => {
    //     currentIndex = (currentIndex > 0) ? currentIndex - 1 : images.length - 1;
    //     updateCarousel(currentIndex);
    // });
    //
    // nextBtn.addEventListener('click', () => {
    //     currentIndex = (currentIndex < images.length - 1) ? currentIndex + 1 : 0;
    //     updateCarousel(currentIndex);
    // });
    //
    // thumbnails.forEach((thumbnail) => {
    //     thumbnail.addEventListener('click', () => {
    //         const index = parseInt(thumbnail.getAttribute('data-index'), 10);
    //         currentIndex = index;
    //         updateCarousel(currentIndex);
    //     });
    // });
    // Fin carousel
// });