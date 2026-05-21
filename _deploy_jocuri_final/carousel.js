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

$(document).ready(function () {
    const $slideContainer = $("#carouselSlide");
    const $slideLink = $("#carouselLink");
    const $slideText = $("#carouselText");
    const $prevBtn = $("#prevBtn");
    const $nextBtn = $("#nextBtn");

    function afiseazaSlide(index) {
        const slide = slides[index];
        $slideContainer.fadeOut(400, function() {
            $slideContainer.css("background-image", "url('" + slide.image + "')");
            $slideLink.attr("href", slide.link);
            $slideText.text(slide.text);
            $slideContainer.fadeIn(400);
        });
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

    if ($slideContainer.length && $slideLink.length && $slideText.length) {
        afiseazaSlide(currentSlide);
        pornesteCarousel();
    }

    $nextBtn.on("click", function () {
        slideUrmator();
        reseteazaCarousel();
    });

    $prevBtn.on("click", function () {
        slideAnterior();
        reseteazaCarousel();
    });
});
