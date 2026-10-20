const loginForm = document.getElementById("loginForm");
const loginError = document.getElementById("loginError");

if (new URLSearchParams(window.location.search).get("timeout") === "1") {
    loginError.textContent = "Your session expired. Please log in again.";
    loginError.hidden = false;
}

loginForm.addEventListener("submit", async function (event) {
    event.preventDefault();
    loginError.hidden = true;

    const button = loginForm.querySelector("button");
    button.disabled = true;

    try {
        const response = await fetch("login.php", {
            method: "POST",
            body: new FormData(loginForm)
        });
        const result = await response.json();

        if (result.success) {
            window.location.href = result.redirect;
        } else {
            loginError.textContent = result.message;
            loginError.hidden = false;
        }
    } catch (error) {
        loginError.textContent = "Could not log in. Please try again.";
        loginError.hidden = false;
    }

    button.disabled = false;
});
