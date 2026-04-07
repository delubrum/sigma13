<style>
.tabulator .tabulator-header .tabulator-col {
    background-color: black;
    color: white;
}
.tabulator .tabulator-header .tabulator-col input,
.tabulator .tabulator-header .tabulator-col select {
    color: black;
}
.flatpickr-input[readonly] {
    height: 24px;
}
.tabulator .tabulator-footer .tabulator-page.active {
    color: black;
}
</style>

<div class="flex justify-between items-center mx-4 mt-4">
    <div class="flex space-x-2">

        <?php if (isset($kanban)) { ?>
        <a class="flex items-center gap-2 bg-black text-white hover:opacity-80 transition-all duration-300 px-4 py-2 rounded-lg font-semibold text-sm shadow"
            href="?c=<?= $_REQUEST['c'] ?>&a=Kanban">
            <i class="ri-layout-masonry-line"></i>
        </a>
        <?php } ?>

        <button class="flex items-center gap-2 bg-black text-white hover:opacity-80 transition-all duration-300 px-4 py-2 rounded-lg font-semibold text-sm shadow"
            id="download-csv">
            <i class="ri-file-excel-2-line"></i>
        </button>

        <?php if (isset($filterReset) && $filterReset) { ?>
        <a class="flex items-center gap-2 bg-red-700 text-white hover:opacity-80 transition-all duration-300 px-4 py-2 rounded-lg font-semibold text-sm shadow"
            href="?c=<?= $_REQUEST['c'] ?>&a=Index" title="Limpiar Filtros">
            <i class="ri-filter-off-line"></i>
        </a>
        <?php } ?>

        <?php if (isset($kpis)) { ?>
        <a class="flex items-center gap-2 bg-black text-white hover:opacity-80 transition-all duration-300 px-4 py-2 rounded-lg font-semibold text-sm shadow"
            target="_blank" href="?c=<?= $_REQUEST['c'] ?>&a=Kpis">
            <i class="ri-line-chart-line"></i>
        </a>
        <?php } ?>
    </div>

    <?php if (! empty($button)) { ?>
    <button class="flex items-center gap-2 bg-black text-white hover:opacity-80 transition-all duration-300 px-4 py-2 rounded-lg font-semibold text-sm shadow"
        hx-get='?c=<?= $_REQUEST['c'] ?>&a=New'
        hx-target="#myModal"
        hx-indicator="#loading"
        @click='showModal = true'>
        <i class="ri-add-line"></i> <?= $button ?? 'New' ?>
    </button>
    <?php } ?>
</div>

<!-- Búsqueda IA natural -->
<div class="mx-4 mt-3 flex gap-2">
    <div class="relative flex-1">
        <i class="ri-sparkling-2-line absolute left-3 top-1/2 -translate-y-1/2 text-black"></i>
        <input id="ai-search-input" type="text"
            placeholder="Busca en lenguaje natural: ej. 'empresa ES tipo cover que el b sea como 2.6 y de alto h tenga mas de 10 pero q no pase de 15.5 y e1 > 0.8'"
            class="w-full pl-9 pr-4 py-2 border border-violet-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-violet-400">
    </div>
    <button id="ai-search-btn"
        class="flex items-center gap-2 bg-black text-white hover:opacity-80 px-4 py-2 rounded-lg text-sm font-semibold shadow">
        <i class="ri-search-line"></i> <span id="ai-search-btn-text">Buscar</span>
    </button>
    <button id="ai-search-clear"
        class="flex items-center gap-2 bg-gray-100 text-gray-600 hover:bg-gray-200 px-3 py-2 rounded-lg text-sm"
        title="Volver a vista normal">
        <i class="ri-close-line"></i>
    </button>
</div>

<div id="stats"></div>
<div class="m-4 border-hidden text-xs" id="list"></div>

<script>
function customSelectFilter(cell, onRendered, success, cancel, editorParams) {
    let sel = document.createElement('select');
    sel.style.cssText = 'width:100%;padding:4px 2px;background:white;color:black;border:none;';
    sel.add(new Option('', ''));
    (editorParams.values || []).forEach(v => sel.add(new Option(v, v)));
    sel.addEventListener('change', () => success(sel.value));
    return sel;
}

var table = new Tabulator("#list", {
    pagination:true,
    paginationMode:"remote",
    paginationSize:15,
    paginationSizeSelector:[10, 15, 20, 50, 100, 500, 1000],
    paginationCounter:"rows",
    movableColumns:true,
    filterMode:"remote",
    sortMode:"remote",
    layout:"fitDataStretch",
    ajaxURL: "?c=<?= $_REQUEST['c'] ?>&a=Data",
    placeholder:"No Data Set",
    columns: <?= $columns ?? '' ?>,
});

// ─── Filtros relacionales ─────────────────────────────────────────────────────
const relationalFields = ['company', 'category', 'b', 'h', 'e1', 'e2'];
let filterOptionsTimer = null;

function updateRelationalOptions(filters) {
    let params = new URLSearchParams();
    filters.forEach(f => params.append(`filter[${f.field}]`, f.value));

    fetch(`?c=<?= $_REQUEST['c'] ?>&a=FilterOptions&${params.toString()}`)
        .then(r => r.json())
        .then(options => {
            relationalFields.forEach(field => {
                if (!options[field]) return;
                let colEl = table.getColumn(field)?.getElement();
                if (!colEl) return;
                let sel = colEl.querySelector('select');
                if (!sel) return;
                let current = sel.value;
                while (sel.options.length) sel.remove(0);
                sel.add(new Option('', ''));
                options[field].forEach(v => {
                    let opt = new Option(v, v);
                    if (v === current) opt.selected = true;
                    sel.add(opt);
                });
            });
        });
}

