import './bootstrap.js';

import { Turbo } from "@hotwired/turbo"; // importe Turbo

import './scripts/burgerMenu.js'

document.addEventListener("turbo:load", () => {
    const eventForm = document.querySelector("#eventForm");
    if (eventForm) {
        import('./scripts/geocodage.js').then(module => {
            module.initGeocodage();
        });
        import('./scripts/searchGames.js').then(module => {
            module.initTomSelect();
        });
    }

    const map = document.querySelector("#map");
    if (map) {
        import('./scripts/map.js');
    }
})