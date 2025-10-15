const modalBackground = document.querySelector("#modal-background");

const deleteForms = document.querySelectorAll(".delete-form");
deleteForms.forEach(deleteForm => {
    deleteForm.addEventListener("submit", (e) => {
        e.preventDefault();
        if (modalBackground) {
            modalBackground.classList.remove("hidden");
            modalBackground.classList.add("modal-background")

            const confirmedDelete = document.querySelector(".delete-entity");
            confirmedDelete.addEventListener("click", () => deleteForm.submit())
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
