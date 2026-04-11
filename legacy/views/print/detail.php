<div class="w-[98%] max-h-[95vh] rounded-xl shadow-2xl relative z-50 bg-white text-gray-800 flex flex-col overflow-hidden">

    <div class="flex items-center justify-between w-full p-4 border-b border-gray-200 noprint">
        <div class="flex items-center gap-2 sm:gap-3 min-w-0">
            <div class="bg-black p-2 rounded-lg shadow-md shrink-0">
                <i class="ri-printer-fill text-white text-lg sm:text-xl"></i>
            </div>
            <div class="min-w-0">
                <h1 class="text-sm sm:text-xl font-extrabold text-gray-900 truncate">
                    <?= $wo->code ?>
                    <span class="sm:inline text-gray-400 mx-1">|</span>
                    <span class="sm:inline">Print Labels</span>
                </h1>
                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">
                    Work Orders
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">

            <button
                type="button"
                hx-post="?c=Print&a=Delete&id=<?= $wo->code ?>"
                hx-confirm="Are you sure you wish to delete this WO?"
                hx-swap="none"
                hx-indicator="#loading"
                class="flex items-center justify-center
                    bg-red-500 text-white hover:bg-red-700
                    w-10 h-10 sm:w-auto sm:h-auto
                    sm:px-6 py-2.5 rounded-xl
                    text-xs font-bold shadow-lg
                    active:scale-95 transition">
                <i class="ri-delete-bin-line text-lg"></i>
                <span class="hidden sm:inline ml-2">DELETE</span>
            </button>

            <button
                id="printTicketsBtn"
                class="flex items-center justify-center bg-black text-white hover:bg-gray-800
                       w-10 h-10 sm:w-auto sm:h-auto
                       sm:px-6 py-2.5 rounded-xl
                       text-xs font-bold shadow-lg active:scale-95">
                <i class="ri-printer-line text-lg"></i>
                <span class="hidden sm:inline ml-2">PRINT SELECTED LABELS</span>
            </button>

            <button
                class="p-1 text-gray-500 hover:text-gray-900"
                id="closeNewModal"
                @click="showModal = false; document.getElementById('myModal').innerHTML = '';">
                <i class="ri-close-line text-xl sm:text-2xl"></i>
            </button>
        </div>
    </div>

    <!-- TABLE -->
    <div class="p-3 sm:p-4 flex-1 overflow-hidden text-xs">
        <div id="labels-table"></div>
    </div>

    <!-- LOADER -->
    <div
        id="printLoader"
        class="absolute inset-0 bg-white/80 backdrop-blur-sm z-[999]
               hidden flex items-center justify-center">
        <div class="flex flex-col items-center gap-3">
            <div class="w-10 h-10 border-4 border-gray-300 border-t-black rounded-full animate-spin"></div>
            <span class="text-xs font-bold tracking-widest text-gray-700 uppercase">
                Preparing print…
            </span>
        </div>
    </div>

</div>

