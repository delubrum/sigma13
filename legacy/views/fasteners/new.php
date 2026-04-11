<div class="w-[95%] max-h-[98vh] sm:w-[50%] p-4 bg-white rounded-xl shadow-2xl relative z-50 overflow-y-auto">
    <button id="closeNewModal"
        class="absolute top-0 right-0 m-3 text-gray-500 hover:text-gray-900"
        @click="showModal = false; document.getElementById('myModal').innerHTML = '';">
        <i class="ri-close-line text-2xl"></i>
    </button>

    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
        <div class="bg-black p-2 rounded-lg shadow-md shrink-0">
            <i class="ri-tools-line text-white text-xl"></i>
        </div>
        <div>
            <h1 class="text-xl font-extrabold text-gray-900">New</h1>
            <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Fasteners</p>
        </div>
    </div>

    <form 
        id="newForm"
        enctype="multipart/form-data"
        class="space-y-4 px-4"
        hx-post="?c=Fasteners&a=Save"
        hx-swap="none"
        hx-indicator="#loading"
    >
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="col-span-1">
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">* Code:</label>
                <input 
                    type="text" name="code" id="codeInput" required
                    placeholder="Ex: SCR-1020"
                    class="w-full p-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none text-sm"
                >
            </div>

            <div class="col-span-1">
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Category:</label>
                <select name="category" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none text-sm">>
                    <option value=""></option>
                    <?php foreach ($this->model->list('DISTINCT(category)', 'screws', ' ORDER BY category ASC') as $r) { ?>
                        <option value="<?php echo $r->category?>"><?php echo $r->category?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div>
            <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Description:</label>
            <input 
                type="text" name="description"
                placeholder="Ex: Flat Head Self-Tapping Screw"
                class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none text-sm"
            >
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 bg-gray-50 p-4 rounded-xl border border-dashed border-gray-300">
            <div>
                <label class="text-[10px] font-bold text-gray-600 uppercase mb-1 block">Head</label>
                <input type="text" name="head" class="w-full p-2 border rounded-lg shadow-sm focus:border-black outline-none text-sm font-semibold">
            </div>
            <div>
                <label class="text-[10px] font-bold text-gray-600 uppercase mb-1 block">Screwdriver</label>
                <input type="text" name="screwdriver" class="w-full p-2 border rounded-lg shadow-sm focus:border-black outline-none text-sm font-semibold">
            </div>
            <div>
                <label class="text-[10px] font-bold text-gray-600 uppercase mb-1 block">Diameter</label>
                <input type="text" name="diameter" class="w-full p-2 border rounded-lg shadow-sm focus:border-black outline-none text-sm font-semibold">
            </div>
            <div>
                <label class="text-[10px] font-bold text-gray-600 uppercase mb-1 block">Length</label>
                <input type="text" name="item_length" class="w-full p-2 border rounded-lg shadow-sm focus:border-black outline-none text-sm font-semibold">
            </div>
        </div>

        <div>
            <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Observation:</label>
            <textarea name="observation" rows="2" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none text-sm"></textarea>
        </div>

        <div class="mt-6 border-t pt-6">
            <div class="flex items-center gap-2 mb-4">
                <i class="ri-attachment-2 text-xl text-gray-400"></i>
                <label class="text-sm font-bold text-gray-700 uppercase tracking-wider">* Files:</label>
            </div>
            <input type="file" class="filepond-init" name="files[]" multiple required>
            <p class="text-[10px] text-red-500 mt-2 italic font-bold uppercase tracking-tighter">* Important: File name must match the Code.</p>
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
    document.querySelectorAll('.tom-select-init').forEach(el => {
        if (el.tomselect) el.tomselect.destroy();
        new TomSelect(el, { create: true, plugins: ['dropdown_input'] });
    });

    const pondInput = document.querySelector('.filepond-init');
    if (pondInput) {
        if (FilePond.find(pondInput)) FilePond.find(pondInput).destroy();
        FilePond.create(pondInput, {
            allowMultiple: true,
            storeAsFile: true,
            labelIdle: 'Drag & Drop files or <span class="filepond--label-action">Browse</span>',
            onaddfile: (err, file) => {
                const codeVal = document.getElementById('codeInput').value.trim().toLowerCase();
                const fileName = file.file.name.split('.').slice(0, -1).join('.').toLowerCase();
                if (codeVal && fileName !== codeVal) {
                    alert(`Warning: The file name "${file.file.name}" does not match the Code "${codeVal}".`);
                }
            }
        });
    }

    if (typeof htmx !== 'undefined') htmx.process(document.querySelector('#newForm'));
})();
</script>