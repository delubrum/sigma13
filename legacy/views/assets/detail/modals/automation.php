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
        <i class="ri-settings-line text-2xl"></i>
        <span class="text-2xl font-semibold">Add Automation</span>
    </h1>

    <form
        autocomplete="off"
        hx-post="?c=Assets&a=SaveAutomation"
        hx-indicator="#loading"
        class="space-y-4"
    >

        <input type="hidden" name="asset_id" value="<?= isset($id) ? $id->id : '' ?>">
        <input type="hidden" name="kind" value="<?= isset($id) ? $id->area : '' ?>">

        <!-- Last Done -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                * Last Done
            </label>
            <input
                type="date"
                name="last_performed_at"
                required
                class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none"
            >
        </div>

        <!-- Frequency -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                * Frequency
            </label>
            <select
                name="frequency"
                required
                class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none"
            >
                <option value=""></option>
                <option value="Weekly">Weekly</option>
                <option value="Monthly">Monthly</option>
                <option value="Quarterly">Quarterly</option>
                <option value="Semiannual">Semiannual</option>
                <option value="Annual">Annual</option>
                <option value="Annualx2">Annualx2</option>
                <option value="Annualx3">Annualx3</option>
                <option value="Annualx5">Annualx5</option>
            </select>
        </div>

        <!-- Activity -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                * Activity
            </label>
            <input
                type="text"
                name="activity"
                required
                class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none"
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
