import '../css/modnav.css';

import $ from "jquery";
import 'bootstrap';

$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip({
        placement: 'right'
    });

    $(".rotate").on('click', function () {
        $(this).addClass("fa-spin");
    });
});
