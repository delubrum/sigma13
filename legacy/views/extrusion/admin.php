<style>
/* Estilos para que los filtros encajen en el diseño oscuro */
.tabulator .tabulator-header .tabulator-col {
    background-color: black;
    color: white;
}

/* Estilo específico para los inputs de filtro */
.tabulator-header-filter input {
    color: black !important;
    padding: 4px;
    width: 100%;
    box-sizing: border-box;
    border-radius: 4px;
    border: 1px solid #ccc;
    margin-top: 4px;
}
</style>

<div class="flex justify-between items-center mx-4 mt-4">
    <div class="flex space-x-2"></div>
    <button 
        class="flex items-center gap-2 bg-black text-white px-4 py-2 rounded-lg font-semibold text-sm shadow"
        hx-get='?c=<?= $_REQUEST['c'] ?>&a=NewItem'
        hx-target="#myModal"
        @click='showModal = true'
    >
        <i class="ri-add-line"></i> <?= $button ?>
    </button>
</div>

<div class="m-4">
    <div id="list"></div>
</div>

<script>
var tableData = [
    <?php foreach ($this->model->list('id, kind, name', 'matrices_db', 'ORDER BY id DESC') as $r) { ?>
    {id: "<?= $r->id ?>", type: "<?= $r->kind ?>", name: "<?= $r->name ?>"},
    <?php } ?>
];

var table = new Tabulator("#list", {
    data: tableData,
    layout: "fitColumns",
    pagination: "local",
    paginationSize: 15,
    columns: [
        {title: "Type", field: "type", headerFilter: "input"},
        {title: "Name", field: "name", headerFilter: "input"},
    ],
});

// Lógica para capturar el clic en la fila
table.on("rowClick", function(e, row){
    // Evitamos disparar el clic si se toca el header o un input de filtro
    if (e.target.closest(".tabulator-header") || e.target.tagName === 'INPUT') return;

    let id = row.getData().id;

    // Llamamos a NewItem pasando el ID para que cargue los datos del registro
    htmx.ajax('GET', `?c=Extrusion&a=NewItem&id=${id}`, {
        target: '#myModal',
        swap: 'innerHTML'
    });

    // Abrimos el modal (Usando la lógica de Alpine de tu proyecto)
    let modalElement = document.getElementById('myModal');
    if (modalElement) {
        Alpine.evaluate(modalElement, 'showModal = true');
    }
});
</script>