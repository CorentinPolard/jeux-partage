export function initDeleteSystem() {
    const modalBackground = document.querySelector("#modal-background");
    const confirmedDelete = document.querySelector(".delete-entity");
    let currentForm = null;

    if (!modalBackground) return () => { };

    function openModal(form) {
        currentForm = form;
        modalBackground.classList.remove("hidden");
        modalBackground.classList.add("modal-background");
        // console.log(currentForm, confirmedDelete)
    }

    function closeModal() {
        if (document.activeElement) {
            document.activeElement.blur();
        }
        modalBackground.classList.remove("modal-background");
        modalBackground.classList.add("hidden");
        currentForm = null;
    }

    // Handle delete confirmation
    async function confirmHandler() {
        if (!currentForm) return;

        if (currentForm.dataset.delete === "message") {
            const formData = new FormData(currentForm);
            try {
                const response = await fetch(currentForm.action, {
                    method: "POST",
                    body: formData
                });
                if (!response.ok) {
                    alert("Erreur lors de la suppression");
                }
            } catch (err) {
                console.error("Delete error:", err);
                alert("Erreur réseau");
            }
        } else {
            currentForm.submit();
        }

        closeModal();
    }

    // Add listener to confirmation button
    if (confirmedDelete) {
        confirmedDelete.addEventListener("click", confirmHandler);
    }

    // Buttons to close the modal
    const closeModalButtons = document.querySelectorAll(".close-modal");
    closeModalButtons.forEach(button =>
        button.addEventListener("click", closeModal)
    );

    // Add listener for delete on forms
    function attachDeleteListener(form) {
        form.addEventListener("submit", (e) => {
            e.preventDefault();
            openModal(form);
        });
    }

    // Attach listeners to all delete forms
    document.querySelectorAll(".delete-form").forEach(attachDeleteListener);

    // Return the function to attach listeners to new forms (for mercure)
    return attachDeleteListener;
}
