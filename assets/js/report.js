import $ from 'jquery';

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

        $.post(url, {
            messageId: messageId,
            reason: reason
        }).done(function (response) {
            responseMessage = response.message;
            toastReport.find('.toast-body').text(responseMessage);

            toastReport.toast('show');
            modal.modal('hide');
        }).fail(function (error) {
            responseMessage = error.responseJSON.message;
            errorMessage.text(responseMessage);
        });
    });
});
