import Routing from "fos-router"
require('bootstrap');
import { gsap } from "gsap";
import './js/home.js';
import './js/form.js';

const $ = require('jquery');

document.addEventListener('DOMContentLoaded', () => {

    const animationRegister = () => {

        const sections = document.querySelectorAll('#register .section');

        if(!sections) return;

        let currentSectionIndex = 0;

        // Cache toutes les sections sauf la première
        const hideSections = () => {
            sections.forEach((section, index) => {
                if (index !== 0) {
                    section.classList.add('d-none');
                }
            });
        }
        hideSections()

        // Fonction pour afficher la section suivante
        const nextSection = () => {
            if (currentSectionIndex < sections.length - 1) {
                gsap.to(sections[currentSectionIndex], {
                    x: '-100%',
                    opacity: 0,
                    duration: 0.7,
                    ease: 'power2.inOut',
                    onComplete: () => {
                        sections[currentSectionIndex].classList.add('d-none');
                        currentSectionIndex++;
                        sections[currentSectionIndex].classList.remove('d-none');
                        gsap.fromTo(sections[currentSectionIndex], {x: '100%', opacity: 0}, {
                            x: '0%',
                            opacity: 1,
                            duration: 0.7,
                            ease: 'power2.inOut'
                        });
                        updateButtons();
                    }
                });
            }
        }

        // Fonction pour afficher la section précédente
        const prevSection = () => {
            if (currentSectionIndex > 0) {
                gsap.to(sections[currentSectionIndex], {
                    x: '100%',
                    opacity: 0,
                    duration: 0.7,
                    ease: 'power2.inOut',
                    onComplete: () => {
                        sections[currentSectionIndex].classList.add('d-none');
                        currentSectionIndex--;
                        sections[currentSectionIndex].classList.remove('d-none');
                        gsap.fromTo(sections[currentSectionIndex], {x: '-100%', opacity: 0}, {
                            x: '0%',
                            opacity: 1,
                            duration: 0.7,
                            ease: 'power2.inOut'
                        });
                        updateButtons();
                    }
                });
            }
        }

        // Mise à jour des boutons (cacher ou afficher selon l'index)
        const updateButtons = () => {
            sections.forEach((section, index) => {
                const btnPrev = section.querySelector('.btn-prev-register');
                const btnNext = section.querySelector('.btn-next-register');

                if (btnPrev) {
                    btnPrev.classList.toggle('d-none', index === 0);
                }
                if (btnNext) {
                    btnNext.classList.toggle('d-none', index === sections.length - 1);
                }
            });
        }
        // Ajoute des Event Listeners sur chaque bouton de chaque section
        const handlerAddEvent = () => {
            sections.forEach((section, index) => {
                const btnNext = section.querySelector('.btn-next-register');
                const btnPrev = section.querySelector('.btn-prev-register');

                if (btnNext) {
                    btnNext.addEventListener('click', nextSection);
                }
                if (btnPrev) {
                    btnPrev.addEventListener('click', prevSection);
                }
            });
        }
        handlerAddEvent();


        // Initialisation
        updateButtons();
    }
    animationRegister()
});

