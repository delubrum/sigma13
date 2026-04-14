<div class="p-3 h-full flex flex-col gap-3">
    @if($data->canEdit ?? true)
        <div class="flex justify-end">
            <button class="btn-sm btn-primary"
                    hx-get="{{ route('improvement.causes.create', $id) }}"
                    hx-target="#modal-body-2"
                    hx-swap="innerHTML">
                <i class="ri-add-line mr-1"></i> Agregar Causa
            </button>
        </div>
    @endif

    <div id="causes-table-{{ $id }}" data-widget="tabulator"
         data-config='{
             "ajaxURL": "{{ route('improvement.causes.data', $id) }}",
             "pagination": true,
             "paginationMode": "remote",
             "ajaxResponse": function(url, params, response){ return response; },
             "paginationSize": 15,
             "layout": "fitColumns",
             "columns": [
                 {"title": "Causa General", "field": "reason",   "widthGrow": 2, "formatter": "textarea"},
                 {"title": "Método",        "field": "method",   "width": 100},
                 {"title": "Causa Probable","field": "probable", "widthGrow": 2, "formatter": "textarea"},
                 {"title": "",              "field": "actions",  "width": 90,  "formatter": "html", "headerSort": false}
             ]
         }'>
    </div>
</div>
