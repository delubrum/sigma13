<div class="mb-5 w-full">
    <h2 class="text-base font-bold text-gray-900 mb-3 flex items-center space-x-1.5">
        <i class="ri-file-text-line text-xl"></i>
        <span>Competencias</span>
    </h2>

    <?php if ($canEdit) { ?>
        <div id="spreadsheet" class="w-full"></div>

        <style>
            #spreadsheet .jss_worksheet {
                width: 100% !important;
            }
            #spreadsheet .jtoolbar {
                width: 110px !important;
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
            .catch(error => {
                notyf.error('Hubo un error al guardar los datos.');
            });
        }

        function debouncedSave(data) {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(() => {
                save(data);
            }, 500);
        }

        jspreadsheet(document.getElementById('spreadsheet'), {
            onchange: function(instance, cell, x, y, value) {
                debouncedSave(jspreadsheet.current.getData());
            },
            worksheets: [
                {
                    data: <?= $data != '[]' ? $data : json_encode([
                        ['Corporativa', 'Responsabilidad', ''],
                        ['Corporativa', 'Comunicación', ''],
                        ['Corporativa', 'Trabajo en equipo', ''],
                        ['Corporativa', 'Orientación Integral de Calidad', ''],
                        ['Corporativa', 'Proactividad', ''],
                        ['Específica/Comportamiento', 'Liderazgo', ''],
                        ['Específica/Comportamiento', 'Capacidad de análisis y toma de decisiones', ''],
                        ['Específica/Comportamiento', 'Planeación y Organización', ''],
                        ['Específica/Comportamiento', 'Aprendizaje continuo', ''],
                        ['Específica/Comportamiento', 'Expresión oral y escrita', ''],
                    ]) ?>,
                    minDimensions: [3, 1],
                    tableOverflow: true,
                    tableWidth: "100%",
                    columnDrag: false,
                    columnResize: false,
                    allowInsertRow: false,
                    allowInsertColumn: false,
                    columns: [
                        { type: 'text', title: 'Tipo de Habilidad', width: 250, readOnly: true, align: 'left' },
                        { type: 'text', title: 'Competencia', wordWrap: true, readOnly: true, width: 450, align: 'left' },
                        { type: 'numeric', title: 'Nivel Requerido (1-5)', width: 180, align: 'center' }
                    ]
                }
            ],
            toolbar: [
                { type: 'i', content: 'undo', onclick: () => jspreadsheet.current.undo() },
                { type: 'i', content: 'redo', onclick: () => jspreadsheet.current.redo() },
                { type: 'i', content: 'save', onclick: () => save(jspreadsheet.current.getData()) },
            ],
            contextMenu: function(items) { 
                return [
                    {title: 'Copiar', icon: 'content_copy', onclick: function() {jspreadsheet.current.copy();}},
                    {title: 'Pegar', icon: 'content_paste', onclick: function() {jspreadsheet.current.paste();}},
                    {type: 'line'},
                ];
            }
        });
        </script>

    <?php } else { ?>

        <table class="w-full border-collapse rounded-md overflow-hidden border border-gray-200">
            <thead>
                <tr>
                    <th class="bg-gray-100 px-3 py-2 text-left font-medium text-gray-700 text-xs">Tipo de Habilidad</th>
                    <th class="bg-gray-100 px-3 py-2 text-left font-medium text-gray-700 text-xs">Competencia</th>
                    <th class="bg-gray-100 px-3 py-2 text-center font-medium text-gray-700 text-xs">Nivel Requerido (1-5)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $rows = json_decode($data, true);
        if (! is_array($rows) || empty($rows)) {
            $rows = [
                ['Corporativa', 'Responsabilidad', ''],
                ['Corporativa', 'Comunicación', ''],
                ['Corporativa', 'Trabajo en equipo', ''],
                ['Corporativa', 'Orientación Integral de Calidad', ''],
                ['Corporativa', 'Proactividad', ''],
                ['Específica/Comportamiento', 'Liderazgo', ''],
                ['Específica/Comportamiento', 'Capacidad de análisis y toma de decisiones', ''],
                ['Específica/Comportamiento', 'Planeación y Organización', ''],
                ['Específica/Comportamiento', 'Aprendizaje continuo', ''],
                ['Específica/Comportamiento', 'Expresión oral y escrita', ''],
            ];
        }

        $hasData = false;
        foreach ($rows as $row) {
            if (! empty(trim($row[2] ?? ''))) {
                $hasData = true;
                ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 border-b border-gray-200 text-xs"><?= htmlspecialchars($row[0] ?? '') ?></td>
                        <td class="px-3 py-2 border-b border-gray-200 text-xs"><?= htmlspecialchars($row[1] ?? '') ?></td>
                        <td class="px-3 py-2 border-b border-gray-200 text-xs text-center"><?= htmlspecialchars($row[2] ?? '') ?></td>
                    </tr>
                <?php
            }
        }

        if (! $hasData) {
            ?>
                    <tr>
                        <td colspan="3" class="px-3 py-4 text-center text-gray-500 text-xs italic">
                            Sin información disponible
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    <?php } ?>
</div>
