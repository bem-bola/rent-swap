document.addEventListener("DOMContentLoaded", () => {
    let currentIndex = 0;
    const slides = document.querySelectorAll("#section-home-5 .carousel-slide");
    const totalSlides = slides.length;
    let autoSlideInterval;

    const showSlide = (index) =>  {
        slides.forEach((slide, i) => {
            slide.classList.toggle("active", i === index);
        });
    }

    const nextSlide = () => {
        currentIndex = (currentIndex + 1) % totalSlides;
        showSlide(currentIndex);
    }

    const prevSlide = () => {
        currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
        showSlide(currentIndex);
    }

    const startAutoSlide = () => {
        autoSlideInterval = setInterval(nextSlide, 5000);
    }

    const stopAutoSlide = () => {
        clearInterval(autoSlideInterval);
    }

    document.querySelector("#carousel-next").addEventListener("click", () => {
        nextSlide();
        stopAutoSlide();
        startAutoSlide();
    });

    document.querySelector("#carousel-prev").addEventListener("click", () => {
        prevSlide();
        stopAutoSlide();
        startAutoSlide();
    });

    showSlide(currentIndex);
    startAutoSlide();
});