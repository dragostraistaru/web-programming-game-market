var currentColumn = "title";
var ascending = true;
var ascendingVertical = true;
var currentSortRow = null;

var rowOrder = ["title", "platform", "price", "rating"];
var rowLabels = {
    "title": "Titlu joc",
    "platform": "Platforma",
    "price": "Pret",
    "rating": "Rating"
};

// alternative table state (for the secondary table in widgets.html)
var currentColumnAlt = "title";
var ascendingAlt = true;

function renderTable() {
    var $tbody = $("#gamesTableBody");
    if (!$tbody.length) return;

    $tbody.empty();

    gamesData.forEach(function (game) {
        var $row = $("<tr>");
        var $cells = [
            $("<td>").text(game.title),
            $("<td>").text(game.platform),
            $("<td>").text(game.price.toFixed(2) + " RON"),
            $("<td>").text(game.rating + " / 5")
        ];
        $row.append($cells);
        $tbody.append($row);
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
    var $headers = $("#gamesTable th");

    $headers.removeClass("sorted-asc").removeClass("sorted-desc");

    $headers.each(function () {
        var $header = $(this);
        var column = $header.attr("data-column");
        if (column === currentColumn) {
            $header.addClass(ascending ? "sorted-asc" : "sorted-desc");
        }
    });
}

function renderTableVertical() {
    var $tbody = $("#gamesTableVerticalBody");
    if (!$tbody.length) return;

    $tbody.empty();

    rowOrder.forEach(function (prop) {
        var $row = $("<tr>");

        var $th = $("<th>")
            .text(rowLabels[prop])
            .addClass("sortable-vertical")
            .attr("data-prop", prop)
            .on("click", function () {
                sortTableVertical(prop);
            });

        if (prop === currentSortRow) {
            $th.addClass(ascendingVertical ? "sorted-asc" : "sorted-desc");
        }

        $row.append($th);

        gamesDataVertical.forEach(function (game) {
            var $td = $("<td>");
            if (prop === "price") {
                $td.text(game[prop].toFixed(2) + " RON");
            } else if (prop === "rating") {
                $td.text(game[prop] + " / 5");
            } else {
                $td.text(game[prop]);
            }
            $row.append($td);
        });

        $tbody.append($row);
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

// de asta nou
function renderAltTable() {
    var $tbody = $("#gamesTableAltBody");
    if (!$tbody.length) return;

    $tbody.empty();

    gamesData.forEach(function (game, idx) {
        var $row = $("<tr>");
        $row.append($("<td>").text(game.title));
        $row.append($("<td>").text(game.platform));
        $row.append($("<td>").text(game.price.toFixed(2) + " RON"));
        $row.append($("<td>").text(game.rating + " / 5"));

        $row.attr("data-index", idx);
        $tbody.append($row);
    });

    $("#gamesTableAlt th").removeClass("sorted-asc sorted-desc");
    $("#gamesTableAlt th[data-column='" + currentColumnAlt + "']").addClass(ascendingAlt ? "sorted-asc" : "sorted-desc");
}

function sortTableAlt(column) {
    if (currentColumnAlt === column) {
        ascendingAlt = !ascendingAlt;
    } else {
        currentColumnAlt = column;
        ascendingAlt = true;
    }

    gamesData.sort(function (a, b) {
        var valueA = a[column];
        var valueB = b[column];

        if (typeof valueA === "string") {
            valueA = valueA.toLowerCase();
            valueB = valueB.toLowerCase();
        }

        if (valueA < valueB) return ascendingAlt ? -1 : 1;
        if (valueA > valueB) return ascendingAlt ? 1 : -1;
        return 0;
    });

    renderAltTable();
}

$(document).ready(function () {

    var $headers = $("#gamesTable th");
    $headers.addClass("sortable");
    $headers.on("click", function () {
        var column = $(this).attr("data-column");
        sortTable(column);
    });

    var $altHeaders = $("#gamesTableAlt th");
    if ($altHeaders.length) {
        $altHeaders.click(function () {
            var col = $(this).attr("data-column");
            sortTableAlt(col);
        });

        $altHeaders.dblclick(function () {
            currentColumnAlt = "title";
            ascendingAlt = true;
            renderAltTable();
        });


        $("#gamesTableAlt tbody").find("tr").hover(
            function () { $(this).addClass("row-hover"); },
            function () { $(this).removeClass("row-hover"); }
        );


        $("#gamesTableAlt").delegate("tbody tr", "click", function () {
            var idx = $(this).attr("data-index");
            if (typeof idx !== 'undefined') {
                var g = gamesData[parseInt(idx, 10)];
                if (g) console.log("Alt table row clicked:", g.title);
            }
        });


        $altHeaders.first().bind("mouseenter", function () {
            $(this).addClass("header-bound");
        });

        $altHeaders.first().bind("mouseleave", function () {
            $(this).removeClass("header-bound");
        });

    }

    renderTable();
    renderTableVertical();
    renderAltTable();

});
