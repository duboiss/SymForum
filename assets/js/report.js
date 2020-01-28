import $ from "jquery";

$(document).ready(function () {
    const modal = $('#reportModal');
    modal.on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget); // Button that triggered the modal

        // Extract info from data-* attributes
        let messageAuthor = button.data('author');
        let messageId = button.data('message');

        let modal = $(this);
        modal.find('.reportText').text('Signaler le message de ' + messageAuthor);
        modal.find('form').attr('action', '/forums/report/' + messageId);
    });

    modal.on('shown.bs.modal', function () {
        $('#message-text').focus();
    })
});
