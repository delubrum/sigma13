<div class="w-[95%] sm:w-[50%] bg-white p-4 rounded-lg shadow-lg relative z-50">
    <button id="closeNestedModal"
        class="absolute top-0 right-0 m-3 text-gray-900 hover:text-gray-700"
        @click="nestedModal = !nestedModal; document.getElementById('nestedModal').innerHTML = '';"
    >
        <i class="ri-close-line text-2xl"></i>
    </button>
    <h1 class="text-black-700"><i class="ri-add-line text-2xl"></i> <span class="text-2xl font-semibold">Return Asset<span></h1>

    <form
        hx-encoding='multipart/form-data'
        hx-post='?c=Assets&a=SaveEvent'
        hx-indicator="#loading">

        <div class="modal-body py-6 px-6 max-h-[calc(90vh-160px)] overflow-y-auto">
            <input type="hidden" name="asset_id" value="<?= isset($id) ? $id->id : '' ?>">
            <input type="hidden" name="kind" value="return">

            <label class="block mb-1 font-medium text-gray-700 text-sm">Hardware:</label>
            <div id="returnHardwareChecklist" class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-2">
                <div class="flex items-center gap-2 text-xs text-gray-800 py-0.5 hardware-item">
                    <span class="font-medium flex-grow">Screen:</span>
                    <div class="flex gap-2">
                        <input type="radio" id="returnScreenGood" name="hardware[Screen]" value="Good" required> <label for="returnScreenGood" class="mb-0 inline-block font-normal text-gray-800 cursor-pointer">Good</label>
                        <input type="radio" id="returnScreenBad" name="hardware[Screen]" value="Bad"> <label for="returnScreenBad" class="mb-0 inline-block font-normal text-gray-800 cursor-pointer">Bad</label>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-800 py-0.5 hardware-item">
                    <span class="font-medium flex-grow">Keyboard:</span>
                    <div class="flex gap-2">
                        <input type="radio" id="returnKeyboardGood" name="hardware[Keyboard]" value="Good" required> <label for="returnKeyboardGood" class="mb-0 inline-block font-normal text-gray-800 cursor-pointer">Good</label>
                        <input type="radio" id="returnKeyboardBad" name="hardware[Keyboard]" value="Bad"> <label for="returnKeyboardBad" class="mb-0 inline-block font-normal text-gray-800 cursor-pointer">Bad</label>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-800 py-0.5 hardware-item">
                    <span class="font-medium flex-grow">Battery:</span>
                    <div class="flex gap-2">
                        <input type="radio" id="returnBatteryGood" name="hardware[Battery]" value="Good" required> <label for="returnBatteryGood" class="mb-0 inline-block font-normal text-gray-800 cursor-pointer">Good</label>
                        <input type="radio" id="returnBatteryBad" name="hardware[Battery]" value="Bad"> <label for="returnBatteryBad" class="mb-0 inline-block font-normal text-gray-800 cursor-pointer">Bad</label>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-800 py-0.5 hardware-item">
                    <span class="font-medium flex-grow">Hard Drive:</span>
                    <div class="flex gap-2">
                        <input type="radio" id="returnHardDriveGood" name="hardware[Hdd]" value="Good" required> <label for="returnHardDriveGood" class="mb-0 inline-block font-normal text-gray-800 cursor-pointer">Good</label>
                        <input type="radio" id="returnHardDriveBad" name="hardware[Hdd]" value="Bad"> <label for="returnHardDriveBad" class="mb-0 inline-block font-normal text-gray-800 cursor-pointer">Bad</label>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-800 py-0.5 hardware-item">
                    <span class="font-medium flex-grow">Processor:</span>
                    <div class="flex gap-2">
                        <input type="radio" id="returnProcessorGood" name="hardware[Processor]" value="Good" required> <label for="returnProcessorGood" class="mb-0 inline-block font-normal text-gray-800 cursor-pointer">Good</label>
                        <input type="radio" id="returnProcessorBad" name="hardware[Processor]" value="Bad"> <label for="returnProcessorBad" class="mb-0 inline-block font-normal text-gray-800 cursor-pointer">Bad</label>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-800 py-0.5 hardware-item">
                    <span class="font-medium flex-grow">RAM Memory:</span>
                    <div class="flex gap-2">
                        <input type="radio" id="returnRAMGood" name="hardware[Ram]" value="Good" required> <label for="returnRAMGood" class="mb-0 inline-block font-normal text-gray-800 cursor-pointer">Good</label>
                        <input type="radio" id="returnRAMBad" name="hardware[Ram]" value="Bad"> <label for="returnRAMBad" class="mb-0 inline-block font-normal text-gray-800 cursor-pointer">Bad</label>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-800 py-0.5 hardware-item">
                    <span class="font-medium flex-grow">Charger:</span>
                    <div class="flex gap-2">
                        <input type="radio" id="returnChargerGood" name="hardware[Charger]" value="Good" required> <label for="returnChargerGood" class="mb-0 inline-block font-normal text-gray-800 cursor-pointer">Good</label>
                        <input type="radio" id="returnChargerBad" name="hardware[Charger]" value="Bad"> <label for="returnChargerBad" class="mb-0 inline-block font-normal text-gray-800 cursor-pointer">Bad</label>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-800 py-0.5 hardware-item">
                    <span class="font-medium flex-grow">Case:</span>
                    <div class="flex gap-2">
                        <input type="radio" id="returnCaseGood" name="hardware[Case]" value="Good" required> <label for="returnCaseGood" class="mb-0 inline-block font-normal text-gray-800 cursor-pointer">Good</label>
                        <input type="radio" id="returnCaseBad" name="hardware[Case]" value="Bad"> <label for="returnCaseBad" class="mb-0 inline-block font-normal text-gray-800 cursor-pointer">Bad</label>
                    </div>
                </div>
            </div>

            <label for="returnDataWipe" class="block mb-1 font-medium text-gray-700 text-sm">Secure Data Wipe:</label>
            <select id="returnDataWipe" name="wipe" class="w-full px-3 py-2.5 mb-3 border border-gray-300 rounded-md text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500" required>
                <option value="">Select...</option>
                <option value="Yes">Yes</option>
                <option value="No">No</option>
                <option value="N/A">N/A</option>
            </select>

            <div class="mb-5">
                <label for="assignmentObs" class="block text-sm font-medium text-gray-700 mb-2">Observations:</label>
                <textarea id="assignmentObs" rows="2" name="notes" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm text-base text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-y min-h-[80px] transition-all duration-200" placeholder="Observations"></textarea>
            </div>
            
            <div class="mb-5">
                <label for="minute" class="block text-sm font-medium text-gray-700 mb-2">Minute:</label>
                <input type="file" name="file" id="minute" accept="application/pdf" required>
            </div>
        </div>

        <div class="flex justify-end gap-2 mt-4 pt-2.5 border-t border-gray-200">
            <button type="submit" class="px-3 py-1.5 rounded-md text-sm font-medium cursor-pointer transition-all duration-200 ease-in-out flex items-center justify-center space-x-1.5 shadow-sm hover:shadow-md hover:-translate-y-0.5 bg-gray-800 text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600 focus:ring-opacity-50" id="saveReturnBtn">Save</button>
        </div>
    </form>
</div>