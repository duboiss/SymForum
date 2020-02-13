import $ from 'jquery';
import axios from 'axios';

$(document).ready(function () {
    let $btn = $('.js-delete-button');

    $btn.on('click', function () {
        if (!confirm('Etes-vous certain de vouloir faire cela ?')) return;

        let alert, type, message;
        let url = $(this).data('delete-url');
        let $row = $(this).closest('tr');

        axios.delete(url).then(function (response) {
            type = 'success';
            message = response.data.message;
            $row.fadeOut('normal', function () {
                $(this).closest('tr').remove();
            });
        }).catch(function (error) {
            type = 'danger';
            message = error.response.data.message;
        }).then(function () {
            alert = '<div class="alert alert-dismissible alert-' + type + '"><button type="button" class="close" data-dismiss="alert">&times;</button>' + message + '</div>';
            $('.messages').append(alert).hide().fadeIn('normal');
        });
    })
});
