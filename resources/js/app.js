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

function sigmaInit(container = document) {
    // 1. Inicializar SlimSelect
    container.querySelectorAll('[data-widget="slimselect"]').forEach(el => {
        if (!el.nextElementSibling?.classList.contains('ss-main')) {
            new SlimSelect({ select: el });
        }
    });

    // 2. Otros widgets se inicializan nativamente

    // 3. Inicializar Flatpickr
    container.querySelectorAll('[data-widget^="flatpickr"]').forEach(el => {
        if (!el.classList.contains('flatpickr-input')) {
            const isRange = el.dataset.widget === 'flatpickr-range';
            flatpickr(el, {
                locale: 'es',
                mode: isRange ? 'range' : 'single',
                dateFormat: 'Y-m-d',
                static: true,
                allowInput: true
            });
        }
    });

    // 4. Inicializar Tabulator (SIGMA Standard)
    container.querySelectorAll('[data-widget="tabulator"]').forEach(el => {
        if (!el.tabulator) {
            const config = JSON.parse(el.dataset.config || '{}');
            
            // Hidratar formateadores (thawColumns)
            if (config.columns) {
                config.columns = config.columns.map(col => {
                    if (typeof col.formatter === 'string' && col.formatter.startsWith('function')) {
                        try {
                            col.formatter = new Function('return ' + col.formatter)();
                        } catch (e) {
                            console.error('Error hydrating formatter:', e);
                        }
                    }
                    return col;
                });
            }

            const table = new Tabulator(el, config);
            el.tabulator = table;

            // Al terminar de renderizar/actualizar datos
            table.on("renderComplete", () => {
                window.htmx.process(el);
            });

            // Al terminar de construir la tabla
            table.on("tableBuilt", () => {
                const urlParams = new URLSearchParams(window.location.search);
                const id = urlParams.get("id");
                const route = el.dataset.route;
                if (id && route && !el.dataset.detailOpened) {
                    el.dataset.detailOpened = "true";
                    setTimeout(() => {
                        window.dispatchEvent(new CustomEvent('open-modal'));
                        window.htmx.ajax('GET', `/${route}/${id}`, {
                            target: '#modal-body',
                            swap: 'innerHTML',
                        });
                    }, 300);
                }
            });

            // Comportamiento global de click en fila
            const route = el.dataset.route;
            if (route) {
                table.on("rowClick", (e, row) => {
                    if (e.target.closest('button, a, input, [tabulator-field="files"], .no-click')) return;
                    const id = row.getData().id;
                    window.dispatchEvent(new CustomEvent('open-modal'));
                    window.htmx.ajax('GET', `/${route}/${id}`, {
                        target: '#modal-body',
                        swap: 'innerHTML',
                    });
                });
            }
        }
    });

    // 5. Inicializar QRCodes
    container.querySelectorAll('[data-widget="qrcode"]').forEach(el => {
        if (!el.dataset.initialized) {
            const text = el.dataset.text;
            if (text) {
                new QRCode(el, {
                    text: text,
                    width: el.dataset.size || 128,
                    height: el.dataset.size || 128,
                    correctLevel: QRCode.CorrectLevel.L
                });
                el.dataset.initialized = "true";
            }
        }
    });
}

// Escuchar cambios de HTMX
document.body.addEventListener('htmx:afterSettle', (evt) => {
    sigmaInit(evt.detail.target);
});

// Carga inicial
document.addEventListener('DOMContentLoaded', () => {
    sigmaInit();
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

    if (!document.startViewTransition) {
        toggle();
        return;
    }

    const transition = document.startViewTransition(toggle);

    await transition.ready;
    root.animate(
        { clipPath: [`circle(0px at ${x}px ${y}px)`, `circle(${endRadius}px at ${x}px ${y}px)`] },
        { duration: 500, easing: 'ease-in-out', pseudoElement: '::view-transition-new(root)' }
    );
};

// --- Alpine Initialization (Siempre al final) ---

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();