import $ from 'jquery';

for (let el of document.querySelectorAll('*[id^="toast-"]')) {
    $("#" + el.id).toast('show');
}
