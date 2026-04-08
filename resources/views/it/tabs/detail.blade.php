{{-- Tab Único: Detalle Base + Bitácora (Homologación SIGMA — sin tabbar si count(tabs)==1) --}}
@php
    /** @var \App\Models\It $ticket */
    /** @var \Illuminate\Database\Eloquent\Collection $assets */
    /** @var \Illuminate\Database\Eloquent\Collection $technicians */
@endphp

<div class="space-y-6">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Status and Critical Information --}}
        <div class="space-y-4">
            <x-sidebar-section icon="ri-information-line" label="Información Básica">
                <x-sidebar-row label="Tipo"     :value="$ticket->kind ?? '—'" />
                <x-sidebar-row label="Usuario"  :value="$ticket->requestor?->name ?? '—'" />
                <x-sidebar-row label="Sede"     :value="$ticket->facility ?? '—'" />
                <x-sidebar-row label="Fecha"    :value="$ticket->created_at?->format('Y-m-d H:i') ?? '—'" />
                <x-sidebar-row label="Iniciado" :value="$ticket->started_at?->format('Y-m-d H:i') ?? '—'" />
                <x-sidebar-row label="Cierre"   :value="$ticket->closed_at?->format('Y-m-d H:i') ?? '—'" />
            </x-sidebar-section>

            <x-sidebar-section icon="ri-file-text-line" label="Descripción">
                <p class="text-xs leading-relaxed whitespace-pre-line bg-sigma-bg2 p-3 rounded-lg border border-sigma-b" style="color:var(--tx)">{{ $ticket->description }}</p>
            </x-sidebar-section>
        </div>

        {{-- Configuration and Assignment --}}
        <div class="space-y-4">
            <x-sidebar-section icon="ri-computer-line" label="Equipo Asignado">
                <select data-widget="slimselect"
                        hx-post="{{ route('it.update', $id) }}"
                        hx-trigger="change"
                        hx-vals='{"field": "asset_id"}'
                        name="value">
                    <option value="">— Sin asignar —</option>
                    @foreach($assets as $a)
                        <option value="{{ $a->id }}" @selected($a->id === $ticket->asset_id)>
                            {{ mb_convert_case($a->hostname ?? '', MB_CASE_TITLE, 'UTF-8') }} | {{ $a->serial }}
                        </option>
                    @endforeach
                </select>
            </x-sidebar-section>

            <div class="grid grid-cols-2 gap-4">
                <x-sidebar-section icon="ri-flag-line" label="Prioridad">
                    <select data-widget="slimselect"
                            hx-post="{{ route('it.update', $id) }}"
                            hx-trigger="change"
                            hx-vals='{"field": "priority"}'
                            name="value">
                        <option value="High"   @selected($ticket->priority === 'High')>High</option>
                        <option value="Medium" @selected($ticket->priority === 'Medium')>Medium</option>
                        <option value="Low"    @selected($ticket->priority === 'Low')>Low</option>
                    </select>
                </x-sidebar-section>

                <x-sidebar-section icon="ri-settings-3-line" label="SGC">
                    <select data-widget="slimselect"
                            hx-post="{{ route('it.update', $id) }}"
                            hx-trigger="change"
                            hx-vals='{"field": "sgc"}'
                            name="value">
                        <option value="">— SGC —</option>
                        @foreach(['Corrective','Preventive','Production','Infrastructure'] as $opt)
                            <option value="{{ $opt }}" @selected($ticket->sgc === $opt)>{{ $opt }}</option>
                        @endforeach
                    </select>
                </x-sidebar-section>
            </div>

            <x-sidebar-section icon="ri-shield-user-line" label="Técnico Responsable">
                <select data-widget="slimselect"
                        hx-post="{{ route('it.update', $id) }}"
                        hx-trigger="change"
                        hx-vals='{"field": "assignee_id"}'
                        name="value">
                    <option value="">— Sin asignar —</option>
                    @foreach($technicians as $t)
                        <option value="{{ $t->id }}" @selected($t->id === $ticket->assignee_id)>
                            {{ $t->name }}
                        </option>
                    @endforeach
                </select>
            </x-sidebar-section>
        </div>
    </div>

    {{-- Bitácora de Tareas (Integral en una sola vista) --}}
    <x-sidebar-section icon="ri-list-check-2" label="Bitácora de Intervenciones">
        <div class="bg-sigma-bg border border-sigma-b rounded-xl overflow-hidden p-4 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-[10px] font-black uppercase tracking-widest opacity-60">Historial de Trabajos</h3>
                <button class="px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-widest flex items-center gap-1 transition-all hover:scale-[1.02] active:scale-[0.98]"
                        style="background:var(--ac); color:var(--ac-inv)"
                        hx-get="{{ route('it.task.form', $id) }}"
                        hx-target="#modal-body-2"
                        hx-swap="innerHTML"
                        onclick="window.dispatchEvent(new CustomEvent('open-modal-2'))">
                    <i class="ri-add-line text-sm"></i>
                    <span>Nueva Tarea</span>
                </button>
            </div>
            
            <div id="itTaskTable-{{ $id }}" class="text-xs"></div>
        </div>
    </x-sidebar-section>

</div>

<script>
(function(){
    function initTable(el) {
        if (!el || el.dataset.tabulatorInitialized || typeof Tabulator === 'undefined') return;
        el.dataset.tabulatorInitialized = true;
        
        new Tabulator(el, {
            pagination: true,
            paginationMode: 'remote',
            paginationSize: 10,
            paginationCounter: 'rows',
            layout: 'fitColumns',
            ajaxURL: '{{ route("it.tasks", $id) }}',
            dataField: 'data',
            placeholder: 'Aún no hay intervenciones registradas',
            columns: [
                { title: 'Fecha',       field: 'date',       width: 130 },
                { title: 'Técnico',     field: 'technician', width: 130 },
                { title: 'Resumen',     field: 'notes',      headerFilter: 'input', formatter: 'textarea' },
                { title: 'Min',         field: 'time',       width: 70, hozAlign: 'right' },
            ],
        });
    }
    document.querySelectorAll('#itTaskTable-{{ $id }}').forEach(initTable);
})();
</script>
