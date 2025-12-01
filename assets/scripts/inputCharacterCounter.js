const countersHTML = document.querySelectorAll("div[data-counter]");

countersHTML.forEach((counter) => {
    const input = document.querySelector("#" + counter.dataset.counter);
    if (input) {
        const update = () => {
            counter.textContent = `${input.value.length}/150`;
        };

        input.addEventListener("input", update);

        update();
    }
});