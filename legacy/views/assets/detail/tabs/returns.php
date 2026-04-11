<div class="mb-5">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-base font-bold text-gray-900 flex items-center space-x-1.5">
            <i class="ri-arrow-go-back-line text-xl"></i>
            <span>Returns</span>
        </h2>

        <?php if ($id->status === 'assigned') { ?>
            <?php
                // Buscamos la última asignación
                $lastA = $this->model->get('id', 'asset_events', " AND asset_id = {$id->id} AND kind = 'assignment' ORDER BY id DESC LIMIT 1");

            // Inicializamos variables de control
            $hasFile = false;
            $eventId = null;

            if ($lastA) {
                $eventId = $lastA->id;
                // Verificamos si el archivo físico existe
                $hasFile = file_exists("uploads/assets/{$id->id}/assignment/{$eventId}.pdf");
            }
            ?>

            <?php if ($hasFile) { ?>
                <button
                    class="px-3 py-1.5 rounded-md text-sm font-medium bg-black text-white flex items-center space-x-1"
                    @click='nestedModal = true'
                    hx-get="?c=Assets&a=DetailModal&modal=return&id=<?= $id->id ?>"
                    hx-target="#nestedModal"
                    hx-indicator="#loading">
                    <i class="ri-add-line text-xs"></i>
                    <span>Add Return</span>
                </button>

            <?php } elseif ($eventId) { ?>
                <div class="flex items-center space-x-2">
                    <span class="text-red-600 font-bold text-[10px] bg-red-50 border border-red-200 px-2 py-1 rounded">SUBIR ACTA PARA DEVOLVER</span>
                    <button class="bg-blue-600 text-white px-2 py-1 rounded text-xs"
                        @click='nestedModal = true'
                        hx-get="?c=Assets&a=DetailModal&modal=assignment&id=<?= $id->id ?>&event_id=<?= $eventId ?>"
                        hx-target="#nestedModal">
                        Subir Ahora
                    </button>
                </div>
            <?php } else { ?>
                <span class="text-gray-400 text-[10px] italic text-right">Error: Sin registro de asignación</span>
            <?php } ?>
        <?php } ?>
    </div>

    <div id="tabTableReturns" class="border-hidden text-xs"></div>

    <script>
        (function(){
            function initTableRet(el){
                if (!el || el.dataset.tabulatorInitialized || typeof Tabulator === 'undefined') return;
                el.dataset.tabulatorInitialized = true;

                new Tabulator(el, {
                    pagination: true,
                    paginationMode: "remote",
                    paginationSize: 15,
                    layout: "fitColumns",
                    ajaxURL: "?c=Assets&a=DataReturns&id=<?= $id->id ?>",
                    columns: [
                        {title: "Date", field: "date"},
                        {title: "Assignee", field: "assignee", width: 300},
                        {title: "Notes", field: "notes"},
                        {title: "Hardware", field: "hardware", formatter: "textarea"},
                        {title: "Software", field: "software"},
                        {title: "Minute", field: "minute", formatter: "html"},
                    ],
                });
            }
            // Inicialización
            document.querySelectorAll('#tabTableReturns').forEach(initTableRet);
        })();
    </script>
</div>