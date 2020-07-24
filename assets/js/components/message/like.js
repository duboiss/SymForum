import $ from "jquery";

let $links = $('.js-message-like');

$links.click(function (event) {
    event.preventDefault();

    let url = this.href;
    let icone = this.querySelector('i');
    let spanCount = this.querySelector('span.js-message-count-likes')

    $.ajax({
        method: "POST",
        url
    }).done(function(response) {
        let text;
        response !== 0 ? text = response.toString() : text = '';

        toggleIcone(icone);
        spanCount.textContent = text;
    }).fail(function() {
        window.alert("Une erreur est survenue ! Essayez d'actualiser.")
    });
});

function toggleIcone(icone) {
    if (icone.classList.contains('far')) {
        icone.classList.replace('far', 'fas');
    }
    else {
        icone.classList.replace('fas', 'far');
    }
}
