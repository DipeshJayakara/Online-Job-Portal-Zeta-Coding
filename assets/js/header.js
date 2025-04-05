document.addEventListener("DOMContentLoaded", function () {
    const menuToggle = document.querySelector(".menu-toggle");
    const navLinks = document.querySelector(".nav-links");
    const accountBtn = document.querySelector(".account-btn");
    const accountMenu = document.getElementById("account-menu");

    // ✅ Toggle Mobile Menu
    menuToggle.addEventListener("click", function () {
        navLinks.classList.toggle("active");
    });

    // ✅ Toggle Account Menu
    if (accountBtn) {
        accountBtn.addEventListener("click", function (event) {
            event.stopPropagation(); // Prevent closing on immediate click
            accountMenu.classList.toggle("show");
        });

        // ✅ Close dropdown when clicking outside
        document.addEventListener("click", function (event) {
            if (!accountBtn.contains(event.target) && !accountMenu.contains(event.target)) {
                accountMenu.classList.remove("show");
            }
        });
    }
});
