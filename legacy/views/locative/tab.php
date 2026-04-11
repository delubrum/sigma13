<div class="mb-5">
    <?php if ($canEdit and $id->status != 'Closed') { ?>
        <div class="flex items-center justify-end mb-3">
            <button 
                @click="nestedModal = true"
                class="px-3 py-1.5 rounded-md text-sm font-medium bg-black text-white"
                hx-get="?c=Locative&a=Modal&modal=task&id=<?= $id->id ?>"
                hx-target="#nestedModal"
                hx-indicator="#loading">
                <i class="ri-add-line text-xs"></i>
                <span>New Task</span>
            </button>
        </div>
    <?php } ?>

    <!-- Tabla de appointments -->
    <div id="taskTable" class="border-hidden text-xs"></div>
</div>

<script>
(function(){
  function initTable(el){
    if (!el || el.dataset.tabulatorInitialized || typeof Tabulator === 'undefined') return;

    taskTable = new Tabulator(el, {
        pagination:true,
        paginationMode:"remote",
        paginationSize:15,
        paginationSizeSelector:[10, 15, 20, 50, 100, 1000000],
        paginationCounter:"rows",
        filterMode:"remote",
        sortMode:"remote",
        layout: "fitColumns",
        ajaxURL: "?c=Locative&a=Task&id=<?= $id->id ?>",
        placeholder:"No Data Set",
        columns: [
            {title:"Date", field:"date", headerFilter:"input"},
            {title:"Operator", field:"user", headerFilter:"input"},
            {title:"Complexity", field:"complexity", headerFilter:"input"},
            {title:"Attends", field:"attends", headerFilter:"input"},
            {title:"Time", field:"time", headerFilter:"input"},
            {title:"Notes", field:"notes", headerFilter:"input",formatter: 'textarea'},
            {title:"File", field:"file", headerFilter:"input", formatter: 'html'},
        ],
    });
  }

  // Inicializa cualquier tabla presente en el DOM
  document.querySelectorAll('#taskTable').forEach(initTable);

})();
</script>
