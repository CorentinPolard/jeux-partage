import TomSelect from "tom-select";
import "tom-select/dist/css/tom-select.default.min.css";

export function initTomSelect() {
    createSelector("games");
    createSelector("participants");
    createSelector("organizer");
};

function createSelector(name) {
    const selector = document.querySelector(`.${name}-selector`);
    if (selector && !selector.tomselect) {
        new TomSelect(selector, {
            plugins: ['remove_button'],
            sortField: { field: "text", direction: "asc" }
        });
    }
}