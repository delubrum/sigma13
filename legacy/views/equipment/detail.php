<style>
.tabulator .tabulator-header .tabulator-col {
    background-color: black;
    color: white;
}
.tabulator .tabulator-header .tabulator-col input {
    color: black;
}
.tabulator .tabulator-footer .tabulator-page.active {
    color: black;
}
</style>

<div class="w-[95%] sm:w-[95%] bg-white p-6 rounded-lg shadow-xl relative overflow-y-auto max-h-[98vh]">
    <button id="closeNewModal"
        class="absolute top-0 right-0 m-3 text-gray-900 hover:text-gray-700"
        @click="showModal = false; document.getElementById('myModal').innerHTML = '';"
    >
        <i class="ri-close-line text-2xl"></i>
    </button>

    <div class="flex justify-between items-center mx-4 mt-4">
        <div class="flex space-x-2">
            <button 
                class="flex items-center gap-2 bg-black text-white hover:opacity-80 transition-all duration-300 px-4 py-2 rounded-lg font-semibold text-sm shadow"
                id="download-excel-detail"
            >
                <i class="ri-file-excel-2-line"></i>
            </button>
        </div>

        <div class="text-gray-800 text-lg font-bold">
            <i class="ri-history-line"></i> Historial de Movimientos <?= $this->model->get('name', 'equipment_db', ' and id = '.$_REQUEST['id'])->name ?>
        </div>
    </div>

    <div class="m-4 border-hidden text-xs" id="detail-list"></div>
</div>

<script>
var detailTable = new Tabulator("#detail-list", {
    pagination:true, //enable pagination
    paginationMode:"remote",
    paginationSize:15,
    paginationSizeSelector:[10, 15, 20, 50, 100, 1000000],
    paginationCounter:"rows",
    movableColumns:true,
    filterMode:"remote",
    sortMode:"remote",
    layout:"fitDataStretch",
    ajaxURL: "?c=EquipmentEntries&a=DetailData",
    ajaxParams: { id: "<?= $_REQUEST['id'] ?>" },
    placeholder:"No Data Set",
    columns: [
        { title: "Fecha", field: "date", width: 150, hozAlign: "center" },
        { 
            title: "Tipo", 
            field: "type", 
            width: 100, 
            formatter: "html", 
            hozAlign: "center" 
        },
        { title: "Cant.", field: "qty", width: 80, hozAlign: "center" },
        { title: "Usuario / Empleado", field: "user", minWidth: 150 },
    ],
});

// Exportación Excel
document.getElementById("download-excel-detail").addEventListener("click", function(){
    detailTable.download("xlsx", "Historial_ID_<?= $_REQUEST['id'] ?>.xlsx", {
        sheetName: "Movimientos"
    });
});

// Listener para refrescar tabla desde otros componentes
document.body.addEventListener("eventChanged", function() {
    detailTable.setData();
});
</script>