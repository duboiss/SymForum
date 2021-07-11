import { Toast } from 'bootstrap';

const toastElList = [].slice.call(document.querySelectorAll('.toast-autoshow'));
const toastList = toastElList.map((toastEl) => new Toast(toastEl));

toastList.forEach((toast) => toast.show());
