function setValid(campId, eroareId, eValid) {
    var camp = document.getElementById(campId);
    var eroare = document.getElementById(eroareId);
    if (!camp) return;
    if (eValid) {
        camp.classList.remove("f-invalid");
        if (eroare) eroare.style.display = "none";
    } else {
        camp.classList.add("f-invalid");
        if (eroare) eroare.style.display = "block";
    }
}


var style = document.createElement("style");
style.textContent = [
    ".f-invalid {",
    "  border: 2px solid #dc2626 !important;",
    "  background-color: #fff5f5 !important;",
    "  border-radius: 4px;",
    "}",
    ".f-eroare {",
    "  color: #dc2626;",
    "  font-size: 0.78em;",
    "  margin-top: 3px;",
    "  display: none;",
    "}"
].join("\n");
document.head.appendChild(style);


function adaugaEroare(campId, mesaj) {
    var camp = document.getElementById(campId);
    if (!camp) return;
    var idEroare = campId + "-err";
    if (!document.getElementById(idEroare)) {
        var span = document.createElement("span");
        span.id = idEroare;
        span.className = "f-eroare";
        span.textContent = mesaj;
        camp.parentNode.insertBefore(span, camp.nextSibling);
    }
}


document.addEventListener("DOMContentLoaded", function () {
    adaugaEroare("title-forum", "Titlul topicului este obligatoriu.");
    adaugaEroare("message-forum", "Mesajul trebuie să aibă minim 10 caractere.");
    adaugaEroare("min-rep-forum", "Scorul minim trebuie să fie între 0 și 100.");
});


document.addEventListener("DOMContentLoaded", function () {


    var inputs = document.querySelectorAll("input[name='title']");
    if (inputs.length > 0) inputs[0].id = "title-forum";

    var textareas = document.querySelectorAll("textarea[name='message']");
    if (textareas.length > 0) textareas[0].id = "message-forum";

    var repInputs = document.querySelectorAll("input[name='min_reputation']");
    if (repInputs.length > 0) repInputs[0].id = "min-rep-forum";

    var form = document.querySelector("form[name='forumForm']");
    if (!form) return;

    form.addEventListener("submit", function (e) {
        e.preventDefault();
        var valid = true;

        var titlu = document.getElementById("title-forum");
        if (titlu && titlu.value.trim().length === 0) {
            setValid("title-forum", "title-forum-err", false);
            valid = false;
        } else if (titlu) {
            setValid("title-forum", "title-forum-err", true);
        }

        var mesaj = document.getElementById("message-forum");
        if (mesaj && mesaj.value.trim().length < 10) {
            setValid("message-forum", "message-forum-err", false);
            valid = false;
        } else if (mesaj) {
            setValid("message-forum", "message-forum-err", true);
        }

        var rep = document.getElementById("min-rep-forum");
        if (rep) {
            var val = parseInt(rep.value, 10);
            if (isNaN(val) || val < 0 || val > 100) {
                setValid("min-rep-forum", "min-rep-forum-err", false);
                valid = false;
            } else {
                setValid("min-rep-forum", "min-rep-forum-err", true);
            }
        }

        var reguli = document.querySelector("input[name='rules']");
        if (reguli && !reguli.checked) {
            reguli.style.outline = "2px solid #dc2626";
            valid = false;
        } else if (reguli) {
            reguli.style.outline = "";
        }

        if (valid) {
            alert("Topicul a fost postat cu succes!");
        }
    });
});