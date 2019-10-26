import $ from "jquery";

$(document).ready(function () {
    for (let el of $('*[id^="toast-"]')) $("#" + el.id).toast('show');
});
