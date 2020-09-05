import '../css/modnav.css';

import $ from 'jquery';
import 'bootstrap';

$('[data-tooltip="tooltip"]').tooltip({
    placement: 'right'
});

$('#deleteModal').on('hidden.bs.modal', () => {
    $('.rotate').removeClass('fa-spin');
})

document.querySelectorAll('.rotate').forEach((el) => el.addEventListener('click', () => {
    el.classList.add('fa-spin')
}));
