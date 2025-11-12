const form = document.querySelector("#registrationForm");
const passwordInput = document.querySelector("#registration_form_plainPassword_first");
const passwordHelp = document.querySelector(".password-help");
const passwordValidators = document.querySelectorAll(".password-validator");

// Compile regex once 
const PASSWORD_REGEX = /^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^\w\d\s:])([^\s]){12,}$/;
const VALID_COLOR = "green";
const INVALID_COLOR = "red";

// Helper function to update validator colors
function updateValidatorColors(isValid) {
    const color = isValid ? VALID_COLOR : INVALID_COLOR;
    passwordValidators.forEach(span => {
        span.style.color = color;
    });
}

// Password validation on input
passwordInput.addEventListener("input", () => {
    passwordHelp.classList.remove("hidden");
    updateValidatorColors(PASSWORD_REGEX.test(passwordInput.value));
});

// Form submission validation
form.addEventListener("submit", (event) => {
    if (!PASSWORD_REGEX.test(passwordInput.value)) {
        event.preventDefault();
        alert("Votre mot de passe ne respecte pas les critères requis !");
        passwordHelp.classList.remove("hidden");
    }
});