const modalBackground = document.querySelector("#modal-background");
const confirmedDelete = document.querySelector(".delete-entity");
let currentForm = null; // formulaire en cours de suppression

const deleteForms = document.querySelectorAll(".delete-form");
deleteForms.forEach(deleteForm => {
    deleteForm.addEventListener("submit", (e) => {
        e.preventDefault();
        currentForm = deleteForm; // on garde le formulaire courant

        if (!modalBackground) return;

        modalBackground.classList.remove("hidden");
        modalBackground.classList.add("modal-background");
    });
});

// Listener unique pour le bouton de confirmation
const confirmHandler = async () => {
    if (!currentForm) return;

    if (currentForm.dataset.delete === "message") {
        const formData = new FormData(currentForm);
        try {
            const response = await fetch(currentForm.action, {
                method: "POST",
                body: formData
            });
            if (!response.ok) alert("Erreur lors de la suppression");
        } catch (err) {
            console.error(err);
            alert("Erreur réseau");
        }
    } else {
        currentForm.submit();
    }

    // Fermeture de la modal
    modalBackground.classList.remove("modal-background");
    modalBackground.classList.add("hidden");
    currentForm = null;
};

confirmedDelete.addEventListener("click", confirmHandler);

// Fermeture de la modal sans action
const closeModalButtons = document.querySelectorAll(".close-modal");
closeModalButtons.forEach(button => {
    button.addEventListener("click", () => {
        modalBackground.classList.remove("modal-background");
        modalBackground.classList.add("hidden");
        currentForm = null;
    });
});
