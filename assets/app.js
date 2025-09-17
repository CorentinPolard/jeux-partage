import './scripts/burgerMenu.js';

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
    import('./scripts/map.js').then(module => {
        module.initMap();
    });
}

const deleteButton = document.querySelector(".delete-button");
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