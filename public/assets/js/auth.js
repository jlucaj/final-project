document.getElementById("loginForm").addEventListener("submit", async (e) => {
    e.preventDefault();

    const username = document.getElementById("username").value.trim();
    const password = document.getElementById("password").value;
    const errorContainer = document.getElementById("errorContainer");

    // clear previous errors
    errorContainer.innerHTML = "";
    errorContainer.classList.add("d-none");

    // validate form
    let errors = [];

    if (!username) {
        errors.push("Username is required");
    }

    if (!password) {
        errors.push("Password is required");
    }

    // display client-side validation errors
    if (errors.length > 0) {
        errorContainer.innerHTML = errors.map(error => `<div>${error}</div>`).join("");
        errorContainer.classList.remove("d-none");
        return;
    }

    // submit login
    try {
        const response = await fetch("/api/auth/login", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ username, password })
        });

        const result = await response.json();

        if (result.success) {
            // login successful, redirect to home page
            window.location.href = "/";
        } else {
            // display server-side validation errors
            let errorMessages = [];

            if (result.errors) {
                for (const key in result.errors) {
                    errorMessages.push(result.errors[key]);
                }
            } else {
                errorMessages.push("Login failed. Please check your username and password.");
            }

            errorContainer.innerHTML = errorMessages.map(error => `<div>${error}</div>`).join("");
            errorContainer.classList.remove("d-none");
        }
    } catch (error) {
        console.error("Login error:", error);
        errorContainer.innerHTML = "<div>An unexpected error occurred. Please try again.</div>";
        errorContainer.classList.remove("d-none");
    }
});
