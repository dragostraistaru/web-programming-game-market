const slides = [
    {
        link: "#elden-ring",
        text: "Elden Ring - ofertă specială pentru fanii RPG",
        image: "images/eldenring.jpg"
    },
    {
        link: "#cyberpunk-2077",
        text: "Cyberpunk 2077 - reduceri pentru jocuri open world",
        image: "images/cyberpunk.jpg"
    },
    {
        link: "#fifa-25",
        text: "FIFA 25 - promoții pentru pasionații de jocuri sportive",
        image: "images/fifa25.jpg"
    },
    {
        link: "#witcher-3",
        text: "The Witcher 3 - unul dintre cele mai apreciate RPG-uri",
        image: "images/witcher3.jpg"
    }
];

let currentSlide = 0;

let intervalId = null;

const slideContainer = document.getElementById("carouselSlide");
const slideLink = document.getElementById("carouselLink");
const slideText = document.getElementById("carouselText");
const prevBtn = document.getElementById("prevBtn");
const nextBtn = document.getElementById("nextBtn");

function afiseazaSlide(index) {
    const slide = slides[index];
    slideContainer.style.backgroundImage = "url('" + slide.image + "')";
    slideLink.href = slide.link;
    slideText.textContent = slide.text;
}

function slideUrmator() {
    currentSlide = (currentSlide + 1) % slides.length;
    afiseazaSlide(currentSlide);
}

function slideAnterior() {
    currentSlide = (currentSlide - 1 + slides.length) % slides.length;
    afiseazaSlide(currentSlide);
}

function pornesteCarousel() {
    intervalId = setInterval(slideUrmator, 3000);
}

function reseteazaCarousel() {
    clearInterval(intervalId);
    pornesteCarousel();
}

if (slideContainer && slideLink && slideText) {
    afiseazaSlide(currentSlide);
    pornesteCarousel();
}

if (nextBtn) {
    nextBtn.addEventListener("click", function () {
        slideUrmator();
        reseteazaCarousel();
    });
}

if (prevBtn) {
    prevBtn.addEventListener("click", function () {
        slideAnterior();
        reseteazaCarousel();
    });
}