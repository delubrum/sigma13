<div class="w-[95%] max-h-[98vh] sm:w-[40%] bg-white rounded-lg flex flex-col overflow-hidden shadow-2xl relative z-50">
    <div class="p-4 border-b flex justify-between items-center bg-gray-50 rounded-t-lg">
        <div class="flex items-center gap-3">
            <div class="bg-black p-2 rounded-lg shadow-md shrink-0">
                <i class="ri-box-3-line text-white text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-gray-900 tracking-tight">New CBM Calculation</h1>
                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Logistics & Packing</p>
            </div>
        </div>
        <button id="closeNewModal" 
                @click="showModal = false; document.getElementById('myModal').innerHTML = '';" 
                class="ri-close-circle-fill text-3xl text-black hover:text-gray-700 transition-colors">
        </button>
    </div>

    <form id="newCbmForm"
          autocomplete="off"
          enctype="multipart/form-data"
          class="p-6 bg-white flex flex-col flex-grow overflow-y-auto space-y-6"
          hx-post='?c=CBM&a=Save'
          hx-swap="none"
          hx-indicator="#loading">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            
            <div class="md:col-span-2">
                <label class="text-xs font-bold text-gray-500 mb-1 block uppercase">* Project / Reference Name</label>
                <input type="text" required name="project" 
                       placeholder="e.g. Export to Houston - Batch A"
                       class="w-full p-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none text-sm">
            </div>

            <div class="md:col-span-2 bg-blue-50 p-3 rounded border border-blue-100">
                <p class="text-[11px] text-blue-700 italic flex items-center gap-2">
                    <i class="ri-information-line"></i>
                    Excel Format: [Width, Height, Length, Quantity, Weight]. First row must be the header.
                </p>
            </div>

            <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg border border-dashed border-gray-300">
                <label class="text-xs font-bold text-gray-500 mb-1 block uppercase">* Packing List (Excel .xlsx)</label>
                <input type="file" name="excel_file" accept=".xlsx, .xls" required 
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-black file:text-white hover:file:bg-gray-800 cursor-pointer">
            </div>
        </div>

        <div class="flex justify-end pt-5 border-t gap-3 mt-auto">
            <button type="button" 
                    @click="showModal = false; document.getElementById('myModal').innerHTML = '';"
                    class="px-6 py-2 rounded-lg font-bold uppercase text-xs text-gray-400 hover:text-gray-600 transition">
                Cancel
            </button>
            <button type="submit"
                    class="bg-black text-white px-8 py-2.5 rounded-lg font-bold uppercase text-xs flex items-center gap-2 hover:bg-gray-800 transition shadow-md active:scale-95">
                <i class="ri-upload-cloud-2-fill text-lg"></i> Upload & Process
            </button>
        </div>
    </form>
</div>