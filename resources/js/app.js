import './bootstrap';

import htmx from 'htmx.org';
window.htmx = htmx;

import './sigma-orchestrator';

// HTMX CSRF & headers
htmx.on('htmx:configRequest', (evt) => {
    // Try meta tag first, then cookie, then form input
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

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// SIGMA Stack
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

// Global Indicator
document.addEventListener('htmx:beforeRequest', () => {
    document.getElementById('hx-indicator')?.classList.remove('hidden');
});
document.addEventListener('htmx:afterRequest', () => {
    document.getElementById('hx-indicator')?.classList.add('hidden');
});
