const spinner = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Chargement...';

document.querySelectorAll('form').forEach((el) => el.addEventListener('submit', () => {
    const btn = el.querySelector('.js-submit-btn');
    btn.disabled = true;
    btn.innerHTML = spinner;
}));

document.querySelectorAll('a.js-submit-link').forEach((el) => el.addEventListener('click', () => {
    const link = el;
    link.classList.add('disabled');
    link.innerHTML = spinner;
}));
