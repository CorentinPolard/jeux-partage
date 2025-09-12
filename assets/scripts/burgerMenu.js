const burgerButton = document.querySelector(".burger-btn");
const navList = document.querySelector(".nav-list");
if (burgerButton) {
    burgerButton.addEventListener("click", () => {
        if (navList) {
            navList.classList.toggle("active");
            document.body.classList.toggle("no-scroll");
        }
    });
}