const registerForm = document.getElementById("registerForm");

registerForm.addEventListener("submit", async function (event) {
    event.preventDefault();

    const name = document.getElementById("regName").value.trim();
    const email = document.getElementById("regEmail").value.trim();
    const mobile = document.getElementById("regPhone").value.trim();
    const password = document.getElementById("regPassword").value;
    const confirmPassword = document.getElementById("regConfirm").value;

    const nameRegex = /^[A-Za-z\s]{3,}$/;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const mobileRegex = /^[6-9][0-9]{9}$/;
    const passwordRegex = /^(?=.*[A-Za-z])(?=.*[0-9]).{6,}$/;

    if (!nameRegex.test(name)) {
        alert("Enter a valid name");
        return;
    }

    if (!emailRegex.test(email) || !document.getElementById("regEmail").checkValidity()) {
        alert("Enter a valid email");
        return;
    }

    if (!mobileRegex.test(mobile)) {
        alert("Enter a valid 10 digit mobile number");
        return;
    }

    if (!passwordRegex.test(password)) {
        alert("Password must be at least 6 characters and contain letters and numbers");
        return;
    }

    if (password !== confirmPassword) {
        alert("Passwords do not match");
        return;
    }

    const button = registerForm.querySelector("button");
    button.disabled = true;
    button.textContent = "Saving...";

    try {
        const response = await fetch("register.php", {
            method: "POST",
            body: new FormData(registerForm)
        });
        const result = await response.json();

        if (result.success) {
            registerForm.hidden = true;
            document.querySelector(".login-text").hidden = true;
            document.querySelector(".register-card h1").hidden = true;
            const successBox = document.getElementById("registrationSuccess");
            successBox.hidden = false;
            successBox.focus();
            registerForm.reset();
        } else {
            alert("Could not save registration");
        }
    } catch (error) {
        alert("Could not connect to the server");
    }

    button.disabled = false;
    button.textContent = "Sign up";
});
