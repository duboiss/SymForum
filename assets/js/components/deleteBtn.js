import $ from 'jquery';
import axios from 'axios';

$(document).ready(function () {
    let btn = $('.js-delete-button');

    btn.click(function (event) {
        if(!confirm('Etes-vous certain de vouloir faire cela ?')) return;

        let url = $(this).attr('data-delete-url');
        let alert, type, message;

        axios.delete(url).then(function (response) {
            type = 'success';
            message = response.data.message;
            event.target.closest('tr').remove();
        }).catch(function (error) {
            type = 'danger';
            message = error.response.data.message;
        }).then(function () {
            alert = '<div class="alert alert-dismissible alert-' + type + '"><button type="button" class="close" data-dismiss="alert">&times;</button>' + message + '</div>';
            $('.messages').append(alert);
        });
    })
});
