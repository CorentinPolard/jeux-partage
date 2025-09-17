const form = document.querySelector("#registrationForm")
const passwordInput = document.querySelector("#registration_form_plainPassword_first");
const passwordHelp = document.querySelector(".password-help");
const passwordValidators = document.querySelectorAll(".password-validator");
const password = passwordInput.value;
const regex = /^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^\w\d\s:])([^\s]){8,}$/;

passwordInput.addEventListener("input", () => {
    passwordHelp.classList.remove("hidden");
    if (!regex.test(passwordInput.value)) {
        passwordValidators.forEach(span => {
            span.style.color = "red";
        })
    } else {
        passwordValidators.forEach(span => {
            span.style.color = "green";
        })
    }
})

form.addEventListener("submit", (event) => {
    if (!regex.test(password)) {
        event.preventDefault();
        alert("Votre mot de passe ne respecte pas les critères requis !");
        passwordHelp.classList.remove("hidden");
    }
});