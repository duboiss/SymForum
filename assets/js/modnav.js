import '../css/modnav.css';

import { Tooltip } from 'bootstrap';

const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
tooltipTriggerList.map((tooltipTriggerEl) => new Tooltip(tooltipTriggerEl));

const deleteModal = document.getElementById('deleteModal');

if (deleteModal) {
    deleteModal.addEventListener('hidden.bs.modal', () => {
        document.querySelectorAll('.rotate')[2].classList.remove('fa-spin');
    });
}

document.querySelectorAll('.rotate').forEach((el) => el.addEventListener('click', () => {
    el.classList.add('fa-spin');
}));
