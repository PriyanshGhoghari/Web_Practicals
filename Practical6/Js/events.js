const themeButton = document.getElementById("themeButton");
const themeIcon = themeButton.querySelector("img");

const eventsContainer = document.getElementById("eventsContainer");
const searchInput = document.getElementById("searchInput");
const categoryFilter = document.getElementById("categoryFilter");
const sortSelect = document.getElementById("sortSelect");
const pagination = document.getElementById("pagination");
const loadingMessage = document.getElementById("loadingMessage");
const errorMessage = document.getElementById("errorMessage");
const resultCount = document.getElementById("resultCount");

let events = [];
let filteredEvents = [];
let currentPage = 1;

const eventsPerPage = 6;

themeButton.addEventListener("click", function () {
    document.body.classList.toggle("dark-mode");

    if (document.body.classList.contains("dark-mode")) {
        themeIcon.src = "Images/sun.png";
    } else {
        themeIcon.src = "Images/moon.png";
    }
});

async function fetchEvents() {
    try {
        loadingMessage.style.display = "block";
        errorMessage.textContent = "";

        const response = await fetch("data/events.json");

        if (!response.ok) {
            throw new Error("Unable to load events.");
        }

        events = await response.json();

        populateCategories();

        filteredEvents = [...events];

        loadingMessage.style.display = "none";

        applyFilters();
    } catch (error) {
        loadingMessage.style.display = "none";
        errorMessage.textContent = "Unable to load events. Please try again.";
    }
}

function populateCategories() {
    const categories = [...new Set(events.map(event => event.category))];

    categories.sort();

    categories.forEach(category => {
        const option = document.createElement("option");

        option.value = category;
        option.textContent = category;

        categoryFilter.appendChild(option);
    });
}

function applyFilters() {
    const searchText = searchInput.value.trim().toLowerCase();
    const selectedCategory = categoryFilter.value;
    const selectedSort = sortSelect.value;

    filteredEvents = events.filter(event => {
        const matchesSearch =
            event.title.toLowerCase().includes(searchText) ||
            event.venue.toLowerCase().includes(searchText) ||
            event.description.toLowerCase().includes(searchText);

        const matchesCategory =
            selectedCategory === "all" ||
            event.category === selectedCategory;

        return matchesSearch && matchesCategory;
    });

    sortEvents(selectedSort);

    currentPage = 1;

    renderEvents();
    renderPagination();
}

function sortEvents(sortType) {
    if (sortType === "date-asc") {
        filteredEvents.sort((a, b) => new Date(a.date) - new Date(b.date));
    }

    if (sortType === "date-desc") {
        filteredEvents.sort((a, b) => new Date(b.date) - new Date(a.date));
    }

    if (sortType === "title-asc") {
        filteredEvents.sort((a, b) =>
            a.title.localeCompare(b.title)
        );
    }

    if (sortType === "title-desc") {
        filteredEvents.sort((a, b) =>
            b.title.localeCompare(a.title)
        );
    }
}

function renderEvents() {
    eventsContainer.innerHTML = "";

    if (filteredEvents.length === 0) {
        resultCount.textContent = "No events found.";
        return;
    }

    const startIndex = (currentPage - 1) * eventsPerPage;
    const endIndex = startIndex + eventsPerPage;

    const pageEvents = filteredEvents.slice(startIndex, endIndex);

    resultCount.textContent = `Showing ${startIndex + 1}-${Math.min(
        endIndex,
        filteredEvents.length
    )} of ${filteredEvents.length} events`;

    pageEvents.forEach(event => {
        const article = document.createElement("article");

        article.className = "event-card";

        article.innerHTML = `
            <div class="event-image">
                <img src="${event.image}" alt="${event.title}" />
            </div>

            <div class="event-content">
                <h2>${event.title}</h2>

                <p class="event-info">
                    ${formatDate(event.date)} • ${event.venue}
                </p>

                <span class="event-category">
                    ${event.category}
                </span>

                <p class="event-description">
                    ${event.description}
                </p>

                <a href="#" class="event-link">
                    Register for ${event.title} →
                </a>
            </div>
        `;

        eventsContainer.appendChild(article);
    });
}

function renderPagination() {
    pagination.innerHTML = "";

    const totalPages = Math.ceil(filteredEvents.length / eventsPerPage);

    if (totalPages <= 1) {
        return;
    }

    const previousButton = document.createElement("button");

    previousButton.textContent = "Previous";
    previousButton.disabled = currentPage === 1;

    previousButton.addEventListener("click", function () {
        currentPage--;
        renderEvents();
        renderPagination();
    });

    pagination.appendChild(previousButton);

    for (let page = 1; page <= totalPages; page++) {
        const pageButton = document.createElement("button");

        pageButton.textContent = page;

        if (page === currentPage) {
            pageButton.classList.add("active-page");
        }

        pageButton.addEventListener("click", function () {
            currentPage = page;
            renderEvents();
            renderPagination();
        });

        pagination.appendChild(pageButton);
    }

    const nextButton = document.createElement("button");

    nextButton.textContent = "Next";
    nextButton.disabled = currentPage === totalPages;

    nextButton.addEventListener("click", function () {
        currentPage++;
        renderEvents();
        renderPagination();
    });

    pagination.appendChild(nextButton);
}

function formatDate(dateString) {
    const date = new Date(dateString);

    return date.toLocaleDateString("en-IN", {
        day: "numeric",
        month: "short"
    });
}

searchInput.addEventListener("input", applyFilters);
categoryFilter.addEventListener("change", applyFilters);
sortSelect.addEventListener("change", applyFilters);

fetchEvents();