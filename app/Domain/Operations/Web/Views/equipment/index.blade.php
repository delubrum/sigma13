@props([
    'config',
    'tab',
])

@php
    $route = 'operations/equipment';
    $jsFriendlyName = str_replace(['.', '-', '/'], '_', $route . '_' . $tab);
    $instanceId = 'dt_' . $jsFriendlyName;
    $storageKey = $jsFriendlyName;

    $tabulatorConfig = [
        'height' => "calc(100vh - 350px)",
        'stickyHeader' => true,
        'index' => "id",
        'ajaxURL' => "/$route/data?tab=$tab",
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
        'columns' => $config->columns ?? [],
    ];
@endphp

<x-layouts.app :title="$config->title" :icon="$config->icon">
    <div class="flex flex-col gap-6 h-full">
        
        {{-- Header & Tabs --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-center gap-1 bg-sigma-bg2 p-1 rounded-2xl w-fit border border-sigma-b shadow-inner">
                @foreach([
                    'db' => ['label' => 'Catalogue', 'icon' => 'ri-settings-5-line'],
                    'stock' => ['label' => 'Stock', 'icon' => 'ri-archive-line'],
                    'deliveries' => ['label' => 'Deliveries', 'icon' => 'ri-tools-line'],
                ] as $key => $item)
                    <a href="?tab={{ $key }}"
                       hx-get="?tab={{ $key }}"
                       hx-target="main"
                       hx-push-url="true"
                       @class([
                           'flex items-center gap-2 px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300',
                           'bg-sigma-bg text-sigma-ac shadow-lg border border-sigma-b' => $tab === $key,
                           'text-sigma-tx2 hover:text-sigma-tx hover:bg-sigma-bg/30' => $tab !== $key,
                       ])>
                        <i class="{{ $item['icon'] }} text-sm {{ $tab === $key ? 'animate-pulse' : '' }}"></i>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </div>

            <div class="flex items-center gap-3">
                 {{-- Botones según tab --}}
                @if($tab === 'db')
                    <button hx-get="/operations/equipment/create" hx-target="#modal-body"
                        class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-sigma-ac text-sigma-ac-inv text-[10px] font-black uppercase tracking-widest hover:scale-[1.02] active:scale-[0.98] transition-all shadow-lg active:shadow-inner">
                        <i class="ri-add-circle-line text-sm"></i>
                        <span>New Equipment</span>
                    </button>
                @elseif($tab === 'stock')
                    <button hx-get="/operations/equipment/entry" hx-target="#modal-body"
                        class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-500 text-white text-[10px] font-black uppercase tracking-widest hover:scale-[1.02] active:scale-[0.98] transition-all shadow-lg active:shadow-inner">
                        <i class="ri-download-line text-sm"></i>
                        <span>New Entry</span>
                    </button>
                @elseif($tab === 'deliveries')
                    <button hx-get="/operations/equipment/delivery" hx-target="#modal-body"
                        class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 text-white text-[10px] font-black uppercase tracking-widest hover:scale-[1.02] active:scale-[0.98] transition-all shadow-lg active:shadow-inner">
                        <i class="ri-send-plane-line text-sm"></i>
                        <span>New Delivery</span>
                    </button>
                @endif
            </div>
        </div>

        {{-- Table Container --}}
        <div class="flex-1 bg-sigma-bg2/50 border border-sigma-b rounded-3xl p-6 shadow-xl backdrop-blur-sm relative overflow-hidden group">
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-sigma-ac/5 rounded-full blur-[100px] pointer-events-none group-hover:bg-sigma-ac/10 transition-colors duration-1000"></div>
            
            <div id="{{ $instanceId }}" 
                 x-ref="table" 
                 data-widget="tabulator"
                 data-route="{{ $route }}"
                 data-config='@json($tabulatorConfig)'
                 class="h-full"></div>
        </div>
    </div>
</x-layouts.app>
