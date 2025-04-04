import Routing from "fos-router"
require('bootstrap');
import { gsap } from "gsap";
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

import 'ol/ol.css'; // Importer le CSS OpenLayers
import Map from 'ol/Map';
import View from 'ol/View';
import TileLayer from 'ol/layer/Tile';
import TileJSON from 'ol/source/TileJSON';
import { fromLonLat } from 'ol/proj';
import VectorLayer from 'ol/layer/Vector';
import VectorSource from 'ol/source/Vector';
import Feature from 'ol/Feature';
import Point from 'ol/geom/Point';
import Style from 'ol/style/Style';
import Icon from 'ol/style/Icon';
import 'htmx.org';

// window.htmx = require('htmx.org');
import './js/home.js';
import './js/form.js';
import './js/show.js';

// Importer les icônes Leaflet pour corriger un bug avec Webpack
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';
import ol from "ol/dist/ol";

const $ = require('jquery');

document.addEventListener('DOMContentLoaded', () => {


    const showMap = () => {
        const lat = document.getElementById('lat');
        const lon =  document.getElementById('lon');
        const mapDiv = document.getElementById('map');

        if(!mapDiv || !lon || !lat) return;

        const map = new Map({
            target: 'map',
            controls: [],
            layers: [
                new TileLayer({
                    source: new TileJSON({
                        url: 'https://api.maptiler.com/maps/basic-v2/tiles.json?key=Z7HwMyoJYtZ89lXjmQGD',
                        tileSize: 512
                    })
                })
            ],
            view: new View({
                center: fromLonLat([lon.value, lat.value]),
                zoom: 13
            })
        });

        // Ajouter un marqueur avec un Layer Vector**
        const marker = new Feature({
            geometry: new Point(fromLonLat([lon.value, lat.value])) // Position du marqueur
        });

        // Appliquer un style avec une icône
        marker.setStyle(new Style({
            image: new Icon({
                anchor: [0.3, 0.5], // Ancrage pour centrer l'icône
                src: 'https://upload.wikimedia.org/wikipedia/commons/8/88/Map_marker.svg', // URL de l'icône
                scale: 0.1 // Réduire la taille
            })
        }));

        // Source vectorielle contenant le marqueur
        const vectorSource = new VectorSource({
            features: [marker]
        });

        // Couche vectorielle ajoutée à la carte
        const vectorLayer = new VectorLayer({
            source: vectorSource
        });

        map.addLayer(vectorLayer);
    }


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

    const slideSimilar = () => {
        const productList = document.querySelector(".product-list");
        const productWrapper = document.querySelector(".product-list-wrapper");
        const prevBtn = document.querySelector(".prev-btn");
        const nextBtn = document.querySelector(".next-btn");

        if(!productList) return;
        const cardWidth = document.querySelector(".product-card").offsetWidth + 20; // Largeur carte + marge
        const visibleCards = Math.floor(productWrapper.clientWidth / cardWidth); // Nombre de cartes visibles
        const totalCards = document.querySelectorAll(".product-card").length;

        let currentIndex = 0;

        function updateButtons() {
            prevBtn.disabled = currentIndex === 0;
            nextBtn.disabled = currentIndex >= totalCards - visibleCards;
        }

        nextBtn.addEventListener("click", () => {
            if (currentIndex < totalCards - visibleCards) {
                currentIndex += visibleCards;
                gsap.to(productList, { x: -currentIndex * cardWidth, duration: 0.1 });
            }
            updateButtons();
        });

        prevBtn.addEventListener("click", () => {
            if (currentIndex > 0) {
                currentIndex -= visibleCards;
                gsap.to(productList, { x: -currentIndex * cardWidth, duration: 0.1 });
            }
            updateButtons();
        });

        updateButtons();
    }
    animationRegister()
    slideSimilar()
    showMap()







    // const lat = 48.866667
    // const lon = 2.333333
    // let map = L.map('map', {
    //     zoom: 13,
    //     center: [lat, lon]
    // })
    //
    // L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    //     minZoom: 1,
    //     maxZoom: 20,
    //     attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    // }).addTo(map);

    // let marker = L.marker([lat, lon]).addTo(map);


//     const lat = 48.866667;
//     const lon = 2.333333;
//
// // Initialiser la carte
//     let map = L.map('map').setView([lat, lon], 13); // Correction ici
//
// // Ajouter le fond de carte (tuile OpenStreetMap)
//     L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
//         minZoom: 1,
//         maxZoom: 20,
//         attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
//     }).addTo(map);
//
// // Ajouter un marqueur sur la carte
//     let marker = L.marker([lat, lon]).addTo(map);















//     document.querySelector('.chat-sidebar-profile-toggle').addEventListener('click', function(e) {
//         e.preventDefault()
//         this.parentElement.classList.toggle('active')
//     })
//
//     document.addEventListener('click', function(e) {
//         if(!e.target.matches('.chat-sidebar-profile, .chat-sidebar-profile *')) {
//             document.querySelector('.chat-sidebar-profile').classList.remove('active')
//         }
//     })
// // end: Sidebar
//
//
//
// // start: Coversation
//     document.querySelectorAll('.conversation-item-dropdown-toggle').forEach(function(item) {
//         item.addEventListener('click', function(e) {
//             e.preventDefault()
//             if(this.parentElement.classList.contains('active')) {
//                 this.parentElement.classList.remove('active')
//             } else {
//                 document.querySelectorAll('.conversation-item-dropdown').forEach(function(i) {
//                     i.classList.remove('active')
//                 })
//                 this.parentElement.classList.add('active')
//             }
//         })
//     })
//
//     document.addEventListener('click', function(e) {
//         if(!e.target.matches('.conversation-item-dropdown, .conversation-item-dropdown *')) {
//             document.querySelectorAll('.conversation-item-dropdown').forEach(function(i) {
//                 i.classList.remove('active')
//             })
//         }
//     })
//
//     document.querySelectorAll('.conversation-form-input').forEach(function(item) {
//         item.addEventListener('input', function() {
//             this.rows = this.value.split('\n').length
//         })
//     })
//
//     document.querySelectorAll('[data-conversation]').forEach(function(item) {
//         item.addEventListener('click', function(e) {
//             e.preventDefault()
//             document.querySelectorAll('.conversation').forEach(function(i) {
//                 i.classList.remove('active')
//             })
//             document.querySelector(this.dataset.conversation).classList.add('active')
//         })
//     })
//
//     document.querySelectorAll('.conversation-back').forEach(function(item) {
//         item.addEventListener('click', function(e) {
//             e.preventDefault()
//             this.closest('.conversation').classList.remove('active')
//             document.querySelector('.conversation-default').classList.add('active')
//         })
//     })







});

