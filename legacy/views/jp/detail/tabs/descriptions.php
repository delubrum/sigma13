<div class="mb-5 w-full">
    <h2 class="text-base font-bold text-gray-900 mb-3 flex items-center space-x-1.5">
        <i class="ri-file-text-line text-xl"></i>
        <span><?= ucwords($type) ?></span>
    </h2>

    <?php if ($canEdit) { ?>
        <!-- 🟢 Vista editable (jspreadsheet) -->
        <div id="spreadsheet" class="w-full"></div>

        <style>
            #spreadsheet .jss_worksheet {
                width: 100% !important;
            }
            #spreadsheet .jtoolbar {
                width: 110px !important;
            }
            #spreadsheet td, 
            #spreadsheet textarea {
                text-align: left !important;
            }
        </style>

        <script>
        var saveTimeout;

        function save(data) {
            const payload = {
                type: '<?= $type ?>',
                jp_id: <?= $id->id ?>,
                data: data
            };
            fetch('?c=JP&a=SaveItem', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(payload)
            })
            .then(response => response.json())
            .then(result => {
                if(result.status === 'success'){
                    notyf.success(result.message);
                } else {
                    notyf.error('Error: ' + result.message);
                }
            })
            .catch(() => {
                notyf.error('Hubo un error al guardar los datos.');
            });
        }

        function debouncedSave(data) {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(() => save(data), 500);
        }

        jspreadsheet(document.getElementById('spreadsheet'), {
            onchange: function() {
                debouncedSave(jspreadsheet.current.getData());
            },
            worksheets: [
                {
                    data: <?= $data ?>,
                    minDimensions: [1, 1],
                    tableOverflow: true,
                    tableWidth: "100%",
                    columnDrag: false,
                    columnResize: false,
                    allowInsertColumn: false,
                    columns: [
                        {type: 'text', title: 'Descripción', wordWrap: true, width: 800}
                    ]
                }
            ],
            toolbar: [
                { type: 'i', content: 'undo', onclick: () => { jspreadsheet.current.undo(); save(jspreadsheet.current.getData()); }},
                { type: 'i', content: 'redo', onclick: () => { jspreadsheet.current.redo(); save(jspreadsheet.current.getData()); }},
                { type: 'i', content: 'save', onclick: () => save(jspreadsheet.current.getData()) },
            ],
            contextMenu: function(items) {
                return [
                    {title: 'Copiar', icon: 'content_copy', onclick: function() {jspreadsheet.current.copy();}},
                    {title: 'Pegar', icon: 'content_paste', onclick: function() {jspreadsheet.current.paste();}},
                    {type: 'line'},
                    {title: 'Insertar fila', icon: 'add_circle', onclick: function() {jspreadsheet.current.insertRow();}},
                    {title: 'Borrar fila', icon: 'remove_circle', onclick: function() {
                        if (confirm('¿Estás seguro de que quieres borrar esta fila?')) {
                            jspreadsheet.current.deleteRow();
                        }
                    }},
                    {title: '', icon: ''}
                ];
            }
        });
        </script>

    <?php } else { ?>
        <!-- 🔵 Vista solo lectura (tabla HTML) -->
        <table class="w-full border-collapse rounded-md overflow-hidden border border-gray-200">
            <thead>
                <tr>
                    <th class="bg-gray-100 px-3 py-2 text-left font-medium text-gray-700 text-xs">Descripción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $rows = json_decode($data, true);
        if (is_array($rows)) {
            foreach ($rows as $row) {
                if (! empty(trim($row[0] ?? ''))) { // Solo mostrar si hay descripción
                    ?>
                    <tr class="hover:bg-gray-100">
                        <td class="px-3 py-2 border-b border-gray-200 text-xs"><?= htmlspecialchars($row[0]) ?></td>
                    </tr>
                <?php
                }
            }
        }
        ?>
            </tbody>
        </table>

    <?php } ?>
</div>
