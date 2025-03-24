document.addEventListener("DOMContentLoaded", function () {
    let form = document.querySelector("form");
    if (form) {
        form.addEventListener("submit", function () {
            alert("Form submitted successfully!");
        });
    }
});
