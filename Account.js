function marcheazaInvalid(campId, eroareId) {
    var camp = document.getElementById(campId);
    var eroare = document.getElementById(eroareId);
    if (camp) camp.classList.add("invalid");
    if (eroare) eroare.classList.add("vizibil");
}

function marcheazaValid(campId, eroareId) {
    var camp = document.getElementById(campId);
    var eroare = document.getElementById(eroareId);
    if (camp) camp.classList.remove("invalid");
    if (eroare) eroare.classList.remove("vizibil");
}

function emailValid(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}


var formLogin = document.getElementById("formLogin");

if (formLogin) {
    formLogin.addEventListener("submit", function (e) {
        e.preventDefault();
        var valid = true;

        document.getElementById("succes-login").classList.remove("vizibil");

        var email = document.getElementById("login-email").value.trim();
        if (!emailValid(email)) {
            marcheazaInvalid("login-email", "err-login-email");
            valid = false;
        } else {
            marcheazaValid("login-email", "err-login-email");
        }

        var parola = document.getElementById("login-parola").value;
        if (parola.length === 0) {
            marcheazaInvalid("login-parola", "err-login-parola");
            valid = false;
        } else {
            marcheazaValid("login-parola", "err-login-parola");
        }

        if (valid) {
            document.getElementById("succes-login").classList.add("vizibil");
        }
    });
}


var inputParola = document.getElementById("parola");

if (inputParola) {
    inputParola.addEventListener("input", function () {
        var val = this.value;
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

        var bar = document.getElementById("putere-bar");
        var labelEl = document.getElementById("putere-label");

        if (bar) {
            bar.style.width = (putere * 25) + "%";
            bar.style.backgroundColor = culoare;
        }
        if (labelEl) {
            labelEl.textContent = label;
            labelEl.style.color = culoare;
        }
    });
}


var selectTara = document.getElementById("tara");
var selectOras = document.getElementById("oras");

if (selectTara && selectOras) {
    selectTara.addEventListener("change", function () {
        var taraSelectata = this.value;

        selectOras.innerHTML = "";

        var optDefault = document.createElement("option");
        optDefault.value = "";
        optDefault.textContent = "-- Selectează orașul sau regiunea --";
        selectOras.appendChild(optDefault);

        if (taraSelectata && typeof tariOrase !== "undefined" && tariOrase[taraSelectata]) {
            tariOrase[taraSelectata].forEach(function (oras) {
                var opt = document.createElement("option");
                opt.value = oras;
                opt.textContent = oras;
                selectOras.appendChild(opt);
            });
        }

        marcheazaValid("oras", "err-oras");
    });
}

var selectTipCont = document.getElementById("tip-cont");
var campMagazin = document.getElementById("camp-magazin");

if (selectTipCont && campMagazin) {
    function actualizeazaCampMagazin() {
        var tip = selectTipCont.value;

        if (tip === "vanzator" || tip === "ambele") {
            campMagazin.style.display = "block";
        } else {
            campMagazin.style.display = "none";

            var inputMagazin = document.getElementById("nume-magazin");
            if (inputMagazin) {
                inputMagazin.value = "";
                inputMagazin.classList.remove("invalid");
            }

            var errMagazin = document.getElementById("err-magazin");
            if (errMagazin) {
                errMagazin.classList.remove("vizibil");
            }
        }
    }

    selectTipCont.addEventListener("change", actualizeazaCampMagazin);
    actualizeazaCampMagazin();
}


var formRegister = document.getElementById("formRegister");

if (formRegister) {
    formRegister.addEventListener("submit", function (e) {
        e.preventDefault();
        var valid = true;

        document.getElementById("succes-register").classList.remove("vizibil");

        var username = document.getElementById("username").value.trim();
        if (username.length < 3) {
            marcheazaInvalid("username", "err-username");
            valid = false;
        } else {
            marcheazaValid("username", "err-username");
        }

        var email = document.getElementById("email").value.trim();
        if (!emailValid(email)) {
            marcheazaInvalid("email", "err-email");
            valid = false;
        } else {
            marcheazaValid("email", "err-email");
        }

        var parola = document.getElementById("parola").value;
        if (parola.length < 8) {
            marcheazaInvalid("parola", "err-parola");
            valid = false;
        } else {
            marcheazaValid("parola", "err-parola");
        }

        var confirma = document.getElementById("confirma-parola").value;
        if (confirma.length === 0 || confirma !== parola) {
            marcheazaInvalid("confirma-parola", "err-confirma");
            valid = false;
        } else {
            marcheazaValid("confirma-parola", "err-confirma");
        }

        var dataNasterii = document.getElementById("data-nasterii").value;
        var azi = new Date().toISOString().split("T")[0];
        if (!dataNasterii || dataNasterii > azi) {
            marcheazaInvalid("data-nasterii", "err-data");
            valid = false;
        } else {
            marcheazaValid("data-nasterii", "err-data");
        }

        var tara = document.getElementById("tara").value;
        if (!tara) {
            marcheazaInvalid("tara", "err-tara");
            valid = false;
        } else {
            marcheazaValid("tara", "err-tara");
        }

        var oras = document.getElementById("oras").value;
        if (!oras) {
            marcheazaInvalid("oras", "err-oras");
            valid = false;
        } else {
            marcheazaValid("oras", "err-oras");
        }

        // MODIFICAT: validare pentru nume magazin doar la vanzator/ambele
        var tipCont = document.getElementById("tip-cont").value;
        var numeMagazin = document.getElementById("nume-magazin");

        if (tipCont === "vanzator" || tipCont === "ambele") {
            if (!numeMagazin || numeMagazin.value.trim().length < 2) {
                marcheazaInvalid("nume-magazin", "err-magazin");
                valid = false;
            } else {
                marcheazaValid("nume-magazin", "err-magazin");
            }
        }

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

        var termeni = document.getElementById("termeni").checked;
        if (!termeni) {
            document.getElementById("err-termeni").classList.add("vizibil");
            valid = false;
        } else {
            document.getElementById("err-termeni").classList.remove("vizibil");
        }

        if (valid) {
            document.getElementById("succes-register").classList.add("vizibil");
        }
    });
}