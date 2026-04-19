// Bootstrap JS
import 'bootstrap';

// Alpine.js
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// IMask para inputs com máscara
import IMask from 'imask';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-mask="phone"]').forEach(el => {
        IMask(el, { mask: '(00) 00000-0000' });
    });

    document.querySelectorAll('[data-mask="date"]').forEach(el => {
        IMask(el, { mask: '00/00/0000' });
    });

    document.querySelectorAll('[data-mask="currency"]').forEach(el => {
        IMask(el, {
            mask: 'R$ num',
            blocks: {
                num: {
                    mask: Number,
                    thousandsSeparator: '.',
                    radix: ',',
                    mapToRadix: ['.'],
                    scale: 2,
                }
            }
        });
    });
});
