<div class="mb-5">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-base font-bold flex items-center gap-1.5" style="color:var(--tx)">
            <i class="ri-user-add-line text-xl"></i>
            <span>Asignaciones</span>
        </h2>

        @if($asset->status === 'available')
        <button
            class="px-3 py-1.5 rounded-md text-sm font-medium flex items-center gap-1"
            style="background:var(--ac); color:var(--ac-inv)"
            hx-get="{{ route('assets.assignments.create', $asset->id) }}"
            hx-target="#modal-body"
            hx-swap="innerHTML"
            hx-on::after-request="window.dispatchEvent(new CustomEvent('open-modal'))">
            <i class="ri-add-line text-xs"></i>
            <span>Nueva Asignación</span>
        </button>
        @endif
    </div>

    <div id="tabTableAssignments" class="border-hidden text-xs"></div>
</div>

<script>
(function(){
    const el = document.getElementById('tabTableAssignments');
    if (!el || el.dataset.tabulatorInitialized || typeof Tabulator === 'undefined') return;
    el.dataset.tabulatorInitialized = true;

    const table = new Tabulator(el, {
        pagination: true,
        paginationSize: 15,
        layout: "fitColumns",
        data: @json($assignments),
        columns: [
            {title:"ID", field:"id", width:70},
            {title:"Fecha", field:"date"},
            {title:"Responsable", field:"assignee", width:250},
            {title:"Hardware", field:"hardware", formatter: "textarea"},
            {title:"Software", field:"software"},
            {title:"Acta", field:"minute", formatter: "html"},
        ],
    });
    el.tabulator = table;
})();
</script>
