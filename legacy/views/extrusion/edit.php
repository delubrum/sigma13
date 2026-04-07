<div class="w-[95%] max-h-[98vh] sm:w-[50%] p-4 bg-white rounded-xl shadow-2xl relative z-50 overflow-y-auto">

    <div class="flex items-center justify-between w-full pb-4 border-b border-gray-200 noprint">
        <div class="flex items-center gap-2 sm:gap-3 min-w-0">
            <div class="bg-black p-2 rounded-lg shadow-md shrink-0">
                <i class="ri-layout-grid-line text-white text-lg sm:text-xl"></i>
            </div>
            <div class="min-w-0">
                <h1 class="text-sm sm:text-xl font-extrabold text-gray-900 truncate">
                    <?= $id->shape ?>
                </h1>
                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">
                    Extrusion Dies
                </p>
            </div>
        </div>


        <div class="flex items-center gap-2 shrink-0">
            <button
                type="button"
                hx-post="?c=Extrusion&a=Delete&id=<?= $id->id ?>"
                hx-confirm="Are you sure you want to delete this item?"
                hx-swap="none"
                class="flex items-center justify-center
                    bg-red-500 text-white hover:bg-red-700
                    w-10 h-10 sm:w-auto sm:h-auto
                    sm:px-6 py-2.5 rounded-xl
                    text-xs font-bold shadow-lg
                    active:scale-95 transition">
                <i class="ri-delete-bin-line text-lg"></i>
                <span class="hidden sm:inline ml-2">DELETE</span>
            </button>

            <button
                id="closeNewModal"
                class="p-1 text-gray-500 hover:text-gray-900"
                @click="showModal = false; document.getElementById('myModal').innerHTML = '';">
                <i class="ri-close-line text-xl sm:text-2xl"></i>
            </button>
        </div>
    </div>

    <input type="hidden" id="matrix_id" value="<?= $id->id ?>">
    <input type="hidden" id="matrix_shape" value="<?= $id->geometry_shape ?>">

    <div class="space-y-4 p-4">

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Shape</label>
                <input type="text" readonly value="<?= $id->geometry_shape ?>"
                    class="w-full p-2 border rounded-lg bg-gray-50 text-gray-500 font-mono text-sm">
            </div>

            <div>
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Company</label>
                <select name="company_id"
                    hx-post="?c=Extrusion&a=UpdateField"
                    hx-trigger="change"
                    hx-swap="none"
                    hx-vals='{"id":"<?= $id->id ?>"}'
                    class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-black text-sm">
                    <?php foreach ($this->model->list('DISTINCT(company_id)', 'matrices', 'ORDER BY company_id') as $r) { ?>
                        <option value="<?= $r->company_id ?>" <?= $r->company_id == $id->company_id ? 'selected' : '' ?>>
                            <?= $r->company_id ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div>
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Category</label>
                <select name="category_id" class="tom-select-edit"
                    hx-post="?c=Extrusion&a=UpdateField"
                    hx-trigger="change"
                    hx-swap="none"
                    hx-vals='{"id":"<?= $id->id ?>"}'>
                    <option value=""></option>
                    <?php foreach ($this->model->list('id, name', 'matrices_db', " AND kind = 'Category' ORDER BY name ASC") as $r) { ?>
                        <option value="<?= $r->name ?>" <?= $r->name == $id->category_id ? 'selected' : '' ?>>
                            <?= $r->name ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 bg-gray-50 p-4 rounded-xl border border-dashed border-gray-300">
            <?php foreach (['b', 'h', 'e1', 'e2'] as $f) { ?>
                <div>
                    <label class="text-xs font-bold text-gray-600 uppercase mb-1 block"><?= $f ?></label>
                    <input type="number" step="0.001" name="<?= $f ?>" value="<?= $id->$f ?>"
                        hx-post="?c=Extrusion&a=UpdateField"
                        hx-trigger="keyup changed delay:800ms"
                        hx-swap="none"
                        hx-vals='{"id":"<?= $id->id ?>"}'
                        class="w-full p-2 border rounded-lg shadow-sm focus:border-black outline-none text-sm font-semibold">
                </div>
            <?php } ?>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Clicks With</label>
                <select name="clicks[]" multiple class="tom-select-multi-edit"
                    hx-post="?c=Extrusion&a=UpdateField"
                    hx-trigger="change"
                    hx-swap="none"
                    hx-vals='{"id":"<?= $id->id ?>"}'>
                    <option value=""></option>
                    <?php
                    $clicks = json_decode($id->clicks ?? '[]', true);
                    foreach ($this->model->list('DISTINCT(geometry_shape) as v', 'matrices') as $r) {
                        ?>
                        <option value="<?= $r->v ?>" <?= in_array($r->v, $clicks) ? 'selected' : '' ?>>
                            <?= $r->v ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div>
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">System / Project</label>
                <select name="systema[]" multiple class="tom-select-multi-edit"
                    hx-post="?c=Extrusion&a=UpdateField"
                    hx-trigger="change"
                    hx-swap="none"
                    hx-vals='{"id":"<?= $id->id ?>"}'>
                    <option value=""></option>
                    <?php
                        $sys = json_decode($id->systema ?? '[]', true);
                    foreach ($this->model->list('id, name', 'matrices_db', " AND kind = 'System' ORDER BY name ASC") as $s) {
                        ?>
                        <option value="<?= $s->name ?>" <?= in_array($s->name, $sys) ? 'selected' : '' ?>>
                            <?= $s->name ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="mt-6 border-t pt-6">
            <div class="flex items-center gap-2 mb-4">
                <i class="ri-attachment-2 text-xl text-gray-400"></i>
                <label class="text-sm font-bold text-gray-700 uppercase tracking-wider">Files</label>
            </div>
            
            <input type="file" class="filepond-edit" multiple>
            
            <div id="file-list" 
                 class="mt-4 bg-white rounded-lg"
                 hx-get="?c=Extrusion&a=RefreshFileList&id=<?= $id->id ?>"
                 hx-trigger="listChanged from:body">
                <?php $this->RefreshFileList(); ?>
            </div>
        </div>
    </div>
</div>

<style>
    .ts-control { border: 1px solid #d1d5db !important; border-radius: 0.5rem !important; padding: 0.5rem !important; }
    .ts-wrapper.focus .ts-control { border-color: #000 !important; }
    .ts-dropdown { border-radius: 0.5rem !important; z-index: 2000 !important; }
</style>

<script>
(() => {
    const matrixId = document.getElementById('matrix_id').value;
    const matrixShape = document.getElementById('matrix_shape').value;

    // 1. Limpiar e inicializar TomSelect
    document.querySelectorAll('.tom-select-edit, .tom-select-multi-edit').forEach(el => {
        if (el.tomselect) el.tomselect.destroy();
    });

    document.querySelectorAll('.tom-select-edit').forEach(el => {
        new TomSelect(el, { create: false, onChange: () => el.dispatchEvent(new Event('change', { bubbles: true })) });
    });

    document.querySelectorAll('.tom-select-multi-edit').forEach(el => {
        new TomSelect(el, {
            plugins: ['remove_button'],
            onChange: () => el.dispatchEvent(new Event('change', { bubbles: true })),
            onItemRemove() {
                if (this.items.length === 0) {
                    const hxPost = el.getAttribute('hx-post');
                    const field = el.name.replace('[]','');
                    htmx.ajax('POST', hxPost, { values: { id: matrixId, [field]: '' }, swap: 'none' });
                }
            }
        });
    });

    // 2. Confirmación antes de borrar archivo (Inyectado en HTMX)
    document.body.addEventListener('htmx:confirm', (evt) => {
        // Si el elemento tiene la acción de borrar archivo
        if (evt.detail.elt.getAttribute('hx-get')?.includes('DeleteFile')) {
            evt.preventDefault();
            if (confirm('Are you sure you want to permanently delete this file?')) {
                evt.detail.issueRequest();
            }
        }
    });

    // 3. FilePond con manejo de Headers HX-Trigger
    const pondEl = document.querySelector('.filepond-edit');
    if (pondEl) {
        if (FilePond.find(pondEl)) FilePond.find(pondEl).destroy();
        
        FilePond.create(pondEl, {
            storeAsFile: true,
            server: {
                process: (fieldName, file, metadata, load, error) => {
                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('id', matrixId);
                    formData.append('shape', matrixShape);

                    fetch('?c=Extrusion&a=UploadSingleFile', { method: 'POST', body: formData })
                    .then(async response => {
                        if (response.ok) {
                            // Extraer el Trigger del header si existe
                            const trigger = response.headers.get('HX-Trigger');
                            if (trigger) {
                                const data = JSON.parse(trigger);
                                // Disparar eventos manualmente para que HTMX los capte
                                Object.keys(data).forEach(key => {
                                    document.body.dispatchEvent(new CustomEvent(key, { detail: data[key] }));
                                });
                            }
                            load();
                        } else { error('Upload failed'); }
                    })
                    .catch(() => error('Network error'));
                }
            }
        });
    }

    htmx.process(document.body);
})();
</script>