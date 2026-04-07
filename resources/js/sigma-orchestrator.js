// SIGMA Orchestrator - Handles HX-Trigger header from Laravel
(function() {
    function init() {
        const htmx = window.htmx;
        if (!htmx) {
            setTimeout(init, 50);
            return;
        }

        function processTriggers(xhr) {
            // Get HX-Trigger header
            const triggerHeader = xhr.getResponseHeader('Hx-Trigger');
            if (triggerHeader) {
                try {
                    const triggers = JSON.parse(triggerHeader);

                    if (triggers.notify) {
                        const { type, message } = triggers.notify;
                        window.notyf[type === 'error' ? 'error' : 'success'](message);
                    }

                    if (triggers['close-modals']?.ids) {
                        triggers['close-modals'].ids.forEach(id => {
                            const el = document.querySelector(id);
                            if (el) {
                                if (typeof el.close === 'function') el.close();
                                else el.classList.add('hidden');
                            }
                        });
                    }

                    if (triggers['refresh-divs']?.ids) {
                        triggers['refresh-divs'].ids.forEach(id => {
                            const el = document.querySelector(id);
                            if (el) htmx.trigger(el, 'refresh');
                        });
                    }

                    if (triggers['refresh-tables']?.ids) {
                        triggers['refresh-tables'].ids.forEach(id => {
                            const el = document.querySelector(id);
                            if (el?.tabulator) el.tabulator.setData();
                        });
                    }

                    if (triggers['update-modal-header']) {
                        const { icon, title, subtitle } = triggers['update-modal-header'];
                        window.dispatchEvent(new CustomEvent('update-modal-header', {
                            detail: { icon, title, subtitle }
                        }));
                    }

                    if (triggers['set-modal-width']?.width) {
                        window.dispatchEvent(new CustomEvent('set-modal-width', {
                            detail: { width: triggers['set-modal-width'].width }
                        }));
                    }
                } catch (err) {}
            }

            // Handle redirect from response body
            const redirect = xhr.getResponseHeader('HX-Redirect');
            if (redirect) {
                window.location.href = redirect;
            }
        }

        document.addEventListener('htmx:afterRequest', function(evt) {
            const xhr = evt.detail.xhr;
            if (xhr) {
                processTriggers(xhr);
            }
        });
    }

    init();
})();