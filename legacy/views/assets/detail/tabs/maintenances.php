<div class="mb-5">
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
        paginationSize:10,
        paginationSizeSelector:[10, 15, 20, 50, 100, 1000000],
        paginationCounter:"rows",
        filterMode:"remote",
        sortMode:"remote",
        layout: "fitColumns",
        ajaxURL: "?c=Assets&a=Maintenances&id=<?= $id->id ?>",
        placeholder:"No Data Set",
        columns: [
            { "title": "ID", "field": "id", "width": 60, "sorter": "number", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Type", "field": "type", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Date", "field": "date", headerHozAlign: "center", headerFilter: customDateRangeFilter, headerFilterFunc: customDateFilterFunc, headerFilterLiveFilter: false },
            { "title": "User", "field": "user", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Description", "field": "description", "formatter": "textarea", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Closed", "field": "closed", headerHozAlign: "center", headerFilter: customDateRangeFilter, headerFilterFunc: customDateFilterFunc, headerFilterLiveFilter: false },
            { "title": "Status", "field": "status", headerHozAlign: "center", hozAlign:"center", headerFilter:"list",
            headerFilterParams:{ values: {"Open": "Open", "Started": "Started", "Attended": "Attended", "Closed": "Closed", "Rated": "Rated"}, clearable:true}},
            { "title": "Rating", "field": "rating", "sorter": "number", "headerHozAlign": "left", "headerFilter": "number"},
        ],
    });
  }

  // Inicializa cualquier tabla presente en el DOM
  document.querySelectorAll('#taskTable').forEach(initTable);

})();
</script>
