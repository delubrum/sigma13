<div class="mb-5">
        <div class="flex items-center justify-between mb-3"> 
            <h2 class="text-base font-bold text-gray-900 flex items-center space-x-1.5">
                <i class="ri-user-add-line text-xl"></i>
                <span>Causes</span>
            </h2>

            <?php if ($canEdit and $id->status != 'Closed' and $id->status != 'Canceled') { ?>
                <button 
                    @click="nestedModal = true"
                    class="px-3 py-1.5 rounded-md text-sm font-medium bg-black text-white"
                    hx-get="?c=Improvement&a=Modal&modal=cause&id=<?= $id->id ?>"
                    hx-target="#nestedModal"
                    hx-indicator="#loading">
                    <i class="ri-add-line text-xs"></i>
                    <span>Add Cause</span>
                </button>
            <?php } ?>
        </div>

    <!-- Tabla de appointments -->
    <div id="causesTable" class="border-hidden text-xs"></div>
</div>

<script>
(function(){
  function initTable(el){
    if (!el || el.dataset.tabulatorInitialized || typeof Tabulator === 'undefined') return;

    causesTable = new Tabulator(el, {
        pagination:true,
        paginationMode:"remote",
        paginationSize:15,
        paginationSizeSelector:[10, 15, 20, 50, 100, 1000000],
        paginationCounter:"rows",
        filterMode:"remote",
        sortMode:"remote",
        layout: "fitColumns",
        ajaxURL: "?c=Improvement&a=CausesData&id=<?= $id->id ?>",
        placeholder:"No Data Set",
        columns: [
            // {title:"Date", field:"created_at", headerFilter:"input"},
            // {title:"User", field:"creator", headerFilter:"input"},
            {title:"Cause", field:"reason", headerFilter:"input", formatter: "textarea"},
            {title:"Method", field:"method", headerFilter:"input"},
            {title:"Probable", field:"probable", headerFilter:"input", formatter: "textarea"},
            {title:"Actions", field:"actions", headerFilter:"input", hozAlign:"center",     
                formatter: function(cell){
                let html = cell.getValue();

                // Una vez el HTML está en el DOM, reconectar frameworks
                setTimeout(() => {
                    htmx.process(cell.getElement());
                    Alpine.initTree(cell.getElement());
                }, 0);

                return html;
            }},
        ],
    });
  }

  // Inicializa cualquier tabla presente en el DOM
  document.querySelectorAll('#causesTable').forEach(initTable);

})();
</script>
