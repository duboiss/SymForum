import $ from 'jquery';

$(document).ready(function () {
    let $btn = $('.js-delete-button');

    $btn.on('click', function () {
        if (!confirm('Etes-vous certain de vouloir faire cela ?')) return;

        let alert, type, message;
        let url = $(this).data('delete-url');
        let $row = $(this).closest('tr');

        $.ajax({
            type: "DELETE",
            url: url
        }).done(function (response) {
            type = 'success';
            message = response.message;
            $row.fadeOut('normal', function () {
                $(this).closest('tr').remove();
            });
        }).fail(function (error) {
            type = 'danger';
            message = error.responseJSON.message;
        }).always(function () {
            alert = '<div class="alert alert-dismissible alert-' + type + '"><button type="button" class="close" data-dismiss="alert">&times;</button>' + message + '</div>';
            $('.messages').append(alert).hide().fadeIn('normal');
        });
    })
});
