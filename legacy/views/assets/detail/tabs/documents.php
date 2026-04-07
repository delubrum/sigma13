<div class="mb-5">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-base font-bold text-gray-900 flex items-center space-x-1.5">
            <i class="ri-file-line text-xl"></i>
            <span>Documents</span>
        </h2>

            <button
                class="px-3 py-1.5 rounded-md text-sm font-medium bg-black text-white flex items-center space-x-1"
                @click='nestedModal = true'
                hx-get="?c=Assets&a=DetailModal&modal=document&id=<?= $id->id ?>"
                hx-target="#nestedModal"
                hx-indicator="#loading">
                <i class="ri-add-line text-xs"></i>
                <span>Add Document</span>
            </button>
    </div>


    <div id="tabTable" class="border-hidden text-xs"></div>

        <script>
            (function(){
            function initTable(el){
                if (!el || el.dataset.tabulatorInitialized || typeof Tabulator === 'undefined') return;

                tabTable = new Tabulator(el, {
                    pagination:true,
                    paginationMode:"remote",
                    paginationSize:15,
                    paginationSizeSelector:[10, 15, 20, 50, 100, 1000000],
                    paginationCounter:"rows",
                    filterMode:"remote",
                    sortMode:"remote",
                    layout:"fitColumns",
                    ajaxURL: "?c=Assets&a=DocumentsData&id=<?= $id->id ?>",
                    placeholder:"No Data Set",
                    columns: [
                        {title:"Name", field:"name", headerFilter:"input"},
                        {title:"Code", field:"code", headerFilter:"input"},
                        {title:"Expiry", field:"expiry", headerFilter:"input",width:300},
                        {title:"File", field:"file", headerFilter:"input",formatter: "html"},
                    ],
                });
            }

            // Inicializa cualquier tabla presente en el DOM
            document.querySelectorAll('#tabTable').forEach(initTable);

            })();

        </script>
</div>
