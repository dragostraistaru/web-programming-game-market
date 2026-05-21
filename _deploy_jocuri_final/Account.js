$(document).ready(function () {

    function marcheazaInvalid(campId, eroareId) {
        $("#" + campId).addClass("invalid");
        if (eroareId) {
            $("#" + eroareId).addClass("vizibil");
        }
    }

    function marcheazaValid(campId, eroareId) {
        $("#" + campId).removeClass("invalid");
        if (eroareId) {
            $("#" + eroareId).removeClass("vizibil");
        }
    }

    function emailValid(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    $("#formLogin").on("submit", function (e) {
        e.preventDefault();
        var valid = true;

        $("#succes-login").removeClass("vizibil");

        var email = $("#login-email").val().trim();
        if (!emailValid(email)) {
            marcheazaInvalid("login-email", "err-login-email");
            valid = false;
        } else {
            marcheazaValid("login-email", "err-login-email");
        }

        var parola = $("#login-parola").val();
        if (parola.length === 0) {
            marcheazaInvalid("login-parola", "err-login-parola");
            valid = false;
        } else {
            marcheazaValid("login-parola", "err-login-parola");
        }

        if (valid && $(this).attr("action") === "#") {
            $("#succes-login").addClass("vizibil");
        } else if (valid) {
            this.submit();
        }
    });

    $("#parola").on("input", function () {
        var val = $(this).val();
        var putere = 0;
        var label = "";
        var culoare = "";

        if (val.length >= 8) putere++;
        if (/[A-Z]/.test(val)) putere++;
        if (/[0-9]/.test(val)) putere++;
        if (/[^A-Za-z0-9]/.test(val)) putere++;

        if (val.length === 0) {
            label = "";
            culoare = "";
        } else if (putere <= 1) {
            label = "Slabă";
            culoare = "#dc2626";
        } else if (putere === 2) {
            label = "Medie";
            culoare = "#f59e0b";
        } else if (putere === 3) {
            label = "Bună";
            culoare = "#3b82f6";
        } else {
            label = "Foarte puternică";
            culoare = "#16a34a";
        }

        $("#putere-bar").css("width", (putere * 25) + "%").css("background-color", culoare);
        $("#putere-label").text(label).css("color", culoare);
    });

    $("#tara").on("change", function () {
        var taraSelectata = $(this).val();
        var $selectOras = $("#oras");

        $selectOras.empty();

        var $optDefault = $("<option>").val("").text("-- Selectează orașul sau regiunea --");
        $selectOras.append($optDefault);

        if (taraSelectata && typeof tariOrase !== "undefined" && tariOrase[taraSelectata]) {
            $.each(tariOrase[taraSelectata], function (index, oras) {
                var $opt = $("<option>").val(oras).text(oras);
                $selectOras.append($opt);
            });
        }

        marcheazaValid("oras", "err-oras");
    });

    function actualizeazaCampMagazin() {
        var tip = $("#tip-cont").val();
        var $campMagazin = $("#camp-magazin");

        if (tip === "vanzator" || tip === "ambele") {
            $campMagazin.show();
        } else {
            $campMagazin.hide();
            $("#nume-magazin").val("").removeClass("invalid");
            $("#err-magazin").removeClass("vizibil");
        }
    }

    $("#tip-cont").on("change", actualizeazaCampMagazin);
    actualizeazaCampMagazin();

    $("#formRegister").on("submit", function (e) {
        e.preventDefault();
        var valid = true;

        $("#succes-register").removeClass("vizibil");

        var username = $("#username").val().trim();
        if (username.length < 3) {
            marcheazaInvalid("username", "err-username");
            valid = false;
        } else {
            marcheazaValid("username", "err-username");
        }

        var email = $("#email").val().trim();
        if (!emailValid(email)) {
            marcheazaInvalid("email", "err-email");
            valid = false;
        } else {
            marcheazaValid("email", "err-email");
        }

        var parola = $("#parola").val();
        if (parola.length < 8) {
            marcheazaInvalid("parola", "err-parola");
            valid = false;
        } else {
            marcheazaValid("parola", "err-parola");
        }

        var confirma = $("#confirma-parola").val();
        if (confirma.length === 0 || confirma !== parola) {
            marcheazaInvalid("confirma-parola", "err-confirma");
            valid = false;
        } else {
            marcheazaValid("confirma-parola", "err-confirma");
        }

        var dataNasterii = $("#data-nasterii").val();
        var azi = new Date().toISOString().split("T")[0];
        if (!dataNasterii || dataNasterii > azi) {
            marcheazaInvalid("data-nasterii", "err-data");
            valid = false;
        } else {
            marcheazaValid("data-nasterii", "err-data");
        }

        var tara = $("#tara").val();
        if (!tara) {
            marcheazaInvalid("tara", "err-tara");
            valid = false;
        } else {
            marcheazaValid("tara", "err-tara");
        }

        var oras = $("#oras").val();
        if (!oras) {
            marcheazaInvalid("oras", "err-oras");
            valid = false;
        } else {
            marcheazaValid("oras", "err-oras");
        }

        var tipCont = $("#tip-cont").val();
        if (tipCont === "vanzator" || tipCont === "ambele") {
            var numeMagazin = $("#nume-magazin").val().trim();
            if (numeMagazin.length < 2) {
                marcheazaInvalid("nume-magazin", "err-magazin");
                valid = false;
            } else {
                marcheazaValid("nume-magazin", "err-magazin");
            }
        }
        //ASTA TREBUIE SA COMENTEZ
        var avatarInput = document.getElementById("avatar");
        if (avatarInput && avatarInput.files.length > 0) {
            var fisier = avatarInput.files[0];
            var numeFisier = fisier.name.toLowerCase();
            var tipuriAcceptate = ["image/jpeg", "image/png", "image/gif", "image/webp"];
            var extensiiOk = [".jpg", ".jpeg", ".png", ".gif", ".webp"];

            var tipValid = tipuriAcceptate.indexOf(fisier.type) !== -1;
            var extensieValida = extensiiOk.some(function (ext) {
                return numeFisier.endsWith(ext);
            });

            if (!tipValid && !extensieValida) {
                marcheazaInvalid("avatar", "err-avatar");
                valid = false;
            } else {
                marcheazaValid("avatar", "err-avatar");
            }
        } else {
            marcheazaValid("avatar", "err-avatar");
        }

        ///Astsa decomentata
        // marcheazaValid("avatar", "err-avatar");

        var termeni = $("#termeni").is(":checked");
        if (!termeni) {
            $("#err-termeni").addClass("vizibil");
            valid = false;
        } else {
            $("#err-termeni").removeClass("vizibil");
        }

        if (valid && $(this).attr("action") === "#") {
            $("#succes-register").addClass("vizibil");
        } else if (valid) {
            this.submit();
        }
    });

});
