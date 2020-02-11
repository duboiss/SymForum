import $ from 'jquery';

$(document).ready(function() {
    $('form').submit(function() {
        let $btn = $(this).find('.js-submit-button');
        $btn.prop('disabled', true);
        $btn.html(
            `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Chargement...`
        );
    });
});
