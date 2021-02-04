import $ from 'jquery';

for (const el of document.querySelectorAll('*[id^="toast-"]')) {
    $(`#${el.id}`).toast('show');
}
