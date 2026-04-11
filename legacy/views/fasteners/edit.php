<div class="w-[95%] max-h-[98vh] sm:w-[50%] p-4 bg-white rounded-xl shadow-2xl relative z-50 overflow-y-auto">

    <div class="flex items-center justify-between w-full pb-4 border-b border-gray-200 noprint">
        <div class="flex items-center gap-2 sm:gap-3 min-w-0">
            <div class="bg-black p-2 rounded-lg shadow-md shrink-0">
                <i class="ri-tools-line text-white text-lg sm:text-xl"></i>
            </div>
            <div class="min-w-0">
                <h1 class="text-sm sm:text-xl font-extrabold text-gray-900 truncate">
                    <?= $id->code ?> </h1>
                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">
                    Fasteners / screws
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <button
                type="button"
                hx-post="?c=Fasteners&a=Delete&id=<?= $id->id ?>"
                hx-confirm="Are you sure you want to delete this fastener?"
                hx-swap="none"
                class="flex items-center justify-center bg-red-500 text-white hover:bg-red-700 w-10 h-10 sm:w-auto sm:h-auto sm:px-6 py-2.5 rounded-xl text-xs font-bold shadow-lg active:scale-95 transition">
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

    <input type="hidden" id="fastener_id" value="<?= $id->id ?>">
    <input type="hidden" id="fastener_code" value="<?= $id->code ?>">

    <div class="space-y-4 p-4">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Code</label>
                <input type="text" readonly value="<?= $id->code ?>"
                    class="w-full p-2 border rounded-lg bg-gray-50 text-gray-500 font-mono text-sm">
            </div>

            <div>
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Category</label>
                <select name="category" class="tom-select-edit"
                    hx-post="?c=Fasteners&a=UpdateField"
                    hx-trigger="change"
                    hx-swap="none"
                    hx-vals='{"id":"<?= $id->id ?>"}'>
                    <option value=""></option>
                    <?php foreach ($this->model->list('DISTINCT(category)', 'screws') as $r) { ?>
                        <option value="<?= $r->category ?>" <?= $r->category == $id->category ? 'selected' : '' ?>>
                            <?= $r->category ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div>
            <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Description</label>
            <input type="text" name="description" value="<?= $id->description ?>"
                hx-post="?c=Fasteners&a=UpdateField"
                hx-trigger="keyup changed delay:800ms"
                hx-swap="none"
                hx-vals='{"id":"<?= $id->id ?>"}'
                class="w-full p-2 border rounded-lg shadow-sm focus:border-black outline-none text-sm">
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 bg-gray-50 p-4 rounded-xl border border-dashed border-gray-300">
            <?php foreach (['head', 'screwdriver', 'diameter', 'item_length'] as $f) { ?>
                <div>
                    <label class="text-[10px] font-bold text-gray-600 uppercase mb-1 block"><?= $f ?></label>
                    <input type="text" name="<?= $f ?>" value="<?= $id->$f ?>"
                        hx-post="?c=Fasteners&a=UpdateField"
                        hx-trigger="keyup changed delay:800ms"
                        hx-swap="none"
                        hx-vals='{"id":"<?= $id->id ?>"}'
                        class="w-full p-2 border rounded-lg shadow-sm focus:border-black outline-none text-sm font-semibold text-center">
                </div>
            <?php } ?>
        </div>

        <div>
            <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Observations</label>
            <textarea name="observation" rows="2"
                hx-post="?c=Fasteners&a=UpdateField"
                hx-trigger="keyup changed delay:800ms"
                hx-swap="none"
                hx-vals='{"id":"<?= $id->id ?>"}'
                class="w-full p-2 border rounded-lg shadow-sm focus:border-black outline-none text-sm resize-none"><?= $id->observation ?></textarea>
        </div>

        <div class="mt-6 border-t pt-6">
            <div class="flex items-center gap-2 mb-4">
                <i class="ri-attachment-2 text-xl text-gray-400"></i>
                <label class="text-sm font-bold text-gray-700 uppercase tracking-wider">Attachments</label>
            </div>
            
            <input type="file" class="filepond-edit-fastener" multiple>
            
            <div id="file-list-fastener" 
                 class="mt-4 bg-white rounded-lg"
                 hx-get="?c=Fasteners&a=RefreshFileList&id=<?= $id->id ?>"
                 hx-trigger="listChanged from:body">
                <?php $this->RefreshFileList($id->id); ?>
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
    const fastenerId = document.getElementById('fastener_id').value;
    const fastenerCode = document.getElementById('fastener_code').value;

    // 1. TomSelect
    document.querySelectorAll('.tom-select-edit').forEach(el => {
        if (el.tomselect) el.tomselect.destroy();
        new TomSelect(el, { create: true, onChange: () => el.dispatchEvent(new Event('change', { bubbles: true })) });
    });

    // 2. Manejo de confirmación de borrado de archivos
    document.body.addEventListener('htmx:confirm', (evt) => {
        if (evt.detail.elt.getAttribute('hx-get')?.includes('DeleteFile')) {
            evt.preventDefault();
            if (confirm('Delete this file permanently?')) {
                evt.detail.issueRequest();
            }
        }
    });

    // 3. FilePond Configurado para Fasteners
    const pondEl = document.querySelector('.filepond-edit-fastener');
    if (pondEl) {
        if (FilePond.find(pondEl)) FilePond.find(pondEl).destroy();
        
        FilePond.create(pondEl, {
            storeAsFile: true,
            server: {
                process: (fieldName, file, metadata, load, error) => {
                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('id', fastenerId);
                    formData.append('code', fastenerCode);

                    fetch('?c=Fasteners&a=UploadSingleFile', { method: 'POST', body: formData })
                    .then(async response => {
                        if (response.ok) {
                            const trigger = response.headers.get('HX-Trigger');
                            if (trigger) {
                                const data = JSON.parse(trigger);
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