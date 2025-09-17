const deleteAccountLink = document.querySelector(".delete-account");
const deleteAccountForm = document.querySelector("#delete-account-form");
const modalBackground = document.querySelector("#modal-background");

deleteAccountLink.addEventListener("click", () => {
    if (modalBackground) {
        modalBackground.classList.remove("hidden");
        modalBackground.classList.add("modal-background");

        const deleteAccountButton = document.querySelector("#confirm-delete-account");
        deleteAccountButton.addEventListener("click", () => {
            deleteAccountForm.submit();
        })
    }
})

const closeModalButtons = document.querySelectorAll(".close-modal");
closeModalButtons.forEach(button => {
    button.addEventListener("click", () => {
        modalBackground.classList.remove("modal-background")
        modalBackground.classList.add("hidden");
    })
})