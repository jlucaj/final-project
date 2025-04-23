document.getElementById("registerForm").addEventListener("submit", async (e) => {
    e.preventDefault();

    const username = document.getElementById("username").value.trim();
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirmPassword").value;
    const errorContainer = document.getElementById("errorContainer");

    // clear previous errors
    errorContainer.innerHTML = "";
    errorContainer.classList.add("d-none");

    // validate form
    let errors = [];

    if (username.length < 3 || username.length > 18) {
        errors.push("Username must be between 3 and 18 characters");
    }

    if (password.length < 6) {
        errors.push("Password must be at least 6 characters");
    }

    if (password !== confirmPassword) {
        errors.push("Passwords do not match");
    }

    // display client-side validation errors
    if (errors.length > 0) {
        errorContainer.innerHTML = errors.map(error => `<div>${error}</div>`).join("");
        errorContainer.classList.remove("d-none");
        return;
    }

    // submit registration
    try {
        const response = await fetch("/api/auth/register", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ username, password })
        });

        const result = await response.json();

        if (result.success) {
            // registration successful, redirect to login
            window.location.href = "/login?registered=true";
        } else {
            // display server-side validation errors
            let errorMessages = [];

            if (result.errors) {
                for (const key in result.errors) {
                    errorMessages.push(result.errors[key]);
                }
            } else {
                errorMessages.push("Registration failed. Please try again.");
            }

            errorContainer.innerHTML = errorMessages.map(error => `<div>${error}</div>`).join("");
            errorContainer.classList.remove("d-none");
        }
    } catch (error) {
        console.error("Registration error:", error);
        errorContainer.innerHTML = "<div>An unexpected error occurred. Please try again.</div>";
        errorContainer.classList.remove("d-none");
    }
});