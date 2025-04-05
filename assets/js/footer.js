document.addEventListener("DOMContentLoaded", function () {
    // Highlight the active link in the footer
    let links = document.querySelectorAll(".footer-section nav ul li a");
    let currentURL = window.location.href;

    links.forEach(link => {
        if (link.href === currentURL) {
            link.style.fontWeight = "bold";
            link.style.color = "#e67e22";
        }
    });

    // Smooth scrolling for internal links (if applicable)
    document.querySelectorAll("a[href^='#']").forEach(anchor => {
        anchor.addEventListener("click", function (e) {
            e.preventDefault();
            let targetID = this.getAttribute("href").substring(1);
            let targetElement = document.getElementById(targetID);
            if (targetElement) {
                targetElement.scrollIntoView({ behavior: "smooth" });
            }
        });
    });
});
