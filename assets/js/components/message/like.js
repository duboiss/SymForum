import $ from 'jquery';

document.querySelectorAll('.js-message-like').forEach((el) => el.addEventListener('click', (e) => {
    e.preventDefault();

    let url = el.href;
    let icone = el.querySelector('i');
    let spanCount = el.querySelector('span.js-message-count-likes');

    $.ajax({
        method: 'POST',
        url
    }).done((response) => {
        let text;
        response !== 0 ? text = response.toString() : text = '';

        toggleIcone(icone);
        spanCount.textContent = text;
    }).fail(() => {
        window.alert('Une erreur est survenue ! Essayez d\'actualiser.');
    });
}));

function toggleIcone(icone) {
    if (icone.classList.contains('far')) {
        icone.classList.replace('far', 'fas');
    }
    else {
        icone.classList.replace('fas', 'far');
    }
}
