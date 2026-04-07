<div class="w-[95%] max-h-[98vh] sm:w-[60%] p-4 bg-white rounded-xl shadow-2xl relative z-50 overflow-y-auto">
            <button id="closeNewModal"
                class="text-black hover:text-gray-600 absolute top-0 right-0 m-5"
                @click="showModal = !showModal; document.getElementById('myModal').innerHTML = '';">
                <i class="ri-close-circle-fill text-3xl"></i>
            </button>

    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
        <div class="bg-black p-2 rounded-lg shadow-md shrink-0">
            <i class="ri-lightbulb-line text-white text-xl"></i>
        </div>
        <div>
            <h1 class="text-xl font-extrabold text-gray-900">New Improvement</h1>
            <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Continuous Improvement System</p>
        </div>
    </div>

    <form 
        id="newForm"
        class="space-y-4 px-4"
        hx-post="?c=Improvement&a=Save"
        hx-swap="none"
        hx-indicator="#loading"
        onhxsuccess="location.reload()"
    >
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="col-span-1">
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">* Date:</label>
                <input 
                    type="date" name="occurrence_date" required
                    class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none text-sm"
                    value="<?php echo date('Y-m-d'); ?>"
                >
            </div>

            <div class="col-span-1">
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">* Type:</label>
                <select name="kind" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black outline-none text-sm" required>
                    <option value=""></option>
                    <option value='Corrective'>Corrective</option>
                    <option value='Preventive'>Preventive</option>
                    <option value='Improvement'>Improvement</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="col-span-1">
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">* Source:</label>
                <select name="source" id="sourceSelect" class="tom-select-init" required>
                    <option value=""></option>
                    <?php foreach ($this->model->list('name', 'hr_db', " and kind='source' ORDER BY name") as $r) { ?>
                        <option value="<?php echo $r->name?>"><?php echo $r->name?></option>
                    <?php } ?>
                    <option value="Otras">Otras</option>
                </select>
            </div>

            <div class="col-span-1">
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">* Responsible:</label>
                <select name="responsible_id" class="tom-select-init" required>
                    <option value=""></option>
                    <?php foreach ($this->model->list('id,username', 'users', ' ORDER BY username,id') as $r) { ?>
                        <option value="<?php echo $r->id?>"><?php echo $r->username?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div id="otherSourceContainer" class="hidden animate-fade-in">
            <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Which Source?:</label>
            <input type="text" name="other" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black outline-none text-sm">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="col-span-1">
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">* Process:</label>
                <select name="process" class="tom-select-init" required>
                    <option value=""></option>
                    <?php foreach ($this->model->list('name', 'hr_db', " and kind='processgc' ORDER BY name") as $r) { ?>
                        <option value="<?php echo $r->name?>"><?php echo $r->name?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-span-1">
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">* Perspective:</label>
                <select name="perspective" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black outline-none text-sm" required>
                    <option value=""></option>
                    <option value='Environmental'>Environmental</option>
                    <option value='Quality'>Quality</option>
                    <option value='SST'>SST</option>
                    <option value='Information Security'>Information Security</option>
                </select>
            </div>
        </div>

        <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
            <label class="text-xs font-bold text-gray-500 uppercase mb-2 block">Has this action been repeated?</label>
            <div class="flex gap-4">
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input type="radio" name="is_repeated" value="0" required class="w-4 h-4 accent-black"> No
                </label>
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input type="radio" name="is_repeated" value="1" class="w-4 h-4 accent-black"> Yes
                </label>
            </div>
        </div>

        <div class="space-y-4">
            <div>
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">* Description:</label>
                <textarea name="description" required rows="3" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black outline-none text-sm"></textarea>
            </div>
            <div>
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">* Immediate Action:</label>
                <textarea name="acim" required rows="2" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black outline-none text-sm"></textarea>
            </div>
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
    .ts-wrapper.focus .ts-control { border-color: #000 !important; ring: 2px !important; }
    .ts-dropdown { border-radius: 0.5rem !important; z-index: 2000 !important; }
    .hidden { display: none; }
</style>

<script>
(function() {
    // Reiniciar TomSelect para evitar duplicados
    document.querySelectorAll('.tom-select-init').forEach(el => {
        if (el.tomselect) el.tomselect.destroy();
        const ts = new TomSelect(el, { 
            create: false, 
            plugins: ['dropdown_input'],
            onChange: function(value) {
                if (el.id === 'sourceSelect') {
                    const container = document.getElementById('otherSourceContainer');
                    value === 'Otras' ? container.classList.remove('hidden') : container.classList.add('hidden');
                }
            }
        });
    });
})();
</script>