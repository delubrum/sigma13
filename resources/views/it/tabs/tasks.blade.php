{{-- Tab: Bitácora de tareas (fiel al legacy it/tab.php) --}}
@php /** @var int $id */ @endphp

<div class="mb-5">

    {{-- Botón New Task → abre modal nivel 2 --}}
    <div class="flex items-center justify-end mb-3">
        <button class="px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-widest flex items-center gap-1 transition-all hover:scale-[1.02] active:scale-[0.98]"
                style="background:var(--ac); color:var(--ac-inv)"
                hx-get="{{ route('it.task.form', $id) }}"
                hx-target="#modal-body-2"
                hx-swap="innerHTML"
                onclick="window.dispatchEvent(new CustomEvent('open-modal-2'))">
            <i class="ri-add-line text-sm"></i>
            <span>Nueva Tarea</span>
        </button>
    </div>

    {{-- Tabulator de tareas --}}
    <div id="taskTable-{{ $id }}" class="text-xs"></div>

</div>

<script>
(function(){
    function initTable(el) {
        if (!el || el.dataset.tabulatorInitialized || typeof Tabulator === 'undefined') return;
        el.dataset.tabulatorInitialized = true;
        window['itTaskTable_{{ $id }}'] = new Tabulator(el, {
            pagination: true,
            paginationMode: 'remote',
            paginationSize: 15,
            paginationSizeSelector: [10, 15, 20, 50, 100],
            paginationCounter: 'rows',
            filterMode: 'remote',
            sortMode: 'remote',
            layout: 'fitColumns',
            ajaxURL: '{{ route("it.tasks", $id) }}',
            dataField: 'data',
            placeholder: 'Sin tareas registradas',
            columns: [
                { title: 'Fecha',       field: 'date',       headerFilter: 'input' },
                { title: 'Técnico',     field: 'technician', headerFilter: 'input' },
                { title: 'Complejidad', field: 'complexity', headerFilter: 'input' },
                { title: 'Atención',    field: 'attends',    headerFilter: 'input' },
                { title: 'Minutos',     field: 'time',       headerFilter: 'input' },
                { title: 'Notas',       field: 'notes',      headerFilter: 'input', formatter: 'textarea' },
            ],
        });
    }

    document.querySelectorAll('#taskTable-{{ $id }}').forEach(initTable);
})();
</script>
