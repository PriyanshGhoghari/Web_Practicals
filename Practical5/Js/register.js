const registerForm = document.getElementById("registerForm");

registerForm.addEventListener("submit", function (event) {
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

    if (!emailRegex.test(email)) {
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

    alert("Registration successful");
    registerForm.reset();
});
