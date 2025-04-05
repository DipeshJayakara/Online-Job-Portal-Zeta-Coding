document.addEventListener("DOMContentLoaded", function () {
    console.log("Script loaded!");

    // Form Submission Alert (Generic)
    let form = document.querySelector("form");
    if (form) {
        form.addEventListener("submit", function () {
            alert("Form submitted successfully!");
        });
    }

    // Job Application (AJAX)
    document.querySelectorAll(".apply-btn").forEach(button => {
        button.addEventListener("click", function (event) {
            event.preventDefault();
            let jobId = this.dataset.jobId;

            fetch("apply.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `job_id=${jobId}`
            })
            .then(response => response.text())
            .then(data => {
                alert("Application Submitted Successfully!");
                window.location.reload();
            })
            .catch(error => console.error("Error:", error));
        });
    });

    // Login Form Validation
    const loginForm = document.getElementById("loginForm");
    if (loginForm) {
        loginForm.addEventListener("submit", function (e) {
            let email = document.getElementById("email").value.trim();
            let password = document.getElementById("password").value.trim();

            if (email === "" || password === "") {
                alert("Both fields are required!");
                e.preventDefault();
            }
        });
    }

    // Registration Form Validation
    const registerForm = document.getElementById("registerForm");
    if (registerForm) {
        registerForm.addEventListener("submit", function (e) {
            let name = document.getElementById("name").value.trim();
            let email = document.getElementById("email").value.trim();
            let password = document.getElementById("password").value.trim();
            let role = document.getElementById("role").value.trim();

            if (name === "" || email === "" || password === "" || role === "") {
                alert("All fields are required!");
                e.preventDefault();
            }
        });
    }

    // ATS Resume Checker
    const atsForm = document.getElementById("atsForm");
    if (atsForm) {
        atsForm.addEventListener("submit", function (e) {
            e.preventDefault();
            let resumeText = document.getElementById("resumeText").value.trim();

            if (resumeText === "") {
                alert("Please enter resume text!");
                return;
            }

            fetch("ats-checker.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `resume_text=${resumeText}`
            })
            .then(response => response.text())
            .then(score => {
                document.getElementById("atsResult").innerHTML = `<strong>${score}</strong>`;
            })
            .catch(error => console.error("Error:", error));
        });
    }

    // Mobile Menu Toggle
    const menuToggle = document.querySelector(".menu-toggle");
    const navLinks = document.querySelector(".nav-links");
    if (menuToggle && navLinks) {
        menuToggle.addEventListener("click", function () {
            navLinks.classList.toggle("active");
        });
    }

    // Job Filter Validation (Ensures min salary is not greater than max salary)
    const filterForm = document.querySelector("form[action='index.php']");
    if (filterForm) {
        filterForm.addEventListener("submit", function (e) {
            let salaryMin = document.getElementById("salary_min").value;
            let salaryMax = document.getElementById("salary_max").value;

            if (salaryMin && salaryMax && parseInt(salaryMin) > parseInt(salaryMax)) {
                alert("Minimum salary cannot be greater than maximum salary.");
                e.preventDefault();
            }
        });
    }
});