// ─── Eventos tabla ────────────────────────────────────────────────────────────
table.on("dataFiltered", function(filters, rows) {
    if (aiModeActive) return;
    let params = new URLSearchParams();
    filters.forEach(f => params.append(`filter[${f.field}]`, f.value));
    htmx.ajax('GET', `?c=<?= $_REQUEST['c'] ?>&a=Stats&${params.toString()}`, { target:"#stats", swap:"innerHTML" });
    clearTimeout(filterOptionsTimer);
    filterOptionsTimer = setTimeout(() => updateRelationalOptions(filters), 250);
});

<?php if ($canEdit) { ?>
table.on("rowClick", function(e, row) {
    if (e.target.closest('[tabulator-field="files"]')) return;
    if (e.target.closest(".tabulator-header") || e.target.tagName === 'INPUT') return;
    let id = row.getData().id;
    htmx.ajax('GET', `?c=<?= $_REQUEST['c'] ?>&a=Detail&id=${id}`, {
        target:'#myModal', swap:'innerHTML', headers:{'HX-Request':'true'}
    });
    Alpine.evaluate(document.getElementById('myModal'), 'showModal = true');
});
<?php } ?>

document.getElementById("download-csv").addEventListener("click", function() {
    let lastColumn = table.getColumns().slice(-1)[0];
    lastColumn.hide();
    table.download("xlsx", "data.xlsx", {sheetName:"Hoja1"});
    lastColumn.show();
});

// ─── Búsqueda IA ─────────────────────────────────────────────────────────────
let aiModeActive = false;

function runAiSearch() {
    let q = document.getElementById("ai-search-input").value.trim();
    if (!q) return;

    let btn = document.getElementById("ai-search-btn-text");
    btn.textContent = "Buscando...";

    fetch(`?c=<?= $_REQUEST['c'] ?>&a=AiSearch&q=${encodeURIComponent(q)}`)
        .then(r => r.json())
        .then(params => {
            btn.textContent = "Buscar";
            console.log('AiSearch params:', params);

            if (!params || !Object.keys(params).length) {
                alert("No pude interpretar la búsqueda, intenta con otros términos.");
                return;
            }

            aiModeActive = true;
            const aiUrl = `?c=<?= $_REQUEST['c'] ?>&a=AiData&params=${encodeURIComponent(JSON.stringify(params))}`;
            console.log('AiData URL:', aiUrl);

            fetch(aiUrl)
                .then(r => r.json())
                .then(data => console.log('AiData response:', data));

            table.setData(aiUrl);
        })
        .catch(e => { console.error(e); btn.textContent = "Buscar"; });
}

document.getElementById("ai-search-btn").addEventListener("click", runAiSearch);
document.getElementById("ai-search-input").addEventListener("keydown", e => {
    if (e.key === 'Enter') runAiSearch();
});

document.getElementById("ai-search-clear").addEventListener("click", function() {
    document.getElementById("ai-search-input").value = '';
    aiModeActive = false;
    table.setData(`?c=<?= $_REQUEST['c'] ?>&a=Data`);
    table.clearHeaderFilter();
    updateRelationalOptions([]);
});

document.getElementById("ai-search-btn").addEventListener("click", runAiSearch);
document.getElementById("ai-search-input").addEventListener("keydown", e => {
    if (e.key === 'Enter') runAiSearch();
});

document.getElementById("ai-search-clear").addEventListener("click", function() {
    document.getElementById("ai-search-input").value = '';
    aiModeActive = false;
    table.setData(`?c=<?= $_REQUEST['c'] ?>&a=Data`);
    table.clearHeaderFilter();
    updateRelationalOptions([]);
});

// ─── Helpers ─────────────────────────────────────────────────────────────────
function openRowById(id) {
    htmx.ajax('GET', `?c=<?= $_REQUEST['c'] ?>&a=Detail&id=${id}`, {
        target:'#myModal', swap:'innerHTML', headers:{'HX-Request':'true'}
    });
    Alpine.evaluate(document.getElementById('myModal'), 'showModal = true');
}

function customDateRangeFilter(cell, onRendered, success, cancel, editorParams) {
    const container = document.createElement("input");
    container.setAttribute("type", "text");
    flatpickr(container, {
        mode:"range", dateFormat:"Y-m-d", locale:"es",
        onClose: function(selectedDates, dateStr) { success(dateStr); }
    });
    return container;
}

function customDateFilterFunc(headerValue, rowValue, rowData, filterParams) { return true; }

document.addEventListener("DOMContentLoaded", function() {
    const params = new URLSearchParams(window.location.search);
    const id     = params.get("id");
    if (id) {
        const tryOpen = () => {
            const row = table.getRows().find(r => r.getData().id == id);
            if (row) {
                openRowById(id);
                const url = new URL(window.location);
                url.searchParams.delete("id");
                window.history.replaceState({}, '', url);
            } else {
                table.on("dataLoaded", function() {
                    const row = table.getRows().find(r => r.getData().id == id);
                    if (row) {
                        openRowById(id);
                        const url = new URL(window.location);
                        url.searchParams.delete("id");
                        window.history.replaceState({}, '', url);
                    }
                });
            }
        };
        setTimeout(tryOpen, 300);
    }
});
</script>