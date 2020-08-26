const spinner = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Chargement...';

document.querySelectorAll('form').forEach((el) => el.addEventListener('submit', () => {
        let btn = this.querySelector('.js-submit-btn');
        btn.disabled = true;
        btn.innerHTML = spinner;
}));


document.querySelectorAll('a.js-submit-link').forEach((el) => el.addEventListener('click', () => {
    this.classList.add('disabled')
    this.innerHTML = spinner;
}));
