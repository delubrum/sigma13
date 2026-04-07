<div class="w-[95%] max-h-[98vh] sm:w-[50%] p-4 bg-white rounded-xl shadow-2xl relative z-50 overflow-y-auto">
    <button id="closeNewModal"
        class="absolute top-0 right-0 m-3 text-gray-500 hover:text-gray-900"
        @click="showModal = false; document.getElementById('myModal').innerHTML = '';">
        <i class="ri-close-line text-2xl"></i>
    </button>

    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
        <div class="bg-black p-2 rounded-lg shadow-md shrink-0">
            <i class="<?= isset($r->id) ? 'ri-edit-line' : 'ri-list-settings-line' ?> text-white text-xl"></i>
        </div>
        <div>
            <h1 class="text-xl font-extrabold text-gray-900"><?= isset($r->id) ? 'Edit Entry' : 'New Entry' ?></h1>
            <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Database</p>
        </div>
    </div>

    <form 
        id="newForm"
        class="space-y-4 px-4"
        hx-post="?c=Extrusion&a=SaveDB"
        hx-swap="none"
        hx-on::after-request="if(event.detail.successful) window.location.reload()"
    >
        <input type="hidden" name="id" value="<?= $r->id ?? '' ?>">

        <div>
            <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">* Name:</label>
            <input 
                type="text" name="name" required
                value="<?= $r->name ?? '' ?>"
                placeholder="Enter name..."
                class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none text-sm font-semibold"
            >
        </div>

        <div>
            <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">* Type:</label>
            <select name="kind" required class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none text-sm font-semibold bg-white">
                <option value="" disabled <?= ! isset($r->kind) ? 'selected' : '' ?>>Select Type</option>
                <option value="Category" <?= (isset($r->kind) && $r->kind == 'Category') ? 'selected' : '' ?>>Category</option>
                <option value="System" <?= (isset($r->kind) && $r->kind == 'System') ? 'selected' : '' ?>>System</option>
            </select>
        </div>

        <div class="flex justify-end pt-4 border-t mt-6">
            <button type="submit" class="flex items-center justify-center bg-black text-white hover:bg-gray-800 px-8 py-2.5 rounded-xl text-xs font-bold shadow-lg active:scale-95 transition-all">
                <i class="ri-save-line text-lg mr-2"></i> 
                <?= isset($r->id) ? 'UPDATE' : 'SAVE' ?>
            </button>
        </div>
    </form>
</div>