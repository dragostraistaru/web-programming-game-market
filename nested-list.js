document.addEventListener("DOMContentLoaded", function () {
    const expandableItems = document.querySelectorAll(".nested-list .expandable");

    expandableItems.forEach(function (item) {
        const label = item.querySelector(".toggle-label");

        if (label) {
            label.addEventListener("click", function () {
                item.classList.toggle("open");
            });
        }
    });
});