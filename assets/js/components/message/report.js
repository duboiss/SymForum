import $ from 'jquery';
import axios from 'axios';

const $modal = $('#reportModal');
const $reasonTextarea = $('textarea[name="reason"]');
const reportSubmitBtn = document.getElementById('js-report-button');
const errorMessage = document.querySelector('.error-message');
const $toastReport = $('#toastHide-report');

let messageAuthor; let messageId; let
    responseMessage;

$modal.on('show.bs.modal', (event) => {
    const button = $(event.relatedTarget); // Button that triggered the modal

    // Extract info from data-* attributes
    messageAuthor = button.data('author');
    messageId = button.data('message');

    // Modal customization
    $modal.find('.reportText').text(`Signaler le message de ${messageAuthor}`);
    $modal.find('form').attr('action', `/forums/reports/${messageId}`);
});

$modal.on('shown.bs.modal', () => {
    $reasonTextarea.focus();
});

$modal.on('hidden.bs.modal', () => {
    $reasonTextarea.val('');
    errorMessage.textContent = '';
});

reportSubmitBtn.addEventListener('click', (e) => {
    e.preventDefault();

    const url = $modal.find('form').attr('action');
    const reason = $modal.find($reasonTextarea).val();

    axios.post(url, {
        reason,
    }).then((response) => {
        responseMessage = response.data.message;
        $toastReport.find('.toast-body').text(responseMessage);

        $toastReport.toast('show');
        $modal.modal('hide');
    }).catch((err) => {
        responseMessage = err.response.data.message;
        errorMessage.textContent = responseMessage;
    });
});
