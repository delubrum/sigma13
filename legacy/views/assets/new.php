<div class="w-[95%] sm:w-[80%] bg-white p-4 rounded-lg shadow-lg relative z-50">
    <!-- Botón cerrar -->
    <button id="closeNewModal"
        class="absolute top-0 right-0 m-3 text-gray-900 hover:text-gray-700"
        @click="showModal = !showModal; document.getElementById('myModal').innerHTML = '';"
    >
        <i class="ri-close-line text-2xl"></i>
    </button>

    <h1 class="text-black-700">
        <i class="ri-add-line text-2xl"></i>
        <span class="text-2xl font-semibold">
            <?php echo (isset($id)) ? 'Edit' : 'New'; ?> Asset
        </span>
    </h1>

    <form id="assetForm"
        enctype="multipart/form-data"
        class="overflow-y-auto max-h-[600px] p-4"
        hx-post='?c=Assets&a=Save'
        hx-swap="none"
        hx-indicator="#loading"
    >
        <input type='hidden' name='status' value='available'>
        <?php echo isset($id) ? "<input type='hidden' name='id' value='$id->id'>" : '' ?>

        <!-- GRID -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

            <div>
                <label class="block text-gray-600 text-sm mt-2">Area</label>
                <select name="area" class="w-full p-1.5 border border-gray-300 rounded-md" required>
                    <option value=""></option>
                    <option value="IT">IT</option>
                    <option value="Machinery">Machinery</option>
                    <option value="Locative">Locative</option>
                    <option value="Metrology">Metrology</option>
                    <option value="Vehicles">Vehicles</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-600 text-sm mt-2">Hostname</label>
                <input type="text" name="hostname"
                    value="<?php echo isset($id) ? $id->hostname : '' ?>"
                    class="w-full p-1.5 border border-gray-300 rounded-md focus:ring focus:ring-black"
                    required>
            </div>

            <div>
                <label class="block text-gray-600 text-sm mt-2">Serial</label>
                <input type="text" name="serial"
                    value="<?php echo isset($id) ? $id->serial : '' ?>"
                    class="w-full p-1.5 border border-gray-300 rounded-md focus:ring focus:ring-black"
                    required>
            </div>

            <div>
                <label class="block text-gray-600 text-sm mt-2">Brand</label>
                <input type="text" name="brand"
                    value="<?php echo isset($id) ? $id->brand : '' ?>"
                    class="w-full p-1.5 border border-gray-300 rounded-md focus:ring focus:ring-black"
                    required>
            </div>

            <div>
                <label class="block text-gray-600 text-sm mt-2">Model</label>
                <input type="text" name="model"
                    value="<?php echo isset($id) ? $id->model : '' ?>"
                    class="w-full p-1.5 border border-gray-300 rounded-md focus:ring focus:ring-black"
                    required>
            </div>

            <div>
                <label class="block text-gray-600 text-sm mt-2">Type</label>
                <select name="kind" class="w-full p-1.5 border border-gray-300 rounded-md" required>
                    <option value=""></option>
                    <option>N/A</option>
                    <option>Access Point</option>
                    <option>All-in-One</option>
                    <option>Biometric</option>
                    <option>Chair</option>
                    <option>Crusher</option>
                    <option>Desk</option>
                    <option>Firewall</option>
                    <option>Gun</option>
                    <option>IP Camera</option>
                    <option>Laptop</option>
                    <option>Mini Box</option>
                    <option>Mini Tower</option>
                    <option>Mobile Phone</option>
                    <option>Monitor</option>
                    <option>NAS</option>
                    <option>Printer</option>
                    <option>Server</option>
                    <option>Shredder</option>
                    <option>Sound Bar</option>
                    <option>Switch</option>
                    <option>Tablet</option>
                    <option>Tower</option>
                    <option>TV</option>
                    <option>UPS</option>
                    <option>Video Camera</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-600 text-sm mt-2">Classification</label>
                <select name="classification" class="w-full p-1.5 border border-gray-300 rounded-md" required>
                    <option value=""></option>
                    <option value="Confidential">Confidential</option>
                    <option value="Restricted">Restricted</option>
                    <option value="Internal">Internal</option>
                    <option valie="Public">Public</option>
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
                <label class="block text-gray-600 text-sm mt-2">CPU</label>
                <input type="text" name="cpu"
                    value="<?php echo isset($id) ? $id->cpu : '' ?>"
                    class="w-full p-1.5 border border-gray-300 rounded-md"
                    required>
            </div>

            <div>
                <label class="block text-gray-600 text-sm mt-2">RAM</label>
                <input type="text" name="ram"
                    value="<?php echo isset($id) ? $id->ram : '' ?>"
                    class="w-full p-1.5 border border-gray-300 rounded-md"
                    required>
            </div>

            <div>
                <label class="block text-gray-600 text-sm mt-2">SSD1</label>
                <input type="text" name="ssd"
                    value="<?php echo isset($id) ? $id->ssd : '' ?>"
                    class="w-full p-1.5 border border-gray-300 rounded-md"
                    required>
            </div>

            <div>
                <label class="block text-gray-600 text-sm mt-2">SSD2</label>
                <input type="text" name="hdd"
                    value="<?php echo isset($id) ? $id->hdd : '' ?>"
                    class="w-full p-1.5 border border-gray-300 rounded-md"
                    required>
            </div>

            <div>
                <label class="block text-gray-600 text-sm mt-2">SO</label>
                <input type="text" name="so"
                    value="<?php echo isset($id) ? $id->so : '' ?>"
                    class="w-full p-1.5 border border-gray-300 rounded-md"
                    required>
            </div>

            <div>
                <label class="block text-gray-600 text-sm mt-2">SAP</label>
                <input type="text" name="sap"
                    value="<?php echo isset($id) ? $id->sap : '' ?>"
                    class="w-full p-1.5 border border-gray-300 rounded-md"
                    required>
            </div>

            <div>
                <label class="block text-gray-600 text-sm mt-2">Price</label>
                <input type="text" name="price"
                    value="<?php echo isset($id) ? $id->price : '' ?>"
                    class="w-full p-1.5 border border-gray-300 rounded-md"
                    required>
            </div>

            <div>
                <label class="block text-gray-600 text-sm mt-2">Date</label>
                <input type="date" name="date"
                    value="<?php echo isset($id) ? $id->date : '' ?>"
                    class="w-full p-1.5 border border-gray-300 rounded-md">
            </div>

            <div>
                <label class="block text-gray-600 text-sm mt-2">Invoice</label>
                <input type="text" name="invoice"
                    value="<?php echo isset($id) ? $id->invoice : '' ?>"
                    class="w-full p-1.5 border border-gray-300 rounded-md"
                    required>
            </div>

            <div>
                <label class="block text-gray-600 text-sm mt-2">Supplier</label>
                <input type="text" name="supplier"
                    value="<?php echo isset($id) ? $id->supplier : '' ?>"
                    class="w-full p-1.5 border border-gray-300 rounded-md"
                    required>
            </div>

            <div>
                <label class="block text-gray-600 text-sm mt-2">Warranty</label>
                <input type="date" name="warranty"
                    value="<?php echo isset($id) ? $id->warranty : '' ?>"
                    class="w-full p-1.5 border border-gray-300 rounded-md"
                    >
            </div>

            <div>
                <label class="block text-gray-600 text-sm mt-2">Work Mode</label>
                <select name="work_mode" class="w-full p-1.5 border border-gray-300 rounded-md" required>
                    <option value=""></option>
                    <option value="On-site">On-site</option>
                    <option value="Remote">Remote</option>
                    <option value="Hybrid">Hybrid</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-600 text-sm mt-2">Location</label>
                <input type="text" name="location"
                    value="<?php echo isset($id) ? $id->location : '' ?>"
                    class="w-full p-1.5 border border-gray-300 rounded-md"
                    required>
            </div>

            <div>
                <label class="block text-gray-600 text-sm mt-2">Phone</label>
                <input type="text" name="phone"
                    value="<?php echo isset($id) ? $id->phone : '' ?>"
                    class="w-full p-1.5 border border-gray-300 rounded-md"
                    >
            </div>

            <div>
                <label class="block text-gray-600 text-sm mt-2">Operator</label>
                <input type="text" name="operator"
                    value="<?php echo isset($id) ? $id->operator : '' ?>"
                    class="w-full p-1.5 border border-gray-300 rounded-md"
                    >
            </div>

        </div>

        <!-- Botón guardar -->
        <div class="mt-6 flex justify-end">
            <button type="submit" class="text-xl text-gray-900 font-bold hover:text-gray-700">
                <i class="ri-save-line"></i> <?php echo (isset($id)) ? 'Update' : 'Save'; ?>
            </button>
        </div>
    </form>
</div>
