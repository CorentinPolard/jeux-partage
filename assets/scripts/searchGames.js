import TomSelect from "tom-select";
import "tom-select/dist/css/tom-select.default.min.css";

document.addEventListener("turbo:load", () => {
    const gamesList = document.querySelector(".games-list");

    if (gamesList) {
        new TomSelect(".games-list", {
            plugins: ['remove_button'],
            sortField: {
                field: "text",
                direction: "asc"
            }
        });
    }
});