<script>
(function () {

    const tableData = <?= json_encode(array_map(function ($r) {
        return [
            'id' => $r->code,
            'description' => stripslashes($r->description),
            'fuc' => $r->fuc,
            'qty' => (int) $r->qty,
            'print_val' => 0,
        ];
    }, $items)); ?>;

    const isEsId    = <?= ($wo->es_id) ? 'true' : 'false' ?>;
    const woId      = '<?= $wo->code ?>';
    const esIdValue = '<?= $wo->es_id ?? '' ?>';
    const loader    = document.getElementById('printLoader');

    const columns = [
        isEsId ? {
            titleFormatter: "rowSelection",
            formatter: "rowSelection",
            hozAlign: "center",
            headerHozAlign: "center",
            headerSort: false,
            width: 50,
            cellClick: function (e, cell) {
                cell.getRow().toggleSelect();
            }
        } : null,

        !isEsId ? {
            title: "Print Qty",
            field: "print_val",
            width: 110,
            hozAlign: "center",
            editor: "number",
            editorParams: { min: 0 },
            headerFilter: "tickCross",
            headerFilterParams: { tristate: false },
            headerFilterFunc: () => true,
            formatter: cell => `<div>${cell.getValue() || 0}</div>`
        } : null,

        { title: "Part #", field: "id", width: 120, headerFilter: "input" },
        { title: "Description", field: "description", headerFilter: "input", formatter: "textarea" },
        { title: "Finish", field: "fuc", headerFilter: "input" },
        { title: "Stock", field: "qty", hozAlign: "center", headerFilter: "number" }
    ].filter(Boolean);

    const table = new Tabulator("#labels-table", {
        data: tableData,
        layout: "fitColumns",
        height: "100%",
        selectable: isEsId,
        headerFilter: true,
        columns: columns,
    });

    if (!isEsId) {
        table.on("renderComplete", () => {
            const headerCheckbox = document.querySelector(".tabulator-header input[type=checkbox]");
            if (headerCheckbox) {
                headerCheckbox.onchange = (e) => {
                    const useStock = e.target.checked;
                    table.getRows("active").forEach(row => {
                        row.update({ print_val: useStock ? row.getData().qty : 0 });
                    });
                };
            }
        });
    }

    document.getElementById('printTicketsBtn').addEventListener('click', () => {

        const payload = new URLSearchParams();
        payload.append('woId', woId);

        const url = isEsId ? '?c=Print&a=ES' : '?c=Print&a=ESM';

        if (isEsId) {
            const selectedData = table.getSelectedData();
            if (!selectedData.length) {
                alert('Select at least one item');
                return;
            }
            selectedData.forEach(r => {
                payload.append('id[]', r.id);
                payload.append('marca[]', r.description);
                payload.append('esid[]', esIdValue);
            });
        } else {
            const rowsWithQty = table.getData().filter(r => r.print_val > 0);
            if (!rowsWithQty.length) {
                alert('Enter at least one quantity');
                return;
            }
            rowsWithQty.forEach(r => {
                payload.append('id[]', r.id);
                payload.append('val[]', r.print_val);
            });
        }

        loader.classList.remove('hidden');

        const printWindow = window.open('about:blank');

        if (!printWindow) {
            loader.classList.add('hidden');
            alert('Por favor permita ventanas emergentes para imprimir');
            return;
        }

        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Generando etiquetas...</title>
                <style>
                    body { margin: 0; height: 100vh; display: flex; align-items: center; justify-content: center; font-family: Arial, sans-serif; background: #f5f5f5; }
                    .loader { text-align: center; }
                    .spinner { width: 50px; height: 50px; border: 5px solid #ddd; border-top-color: #333; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 20px; }
                    @keyframes spin { to { transform: rotate(360deg); } }
                </style>
            </head>
            <body>
                <div class="loader">
                    <div class="spinner"></div>
                    <p>Generando etiquetas...</p>
                </div>
            </body>
            </html>
        `);

        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: payload.toString()
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.text();
        })
        .then(html => {
            loader.classList.add('hidden');
            if (!html || html.trim().length === 0) throw new Error('Empty response from server');
            printWindow.document.open();
            printWindow.document.write(html);
            printWindow.document.close();
        })
        .catch(error => {
            loader.classList.add('hidden');
            console.error('Print error:', error);
            printWindow.document.open();
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Error</title>
                    <style>
                        body { margin: 0; height: 100vh; display: flex; align-items: center; justify-content: center; font-family: Arial, sans-serif; background: #f5f5f5; }
                        .error-box { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 30px rgba(0,0,0,0.1); max-width: 500px; text-align: center; }
                        .error-icon { font-size: 48px; color: #e74c3c; margin-bottom: 20px; }
                        h1 { color: #e74c3c; margin: 0 0 10px 0; }
                        p { color: #666; }
                    </style>
                </head>
                <body>
                    <div class="error-box">
                        <div class="error-icon">⚠️</div>
                        <h1>Error al generar etiquetas</h1>
                        <p>${error.message}</p>
                        <p style="font-size: 12px; color: #999; margin-top: 20px;">Puede cerrar esta ventana</p>
                    </div>
                </body>
                </html>
            `);
            printWindow.document.close();
        });
    });

})();
</script>