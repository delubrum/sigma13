<div class="w-[95%] max-h-[98vh] sm:w-[50%] p-4 bg-white rounded-xl shadow-2xl relative z-50 overflow-y-auto">
    <button id="closeNewModal"
        class="absolute top-0 right-0 m-3 text-gray-500 hover:text-gray-900"
        @click="showModal = false; document.getElementById('myModal').innerHTML = '';">
        <i class="ri-close-line text-2xl"></i>
    </button>

    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
        <div class="bg-black p-2 rounded-lg shadow-md shrink-0">
            <i class="ri-layout-grid-line text-white text-xl"></i>
        </div>
        <div>
            <h1 class="text-xl font-extrabold text-gray-900">New</h1>
            <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Extrusion Dies</p>
        </div>
    </div>

    <form 
        id="newForm"
        enctype="multipart/form-data"
        class="space-y-4 px-4"
        hx-post="?c=Extrusion&a=Save"
        hx-swap="none"
        hx-indicator="#loading"
    >
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="col-span-1">
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">* Shape:</label>
                <input 
                    type="text" name="shape" id="shapeInput" required
                    placeholder="Enter shape name"
                    class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none text-sm"
                >
            </div>

            <div class="col-span-1">
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Company:</label>
                <select name="company_id" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none text-sm">
                    <option value=""></option>
                    <?php foreach ($this->model->list('DISTINCT(company_id)', 'matrices', ' ORDER BY company_id ASC') as $r) { ?>
                        <option value="<?php echo $r->company_id?>"><?php echo $r->company_id?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div>
            <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Category:</label>
            <select name="category_id" class="tom-select-init">
                <option value=""></option>
                <?php foreach ($this->model->list('id, name', 'matrices_db', " AND kind = 'Category' ORDER BY name ASC") as $r) { ?>
                    <option value="<?php echo $r->name?>"><?php echo $r->name?></option>
                <?php } ?>
            </select>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 bg-gray-50 p-4 rounded-xl border border-dashed border-gray-300">
            <div>
                <label class="text-xs font-bold text-gray-600 uppercase mb-1 block">* b:</label>
                <input type="number" step="0.001" name="b" required class="w-full p-2 border rounded-lg shadow-sm focus:border-black outline-none text-sm font-semibold">
            </div>
            <div>
                <label class="text-xs font-bold text-gray-600 uppercase mb-1 block">* h:</label>
                <input type="number" step="0.001" name="h" required class="w-full p-2 border rounded-lg shadow-sm focus:border-black outline-none text-sm font-semibold">
            </div>
            <div>
                <label class="text-xs font-bold text-gray-600 uppercase mb-1 block">e1:</label>
                <input type="number" step="0.001" name="e1" class="w-full p-2 border rounded-lg shadow-sm focus:border-black outline-none text-sm font-semibold">
            </div>
            <div>
                <label class="text-xs font-bold text-gray-600 uppercase mb-1 block">e2:</label>
                <input type="number" step="0.001" name="e2" class="w-full p-2 border rounded-lg shadow-sm focus:border-black outline-none text-sm font-semibold">
            </div>
        </div>

        <div class="space-y-4">
            <div>
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Clicks With:</label>
                <select name="clicks[]" multiple class="tom-select-multi-init">
                    <?php foreach ($this->model->list('distinct(geometry_shape) as v', 'matrices', ' ORDER BY geometry_shape ASC') as $r) { ?>
                        <option value="<?php echo $r->v?>"><?php echo $r->v?></option>
                    <?php } ?>
                </select>
            </div>

            <div>
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">System / Project:</label>
                <select name="systema[]" multiple class="tom-select-multi-init">
                    <?php foreach ($this->model->list('id, name', 'matrices_db', " AND kind = 'System' ORDER BY name ASC") as $r) { ?>
                        <option value="<?php echo $r->name ?>"><?php echo $r->name ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="mt-6 border-t pt-6">
            <div class="flex items-center gap-2 mb-4">
                <i class="ri-attachment-2 text-xl text-gray-400"></i>
                <label class="text-sm font-bold text-gray-700 uppercase tracking-wider">* Files:</label>
            </div>
            <input type="file" class="filepond-init" name="files[]" multiple data-allow-reorder="true" required>
        </div>

        <div class="flex justify-end pt-4 border-t mt-4">
            <button type="submit" class="flex items-center justify-center bg-black text-white hover:bg-gray-800 px-8 py-2.5 rounded-xl text-xs font-bold shadow-lg active:scale-95 transition-all">
                <i class="ri-save-line text-lg mr-2"></i> 
                SAVE
            </button>
        </div>
    </form>
</div>

<style>
    .ts-control { border: 1px solid #d1d5db !important; border-radius: 0.5rem !important; padding: 0.5rem !important; }
    .ts-wrapper.focus .ts-control { border-color: #000 !important; }
    .ts-dropdown { border-radius: 0.5rem !important; z-index: 2000 !important; }
</style>

<script>
(function() {
    document.querySelectorAll('.tom-select-init, .tom-select-multi-init').forEach(el => {
        if (el.tomselect) el.tomselect.destroy();
    });

    document.querySelectorAll('.tom-select-init').forEach(el => {
        new TomSelect(el, { create: false, plugins: ['dropdown_input'] });
    });
    document.querySelectorAll('.tom-select-multi-init').forEach(el => {
        new TomSelect(el, { create: false, plugins: ['remove_button', 'dropdown_input'] });
    });

    const pondInput = document.querySelector('.filepond-init');
    if (pondInput) {
        if (FilePond.find(pondInput)) FilePond.find(pondInput).destroy();
        FilePond.create(pondInput, {
            allowMultiple: true,
            storeAsFile: true,
            labelIdle: 'Drag & Drop files or <span class="filepond--label-action">Browse</span>',
            onaddfile: (err, file) => {
                const shapeVal = document.getElementById('shapeInput').value.trim().toLowerCase();
                const fileName = file.file.name.split('.').slice(0, -1).join('.').toLowerCase();
                if (shapeVal && fileName !== shapeVal) alert(`Warning: File "${file.file.name}" name does not match Shape.`);
            }
        });
    }
    
    if (typeof htmx !== 'undefined') htmx.process(document.querySelector('#newForm'));
})();
</script>