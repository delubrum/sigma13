<div class="w-[95%] max-h-[98vh] sm:w-[35%] bg-white p-6 rounded-2xl shadow-lg relative z-50 overflow-y-auto">
    <button id="closeNewModal"
        class="absolute top-0 right-0 m-3 text-gray-900 hover:text-gray-700"
        @click="showModal = false; document.getElementById('myModal').innerHTML = '';">
        <i class="ri-close-line text-2xl"></i>
    </button>

    <h1 class="text-gray-800 mb-6 flex items-center space-x-2">
        <i class="ri-add-box-line text-2xl"></i>
        <span class="text-2xl font-semibold">New Work Order</span>
    </h1>

    <form 
        id="newForm"
        enctype="multipart/form-data"
        class="space-y-6"
        hx-post="?c=Print&a=Save"
        hx-swap="none"
        hx-indicator="#loading"
    >

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">ESWindows Id:</label>
            <input 
                type="text" 
                name="esId" 
                class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none"
                placeholder="Enter ID..."
            >
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">* Excel File:</label>
            <input 
                type="file" 
                name="excel_file" 
                required 
                accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                class="w-full text-sm text-gray-600 border border-gray-300 rounded-lg p-2 cursor-pointer file:mr-4 file:py-1 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-black file:text-white hover:file:opacity-80"
            >
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">* QR (Image):</label>
            <input 
                type="file" 
                name="files[]" 
                required 
                accept="image/*"
                class="w-full text-sm text-gray-600 border border-gray-300 rounded-lg p-2 cursor-pointer file:mr-4 file:py-1 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-black file:text-white hover:file:opacity-80"
            >
            <p class="text-xs text-gray-500 mt-1">Accepted formats: Images (PNG, JPG, etc.)</p>
        </div>

        <div class="flex justify-end pt-4">
            <button 
                type="submit"
                class="flex items-center gap-2 bg-black text-white hover:opacity-80 transition-all duration-300 px-6 py-2 rounded-lg font-semibold text-sm shadow"
            >
                <i class="ri-save-line"></i> Save
            </button>
        </div>

    </form>
</div>