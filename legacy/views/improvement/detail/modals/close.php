<div class="w-[95%] max-h-[98vh] sm:w-[60%] p-4 bg-white rounded-xl shadow-2xl relative z-50 overflow-y-auto">
    <button id="closeNestedModal"
        class="text-black hover:text-gray-600 absolute top-0 right-0 m-5"
        @click="nestedModal = !nestedModal; document.getElementById('nestedModal').innerHTML = '';">
        <i class="ri-close-circle-fill text-3xl"></i>
    </button>

    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
        <div class="bg-black p-2 rounded-lg shadow-md shrink-0">
            <i class="ri-checkbox-circle-line text-white text-xl"></i>
        </div>
        <div>
            <h1 class="text-xl font-extrabold text-gray-900">Closure</h1>
            <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Continuous Improvement Management</p>
        </div>
    </div>

    <form 
        id="formClosure"
        class="space-y-4 px-2"
        hx-post="?c=Improvement&a=CloseSave"
        hx-swap="none"
        hx-indicator="#loading"
    >
        <input type="hidden" name="id" value="<?php echo $id ?>">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="col-span-1">
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Closure Date:</label>
                <input 
                    type="date" 
                    name="cdate" 
                    required
                    class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none text-sm"
                >
            </div>

            <div class="col-span-1">
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Convenience:</label>
                <input 
                    type="text"
                    name="convenience" 
                    required 
                    class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none text-sm"
                    placeholder="Enter convenience..."
                >
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="col-span-1">
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Adequacy:</label>
                <input 
                    type="text"
                    name="adequacy" 
                    required 
                    class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none text-sm"
                    placeholder="Enter adequacy..."
                >
            </div>

            <div class="col-span-1">
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Effectiveness:</label>
                <input 
                    type="text"
                    name="effectiveness" 
                    required 
                    class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none text-sm"
                    placeholder="Enter effectiveness..."
                >
            </div>
        </div>

        <div class="grid grid-cols-1">
            <div>
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">* Notes:</label>
                <textarea 
                    name="notes" 
                    rows="4" 
                    class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none text-sm"
                    placeholder="Additional notes about the closure..."
                ></textarea>
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