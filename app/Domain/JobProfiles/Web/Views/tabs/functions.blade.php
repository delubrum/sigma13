{{-- Tab: Funciones / Formación / Entrenamiento — 1 col editable via jspreadsheet --}}
<div class="mb-2">
    <h3 class="text-xs font-semibold flex items-center gap-1.5 mb-2" style="color:var(--tx2)">
        <i class="ri-file-list-line text-base"></i>
        <span class="uppercase tracking-wider">{{ $kind }}</span>
    </h3>

    @if($canEdit)
    <div class="flex gap-1.5 mb-2" id="jp-toolbar-{{ Str::slug($kind) }}">
        <button type="button" onclick="jpUndo('{{ Str::slug($kind) }}')"
            class="text-xs px-2 py-1 rounded flex items-center gap-1"
            style="background:var(--bg2); color:var(--tx2); border:1px solid var(--b)">
            <i class="ri-arrow-go-back-line"></i>
        </button>
        <button type="button" onclick="jpRedo('{{ Str::slug($kind) }}')"
            class="text-xs px-2 py-1 rounded flex items-center gap-1"
            style="background:var(--bg2); color:var(--tx2); border:1px solid var(--b)">
            <i class="ri-arrow-go-forward-line"></i>
        </button>
        <button type="button" onclick="jpSave('{{ Str::slug($kind) }}')"
            class="text-xs px-2 py-1 rounded flex items-center gap-1"
            style="background:var(--bg2); color:var(--tx2); border:1px solid var(--b)">
            <i class="ri-save-line"></i>
        </button>
    </div>
    @endif

    <div id="jp-sheet-{{ Str::slug($kind) }}" class="w-full"></div>
</div>

<script>
(function() {
    var slugKey = '{{ Str::slug($kind) }}';
    var data    = {!! $content && $content !== '[]' ? $content : '[[""]]' !!};
    var jpId    = {{ $jpId }};
    var kind    = @json($kind);
    var saveUrl = @json(route('job-profiles.save-item'));
    var csrf    = document.querySelector('meta[name="csrf-token"]').content;

    window['jpInst_' + slugKey] = null;

    var saveTimers = {};
    window['jpSave'] = window['jpSave'] || function(key) {
        var inst = window['jpInst_' + key];
        if (!inst) return;
        var payload = { jp_id: jpId, type: kind, data: inst.worksheets[0].getData() };
        fetch(saveUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify(payload),
        })
        .then(r => r.json())
        .then(d => d.status === 'success' ? notyf?.success('Guardado') : notyf?.error('Error'))
        .catch(() => notyf?.error('Error al guardar'));
    };
    window['jpUndo'] = window['jpUndo'] || function(key) { window['jpInst_' + key]?.undo(); };
    window['jpRedo'] = window['jpRedo'] || function(key) { window['jpInst_' + key]?.redo(); };

    function debouncedSave() {
        clearTimeout(saveTimers[slugKey]);
        saveTimers[slugKey] = setTimeout(() => window['jpSave'](slugKey), 600);
    }

    var el = document.getElementById('jp-sheet-' + slugKey);
    window['jpInst_' + slugKey] = jspreadsheet(el, {
        toolbar: false,
        worksheets: [{
            data: data,
            minDimensions: [1, 3],
            tableOverflow: true,
            tableWidth: '100%',
            columnDrag: false,
            columnResize: false,
            allowInsertColumn: false,
            @if(!$canEdit) editable: false, @endif
            columns: [
                { type: 'text', title: 'Descripción', wordWrap: true, width: 800 }
            ],
        }],
        @if($canEdit)
        onchange: debouncedSave,
        @endif
    });
})();
</script>
