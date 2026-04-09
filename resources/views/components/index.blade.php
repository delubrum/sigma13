@props([
    'route',
    'config',
])

<x-layouts.app :title="$config->title" :icon="$config->icon">
    @php
        $jsFriendlyName = str_replace(['.', '-', '/'], '_', $route);
        $instanceName = 'dt_' . $jsFriendlyName;
        $storageKey = $jsFriendlyName; 
    @endphp

    <div x-data="{{ $instanceName }}()" x-init="init()" class="flex flex-col gap-4 h-full animate-core">
        
        {{-- ── Toolbar ────────────────────────────────────────────────── --}}
        <div class="flex items-center justify-between px-1">

            <div class="flex items-center gap-2">
                {{-- Selector de Columnas --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open"
                        class="flex items-center gap-1.5 px-3 py-2 rounded-xl border border-sigma-b bg-sigma-bg2 text-sigma-tx2 hover:text-sigma-tx transition-all text-[10px] font-bold uppercase tracking-widest">
                        <i class="ri-layout-column-line text-amber-500 text-sm"></i>
                        <span class="hidden lg:inline">Columnas</span>
                    </button>

                    <div x-show="open" x-cloak x-transition
                        class="absolute left-0 mt-2 w-64 bg-sigma-bg border border-sigma-b rounded-xl shadow-2xl z-50 p-3 border-sigma-b">
                        <span class="text-[9px] font-black uppercase text-sigma-tx2 mb-2 block opacity-50">Visibilidad</span>
                        <div class="flex flex-col gap-1 max-h-64 overflow-y-auto pr-1 scrollbar-thin">
                            <template x-for="col in allColumns" :key="col.field">
                                <label class="flex items-center gap-3 cursor-pointer hover:bg-sigma-bg2 p-2 rounded-lg transition-colors group">
                                    <input type="checkbox" :checked="col.visible" @change="toggleColumn(col.field)"
                                           class="rounded border-sigma-b text-sigma-ac focus:ring-sigma-ac bg-sigma-bg2 w-4 h-4">
                                    <span class="text-[10px] font-bold text-sigma-tx2 uppercase group-hover:text-sigma-tx" x-text="col.title"></span>
                                </label>
                            </template>
                        </div>
                        <hr class="border-sigma-b my-2">
                        <button @click="resetColumns()" class="w-full text-center py-2 text-[9px] font-black text-red-500 hover:bg-red-500/10 rounded-lg uppercase transition-all">
                            Restablecer Vista
                        </button>
                    </div>
                </div>

                {{-- Export Dropdown (Direct Links) --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open"
                        class="flex items-center gap-1.5 px-3 py-2 rounded-xl border border-sigma-b bg-sigma-bg2 text-sigma-tx2 hover:text-sigma-tx transition-all text-[10px] font-bold uppercase tracking-widest">
                        <i class="ri-file-excel-2-line text-emerald-500 text-sm"></i>
                        <span class="hidden lg:inline">Exportar</span>
                        <i class="ri-arrow-down-s-line text-xs transition-transform hidden lg:inline" :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="open" x-cloak x-transition
                        class="absolute left-0 mt-2 w-48 bg-sigma-bg border border-sigma-b rounded-xl shadow-2xl z-50 p-2 border-sigma-b">                       
                        <div class="flex flex-col gap-1">
                            @foreach([
                                'all'   => ['label' => 'Todo el historial', 'icon' => 'ri-database-line'],
                                'today' => ['label' => 'Solo hoy', 'icon' => 'ri-calendar-event-line'],
                                'week'  => ['label' => 'Esta semana', 'icon' => 'ri-calendar-todo-line'],
                                'month' => ['label' => 'Este mes', 'icon' => 'ri-calendar-2-line']
                            ] as $key => $item)
                                <a href="/{{ $route }}/export?range={{ $key }}" target="_blank"
                                   class="w-full px-3 py-2 text-[9px] font-bold uppercase text-sigma-tx2 hover:bg-sigma-bg2 hover:text-sigma-ac rounded-lg flex items-center gap-2 transition-all">
                                    <i class="{{ $item['icon'] }} opacity-50 text-sm"></i>
                                    {{ $item['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                @if($config->showKpi)
                    <a href="/{{ $route }}/kpis" target="_blank" 
                       class="flex items-center gap-1.5 px-3 py-2 rounded-xl border border-sigma-b bg-sigma-bg2 text-sigma-tx2 hover:text-sigma-tx transition-all text-[10px] font-bold uppercase tracking-widest">
                        <i class="ri-bar-chart-box-line text-blue-500 text-sm"></i>
                        <span class="hidden lg:inline">KPIs</span>
                    </a>
                @endif
            </div>

            {{-- Botón Nuevo --}}
            @if($config->newButtonLabel)
            <div class="flex items-center">
                <button
                    hx-get="/{{ $route }}/create"
                    hx-target="#modal-body"
                    hx-swap="innerHTML"
                    hx-on::after-request="window.dispatchEvent(new CustomEvent('open-modal'))"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl bg-sigma-ac text-sigma-ac-inv text-[10px] font-black uppercase tracking-widest hover:scale-[1.02] active:scale-[0.98] transition-all">
                    <i class="{{ $config->icon }} text-sm"></i>
                    <span>{{ $config->newButtonLabel }}</span>
                </button>
            </div>
            @endif
        </div>

        {{-- Tabla --}}
        <div class="flex-1 min-h-0">
            <div x-ref="table" class="h-full"></div>
        </div>
    </div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('{{ $instanceName }}', () => ({
        table: null,
        allColumns: [],
        storageKey: '{{ $storageKey }}',

        init() {
            this.table = new Tabulator(this.$refs.table, {
                height: "100%",
                index: "id",
                ajaxURL: '/{{ $route }}/data',
                ajaxConfig: 'GET',
                pagination: true,
                paginationMode: "remote",
                paginationSize: 15,
                paginationButtonCount: 10,
                dataField: "data",
                paginationDataReceived: {
                    last_page: "last_page",
                },
                ajaxParams: {},
                filterMode: "remote",
                sortMode: "remote",
                layout: "fitDataStretch",
                movableColumns: true,
                persistence: {
                    columns: ["width", "visible", "order"],
                },
                persistenceMode: "local",
                persistenceID: this.storageKey,
                locale: "es",
                columns: this.thawColumns(@js($config->columns)),
            });

            this.table.on("tableBuilt", () => {
                this.refreshColumnList();
                const urlParams = new URLSearchParams(window.location.search);
                const id = urlParams.get("id");
                if(id) setTimeout(() => this.openRow(id), 300);
            });

            const events = ["columnMoved", "columnResized", "columnVisibilityChanged"];
            events.forEach(event => {
                this.table.on(event, () => this.refreshColumnList());
            });

            this.table.on("rowClick", (e, row) => {
                if(e.target.closest('button, a, input, [tabulator-field="files"], .no-click')) return;
                this.openRow(row.getData().id);
            });
        },

        thawColumns(columns) {
            return columns.map(col => {
                // Si la columna tiene un formateador que empieza con 'function', lo hidratamos
                if (typeof col.formatter === 'string' && col.formatter.startsWith('function')) {
                    try {
                        col.formatter = new Function('return ' + col.formatter)();
                    } catch (e) {
                        console.error('Error hydrating formatter for field: ' + col.field, e);
                    }
                }
                return col;
            });
        },

        refreshColumnList() {
            const cols = this.table.getColumns();
            this.allColumns = cols
                .filter(c => c.getDefinition().field)
                .map(c => ({
                    title: c.getDefinition().title || c.getDefinition().field,
                    field: c.getDefinition().field,
                    visible: c.isVisible()
                }));
        },

        toggleColumn(field) {
            this.table.toggleColumn(field);
            this.refreshColumnList();
        },

        resetColumns() {
            const key = `tabulator-${this.storageKey}-columns`;
            localStorage.removeItem(key);
            window.location.reload(); 
        },

        openRow(id) {
            // Usamos htmx.ajax pero sin disparar eventos manuales en el .then()
            // El backend usará el HtmxOrchestrator para enviar los triggers necesarios (open-modal, etc)
            window.dispatchEvent(new CustomEvent('open-modal'));
            htmx.ajax('GET', `/{{ $route }}/${id}`, {
                target: '#modal-body',
                swap: 'innerHTML',
            });
        }
    }));
});
</script>
@endpush
</x-layouts.app>
