{{-- Tab: Educación — 3 cols: Tipo, Título, Área de conocimiento --}}
<div class="mb-2">
    <h3 class="text-xs font-semibold flex items-center gap-1.5 mb-2" style="color:var(--tx2)">
        <i class="ri-graduation-cap-line text-base"></i>
        <span class="uppercase tracking-wider">Educación</span>
    </h3>

    @if($canEdit)
    <div class="flex gap-1.5 mb-2">
        <button type="button" onclick="jpUndo('education')"
            class="text-xs px-2 py-1 rounded" style="background:var(--bg2); color:var(--tx2); border:1px solid var(--b)">
            <i class="ri-arrow-go-back-line"></i>
        </button>
        <button type="button" onclick="jpRedo('education')"
            class="text-xs px-2 py-1 rounded" style="background:var(--bg2); color:var(--tx2); border:1px solid var(--b)">
            <i class="ri-arrow-go-forward-line"></i>
        </button>
        <button type="button" onclick="jpSave('education')"
            class="text-xs px-2 py-1 rounded" style="background:var(--bg2); color:var(--tx2); border:1px solid var(--b)">
            <i class="ri-save-line"></i>
        </button>
    </div>
    @endif

    <div id="jp-sheet-education" class="w-full"></div>
</div>

<script>
(function() {
    var data    = {!! $content && $content !== '[]' ? $content : '[["","",""]]' !!};
    var jpId    = {{ $jpId }};
    var kind    = @json($kind);
    var saveUrl = @json(route('job-profiles.save-item'));
    var csrf    = document.querySelector('meta[name="csrf-token"]').content;
    var saveTimer;

    window['jpInst_education'] = null;

    window['jpSave'] = window['jpSave'] || function(key) {
        var inst = window['jpInst_' + key];
        if (!inst) return;
        fetch(saveUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ jp_id: jpId, type: kind, data: inst.worksheets[0].getData() }),
        })
        .then(r => r.json())
        .then(d => d.status === 'success' ? notyf?.success('Guardado') : notyf?.error('Error'))
        .catch(() => notyf?.error('Error al guardar'));
    };
    window['jpUndo'] = window['jpUndo'] || function(key) { window['jpInst_' + key]?.undo(); };
    window['jpRedo'] = window['jpRedo'] || function(key) { window['jpInst_' + key]?.redo(); };

    var el = document.getElementById('jp-sheet-education');
    window['jpInst_education'] = jspreadsheet(el, {
        toolbar: false,
        worksheets: [{
            data: data,
            minDimensions: [3, 3],
            tableOverflow: true,
            tableWidth: '100%',
            columnDrag: false,
            columnResize: false,
            allowInsertColumn: false,
            @if(!$canEdit) editable: false, @endif
            columns: [
                { type: 'text', title: 'Tipo',                 wordWrap: true, width: 180 },
                { type: 'text', title: 'Título',               wordWrap: true, width: 280 },
                { type: 'text', title: 'Área de conocimiento', wordWrap: true, width: 280 },
            ],
        }],
        @if($canEdit)
        onchange: function() {
            clearTimeout(saveTimer);
            saveTimer = setTimeout(() => window['jpSave']('education'), 600);
        },
        @endif
    });
})();
</script>
