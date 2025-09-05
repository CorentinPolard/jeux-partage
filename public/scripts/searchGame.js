document.addEventListener("turbo:load", () => {
    new TomSelect(".games-list", {
        sortField: {
            field: "text",
            direction: "asc"
        }
    });
});