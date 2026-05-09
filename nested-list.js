$(document).ready(function () {
    $(".nested-list .expandable").each(function () {
        var $label = $(this).find(".toggle-label");

        if ($label.length) {
            $label.on("click", function () {
                $(this).closest(".expandable").toggleClass("open");
            });
        }
    });
});