// SIGMA Orchestrator - Handles HX-Trigger header from Laravel
(function () {
    function init() {
        const htmx = window.htmx;
        if (!htmx) {
            setTimeout(init, 50);
            return;
        }

        function processTriggers(xhr) {
            // Get HX-Trigger header
            const triggerHeader = xhr.getResponseHeader('HX-Trigger') || xhr.getResponseHeader('Hx-Trigger') || xhr.getResponseHeader('hx-trigger');
            if (triggerHeader) {
                try {
                    const triggers = JSON.parse(triggerHeader);

                    if (triggers.notify) {
                        const { type, message } = triggers.notify;
                        window.notyf[type === 'error' ? 'error' : 'success'](message);
                    }

                    if (triggers['close-modals']?.ids) {
                        triggers['close-modals'].ids.forEach(id => {
                            // Cascada: si se cierra el nivel 1, se cierran todos.
                            // Si se cierra el nivel 2, se cierran el 2 y el 3, etc.
                            const level = id.includes('-3') ? 3 : (id.includes('-2') ? 2 : 1);
                            
                            if (level <= 1) window.dispatchEvent(new CustomEvent('close-modal'));
                            if (level <= 2) window.dispatchEvent(new CustomEvent('close-modal-2'));
                            if (level <= 3) window.dispatchEvent(new CustomEvent('close-modal-3'));
                        });
                    }

                    if (triggers['refresh-divs']?.ids) {
                        triggers['refresh-divs'].ids.forEach(id => {
                            const selector = id.startsWith('#') || id.startsWith('.') ? id : `#${id}`;
                            const el = document.querySelector(selector);
                            if (el) htmx.trigger(el, 'refresh');
                        });
                    }

                    if (triggers['refresh-tables']?.ids) {
                        triggers['refresh-tables'].ids.forEach(id => {
                            const selector = id.startsWith('#') || id.startsWith('.') ? id : `#${id}`;
                            const el = document.querySelector(selector);
                            if (el?.tabulator) el.tabulator.setData();
                        });
                    }

                    // Procesar triggers de cabecera y ancho de forma dinámica (multi-nivel)
                    Object.keys(triggers).forEach(key => {
                        // Actualización de cabeceras (update-modal-header, update-modal-header-2, etc.)
                        if (key.startsWith('update-modal-header')) {
                            const suffix = key.replace('update-modal-header', '');
                            window.dispatchEvent(new CustomEvent(key, {
                                detail: triggers[key]
                            }));
                        }

                        // Actualización de anchos (set-modal-width, set-modal-width-2, etc.)
                        if (key.startsWith('set-modal-width')) {
                            const suffix = key.replace('set-modal-width', '');
                            window.dispatchEvent(new CustomEvent(key, {
                                detail: { width: triggers[key].width }
                            }));
                        }

                        // Actualización de acciones/menús (update-modal-actions, update-modal-actions-2, etc.)
                        if (key.startsWith('update-modal-actions')) {
                            window.dispatchEvent(new CustomEvent(key, {
                                detail: { html: triggers[key].html }
                            }));
                        }
                    });
                } catch (err) { }
            }

            // Handle redirect from response body
            const redirect = xhr.getResponseHeader('HX-Redirect');
            if (redirect) {
                window.location.href = redirect;
            }
        }

        document.addEventListener('htmx:afterRequest', function (evt) {
            const xhr = evt.detail.xhr;
            if (xhr) {
                processTriggers(xhr);
            }
        });
    }

    init();
})();