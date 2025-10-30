import './scripts/burgerMenu.js';

const eventForm = document.querySelector("#eventForm");
if (eventForm) {
    import('./scripts/geocodage.js');
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

// const deleteButton = document.querySelector(".delete-button");
const deleteButton = document.querySelector(".close-modal");
if (deleteButton) {
    import('./scripts/deleteModal.js');
}

const registrationForm = document.querySelector("#registrationForm");
if (registrationForm) {
    import('./scripts/checkPassword.js');
}

const deleteAccountLink = document.querySelector(".delete-account");
if (deleteAccountLink) {
    import('./scripts/deleteAccount.js');
}