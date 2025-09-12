import TomSelect from "tom-select";
import "tom-select/dist/css/tom-select.default.min.css";

export function initTomSelect() {
    console.log("TomSelect chargé")
    const gamesSelector = document.querySelector(".games-selector");

    if (gamesSelector && !gamesSelector.tomselect) {
        new TomSelect(gamesSelector, {
            plugins: ['remove_button'],
            sortField: { field: "text", direction: "asc" }
        });
    } else {
        console.log("selecteur non trouvé")
    }
};
