export function initDeleteSystem() {
    const modalBackground = document.querySelector("#modal-background");
    const confirmedDelete = document.querySelector(".delete-entity");
    let currentForm = null;

    // Fonction d’ouverture du modal
    function openModal(form) {
        currentForm = form;
        if (!modalBackground) return;
        modalBackground.classList.remove("hidden");
        modalBackground.classList.add("modal-background");
    }

    // Fonction de fermeture du modal
    function closeModal() {
        if (!modalBackground) return;

        if (document.activeElement) {
            document.activeElement.blur();
        }

        modalBackground.classList.remove("modal-background");
        modalBackground.classList.add("hidden");
        currentForm = null;
    }

    // Confirmation de suppression
    async function confirmHandler() {
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

        closeModal();
    }

    // Ajout listener sur le bouton de confirmation
    if (confirmedDelete) {
        confirmedDelete.addEventListener("click", confirmHandler);
    }

    // Boutons pour fermer la modale
    const closeModalButtons = document.querySelectorAll(".close-modal");
    closeModalButtons.forEach(button =>
        button.addEventListener("click", closeModal)
    );

    // Fonction d'attache la logique à un formulaire
    function attachDeleteListener(form) {
        form.addEventListener("submit", (e) => {
            e.preventDefault();
            openModal(form);
        });
    }

    // Attache à tous les formulaires existants
    document.querySelectorAll(".delete-form").forEach(attachDeleteListener);

    // Et retourne la fonction pour l’utiliser sur les nouveaux messages
    return attachDeleteListener;
}
