var currentColumn = "title";
var ascending = true;

function renderTable() {
    var tbody = document.getElementById("gamesTableBody");
    if (!tbody) return;

    tbody.innerHTML = "";

    gamesData.forEach(function (game) {
        var row = document.createElement("tr");
        row.innerHTML =
            "<td>" + game.title + "</td>" +
            "<td>" + game.platform + "</td>" +
            "<td>" + game.price.toFixed(2) + " RON</td>" +
            "<td>" + game.rating + " / 5</td>";
        tbody.appendChild(row);
    });

    updateHeaderStyles();
}

function sortTable(column) {
    if (currentColumn === column) {
        ascending = !ascending;
    } else {
        currentColumn = column;
        ascending = true;
    }

    gamesData.sort(function (a, b) {
        var valueA = a[column];
        var valueB = b[column];

        if (typeof valueA === "string") {
            valueA = valueA.toLowerCase();
            valueB = valueB.toLowerCase();
        }

        if (valueA < valueB) return ascending ? -1 : 1;
        if (valueA > valueB) return ascending ? 1 : -1;
        return 0;
    });

    renderTable();
}

function updateHeaderStyles() {
    var headers = document.querySelectorAll("#gamesTable th");

    headers.forEach(function (header) {
        header.classList.remove("sorted-asc");
        header.classList.remove("sorted-desc");

        var column = header.getAttribute("data-column");
        if (column === currentColumn) {
            header.classList.add(ascending ? "sorted-asc" : "sorted-desc");
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    var headers = document.querySelectorAll("#gamesTable th");

    headers.forEach(function (header) {
        header.classList.add("sortable");
        header.addEventListener("click", function () {
            var column = this.getAttribute("data-column");
            sortTable(column);
        });
    });

    renderTable();
    renderTableVertical();
});



var ascendingVertical = true;
var currentSortRow = null;

var rowOrder = ["title", "platform", "price", "rating"];
var rowLabels = {
    "title": "Titlu joc",
    "platform": "Platforma",
    "price": "Pret",
    "rating": "Rating"
};

function renderTableVertical() {
    var tbody = document.getElementById("gamesTableVerticalBody");
    if (!tbody) return;

    tbody.innerHTML = "";

    rowOrder.forEach(function (prop) {
        var row = document.createElement("tr");

        var th = document.createElement("th");
        th.textContent = rowLabels[prop];
        th.classList.add("sortable-vertical");
        th.setAttribute("data-prop", prop);
        th.addEventListener("click", function () {
            sortTableVertical(prop);
        });

        if (prop === currentSortRow) {
            th.classList.add(ascendingVertical ? "sorted-asc" : "sorted-desc");
        }

        row.appendChild(th);

        // MODIFICAT: gamesDataVertical in loc de gamesData
        gamesDataVertical.forEach(function (game) {
            var td = document.createElement("td");
            if (prop === "price") {
                td.textContent = game[prop].toFixed(2) + " RON";
            } else if (prop === "rating") {
                td.textContent = game[prop] + " / 5";
            } else {
                td.textContent = game[prop];
            }
            row.appendChild(td);
        });

        tbody.appendChild(row);
    });
}

function sortTableVertical(prop) {
    if (currentSortRow === prop) {
        ascendingVertical = !ascendingVertical;
    } else {
        currentSortRow = prop;
        ascendingVertical = true;
    }

    gamesDataVertical.sort(function (a, b) {
        var valueA = a[prop];
        var valueB = b[prop];

        if (typeof valueA === "string") {
            valueA = valueA.toLowerCase();
            valueB = valueB.toLowerCase();
        }

        if (valueA < valueB) return ascendingVertical ? -1 : 1;
        if (valueA > valueB) return ascendingVertical ? 1 : -1;
        return 0;
    });

    renderTableVertical();

}