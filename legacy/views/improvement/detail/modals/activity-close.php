<div class="w-[95%] max-h-[98vh] sm:w-[50%] p-4 bg-white rounded-xl shadow-2xl relative z-50 overflow-y-auto" 
     x-data="{ fulfill: '' }">
    
    <button id="closeNestedModal"
        class="text-black hover:text-gray-600 absolute top-0 right-0 m-5"
        @click="nestedModal = false; document.getElementById('nestedModal').innerHTML = '';">
        <i class="ri-close-circle-fill text-3xl"></i>
    </button>

    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
        <div class="bg-black p-2 rounded-lg shadow-md shrink-0">
            <i class="ri-checkbox-circle-line text-white text-xl"></i>
        </div>
        <div>
            <h1 class="text-xl font-extrabold text-gray-900">Activity Result</h1>
            <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Continuous Improvement</p>
        </div>
    </div>

    <form 
        id="formResult"
        class="space-y-4 px-2"
        hx-post="?c=Improvement&a=ActivitySave"
        hx-encoding="multipart/form-data"
        hx-swap="none"
        hx-indicator="#loading"
        enctype="multipart/form-data"
    >
        <input type="hidden" name="id" value="<?php echo $id ?>">
        <input type="hidden" name="activity_id" value="<?php echo $activity_id ?>">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="col-span-1">
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">* Date:</label>
                <input 
                    type="date" 
                    name="date" 
                    required
                    class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none text-sm"
                >
            </div>

            <div class="col-span-1">
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">* Done?:</label>
                <select 
                    name="fulfill" 
                    required 
                    class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none text-sm cursor-pointer"
                >
                    <option value=""></option>
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>
            </div>
        </div>

        <div>
            <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">* Results:</label>
            <textarea 
                name="results" 
                required 
                rows="3" 
                class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none text-sm"
                placeholder="Detail the findings or results..."
            ></textarea>
        </div>

        <div class="p-4 border-2 border-dashed border-gray-200 rounded-lg bg-gray-50 transition-colors hover:border-gray-400 group">
            <label class="block">
                <span class="sr-only">EVIDENCE</span>
                <input type="file" name="file" 
                    class="block w-full text-xs text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-full file:border-0
                    file:text-[10px] file:font-bold
                    file:bg-black file:text-white
                    hover:file:bg-gray-800 cursor-pointer transition-all">
            </label>
            <div class="flex items-center gap-2 mt-2">
                <i class="ri-information-line text-gray-400"></i>
                <p class="text-[10px] text-gray-400">JPG / PNG.</p>
            </div>
        </div>

        <div class="flex justify-end pt-4 border-t mt-4">
            <button type="submit" class="flex items-center justify-center bg-black text-white hover:bg-gray-800 px-10 py-3 rounded-xl text-xs font-bold shadow-lg active:scale-95 transition-all">
                <i class="ri-save-line text-lg mr-2"></i> 
                SAVE
            </button>
        </div>
    </form>
</div>