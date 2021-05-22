import axios from 'axios';

function toggleIcone(icone) {
    if (icone.classList.contains('far')) {
        icone.classList.replace('far', 'fas');
    } else {
        icone.classList.replace('fas', 'far');
    }
}

document.querySelectorAll('.js-message-like').forEach((el) => el.addEventListener('click', (e) => {
    e.preventDefault();

    const url = el.href;
    const icone = el.querySelector('i');
    const spanCount = el.querySelector('span.js-message-count-likes');

    axios.post(url)
        .then((response) => {
            let text = '';

            if (response.data !== 0) {
                text = response.data.toString();
            }

            toggleIcone(icone);
            spanCount.textContent = text;
        }).catch(() => {
            window.alert("Une erreur est survenue ! Essayez d'actualiser.");
        });
}));
