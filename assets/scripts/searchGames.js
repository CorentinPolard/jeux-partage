import TomSelect from "tom-select";
import "tom-select/dist/css/tom-select.default.min.css";

function initTomSelect() {
    const gamesList = document.querySelector(".games-list");

    // Si il y a le select et que le tomSelect n'a pas déjà été initialisé (turbo:load)
    if (gamesList && !gamesList.tomselect) {
        new TomSelect(gamesList, {
            plugins: ['remove_button'],
            sortField: { field: "text", direction: "asc" }
        });
    }
};

document.addEventListener("turbo:load", initTomSelect);
// Pour le premier chargement si Turbo n'est pas encore actif
initTomSelect();