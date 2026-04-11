<style>
    .tabulator-cell.multi-line-cell {
    white-space: pre-wrap; /* Mantiene los saltos de línea del textarea */
    height: auto !important;
}
</style>

<div class="mb-5">
        <div class="flex items-center justify-between mb-3"> 
            <h2 class="text-base font-bold text-gray-900 flex items-center space-x-1.5">
                <i class="ri-user-add-line text-xl"></i>
                <span>Activities</span>
            </h2>

            <?php if ($canEdit and $id->status != 'Closed' and $id->status != 'Canceled') { ?>
                <button 
                    @click="nestedModal = true"
                    class="px-3 py-1.5 rounded-md text-sm font-medium bg-black text-white"
                    hx-get="?c=Improvement&a=Modal&modal=activity&id=<?= $id->id ?>"
                    hx-target="#nestedModal"
                    hx-indicator="#loading">
                    <i class="ri-add-line text-xs"></i>
                    <span>Add Activity</span>
                </button>
            <?php } ?>
        </div>

    <!-- Tabla de appointments -->
    <div id="activitiesTable" class="border-hidden text-xs"></div>
</div>

<script>
(function(){
  function initTable(el){
    if (!el || el.dataset.tabulatorInitialized || typeof Tabulator === 'undefined') return;

    activitiesTable = new Tabulator(el, {
        pagination:true,
        paginationMode:"remote",
        paginationSize:15,
        paginationSizeSelector:[10, 15, 20, 50, 100, 1000000],
        paginationCounter:"rows",
        filterMode:"remote",
        sortMode:"remote",
        layout: "fitColumns",
        ajaxURL: "?c=Improvement&a=ActivitiesData&id=<?= $id->id ?>",
        placeholder:"No Data Set",
        columns: [
            {title:"Date", field:"created_at", headerFilter:"input"},
            {title:"Creator", field:"creator", headerFilter:"input"},
            {title:"Activity", field:"activity", headerFilter:"input", formatter:"textarea"},
            {title:"How To", field:"how", headerFilter:"input", formatter:"textarea"},
            {title:"Responsible", field:"responsible", headerFilter:"input"},
            {title:"When", field:"when", headerFilter:"input"},
            {title:"Done", field:"done", headerFilter:"input", hozAlign:"center"},
            {title:"Results", field:"results", width:200, headerFilter:"input", formatter:"html",cssClass: "multi-line-cell"},
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
  document.querySelectorAll('#activitiesTable').forEach(initTable);

})();
</script>
