import htmx from 'htmx.org';
window.htmx = htmx;

import './sigma-orchestrator';

// HTMX CSRF & headers
htmx.on('htmx:configRequest', (evt) => {
    let csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!csrfToken) {
        const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
        csrfToken = match ? decodeURIComponent(match[1]) : null;
    }
    if (!csrfToken) {
        const tokenInput = document.querySelector('input[name="_token"]');
        if (tokenInput) csrfToken = tokenInput.value;
    }
    if (csrfToken) {
        evt.detail.headers['X-CSRF-TOKEN'] = csrfToken;
    }
    evt.detail.headers['HX-Requested-With'] = 'XMLHttpRequest';
});

// --- SIGMA Stack (Definiciones globales antes de Alpine) ---

import { TabulatorFull as Tabulator } from 'tabulator-tables';
import 'tabulator-tables/dist/css/tabulator.min.css';
window.Tabulator = Tabulator;

import * as FilePond from 'filepond';
import 'filepond/dist/filepond.min.css';
window.FilePond = FilePond;

import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";
window.flatpickr = flatpickr;

import SlimSelect from 'slim-select';
import 'slim-select/styles';
window.SlimSelect = SlimSelect;

import Chart from 'chart.js/auto';
window.Chart = Chart;

import { Notyf } from 'notyf';
import 'notyf/notyf.min.css';
window.notyf = new Notyf({
    duration: 3000,
    position: { x: 'center', y: 'top' },
    ripple: true,
    dismissible: true
});

import QRCode from 'qrcode';
window.QRCode = QRCode;

// Global Indicator
document.addEventListener('htmx:beforeRequest', () => {
    document.getElementById('hx-indicator')?.classList.remove('hidden');
});
document.addEventListener('htmx:afterRequest', () => {
    document.getElementById('hx-indicator')?.classList.add('hidden');
});

document.body.addEventListener('htmx:afterSettle', function (evt) {
    // 1. Inicializar SlimSelect
    document.querySelectorAll('[data-widget="slimselect"]').forEach(el => {
        // Evitamos duplicar si ya tiene la clase de SlimSelect
        if (!el.nextElementSibling?.classList.contains('ss-main')) {
            new SlimSelect({ select: el });
        }
    });

    // 2. Inicializar FilePond
    document.querySelectorAll('[data-widget="filepond"]').forEach(el => {
        if (!el.classList.contains('filepond--browser')) {
            FilePond.create(el, {
                labelIdle: 'Arrastra archivos',
                allowMultiple: false,
            });
        }
    });

    // 3. Inicializar Flatpickr
    document.querySelectorAll('[data-widget^="flatpickr"]').forEach(el => {
        if (!el.classList.contains('flatpickr-input')) {
            const isRange = el.dataset.widget === 'flatpickr-range';
            flatpickr(el, {
                locale: 'es',
                mode: isRange ? 'range' : 'single',
                dateFormat: 'Y-m-d'
            });
        }
    });
});

document.addEventListener('htmx:afterRequest', (e) => {
    const tab = e.target.closest('[data-tab]');
    if (!tab) return;

    document.querySelectorAll('[data-tab]').forEach(t => {
        t.style.color = 'var(--tx2)';
        t.style.borderBottom = 'none';
    });
    tab.style.color = 'var(--ac)';
    tab.style.borderBottom = '2px solid var(--ac)';
});

// --- Alpine Initialization (Siempre al final) ---

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();