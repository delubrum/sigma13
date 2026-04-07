<div class="mb-5">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-base font-bold text-gray-900 flex items-center space-x-1.5">
            <i class="ri-user-add-line text-xl"></i>
            <span>Assignments</span>
        </h2>

        <?php if ($id->status === 'available') { ?>
            <button
                class="px-3 py-1.5 rounded-md text-sm font-medium bg-black text-white flex items-center space-x-1"
                @click='nestedModal = true'
                hx-get="?c=Assets&a=DetailModal&modal=assignment&id=<?= $id->id ?>"
                hx-target="#nestedModal"
                hx-indicator="#loading">
                <i class="ri-add-line text-xs"></i>
                <span>Add Assignment</span>
            </button>
        <?php } ?>
    </div>

    <div id="tabTableAssignments" class="border-hidden text-xs"></div>

    <script>
        (function(){
            // Función para abrir el modal de edición manualmente
            window.editAssignment = function(assetId, eventId) {
                // 1. Disparamos la carga del contenido vía AJAX (Lógica de tu archivo ejemplo)
                htmx.ajax('GET', `?c=Assets&a=DetailModal&modal=assignment&id=${assetId}&event_id=${eventId}`, {
                    target: '#nestedModal',
                    swap: 'innerHTML'
                });

                // 2. Abrimos el modal usando Alpine (Lógica de tu archivo ejemplo)
                // Buscamos el componente que tiene el x-data de nestedModal
                let component = document.querySelector('[x-data*="nestedModal"]');
                if (component) {
                    Alpine.evaluate(component, 'nestedModal = true');
                }
            };

            function initTableAssig(el){
                if (!el || el.dataset.tabulatorInitialized || typeof Tabulator === 'undefined') return;
                el.dataset.tabulatorInitialized = true;

                new Tabulator(el, {
                    pagination: true,
                    paginationMode: "remote",
                    paginationSize: 15,
                    layout: "fitColumns",
                    ajaxURL: "?c=Assets&a=DataAssignments&id=<?= $id->id ?>",
                    columns: [
                        {title:"ID", field:"id", width:70},
                        {title:"Date", field:"date"},
                        {title:"Assignee", field:"assignee", width:250},
                        {title:"Hardware", field:"hardware", formatter: "textarea"},
                        {title:"Software", field:"software"},
                        {title:"Minute", field:"minute", formatter: "html"},
                        {
                            title: "Edit", 
                            field: "is_latest", 
                            headerSort: false, 
                            width: 80, 
                            formatter: function(cell) {
                                if(cell.getValue()){
                                    let d = cell.getData();
                                    // Llamamos a la función global de JS
                                    return `<button onclick="editAssignment('${d.asset_id}', '${d.id}')" class="bg-blue-600 text-white px-2 py-1 rounded text-[10px] hover:bg-blue-700 transition-colors">
                                                <i class="ri-edit-line"></i> Edit
                                            </button>`;
                                }
                                return "";
                            }
                        }
                    ],
                });
            }

            document.querySelectorAll('#tabTableAssignments').forEach(initTableAssig);
        })();
    </script>
</div>