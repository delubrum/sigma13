@props([
    'config',
    'parentId',
    'route', // e.g. 'assets.documents'
    'customCreateRoute' => null,
    'newButtonClass' => null,
])

@php
    $jsFriendlyName = str_replace(['.', '-', '/'], '_', $route) . '_' . $parentId;
    $instanceName = 'tab_dt_' . $jsFriendlyName;
    $storageKey = 'tab_' . $jsFriendlyName;
    $createUrl = $customCreateRoute ?? (Route::has($route . '.create') ? route($route . '.create', $parentId) : '#');
    $btnClass = $newButtonClass ?? 'px-4 py-1.5 rounded-lg text-xs font-black uppercase tracking-wider flex items-center space-x-1.5 transition-all outline-none hover:scale-[1.02] active:scale-[0.98]';
    $btnStyle = $newButtonClass ? '' : 'background:var(--ac); color:var(--ac-inv)';
@endphp

<script>
    (function() {
        const initComponent = () => {
            Alpine.data('{{ $instanceName }}', () => ({
                table: null,
                storageKey: '{{ $storageKey }}',

                init() {
                    setTimeout(() => {
                        this.table = new Tabulator(this.$refs.table, {
                            pagination: true,
                            paginationMode: "remote",
                            paginationSize: 10,
                            paginationButtonCount: 5,
                            layout: "fitColumns",
                            ajaxURL: "{{ route($route . '.data', $parentId) }}",
                            dataField: "data",
                            paginationDataReceived: {
                                last_page: "last_page",
                            },
                            placeholder: "No se encontraron registros",
                            locale: "es",
                            columns: @js($config->columns)
                        });
                    }, 50);
                }
            }));
        };

        if (window.Alpine) {
            initComponent();
        } else {
            document.addEventListener('alpine:init', initComponent);
        }
    })();
</script>

<div x-data="{{ $instanceName }}()" x-init="init()" class="mb-5 relative" id="tab-{{ $jsFriendlyName }}-container">
    <div class="flex items-center justify-between mb-3 border-b pb-3" style="border-color:var(--b)">
        <h2 class="text-base font-bold flex items-center gap-1.5" style="color:var(--tx)">
            <i class="{{ $config->icon }} text-xl opacity-70"></i>
            <span>{{ $config->title }}</span>
        </h2>

        @if(count($config->formFields) > 0 || $customCreateRoute)
        <button
            class="{{ $btnClass }}"
            style="{{ $btnStyle }}"
            hx-get="{{ $createUrl }}"
            hx-target="#modal-body-2"
            hx-indicator="#loading">
            <i class="ri-add-line text-sm"></i>
            <span>{{ $config->newButtonLabel ?? 'Nuevo' }}</span>
        </button>
        @endif
    </div>

    <!-- Table Container for Tabulator -->
    <div x-ref="table" class="w-full text-xs bg-sigma-bg2 border border-sigma-b rounded-lg overflow-hidden"></div>
</div>
