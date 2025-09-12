const modalBackground = document.querySelector("#modal-background");

const deleteButtons = document.querySelectorAll(".delete-button");
deleteButtons.forEach(button => {
    button.addEventListener("click", (e) => {
        e.preventDefault();
        if (modalBackground) {
            modalBackground.classList.remove("hidden");
            modalBackground.classList.add("modal-background")

            confirmedDelete = document.querySelector(".delete-entity");
            confirmedDelete.setAttribute("href", e.currentTarget.getAttribute("href"));
        }
    })
})

const closeModalButtons = document.querySelectorAll(".close-modal");
closeModalButtons.forEach(button => {
    button.addEventListener("click", () => {
        modalBackground.classList.remove("modal-background")
        modalBackground.classList.add("hidden");
    })
})
