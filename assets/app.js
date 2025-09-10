import './bootstrap.js';

import { Turbo } from "@hotwired/turbo"; // importe Turbo

import './scripts/burgerMenu.js'

document.addEventListener("turbo:load", () => {
    const eventForm = document.querySelector("#eventForm");
    if (eventForm) {
        import('./scripts/geocodage.js');
        import('./scripts/searchGames.js');
    }

    const map = document.querySelector("#map");
    if (map) {
        import('./scripts/map.js');
    }
})