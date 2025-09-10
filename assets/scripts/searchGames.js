import TomSelect from "tom-select";
import "tom-select/dist/css/tom-select.default.min.css";

function initTomSelect() {
    const gamesSelector = document.querySelector(".games-selector");

    // Si il y a le select et que le tomSelect n'a pas déjà été initialisé (turbo:load)
    if (gamesSelector && !gamesSelector.tomselect) {
        new TomSelect(gamesSelector, {
            plugins: ['remove_button'],
            sortField: { field: "text", direction: "asc" }
        });
    }
};

initTomSelect();