import $ from 'jquery';

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

    $.ajax({
        method: 'POST',
        url,
        data: JSON.stringify({ reason }),
        dataType: 'json',
    }).done((response) => {
        responseMessage = response.message;
        $toastReport.find('.toast-body').text(responseMessage);

        $toastReport.toast('show');
        $modal.modal('hide');
    }).fail((error) => {
        responseMessage = error.responseJSON.message;
        errorMessage.textContent = responseMessage;
    });
});
