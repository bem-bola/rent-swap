document.addEventListener("DOMContentLoaded", () => {
    const carouselNotice = () => {

        const slides = document.querySelectorAll("#section-home-5 .carousel-slide, #section-show-1 .carousel-slide");
        const btnCarouselNext = document.querySelector(".carousel-next")
        const btnCarouselPrev = document.querySelector(".carousel-prev")

        if(!slides) return;

        let currentIndex = 0;
        const totalSlides = slides.length;
        let autoSlideInterval;
        const showSlide = (index) => {
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

        if (btnCarouselNext) btnCarouselNext.addEventListener("click", () => {
            nextSlide();
            stopAutoSlide();
            startAutoSlide();
        });

        if(btnCarouselPrev) btnCarouselPrev.addEventListener("click", () => {
            prevSlide();
            stopAutoSlide();
            startAutoSlide();
        });

        showSlide(currentIndex);
        startAutoSlide();

    }
    const carouselInfinity = () => {
        const track = document.querySelector(".carousel-track");
        if(!track) return;
        const cards = Array.from(track.children);

        if(!cards) return;
        // Dupliquer les cartes pour un effet infini
        cards.forEach(card => {
            let clone = card.cloneNode(true);
            track.appendChild(clone);
        });
    }

    carouselNotice()
    carouselInfinity()

});