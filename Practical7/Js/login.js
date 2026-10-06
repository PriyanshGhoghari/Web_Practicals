const loginForm = document.getElementById("loginForm");

loginForm.addEventListener("submit", function (event) {
    event.preventDefault();

    const email = document.getElementById("loginEmail").value.trim();
    const password = document.getElementById("loginPassword").value;

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const passwordRegex = /^(?=.*[A-Za-z])(?=.*[0-9]).{6,}$/;

    if (!emailRegex.test(email)) {
        alert("Enter a valid email");
        return;
    }

    if (!passwordRegex.test(password)) {
        alert("Enter a valid password");
        return;
    }

    alert("Login successful");
    loginForm.reset();
});
