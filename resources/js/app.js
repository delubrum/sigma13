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
    if (csrfToken) evt.detail.headers['X-CSRF-TOKEN'] = csrfToken;
    evt.detail.headers['HX-Requested-With'] = 'XMLHttpRequest';
});

// --- SIGMA Stack (Definiciones globales antes de Alpine) ---

import { TabulatorFull as Tabulator } from 'tabulator-tables';
window.Tabulator = Tabulator;

import jspreadsheet from 'jspreadsheet-ce';
import 'jspreadsheet-ce/dist/jspreadsheet.css';
window.jspreadsheet = jspreadsheet;

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

// --- Tabulator init (single element) ---
window.initTabulatorEl = function(el) {
    const config = JSON.parse(el.dataset.config || '{}');

    if (config.columns) {
        const INTERNAL_KEYS = new Set(['hide', 'clearable']);
        config.columns = config.columns.map(col => {
            if (typeof col.formatter === 'string' && col.formatter.startsWith('function')) {
                try { col.formatter = new Function('return ' + col.formatter)(); }
                catch (e) { console.error('Error hydrating formatter:', e); }
            }
            // Strip nulls + internal-only keys Tabulator must never see
            return Object.fromEntries(
                Object.entries(col).filter(([k, v]) => v !== null && !INTERNAL_KEYS.has(k))
            );
        });
    }

    const table = new Tabulator(el, config);
    el.tabulator = table;

    table.on("renderComplete", () => { window.htmx.process(el); });

    table.on("tableBuilt", () => {
        requestAnimationFrame(() => table.redraw(true));

        if (window.ResizeObserver) {
            const ro = new ResizeObserver(() => { if (el.offsetWidth > 0) table.redraw(); });
            ro.observe(el);
        }

        const id = new URLSearchParams(window.location.search).get("id");
        const route = el.dataset.route;
        if (id && route && !el.dataset.detailOpened) {
            el.dataset.detailOpened = "true";
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('open-modal'));
                window.htmx.ajax('GET', `/${route}/${id}`, { target: '#modal-body', swap: 'innerHTML' });
            }, 300);
        }
    });

    const route = el.dataset.route;
    if (route) {
        table.on("rowClick", (e, row) => {
            if (e.target.closest('button, a, input, [tabulator-field="files"], .no-click')) return;
            const id = row.getData().id;
            window.dispatchEvent(new CustomEvent('open-modal'));
            window.htmx.ajax('GET', `/${route}/${id}`, { target: '#modal-body', swap: 'innerHTML' });
        });
    }
};

// --- Tabulator reset (full destroy + recreate from original config) ---
// Tabulator stores persistence under keys: "tabulator-{persistenceID}-{type}"
// Reset persistence + restore original column layout (no destroy needed)
window.resetTabulatorEl = function(el) {
    const table = el.tabulator;
    if (!table) return;

    // Persistence key format: "tabulator-{persistenceID}-{type}"
    const prefix = 'tabulator-' + (table.options.persistenceID || el.id || '');
    Object.keys(localStorage)
        .filter(k => k.startsWith(prefix))
        .forEach(k => localStorage.removeItem(k));

    // Restore original column definitions from data-config (widths, order, visibility)
    const originalColumns = JSON.parse(el.dataset.config || '{}').columns || [];
    const INTERNAL_KEYS = new Set(['hide', 'clearable']);
    const cleanColumns = originalColumns.map(col =>
        Object.fromEntries(Object.entries(col).filter(([k, v]) => v !== null && !INTERNAL_KEYS.has(k)))
    );

    // setColumnLayout merges original defs back — resets width + visible + order
    table.setColumnLayout(cleanColumns);

    // Show all columns (in case any were hidden)
    table.getColumns().forEach(col => col.show());

    table.redraw(true);
};

// --- sigmaInit (container-level widget init) ---
function sigmaInit(container = document) {
    container.querySelectorAll('[data-widget="slimselect"]').forEach(el => {
        if (!el.nextElementSibling?.classList.contains('ss-main')) {
            new SlimSelect({ select: el });
        }
    });

    container.querySelectorAll('[data-widget^="flatpickr"]').forEach(el => {
        if (!el.classList.contains('flatpickr-input')) {
            flatpickr(el, {
                locale: 'es',
                mode: el.dataset.widget === 'flatpickr-range' ? 'range' : 'single',
                dateFormat: 'Y-m-d',
                static: true,
                allowInput: true
            });
        }
    });

    container.querySelectorAll('[data-widget="tabulator"]').forEach(el => {
        if (!el.tabulator) window.initTabulatorEl(el);
    });

    container.querySelectorAll('[data-widget="qrcode"]').forEach(el => {
        if (!el.dataset.initialized) {
            const text = el.dataset.text;
            if (text) {
                new QRCode(el, {
                    text,
                    width: el.dataset.size || 128,
                    height: el.dataset.size || 128,
                    correctLevel: QRCode.CorrectLevel.L
                });
                el.dataset.initialized = "true";
            }
        }
    });
}

// Redraw on resize (registered once globally)
window.addEventListener('resize', () => {
    document.querySelectorAll('[data-widget="tabulator"]').forEach(el => {
        el.tabulator?.redraw();
    });
});

document.body.addEventListener('htmx:afterSettle', (evt) => { sigmaInit(evt.detail.target); });
document.addEventListener('DOMContentLoaded', () => { sigmaInit(); });

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

// --- SIGMA Theme Helper ---
window.toggleTheme = async (event) => {
    const root = document.documentElement;
    const x = event?.clientX ?? window.innerWidth / 2;
    const y = event?.clientY ?? window.innerHeight / 2;
    const endRadius = Math.hypot(Math.max(x, window.innerWidth - x), Math.max(y, window.innerHeight - y));
    const toggle = () => {
        const isDark = root.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        window.dispatchEvent(new CustomEvent('theme-changed', { detail: { isDark } }));
    };
    if (!document.startViewTransition) { toggle(); return; }
    const transition = document.startViewTransition(toggle);
    await transition.ready;
    root.animate(
        { clipPath: [`circle(0px at ${x}px ${y}px)`, `circle(${endRadius}px at ${x}px ${y}px)`] },
        { duration: 500, easing: 'ease-in-out', pseudoElement: '::view-transition-new(root)' }
    );
};

// --- Alpine (always last) ---
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();
