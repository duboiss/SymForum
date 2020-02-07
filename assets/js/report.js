import $ from 'jquery';
import axios from 'axios';

$(document).ready(function () {
    const modal = $('#reportModal');
    const reasonTextarea = $('textarea[name="reason"]');
    const reportSubmitBtn = $('#js-report-button');
    const errorMessage = $('.error-message');
    const toastReport = $('#toastHide-report');

    let messageAuthor, messageId, responseMessage;

    modal.on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget); // Button that triggered the modal

        // Extract info from data-* attributes
        messageAuthor = button.data('author');
        messageId = button.data('message');

        // Modal customization
        modal.find('.reportText').text('Signaler le message de ' + messageAuthor);
        modal.find('form').attr('action', '/forums/report/' + messageId);
    });

    modal.on('shown.bs.modal', function () {
        reasonTextarea.focus();
    });

    modal.on('hidden.bs.modal', function () {
        reasonTextarea.val('');
        errorMessage.text('');
    });

    reportSubmitBtn.click(function (event) {
        event.preventDefault();

        let url = modal.find('form').attr('action');
        let reason = modal.find(reasonTextarea).val();

        axios.post(url, {
            messageId: messageId,
            reason: reason
        }).then(function (response) {
            responseMessage = response.data.message;
            console.log(responseMessage);
            toastReport.find('.toast-body').text(responseMessage);

            toastReport.toast('show');
            modal.modal('hide');
        }).catch(function (error) {
            responseMessage = error.response.data.message;
            errorMessage.text(responseMessage);
        });
    });
});
