import axios from 'axios';

document.querySelectorAll('.js-delete-button').forEach((btn) => btn.addEventListener('click', () => {
    if (!confirm('Etes-vous certain de vouloir faire cela ?')) return;

    let alert; let type; let
        message;

    axios.delete(btn.dataset.deleteUrl)
        .then((response) => {
            type = 'success';
            message = response.data.message;
            btn.closest('tr').remove();
        })
        .catch((err) => {
            type = 'danger';
            message = err.response.data.message;
        })
        .then(() => {
            alert = `<div class="alert alert-dismissible alert-${type}"><button type="button" class="close" data-dismiss="alert">&times;</button>${message}</div>`;
            document.getElementById('messages').innerHTML += alert;
        });
}));
