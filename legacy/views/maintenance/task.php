<div class="w-[95%] sm:w-[30%] bg-white p-4 rounded-lg shadow-lg relative z-50">

    <!-- Close -->
    <button id="closeNestedModal"
        class="absolute top-0 right-0 m-3 text-black"
        @click="nestedModal = false; document.getElementById('nestedModal').innerHTML='';"
    >
        <i class="ri-close-line text-2xl"></i>
    </button>

    <!-- Title -->
    <h1 class="text-black flex items-center gap-2 mb-4">
        <i class="ri-add-line text-2xl"></i>
        <span class="text-2xl font-semibold">New Task</span>
    </h1>

    <form
        id="formItem"
        autocomplete="off"
        hx-post="?c=Maintenance&a=SaveTask"
        hx-encoding="multipart/form-data"
        hx-indicator="#loading"
        hx-on="htmx:afterRequest: document.getElementById('nestedModal').innerHTML=''; table.ajax.reload(null,false)"
        class="space-y-4"
    >

        <input type="hidden" name="id" value="<?= $id->id ?>">

        <!-- Complexity -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                * Complexity
            </label>
            <select
                name="complexity"
                required
                class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none"
            >
                <option value=""></option>
                <option value="High">High</option>
                <option value="Medium">Medium</option>
                <option value="Low">Low</option>
            </select>
        </div>

        <!-- Attends -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                * Attends
            </label>
            <select
                name="attends"
                required
                class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none"
            >
                <option value=""></option>
                <option value="Internal">Internal</option>
                <option value="External">External</option>
            </select>
        </div>

        <!-- Time -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                * Time (minutes)
            </label>
            <input
                type="number"
                step="1"
                name="duration"
                required
                class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none"
            >
        </div>

        <!-- Notes -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                * Notes
            </label>
            <textarea
                name="notes"
                required
                rows="4"
                class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none"
            ></textarea>
        </div>

        <!-- Picture -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Picture
            </label>
            <input
                type="file"
                name="files[]"
                required
                class="w-full text-sm text-gray-600
                       file:mr-4 file:py-2 file:px-4
                       file:rounded-lg file:border-0
                       file:bg-black file:text-white
                       hover:file:opacity-80"
            >
        </div>

        <!-- Actions -->
        <div class="flex justify-end pt-2">
            <button
                type="submit"
                class="flex items-center gap-2 bg-black text-white hover:opacity-80 transition-all duration-300 px-4 py-2 rounded-lg font-semibold text-sm shadow"
            >
                <i class="ri-save-line"></i> Save
            </button>
        </div>

    </form>
</div>
