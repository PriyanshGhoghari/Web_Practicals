const themeButton = document.getElementById("themeButton");
const themeIcon = themeButton.querySelector("img");

themeButton.addEventListener("click", function () {
    document.body.classList.toggle("dark-mode");

    if (document.body.classList.contains("dark-mode")) {
        themeIcon.src = "Images/sun.png";
    } else {
        themeIcon.src = "Images/moon.png";
    }
});