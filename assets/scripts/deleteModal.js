export function initDeleteSystem() {
    const modalBackground = document.querySelector("#modal-background");
    const confirmedDelete = document.querySelector(".delete-entity");
    let currentForm = null;

    // Fonction to open the modal
    function openModal(form) {
        currentForm = form;
        if (!modalBackground) return;
        modalBackground.classList.remove("hidden");
        modalBackground.classList.add("modal-background");
    }

    // Function to close the modal
    function closeModal() {
        if (!modalBackground) return;

        if (document.activeElement) {
            document.activeElement.blur();
        }

        modalBackground.classList.remove("modal-background");
        modalBackground.classList.add("hidden");
        currentForm = null;
    }

    // Confirmation for delete
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

    // Add listener to show the modal before delete
    if (confirmedDelete) {
        confirmedDelete.addEventListener("click", confirmHandler);
    }

    // Buttons to close the modal
    const closeModalButtons = document.querySelectorAll(".close-modal");
    closeModalButtons.forEach(button =>
        button.addEventListener("click", closeModal)
    );

    // Adding listener for delete on forms
    function attachDeleteListener(form) {
        form.addEventListener("submit", (e) => {
            e.preventDefault();
            openModal(form);
        });
    }

    // Adding attachDeleteListener on all forms
    document.querySelectorAll(".delete-form").forEach(attachDeleteListener);

    // Return the function to use them on new messages (for mercure)
    return attachDeleteListener;
}
