<div class="mb-5">
        <div class="flex items-center justify-between mb-3"> 
            <h2 class="text-base font-bold text-gray-900 flex items-center space-x-1.5">
                <i class="ri-user-add-line text-xl"></i>
                <span>Plans</span>
            </h2>

            <button 
                @click="nestedModal = true"
                class="px-3 py-1.5 rounded-md text-sm font-medium bg-black text-white"
                hx-get="?c=Performance&a=Modal&modal=plan&id=<?= $id->id ?>"
                hx-target="#nestedModal"
                hx-indicator="#loading">
                <i class="ri-add-line text-xs"></i>
                <span>Add Plans</span>
            </button>
        </div>

    <!-- Tabla de appointments -->
    <div id="appointmentsTable" class="border-hidden text-xs"></div>
</div>

<script>
(function(){
  function initTable(el){
    if (!el || el.dataset.tabulatorInitialized || typeof Tabulator === 'undefined') return;

    appointmentsTable = new Tabulator(el, {
        pagination:true,
        paginationMode:"remote",
        paginationSize:15,
        paginationSizeSelector:[10, 15, 20, 50, 100, 1000000],
        paginationCounter:"rows",
        filterMode:"remote",
        sortMode:"remote",
        layout:"fitColumns",
        ajaxURL: "?c=Performance&a=PlanData&id=<?= $id->id ?>",
        placeholder:"No Data Set",
        columns: [
            {title:"Date", field:"created_at", headerFilter:"input"},
            {title:"Competency", field:"competency", headerFilter:"input", formatter:"textarea"},
            {title:"Plan", field:"plan", headerFilter:"input", formatter:"textarea"},
            {title:"Start", field:"started_at", headerFilter:"input"},
            {title:"End", field:"ended_at", headerFilter:"input"},
            {title:"Follow", field:"follow", headerFilter:"input"},
            {
                title: "Progress",
                field: "progress",
                headerHozAlign: "center",
                headerFilter:"input",
                formatter(cell, formatterParams) {
                    let v = Number(cell.getValue()) || 0;
                    return `
                        <div class="progress-outer" style="position:relative; height:15px; background:#e5e7eb; border-radius:4px;">
                            <div class="progress-inner" style="
                                width:${v}%;
                                height:100%;
                                background:gray;
                                border-radius:4px;">
                            </div>
                            <div class="progress-label" style="
                                position:absolute;
                                top:0; left:0; right:0; bottom:0;
                                display:flex; align-items:center; justify-content:center;
                                font-size:12px; font-weight:bold">
                                ${v}%
                            </div>
                        </div>
                    `;
                }
            },
            {title:"Status", field:"status", headerFilter:"input",},
            {title:"Result", field:"result", headerFilter:"input", formatter:"textarea"},
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
  document.querySelectorAll('#appointmentsTable').forEach(initTable);

})();
</script>
