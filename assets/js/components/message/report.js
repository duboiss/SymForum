import axios from 'axios';
import { Modal, Toast } from 'bootstrap';

const modalElement = document.getElementById('reportModal');
const form = modalElement.querySelector('form');
const reasonTextarea = document.getElementById('message-text');
const reportSubmitBtn = document.getElementById('js-report-button');
const errorMessage = document.querySelector('.error-message');

const modal = new Modal(modalElement);

let messageAuthor; let messageUuid; let
    responseMessage;

modalElement.addEventListener('show.bs.modal', (event) => {
    const button = event.relatedTarget; // Button that triggered the modal

    // Extract info from data-* attributes
    messageAuthor = button.dataset.author;
    messageUuid = button.dataset.message;

    document.getElementById('reportText').textContent = `Signaler le message de ${messageAuthor}`;
    form.setAttribute('action', `/forums/reports/${messageUuid}`);
});

modalElement.addEventListener('shown.bs.modal', () => reasonTextarea.focus());

modalElement.addEventListener('hidden.bs.modal', () => {
    reasonTextarea.value = '';
    errorMessage.textContent = '';
});

reportSubmitBtn.addEventListener('click', (e) => {
    e.preventDefault();

    const url = form.getAttribute('action');
    const reason = reasonTextarea.value.trim();

    axios.post(url, {
        reason,
    }).then((response) => {
        responseMessage = response.data.message;
        document.getElementById('hiddenToast-report').getElementsByClassName('toast-body')[0].textContent = responseMessage;

        modal.hide();
        new Toast(document.getElementById('hiddenToast-report')).show();
    }).catch((err) => {
        responseMessage = err.response.data.message;
        errorMessage.textContent = responseMessage;
    });
});
