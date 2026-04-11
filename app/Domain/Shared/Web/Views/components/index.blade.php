@props([
    'route',
    'config',
])

<x-layouts.app :title="$config->title" :icon="$config->icon">
    @php
        $jsFriendlyName = str_replace(['.', '-', '/'], '_', $route);
        $instanceId = 'dt_' . $jsFriendlyName;
        $storageKey = $jsFriendlyName; 

        $tabulatorConfig = [
            'height' => "100%",
            'stickyHeader' => true,
            'index' => "id",
            'ajaxURL' => "/$route/data",
            'ajaxConfig' => 'GET',
            'pagination' => true,
            'paginationMode' => "remote",
            'paginationSize' => 15,
            'paginationSizeSelector' => [15, 50, 100, 500],
            'paginationButtonCount' => 10,
            'dataField' => "data",
            'paginationDataReceived' => [
                'last_page' => "last_page",
            ],
            'ajaxParams' => [],
            'filterMode' => "remote",
            'sortMode' => "remote",
            'columnDefaults' => [
                'width' => 150,
                'minWidth' => 100,
            ],
            'layout' => "fitDataFill",
            'movableColumns' => true,
            'persistence' => [
                'columns' => ["width", "visible", "order"],
            ],
            'persistenceMode' => "local",
            'persistenceID' => $storageKey,
            'locale' => "es",
            'columns' => $config->columns,
        ];
    @endphp

    <div class="flex flex-col gap-4 h-full animate-core">
        
        {{-- ── Toolbar ────────────────────────────────────────────────── --}}
        <div class="flex items-center justify-between px-1">

            <div class="flex items-center gap-2">
                {{-- Selector de Columnas --}}
                <div class="relative" x-data="{ 
                    open: false, 
                    columns: [],
                    refresh() {
                        const table = document.getElementById('{{ $instanceId }}')?.tabulator;
                        if (table) {
                            this.columns = table.getColumns().filter(c => c.getDefinition().field).map(c => ({
                                title: c.getDefinition().title || c.getDefinition().field,
                                field: c.getDefinition().field,
                                visible: c.isVisible()
                            }));
                        }
                    },
                    toggle(field) {
                        const table = document.getElementById('{{ $instanceId }}')?.tabulator;
                        table?.toggleColumn(field);
                        this.refresh();
                    },
                    reset() {
                        localStorage.removeItem('tabulator-{{ $storageKey }}-columns');
                        window.location.reload();
                    }
                }" @click.outside="open = false">
                    <button @click="open = !open; if(open) refresh()"
                        class="flex items-center gap-1.5 px-3 py-2 rounded-lg bg-sigma-bg2 text-sigma-tx2 hover:text-sigma-tx hover:bg-sigma-b/30 transition-all text-[10px] font-bold uppercase tracking-widest outline-none">
                        <i class="ri-layout-column-line text-amber-500 text-sm"></i>
                        <span class="hidden lg:inline">Columnas</span>
                    </button>

                    <div x-show="open" x-cloak x-transition
                        class="absolute left-0 mt-2 w-64 bg-sigma-bg border border-sigma-b rounded-xl shadow-2xl z-50 p-3 overflow-hidden">
                        <span class="text-[9px] font-black uppercase text-sigma-tx2 mb-2 block opacity-50 px-1">Visibilidad</span>
                        <div class="flex flex-col gap-1 max-h-64 overflow-y-auto pr-1 scrollbar-thin">
                            <template x-for="col in columns" :key="col.field">
                                <label class="flex items-center gap-3 cursor-pointer hover:bg-sigma-bg2 p-2 rounded-lg transition-colors group">
                                    <input type="checkbox" :checked="col.visible" @change="toggle(col.field)"
                                           class="rounded border-sigma-b text-sigma-ac focus:ring-sigma-ac bg-sigma-bg2 w-4 h-4">
                                    <span class="text-[10px] font-bold text-sigma-tx2 uppercase group-hover:text-sigma-tx" x-text="col.title"></span>
                                </label>
                            </template>
                        </div>
                        <hr class="border-sigma-b my-2">
                        <button @click="reset()" class="w-full text-center py-2 text-[9px] font-black text-red-500 hover:bg-red-500/10 rounded-lg uppercase transition-all">
                            Restablecer Vista
                        </button>
                    </div>
                </div>

                {{-- Export Dropdown --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open"
                        class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-sigma-bg2 text-sigma-tx2 hover:text-sigma-tx hover:bg-sigma-b/30 transition-all text-[10px] font-bold uppercase tracking-widest outline-none">
                        <i class="ri-file-excel-2-line text-emerald-500 text-sm"></i>
                        <span class="hidden lg:inline">Exportar</span>
                        <i class="ri-arrow-down-s-line text-xs transition-transform hidden lg:inline" :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="open" x-cloak x-transition
                        class="absolute left-0 mt-2 w-48 bg-sigma-bg border border-sigma-b rounded-xl shadow-2xl z-50 p-2 overflow-hidden">                       
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
                       class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-sigma-bg2 text-sigma-tx2 hover:text-sigma-tx hover:bg-sigma-b/30 transition-all text-[10px] font-bold uppercase tracking-widest outline-none">
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
                    hx-indicator="#global-loader"
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
            <div id="{{ $instanceId }}" 
                 x-ref="table" 
                 data-widget="tabulator"
                 data-route="{{ $route }}"
                 data-config='@json($tabulatorConfig)'
                 class="h-full"></div>
        </div>
    </div>

</x-layouts.app>
