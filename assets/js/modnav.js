import '../css/modnav.css';

import $ from 'jquery';
import 'bootstrap';

$(document).ready(function () {
    $('[data-tooltip="tooltip"]').tooltip({
        placement: 'right'
    });

    $(".rotate").on('click', function () {
        $(this).addClass('fa-spin');
    });

    $('#deleteModal').on('hidden.bs.modal', function () {
        $('.rotate').removeClass('fa-spin');
        $('[data-tooltip="tooltip"]').tooltip('blur');
    });
});
