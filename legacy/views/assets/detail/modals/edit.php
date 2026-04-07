<div class="w-[95%] sm:w-[50%] bg-white p-4 rounded-lg shadow-lg relative z-50">
    <!-- Close Button (X) in Top-Right Corner -->
    <button id="closeNestedModal"
        class="absolute top-0 right-0 m-3 text-gray-900 hover:text-gray-700"
        @click="nestedModal = !nestedModal; document.getElementById('nestedModal').innerHTML = '';"
    >
        <i class="ri-close-line text-2xl"></i>
    </button>
    <h1 class="text-black-700"><i class="ri-add-line text-2xl"></i> <span class="text-2xl font-semibold"> Edit Asset <span></h1>

    <form class="flex flex-col max-h-[calc(90vh-160px)] overflow-y-auto"
        hx-post='?c=Assets&a=Save'
        hx-indicator="#loading">

        <div class="modal-body py-6 grid grid-cols-2 gap-6">
            <input type="hidden" name="id" value="<?php echo isset($id) ? $id->id : '' ?>">
        <div>
            <label for="hostname" class="block mb-1 font-medium text-gray-700 text-sm">* Hostname:</label>
            <input type="text" id="hostname" name="hostname" class="w-full px-3 py-2.5 border border-gray-300 rounded-md text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500" value="<?php echo isset($id) ? $id->hostname : '' ?>" required>
        </div>

        <div>
            <label for="serial" class="block mb-1 font-medium text-gray-700 text-sm">* Serial:</label>
            <input type="text" id="serial" name="serial" class="w-full px-3 py-2.5 border border-gray-300 rounded-md text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500" value="<?php echo isset($id) ? $id->serial : '' ?>" required>
        </div>

        <div>
            <label for="brand" class="block mb-1 font-medium text-gray-700 text-sm">* Brand:</label>
            <input type="text" id="brand" name="brand" class="w-full px-3 py-2.5 border border-gray-300 rounded-md text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500" value="<?php echo isset($id) ? $id->brand : '' ?>" required>
        </div>

        <div>
            <label for="model" class="block mb-1 font-medium text-gray-700 text-sm">* Model:</label>
            <input type="text" id="model" name="model" class="w-full px-3 py-2.5 border border-gray-300 rounded-md text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500" value="<?php echo isset($id) ? $id->model : '' ?>" required>
        </div>

        <div>
            <label for="type" class="block mb-1 font-medium text-gray-700 text-sm">* Type:</label>
            <select id="type" name="kind" class="w-full px-3 py-2.5 border border-gray-300 rounded-md text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500" required>
                <option value="N/A" <?php echo (isset($id) && $id->kind == 'N/A') ? 'selected' : '' ?>>N/A</option>
                <option value="Access Point" <?php echo (isset($id) && $id->kind == 'Access Point') ? 'selected' : '' ?>>Access Point</option>
                <option value="All-in-One" <?php echo (isset($id) && $id->kind == 'All-in-One') ? 'selected' : '' ?>>All-in-One</option>
                <option value="Biometric" <?php echo (isset($id) && $id->kind == 'Biometric') ? 'selected' : '' ?>>Biometric</option>
                <option value="Chair" <?php echo (isset($id) && $id->kind == 'Chair') ? 'selected' : '' ?>>Chair</option>
                <option value="Crusher" <?php echo (isset($id) && $id->kind == 'Crusher') ? 'selected' : '' ?>>Crusher</option>
                <option value="Desk" <?php echo (isset($id) && $id->kind == 'Desk') ? 'selected' : '' ?>>Desk</option>
                <option value="Firewall" <?php echo (isset($id) && $id->kind == 'Firewall') ? 'selected' : '' ?>>Firewall</option>
                <option value="Gun" <?php echo (isset($id) && $id->kind == 'Gun') ? 'selected' : '' ?>>Gun</option>
                <option value="IP Camera" <?php echo (isset($id) && $id->kind == 'IP Camera') ? 'selected' : '' ?>>IP Camera</option>
                <option value="Laptop" <?php echo (isset($id) && $id->kind == 'Laptop') ? 'selected' : '' ?>>Laptop</option>
                <option value="Mini Box" <?php echo (isset($id) && $id->kind == 'Mini Box') ? 'selected' : '' ?>>Mini Box</option>
                <option value="Mini Tower" <?php echo (isset($id) && $id->kind == 'Mini Tower') ? 'selected' : '' ?>>Mini Tower</option>
                <option value="Mobile Phone" <?php echo (isset($id) && $id->kind == 'Mobile Phone') ? 'selected' : '' ?>>Mobile Phone</option>
                <option value="Monitor" <?php echo (isset($id) && $id->kind == 'Monitor') ? 'selected' : '' ?>>Monitor</option>
                <option value="NAS" <?php echo (isset($id) && $id->kind == 'NAS') ? 'selected' : '' ?>>NAS</option>
                <option value="Printer" <?php echo (isset($id) && $id->kind == 'Printer') ? 'selected' : '' ?>>Printer</option>
                <option value="Server" <?php echo (isset($id) && $id->kind == 'Server') ? 'selected' : '' ?>>Server</option>
                <option value="Shredder" <?php echo (isset($id) && $id->kind == 'Shredder') ? 'selected' : '' ?>>Shredder</option>
                <option value="Sound Bar" <?php echo (isset($id) && $id->kind == 'Sound Bar') ? 'selected' : '' ?>>Sound Bar</option>
                <option value="Switch" <?php echo (isset($id) && $id->kind == 'Switch') ? 'selected' : '' ?>>Switch</option>
                <option value="Tablet" <?php echo (isset($id) && $id->kind == 'Tablet') ? 'selected' : '' ?>>Tablet</option>
                <option value="Tower" <?php echo (isset($id) && $id->kind == 'Tower') ? 'selected' : '' ?>>Tower</option>
                <option value="TV" <?php echo (isset($id) && $id->kind == 'TV') ? 'selected' : '' ?>>TV</option>
                <option value="UPS" <?php echo (isset($id) && $id->kind == 'UPS') ? 'selected' : '' ?>>UPS</option>
                <option value="Video Camera" <?php echo (isset($id) && $id->kind == 'Video Camera') ? 'selected' : '' ?>>Video Camera</option>
            </select>
        </div>

                    <div>
                <label class="block text-gray-600 text-sm mt-2">Classification</label>
                <select name="classification" class="w-full p-1.5 border border-gray-300 rounded-md" required>
                    <option value=""></option>
                    <option value="Confidential" <?php echo (isset($id) && $id->classification == 'Confidential') ? 'selected' : '' ?>>Confidential</option>
                    <option value="Restricted" <?php echo (isset($id) && $id->classification == 'Restricted') ? 'selected' : '' ?>>Restricted</option>
                    <option value="Internal" <?php echo (isset($id) && $id->classification == 'Internal') ? 'selected' : '' ?>>Internal</option>
                    <option valie="Public" <?php echo (isset($id) && $id->classification == 'Public') ? 'selected' : '' ?>>Public</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-600 text-sm mt-2">Confidentiality</label>
                <input type="number" name="confidentiality"
                    value="<?php echo isset($id) ? $id->confidentiality : '' ?>"
                    class="w-full p-1.5 border border-gray-300 rounded-md"
                    required>
            </div>

            <div>
                <label class="block text-gray-600 text-sm mt-2">Integrity</label>
                <input type="number" name="integrity"
                    value="<?php echo isset($id) ? $id->integrity : '' ?>"
                    class="w-full p-1.5 border border-gray-300 rounded-md"
                    required>
            </div>

            <div>
                <label class="block text-gray-600 text-sm mt-2">Availability</label>
                <input type="number" name="availability"
                    value="<?php echo isset($id) ? $id->availability : '' ?>"
                    class="w-full p-1.5 border border-gray-300 rounded-md"
                    required>
            </div>

        <div>
            <label for="cpu" class="block mb-1 font-medium text-gray-700 text-sm">* CPU:</label>
            <input type="text" id="cpu" name="cpu" class="w-full px-3 py-2.5 border border-gray-300 rounded-md text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500" value="<?php echo isset($id) ? $id->cpu : '' ?>" required>
        </div>

        <div>
            <label for="ram" class="block mb-1 font-medium text-gray-700 text-sm">* RAM:</label>
            <input type="text" id="ram" name="ram" class="w-full px-3 py-2.5 border border-gray-300 rounded-md text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500" value="<?php echo isset($id) ? $id->ram : '' ?>" required>
        </div>

        <div>
            <label for="ssd" class="block mb-1 font-medium text-gray-700 text-sm">* SSD1:</label>
            <input type="text" id="ssd" name="ssd" class="w-full px-3 py-2.5 border border-gray-300 rounded-md text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500" value="<?php echo isset($id) ? $id->ssd : '' ?>" required>
        </div>

        <div>
            <label for="hdd" class="block mb-1 font-medium text-gray-700 text-sm">* SSD2:</label>
            <input type="text" id="hdd" name="hdd" class="w-full px-3 py-2.5 border border-gray-300 rounded-md text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500" value="<?php echo isset($id) ? $id->hdd : '' ?>" required>
        </div>

        <div>
            <label for="so" class="block mb-1 font-medium text-gray-700 text-sm">* SO:</label>
            <input type="text" id="so" name="so" class="w-full px-3 py-2.5 border border-gray-300 rounded-md text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500" value="<?php echo isset($id) ? $id->so : '' ?>" required>
        </div>

        <div>
            <label for="sap" class="block mb-1 font-medium text-gray-700 text-sm">* SAP:</label>
            <input type="text" id="sap" name="sap" class="w-full px-3 py-2.5 border border-gray-300 rounded-md text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500" value="<?php echo isset($id) ? $id->sap : '' ?>" required>
        </div>

        <div>
            <label for="price" class="block mb-1 font-medium text-gray-700 text-sm">* Price:</label>
            <input type="text" id="price" name="price" class="w-full px-3 py-2.5 border border-gray-300 rounded-md text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500" value="<?php echo isset($id) ? $id->price : '' ?>" required>
        </div>

        <div>
            <label for="date" class="block mb-1 font-medium text-gray-700 text-sm">* Date:</label>
            <input type="date" id="date" name="date" class="w-full px-3 py-2.5 border border-gray-300 rounded-md text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500" value="<?php echo isset($id) ? $id->date : '' ?>">
        </div>

        <div>
            <label for="invoice" class="block mb-1 font-medium text-gray-700 text-sm">* Invoice:</label>
            <input type="text" id="invoice" name="invoice" class="w-full px-3 py-2.5 border border-gray-300 rounded-md text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500" value="<?php echo isset($id) ? $id->invoice : '' ?>" required>
        </div>

        <div>
            <label for="supplier" class="block mb-1 font-medium text-gray-700 text-sm">* Supplier:</label>
            <input type="text" id="supplier" name="supplier" class="w-full px-3 py-2.5 border border-gray-300 rounded-md text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500" value="<?php echo isset($id) ? $id->supplier : '' ?>" required>
        </div>

        <div>
            <label for="warranty" class="block mb-1 font-medium text-gray-700 text-sm">* Warranty:</label>
            <input type="date" id="warranty" name="warranty" class="w-full px-3 py-2.5 border border-gray-300 rounded-md text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500" value="<?php echo isset($id) ? $id->warranty : '' ?>">
        </div>

        <div>
            <label class="block text-gray-600 text-sm mt-2">Work Mode</label>
            <select name="work_mode" class="w-full p-1.5 border border-gray-300 rounded-md" required>
                <option value=""></option>
                <option value="On-site" <?php echo (isset($id) && $id->work_mode == 'On-site') ? 'selected' : '' ?>>On-site</option>
                <option value="Remote" <?php echo (isset($id) && $id->work_mode == 'Remote') ? 'selected' : '' ?>>Remote</option>
                <option value="Hybrid" <?php echo (isset($id) && $id->work_mode == 'Hybrid') ? 'selected' : '' ?>>Hybrid</option>
            </select>
        </div>

        <div>
            <label for="location" class="block mb-1 font-medium text-gray-700 text-sm">* Location:</label>
            <input type="text" id="location" name="location" class="w-full px-3 py-2.5 border border-gray-300 rounded-md text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500" value="<?php echo isset($id) ? $id->location : '' ?>" required>
        </div>

        <div>
            <label for="phone" class="block mb-1 font-medium text-gray-700 text-sm">* Phone:</label>
            <input type="text" id="phone" name="phone" class="w-full px-3 py-2.5 border border-gray-300 rounded-md text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500" value="<?php echo isset($id) ? $id->phone : '' ?>" >
        </div>

        <div>
            <label for="phone" class="block mb-1 font-medium text-gray-700 text-sm">* Operator:</label>
            <input type="text" id="operator" name="operator" class="w-full px-3 py-2.5 border border-gray-300 rounded-md text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500" value="<?php echo isset($id) ? $id->operator : '' ?>" >
        </div>
        </div>

        <div class="flex justify-end gap-2 mt-4 pt-2.5 border-t border-gray-200">
            <button @click="$dispatch('close-modal-from-inner')" class="px-4 py-2 rounded-md text-sm font-medium cursor-pointer transition-all duration-200 ease-in-out shadow-sm hover:shadow-md hover:-translate-y-0.5 bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300">Cancel</button>
            <button type="submit" class="px-3 py-1.5 rounded-md text-sm font-medium cursor-pointer transition-all duration-200 ease-in-out flex items-center justify-center space-x-1.5 shadow-sm hover:shadow-md hover:-translate-y-0.5 bg-gray-800 text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600 focus:ring-opacity-50" id="saveReturnBtn">Save</button>
        </div>
    </form>
</div>