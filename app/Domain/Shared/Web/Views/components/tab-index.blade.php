@props([
    'config',
    'parentId',
    'route',
    'customCreateRoute' => null,
    'customCreateLabel' => null,
    'customCreateIcon' => null,
    'customCreateStyle' => null,
    'newButtonClass' => null,
    'tableId' => null,
])

@php
    $title = data_get($config, 'title');
    $subtitle = data_get($config, 'subtitle');
    $icon = data_get($config, 'icon');
    
    $newBtnLabel = $customCreateLabel ?: data_get($config, 'newButtonLabel') ?: 'Nuevo';
    $newBtnIcon = $customCreateIcon ?: 'ri-add-line';
    
    $fields = data_get($config, 'formFields', []);
    $columns = data_get($config, 'columns', []);

    $jsFriendlyName = str_replace(['.', '-', '/'], '_', $route) . '_' . $parentId;
    
    $routeName = $route . '.create';
    $createUrl = $customCreateRoute ?? (Route::has($routeName) ? route($routeName, $parentId) : '#');
    $showBtn = (count($fields) > 0 || $customCreateRoute) && $createUrl !== '#';
    
    $btnClass = $newButtonClass ?? 'flex items-center gap-2 px-4 py-2 rounded-lg bg-sigma-ac text-sigma-ac-inv text-[10px] font-black uppercase tracking-widest hover:scale-[1.02] active:scale-[0.98] transition-all';
    $btnStyle = $customCreateStyle ?? '';

    $tabulatorConfig = [
        'height' => "450px",
        'stickyHeader' => false,
        'pagination' => true,
        'paginationMode' => "remote",
        'paginationSize' => 15,
        'paginationSizeSelector' => [15, 50, 100, 500],
        'paginationButtonCount' => 5,
        'layout' => "fitDataFill",
        'columnDefaults' => [
            'width' => 150,
            'minWidth' => 100,
        ],
        'ajaxURL' => route($route . '.data', $parentId),
        'dataField' => "data",
        'paginationDataReceived' => [
            'last_page' => "last_page",
        ],
        'placeholder' => "No se encontraron registros",
        'locale' => "es",
        'columns' => $columns,
    ];
@endphp

<div class="mb-5 relative" 
     id="tab-{{ $jsFriendlyName }}-container"
     hx-get="{{ route($route, $parentId) }}"
     hx-trigger="refresh">
    <div class="flex items-center justify-between mb-3 border-b pb-3" style="border-color:var(--b)">
        <div class="flex items-center gap-3">
            @if($icon)
                <div class="p-1.5 rounded-lg bg-sigma-ac/5 text-sigma-ac">
                    <i class="{{ $icon }} text-xl"></i>
                </div>
            @endif
            <div>
                <h2 class="text-base font-bold" style="color:var(--tx)">{{ $title }}</h2>
                @if($subtitle)
                    <p class="text-[10px] uppercase font-bold text-sigma-tx2 opacity-60 tracking-widest">{{ $subtitle }}</p>
                @endif
            </div>
        </div>

        @if($showBtn)
            <button
                class="{{ $btnClass }}"
                style="{{ $btnStyle }}"
                hx-get="{{ $createUrl }}"
                hx-target="#modal-body-2"
                hx-indicator="#global-loader"
                hx-on::after-request="if(event.detail.successful) window.dispatchEvent(new CustomEvent('open-modal-2'))">
                <i class="{{ $newBtnIcon }} text-sm"></i>
                <span>{{ $newBtnLabel }}</span>
            </button>
        @endif
    </div>

    <!-- Table Container for Tabulator Auto-Init -->
    <div id="dt_{{ $tableId ?? $jsFriendlyName }}" 
         data-widget="tabulator" 
         data-config='@json($tabulatorConfig)'
         class="w-full text-xs bg-sigma-bg2 border border-sigma-b rounded-lg"></div>
</div>
