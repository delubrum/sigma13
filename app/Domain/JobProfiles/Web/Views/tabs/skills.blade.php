{{-- Tab: Competencias — 3 cols (Tipo/Competencia readonly, Nivel editable) --}}
@php
$defaultSkills = [
    ['Corporativa', 'Responsabilidad', ''],
    ['Corporativa', 'Comunicación', ''],
    ['Corporativa', 'Trabajo en equipo', ''],
    ['Corporativa', 'Orientación Integral de Calidad', ''],
    ['Corporativa', 'Proactividad', ''],
    ['Específica/Comportamiento', 'Liderazgo', ''],
    ['Específica/Comportamiento', 'Capacidad de análisis y toma de decisiones', ''],
    ['Específica/Comportamiento', 'Planeación y Organización', ''],
    ['Específica/Comportamiento', 'Aprendizaje continuo', ''],
    ['Específica/Comportamiento', 'Expresión oral y escrita', ''],
];
$skillData = json_decode($content ?? '[]', true);
$rows = (is_array($skillData) && count($skillData) > 0) ? $skillData : $defaultSkills;
@endphp

<div class="mb-2">
    <h3 class="text-xs font-semibold flex items-center gap-1.5 mb-2" style="color:var(--tx2)">
        <i class="ri-medal-line text-base"></i>
        <span class="uppercase tracking-wider">Competencias</span>
    </h3>

    @if($canEdit)
    <div class="flex gap-1.5 mb-2">
        <button type="button" onclick="jpSave('skills')"
            class="text-xs px-2 py-1 rounded" style="background:var(--bg2); color:var(--tx2); border:1px solid var(--b)">
            <i class="ri-save-line"></i> Guardar
        </button>
    </div>
    @endif

    <div id="jp-sheet-skills" class="w-full"></div>
</div>

<script>
(function() {
    var data    = {!! json_encode($rows) !!};
    var jpId    = {{ $jpId }};
    var kind    = @json($kind);
    var saveUrl = @json(route('job-profiles.save-item'));
    var csrf    = document.querySelector('meta[name="csrf-token"]').content;
    var saveTimer;

    window['jpInst_skills'] = null;

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

    var el = document.getElementById('jp-sheet-skills');
    window['jpInst_skills'] = jspreadsheet(el, {
        toolbar: false,
        worksheets: [{
            data: data,
            minDimensions: [3, 10],
            tableOverflow: true,
            tableWidth: '100%',
            columnDrag: false,
            columnResize: false,
            allowInsertRow: false,
            allowInsertColumn: false,
            columns: [
                { type: 'text', title: 'Tipo de Habilidad', width: 220, readOnly: true,  align: 'left' },
                { type: 'text', title: 'Competencia',       width: 380, readOnly: true,  align: 'left', wordWrap: true },
                { type: 'text', title: 'Nivel requerido',   width: 180, readOnly: @json(!$canEdit), align: 'left' },
            ],
        }],
        @if($canEdit)
        onchange: function() {
            clearTimeout(saveTimer);
            saveTimer = setTimeout(() => window['jpSave']('skills'), 600);
        },
        @endif
    });
})();
</script>
