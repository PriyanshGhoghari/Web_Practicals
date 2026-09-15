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

const slides = document.querySelector(".slides");
const prevButton = document.querySelector(".prev");
const nextButton = document.querySelector(".next");
const totalSlides = slides.children.length;
let currentSlide = 0;

function showSlide() {
    slides.style.transform = `translateX(${currentSlide * -100}%)`;
}

function nextSlide() {
    currentSlide++;

    if (currentSlide === totalSlides) {
        currentSlide = 0;
    }

    showSlide();
}

function previousSlide() {
    currentSlide--;

    if (currentSlide < 0) {
        currentSlide = totalSlides - 1;
    }

    showSlide();
}

nextButton.addEventListener("click", nextSlide);
prevButton.addEventListener("click", previousSlide);


