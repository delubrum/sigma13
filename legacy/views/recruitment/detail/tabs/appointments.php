<div class="mb-5">
    <?php if ($id->approved_at and ! $id->rejection and $id->status != 'closed') { ?>
        <div class="flex items-center justify-between mb-3"> 
            <h2 class="text-base font-bold text-gray-900 flex items-center space-x-1.5">
                <i class="ri-user-add-line text-xl"></i>
                <span>Candidates</span>
            </h2>

            <button 
                @click="nestedModal = true"
                class="px-3 py-1.5 rounded-md text-sm font-medium bg-black text-white"
                hx-get="?c=Recruitment&a=DetailModal&modal=appointment&id=<?= $id->id ?>"
                hx-target="#nestedModal"
                hx-indicator="#loading">
                <i class="ri-add-line text-xs"></i>
                <span>Add Candidate</span>
            </button>
        </div>
    <?php } else { ?>
        <h2 class="text-base font-bold text-gray-900 mb-3 flex items-center space-x-1.5">
            <i class="ri-user-add-line text-xl"></i>
            <span>Candidates</span>
        </h2>
    <?php } ?>

    <div id="appointmentsTable" class="border-hidden text-xs"></div>
</div>

<script>
(function(){
  // 1. Definimos el ID del usuario logueado desde PHP
  const currentUserId = <?= json_encode($user->id) ?>;

  function initTable(el){
    if (!el || el.dataset.tabulatorInitialized || typeof Tabulator === 'undefined') return;

    const appointmentsTable = new Tabulator(el, {
        pagination: true,
        paginationMode: "remote",
        paginationSize: 15,
        paginationSizeSelector: [10, 15, 20, 50, 100, 1000000],
        paginationCounter: "rows",
        filterMode: "remote",
        sortMode: "remote",
        layout: "fitColumns",
        ajaxURL: "?c=Recruitment&a=DataCandidates&id=<?= $id->id ?>",
        placeholder: "No Data Set",
        columns: [
            {title:"Date", field:"appointment", headerFilter:"input"},
            {title:"Type", field:"type", headerFilter:"input"},
            {title:"Creator", field:"user", headerFilter:"input"},
            {title:"Name", field:"name", headerFilter:"input"},
            {title:"CC", field:"cc", headerFilter:"input"},
            {title:"Email", field:"email", headerFilter:"input"},
            {title:"Phone", field:"phone", headerFilter:"input"},
            {title:"Status", field:"status", headerFilter:"input"},
            
            // --- COLUMNA DE ELIMINAR ---
            {
                title: "", 
                field: "user_id", 
                headerSort: false, 
                width: 40,
                hozAlign: "center",
                formatter: function(cell) {
                    const val = cell.getValue();
                    // Solo mostramos el icono si el ID del creador coincide con el logueado
                    if (val == currentUserId) {
                        return `<button class="text-red-500 hover:text-red-700 transition-colors">
                                    <i class="ri-delete-bin-line text-base"></i>
                                </button>`;
                    }
                    return "";
                },
                cellClick: function(e, cell) {
                    const data = cell.getRow().getData();
                    
                    if (data.user_id == currentUserId) {
                        e.stopPropagation(); // Evita el rowClick
                        
                        if (confirm(`¿Estás seguro de eliminar al candidato ${data.name}?`)) {
                            // HTMX enviará la petición y el PHP responderá con eventChanged para refrescar
                            htmx.ajax('DELETE', `?c=Recruitment&a=DeleteCandidate&id=${data.id}`);
                        }
                    }
                }
            }
        ],
    });

    // 2. Escuchar el evento global para refrescar la tabla automáticamente
    // Esto funciona tanto para SaveCandidate como para DeleteCandidate
    document.body.addEventListener("eventChanged", function(evt){
        appointmentsTable.setData(); 
    });

    // 3. Evento de clic en la fila para ver detalle
    appointmentsTable.on("rowClick", function(e, row){
        // No abrir si es click en inputs, botones o el icono de borrar
        if (e.target.closest(".tabulator-header") || 
            e.target.tagName === 'INPUT' || 
            e.target.closest("button") || 
            e.target.closest(".ri-delete-bin-line")) return;

        let id = row.getData().id;

        htmx.ajax('GET', `?c=Recruitment&a=Candidate&id=${id}`, {
            target: '#nestedModal',
            swap: 'innerHTML',
            headers: { 'HX-Request': 'true' }
        });

        let component = document.getElementById('myModal');
        if(component) Alpine.evaluate(component, 'nestedModal = true');
    });

    el.dataset.tabulatorInitialized = true;
  }

  // Inicializa la tabla
  document.querySelectorAll('#appointmentsTable').forEach(initTable);

})();
</script>