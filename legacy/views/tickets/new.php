<div class="w-[95%] max-h-[98vh] sm:w-[50%] bg-white rounded-lg flex flex-col overflow-hidden shadow-2xl relative z-50">
    <div class="p-4 border-b flex justify-between items-center bg-gray-50 rounded-t-lg">
        <div class="flex items-center gap-3">
            <div class="bg-black p-2 rounded-lg shadow-md shrink-0">
                <i class="ri-customer-service-2-fill text-white text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-gray-900 uppercase tracking-tight">New Ticket</h1>
                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Admin Desk</p>
            </div>
        </div>
        <button id="closeNewModal" 
                @click="showModal = false; document.getElementById('myModal').innerHTML = '';" 
                class="ri-close-circle-fill text-3xl text-black hover:text-gray-700 transition-colors">
        </button>
    </div>

    <form id="newForm"
          autocomplete="off"
          enctype="multipart/form-data"
          class="p-6 bg-white flex flex-col flex-grow overflow-y-auto space-y-6"
          hx-post="?c=Tickets&a=Save"
          hx-swap="none"
          hx-indicator="#loading">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            
            <div>
                <label class="text-xs font-bold text-gray-500 mb-1 block uppercase">* Facility</label>
                <select name="facility" required
                        class="w-full p-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none text-sm bg-white">
                    <option value=""></option>
                    <option value="ESM1">ESM1</option>
                    <option value="ESM2">ESM2</option>
                    <option value="ESM3">ESM3</option>
                    <option value="Medellín">Medellín</option>
                    <option value="Barranquilla">Barranquilla</option>
                </select>
            </div>

            <div>
                <label class="text-xs font-bold text-gray-500 mb-1 block uppercase">* Type</label>
                <select name="kind" id="type" required
                        class="w-full p-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none text-sm bg-white">
                    <option value=""></option>
                    <option value="HR">HR</option>
                    <option value="OHS">OHS</option>
                    <option value="Marketing">Marketing</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="text-xs font-bold text-gray-500 mb-1 block uppercase">* Priority</label>
                <select name="priority" required
                        class="w-full p-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none text-sm bg-white">
                    <option value=""></option>
                    <option value="High">Right Now. Locked</option>
                    <option value="Medium">Today. Need Attention</option>
                    <option value="Low">Tomorrow. I Can Wait</option>
                </select>
            </div>
        </div>

        <div>
            <label class="text-xs font-bold text-gray-500 mb-1 block uppercase">* Description</label>
            <textarea name="description" required rows="4" 
                      placeholder="Describe the issue in detail..."
                      class="w-full p-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:outline-none text-sm"></textarea>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg border border-dashed border-gray-300">
            <label class="text-xs font-bold text-gray-500 mb-1 block uppercase">Attachment / Picture</label>
            <input type="file" name="files[]" accept="image/*" capture
                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-black file:text-white hover:file:bg-gray-800 cursor-pointer">
        </div>

        <div class="flex justify-end pt-4 border-t">
            <button type="submit"
                    class="bg-black text-white px-6 py-2 rounded-lg font-bold uppercase text-xs flex items-center gap-2 hover:bg-gray-800 transition shadow-md active:scale-95">
                <i class="ri-save-line text-lg"></i> Save Ticket
            </button>
        </div>
    </form>
</div>