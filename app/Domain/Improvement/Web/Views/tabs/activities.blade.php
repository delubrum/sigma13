<div class="p-3 h-full flex flex-col gap-3">
    @if($data->canEdit ?? true)
        <div class="flex justify-end">
            <button class="btn-sm btn-primary"
                    hx-get="{{ route('improvement.activities.create', $id) }}"
                    hx-target="#modal-body-2"
                    hx-swap="innerHTML">
                <i class="ri-add-line mr-1"></i> Agregar Actividad
            </button>
        </div>
    @endif

    <div id="activities-table-{{ $id }}" data-widget="tabulator"
         data-config='{
             "ajaxURL": "{{ route('improvement.activities.data', $id) }}",
             "pagination": true,
             "paginationMode": "remote",
             "ajaxResponse": function(url, params, response){ return response; },
             "paginationSize": 15,
             "layout": "fitColumns",
             "columns": [
                 {"title": "Actividad",   "field": "action",   "widthGrow": 2, "formatter": "textarea"},
                 {"title": "Cómo",        "field": "how_to",   "widthGrow": 1, "formatter": "textarea"},
                 {"title": "Fecha",       "field": "whenn",    "width": 100,  "hozAlign": "center"},
                 {"title": "Completada",  "field": "done",     "width": 100,  "hozAlign": "center"},
                 {"title": "Resultados",  "field": "results",  "width": 200,  "formatter": "html"},
                 {"title": "",            "field": "actions",  "width": 110,  "formatter": "html", "headerSort": false}
             ]
         }'>
    </div>
</div>
