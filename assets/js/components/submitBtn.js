import $ from 'jquery';

const spinner = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Chargement...';

$(document).ready(function() {
    $('form').submit(function() {
        let $btn = $(this).find('.js-submit-btn');
        $btn.prop('disabled', true);
        $btn.html(spinner);
    });

    $('a.js-submit-link').click(function() {
        $(this).addClass('disabled');
        $(this).html(spinner);
    });
});
