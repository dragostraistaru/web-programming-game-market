
var style = document.createElement("style");
style.textContent = [
    ".l-invalid {",
    "  border: 2px solid #dc2626 !important;",
    "  background-color: #fff5f5 !important;",
    "  border-radius: 4px;",
    "}",
    ".l-eroare {",
    "  color: #dc2626;",
    "  font-size: 0.78em;",
    "  margin-top: 3px;",
    "  display: none;",
    "}"
].join("\n");
document.head.appendChild(style);


function setValidL(camp, eroareId, eValid) {
    if (!camp) return;
    var eroare = eroareId ? document.getElementById(eroareId) : null;
    if (eValid) {
        camp.classList.remove("l-invalid");
        if (eroare) eroare.style.display = "none";
    } else {
        camp.classList.add("l-invalid");
        if (eroare) eroare.style.display = "block";
    }
}

function adaugaEroareL(camp, mesaj, id) {
    if (!camp || document.getElementById(id)) return;
    var span = document.createElement("span");
    span.id = id;
    span.className = "l-eroare";
    span.textContent = mesaj;
    camp.parentNode.insertBefore(span, camp.nextSibling);
}


document.addEventListener("DOMContentLoaded", function () {

    var form = document.querySelector("form[name='listingForm']");
    if (!form) return;

    var campSearch = form.querySelector("input[name='search']");
    var campPret = form.querySelector("input[name='max_price']");
    var campPromo = form.querySelector("input[name='promo_code']");

    if (campSearch) adaugaEroareL(campSearch, "Introdu un termen de căutare (minim 2 caractere).", "err-search");
    if (campPret) adaugaEroareL(campPret, "Prețul maxim trebuie să fie între 1 și 9999 RON.", "err-pret");
    if (campPromo) adaugaEroareL(campPromo, "Codul promoțional poate conține doar litere și cifre.", "err-promo");

    var btnSearch = form.querySelector("input[name='filter']");
    if (btnSearch) {
        btnSearch.addEventListener("click", function (e) {
            e.preventDefault();
            var valid = true;

            if (campSearch && campSearch.value.trim().length > 0 && campSearch.value.trim().length < 2) {
                setValidL(campSearch, "err-search", false);
                valid = false;
            } else if (campSearch) {
                setValidL(campSearch, "err-search", true);
            }

            if (campPret) {
                var pret = parseFloat(campPret.value);
                if (isNaN(pret) || pret < 1 || pret > 9999) {
                    setValidL(campPret, "err-pret", false);
                    valid = false;
                } else {
                    setValidL(campPret, "err-pret", true);
                }
            }

            if (campPromo && campPromo.value.trim().length > 0) {
                if (!/^[a-zA-Z0-9]+$/.test(campPromo.value.trim())) {
                    setValidL(campPromo, "err-promo", false);
                    valid = false;
                } else {
                    setValidL(campPromo, "err-promo", true);
                }
            } else if (campPromo) {
                setValidL(campPromo, "err-promo", true);
            }

            if (valid) {
                alert("Filtrele au fost aplicate!");
            }
        });
    }

    var btnBuy = form.querySelector("input[name='buy']");
    if (btnBuy) {
        btnBuy.addEventListener("click", function (e) {
            e.preventDefault();

            var selectGen = form.querySelector("select[name='genre']");
            var optiuniSelectate = selectGen
                ? Array.from(selectGen.options).filter(function (o) { return o.selected; }).length
                : 0;

            if (optiuniSelectate === 0) {
                if (selectGen) {
                    selectGen.style.border = "2px solid #dc2626";
                    selectGen.style.backgroundColor = "#fff5f5";
                }
                alert("Selectează cel puțin un gen pentru a cumpăra!");
            } else {
                if (selectGen) {
                    selectGen.style.border = "";
                    selectGen.style.backgroundColor = "";
                }
                alert("Redirecționare spre checkout...");
            }
        });
    }
});