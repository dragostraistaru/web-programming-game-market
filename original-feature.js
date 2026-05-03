document.addEventListener("DOMContentLoaded", function () {
    const estimateBtn = document.getElementById("estimateBtn");
    if (!estimateBtn) return;

    const gamesCatalog = [
        { name: "Elden Ring", price: 89.99 },
        { name: "Cyberpunk 2077", price: 59.99 },
        { name: "FIFA 25", price: 149.99 },
        { name: "The Witcher 3", price: 39.99 },
        { name: "Hades", price: 49.99 }
    ];

    function gasesteJoc(cautare, maxPrice) {
        const text = cautare.trim().toLowerCase();

        if (text.length > 0) {
            const gasit = gamesCatalog.find(function (game) {
                return game.name.toLowerCase().includes(text);
            });

            if (gasit) {
                return gasit;
            }
        }

        const pretMaxim = parseFloat(maxPrice);
        const posibil = gamesCatalog.find(function (game) {
            return game.price <= pretMaxim;
        });

        return posibil || gamesCatalog[0];
    }

    estimateBtn.addEventListener("click", function () {
        const searchValue = document.getElementById("searchGame").value;
        const maxPriceValue = document.getElementById("maxPrice").value;
        const promoCode = document.getElementById("promoCode").value.trim().toUpperCase();
        const instantDelivery = document.getElementById("instantDelivery").checked;
        const manualDelivery = document.getElementById("manualDelivery").checked;

        const game = gasesteJoc(searchValue, maxPriceValue);

        let basePrice = game.price;
        let discount = 0;
        let deliveryFee = 0;
        let status = "Ofertă standard";

        if (promoCode === "SAVE10") {
            discount = basePrice * 0.10;
            status = "Cod promo SAVE10 aplicat";
        } else if (promoCode === "VIP20") {
            discount = basePrice * 0.20;
            status = "Cod promo VIP20 aplicat";
        } else if (promoCode.length > 0) {
            status = "Cod promo invalid";
        }

        if (instantDelivery && !manualDelivery) {
            deliveryFee = 5;
        } else if (!instantDelivery && manualDelivery) {
            deliveryFee = 2;
        } else if (instantDelivery && manualDelivery) {
            deliveryFee = 6;
        } else {
            deliveryFee = 0;
        }

        let finalPrice = basePrice - discount + deliveryFee;

        if (finalPrice < 0) {
            finalPrice = 0;
        }

        if (finalPrice <= 40) {
            status += " | Excellent deal";
        } else if (finalPrice <= 80) {
            status += " | Good deal";
        } else {
            status += " | Premium offer";
        }

        document.getElementById("estGameName").textContent = game.name;
        document.getElementById("estBasePrice").textContent = basePrice.toFixed(2) + " RON";
        document.getElementById("estDiscount").textContent = discount.toFixed(2) + " RON";
        document.getElementById("estDelivery").textContent = deliveryFee.toFixed(2) + " RON";
        document.getElementById("estFinalPrice").textContent = finalPrice.toFixed(2) + " RON";
        document.getElementById("estStatus").textContent = status;
    });
});