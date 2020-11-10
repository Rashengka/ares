document.addEventListener('DOMContentLoaded', () => naja.initialize({history: false}));

var bindShowModal = function () {
    $(".show-modal").unbind("click").click(function (ev) {
        ev.preventDefault();
        naja.makeRequest("get", $(this).attr("href")).then(function (e) {
            if (e.showModal === true) {
                $("#detailModal").modal("show");
            }
        });
    });
}

naja.addEventListener("complete", function () {
    bindShowModal();
})
$(function () {
    bindShowModal();
});
