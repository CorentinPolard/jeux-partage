import './scripts/burgerMenu.js';
import { initDeleteSystem } from './scripts/deleteModal.js';

const eventForm = document.querySelector("#eventForm");
if (eventForm) {
    import('./scripts/addresses.js');
    import('./scripts/searchGames.js');
}

const map = document.querySelector("#map");
if (map) {
    import('./scripts/map.js');
}

const messagesContainer = document.querySelector("div[data-topic]");
if (messagesContainer) {
    import('./scripts/mercure.js');
}

const deleteButton = document.querySelector(".close-modal");
if (deleteButton) {
    initDeleteSystem();
}

const registrationForm = document.querySelector("#registrationForm");
if (registrationForm) {
    import('./scripts/checkPassword.js');
}

const deleteAccountLink = document.querySelector(".delete-account");
if (deleteAccountLink) {
    import('./scripts/deleteAccount.js');
}