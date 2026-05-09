$(document).ready(function () {

    $("<style>").text([
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
    ].join("\n")).appendTo("head");

    function setValidL($camp, eroareId, eValid) {
        if (!$camp.length) return;
        if (eValid) {
            $camp.removeClass("l-invalid");
            if (eroareId) {
                $("#" + eroareId).hide();
            }
        } else {
            $camp.addClass("l-invalid");
            if (eroareId) {
                $("#" + eroareId).show();
            }
        }
    }

    function adaugaEroareL($camp, mesaj, id) {
        if (!$camp.length || $("#" + id).length) return;
        $("<span>").attr("id", id).addClass("l-eroare").text(mesaj).insertAfter($camp);
    }

    var $form = $("form[name='listingForm']");
    if (!$form.length) return;

    var $campSearch = $form.find("input[name='search']");
    var $campPret = $form.find("input[name='max_price']");
    var $campPromo = $form.find("input[name='promo_code']");

    if ($campSearch.length) adaugaEroareL($campSearch, "Introdu un termen de căutare (minim 2 caractere).", "err-search");
    if ($campPret.length) adaugaEroareL($campPret, "Prețul maxim trebuie să fie între 1 și 9999 RON.", "err-pret");
    if ($campPromo.length) adaugaEroareL($campPromo, "Codul promoțional poate conține doar litere și cifre.", "err-promo");

    var $btnSearch = $form.find("input[name='filter']");
    $btnSearch.on("click", function (e) {
        e.preventDefault();
        var valid = true;

        if ($campSearch.length && $campSearch.val().trim().length > 0 && $campSearch.val().trim().length < 2) {
            setValidL($campSearch, "err-search", false);
            valid = false;
        } else if ($campSearch.length) {
            setValidL($campSearch, "err-search", true);
        }

        if ($campPret.length) {
            var pret = parseFloat($campPret.val());
            if (isNaN(pret) || pret < 1 || pret > 9999) {
                setValidL($campPret, "err-pret", false);
                valid = false;
            } else {
                setValidL($campPret, "err-pret", true);
            }
        }

        if ($campPromo.length && $campPromo.val().trim().length > 0) {
            if (!/^[a-zA-Z0-9]+$/.test($campPromo.val().trim())) {
                setValidL($campPromo, "err-promo", false);
                valid = false;
            } else {
                setValidL($campPromo, "err-promo", true);
            }
        } else if ($campPromo.length) {
            setValidL($campPromo, "err-promo", true);
        }

        if (valid) {
            alert("Filtrele au fost aplicate!");
        }
    });

    var $btnBuy = $form.find("input[name='buy']");
    $btnBuy.on("click", function (e) {
        e.preventDefault();

        var $selectGen = $form.find("select[name='genre']");
        var optiuniSelectate = $selectGen.find("option:selected").length;

        if (optiuniSelectate === 0) {
            $selectGen.css({
                "border": "2px solid #dc2626",
                "background-color": "#fff5f5"
            });
            alert("Selectează cel puțin un gen pentru a cumpăra!");
        } else {
            $selectGen.css({
                "border": "",
                "background-color": ""
            });
            alert("Redirecționare spre checkout...");
        }
    });

});