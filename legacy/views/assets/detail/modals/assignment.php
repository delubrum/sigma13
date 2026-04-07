<?php
$event_id = $_REQUEST['event_id'] ?? '';
$asset_id = $_REQUEST['id'] ?? '';
$eventData = $event_id ? $this->model->get('*', 'asset_events', ' AND id = '.$event_id) : null;
$asset_id = $asset_id ?: ($eventData ? $eventData->asset_id : '');
$filePath = "uploads/assets/{$asset_id}/assignment/{$event_id}.pdf";
?>

<div class="w-[95%] sm:w-[50%] bg-white p-4 rounded-lg shadow-lg relative z-50 overflow-y-auto">
    <button id="closeNestedModal"
        class="absolute top-0 right-0 m-3 text-gray-900 hover:text-gray-700"
        @click="nestedModal = !nestedModal; document.getElementById('nestedModal').innerHTML = '';"
    >
        <i class="ri-close-line text-2xl"></i>
    </button>
    
    <h1 class="text-black-700 mb-4">
        <i class="ri-add-line text-2xl"></i> 
        <span class="text-2xl font-semibold"><?= $eventData ? 'Edit Assignment' : 'Add Assignment' ?></span>
    </h1>

    <form hx-encoding='multipart/form-data' hx-post='?c=Assets&a=SaveEvent' hx-indicator="#loading">
        <input type="hidden" name="event_id" value="<?= $event_id ?>">
        <input type="hidden" name="asset_id" value="<?= $asset_id ?>">
        <input type="hidden" name="kind" value="assignment">

        <div class="modal-body py-6 px-6 max-h-[calc(90vh-120px)] overflow-y-scroll">
            
            <div class="mb-5">
                <label for="employeeSelect" class="block text-sm font-medium text-gray-700 mb-2">* Assign To:</label>
                <select id="employeeSelect" class="tomselect w-full px-4 py-2.5 border border-gray-300 rounded-lg text-black" name="employee_id" required>
                    <option value=''>Select an employee</option>
                    <?php foreach ($this->model->list('*', 'employees', ' ORDER BY name,id') as $r) {
                        $selected = ($eventData && $eventData->employee_id == $r->id) ? 'selected' : '';
                        echo "<option value='{$r->id}' {$selected}>{$r->id} || ".mb_convert_case($r->name, MB_CASE_TITLE, 'UTF-8').'</option>';
                    } ?>
                </select>
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2"><strong>Hardware:</strong></label>
                <div id="hardwareCheckboxes" class="grid grid-cols-2 sm:grid-cols-3 gap-y-2 gap-x-4">
                    <?php
                    $hardware = ['Base', 'Keyboard', 'Mouse', 'Headset', 'Webcam'];
$currentH = $eventData ? json_decode($eventData->hardware, true) : [];
foreach ($hardware as $item) {
    $checked = in_array($item, $currentH ?? []) ? 'checked' : '';
    echo "<div class='flex items-center dynamic-checkbox-item'>
                                <input class='form-checkbox h-5 w-5 text-black border-gray-300 rounded-md' type='checkbox' name='hardware[]' value='$item' id='hardware_$item' $checked>
                                <label class='ml-2 text-sm text-gray-900 cursor-pointer' for='hardware_$item'>$item</label>
                            </div>";
}
?>
                </div>
                <div class="mt-4 flex items-end space-x-2">
                    <input type="text" id="newHardwareInput" class="flex-grow px-3 py-1.5 border border-gray-300 rounded-lg text-sm" placeholder="Add New Hardware">
                    <button type="button" id="addHardwareButton" class="px-3 py-1.5 rounded-md text-xs bg-black text-white">Add</button>
                </div>
            </div>

            <div class="mb-5 border-b pb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2"><strong>Software:</strong></label>
                <div id="softwareCheckboxes" class="grid grid-cols-2 sm:grid-cols-3 gap-y-2 gap-x-4">
                    <?php
$software = ['Office 365', 'Autodesk', 'Adobe', 'Bluebeam', 'Smard2d', 'Sap Erp', 'Hilti', 'Idea Statica', 'Sap 2000', 'Rfam', '3Dexperience', 'Trutops', 'Harmony'];
$currentS = $eventData ? json_decode($eventData->software, true) : [];
foreach ($software as $item) {
    $checked = in_array($item, $currentS ?? []) ? 'checked' : '';
    echo "<div class='flex items-center dynamic-checkbox-item'>
                                <input class='form-checkbox h-5 w-5 text-black border-gray-300 rounded-md' type='checkbox' name='software[]' value='$item' id='software_$item' $checked>
                                <label class='ml-2 text-sm text-gray-900 cursor-pointer' for='software_$item'>$item</label>
                            </div>";
}
?>
                </div>
                <div class="mt-4 flex items-end space-x-2">
                    <input type="text" id="newSoftwareInput" class="flex-grow px-3 py-1.5 border border-gray-300 rounded-lg text-sm" placeholder="Add New Software">
                    <button type="button" id="addSoftwareButton" class="px-3 py-1.5 rounded-md text-xs bg-black text-white">Add</button>
                </div>
            </div>

            <div class="mb-5 pt-4" id="fileBlock">
                <label class="block text-sm font-bold text-gray-700 mb-2">Minute (PDF Only):</label>
                
                <?php if ($eventData && file_exists($filePath)) { ?>
                    <div class="flex items-center justify-between bg-gray-50 p-2 rounded border border-blue-200 mb-3 shadow-sm animate__animated animate__fadeIn">
                        <span class="text-[10px] text-blue-600 font-bold italic uppercase"><i class="ri-file-pdf-line"></i> Minute Attached</span>
                        <div class="flex gap-2">
                            <a href="<?= $filePath ?>?t=<?= time() ?>" target="_blank" class="text-[10px] bg-white border px-2 py-0.5 rounded shadow-sm">View</a>
                            <button type="button" 
                                class="text-[10px] bg-red-50 text-red-600 border border-red-200 px-2 py-0.5 rounded hover:bg-red-100"
                                hx-post="?c=Assets&a=DeleteEventFile&asset_id=<?= $asset_id ?>&event_id=<?= $eventData->id ?>&kind=assignment"
                                hx-confirm="Confirm delete current file?"
                                hx-target="#fileBlock"
                                hx-swap="innerHTML">
                                Remove
                            </button>
                        </div>
                    </div>
                <?php } ?>

                <input type="file" name="file" accept="application/pdf"
                       class="block w-full text-xs text-gray-500 
                              file:mr-4 file:py-2 file:px-4 
                              file:rounded-md file:border-0 
                              file:text-xs file:font-semibold 
                              file:bg-black file:text-white 
                              hover:file:bg-gray-800 cursor-pointer shadow-sm">
            </div>

            <div class="flex justify-end pt-6">
                <button type="submit" class="px-8 py-2.5 rounded-md text-sm font-bold bg-black text-white hover:bg-gray-800 transition-all shadow-md active:scale-95">
                    Save Changes
                </button>
            </div>
        </div>
    </form>
</div>

<script>
  // Inicialización TomSelect
  document.querySelectorAll('.tomselect').forEach(el => {
    if(!el.tomselect) new TomSelect(el, { openOnFocus: true });
  });

  // Funciones Add New Hardware
  document.getElementById('addHardwareButton').onclick = () => {
      const input = document.getElementById('newHardwareInput');
      if (input.value.trim()) {
          document.getElementById('hardwareCheckboxes').insertAdjacentHTML('beforeend', 
            `<div class='flex items-center dynamic-checkbox-item'>
                <input class='form-checkbox h-5 w-5 text-black border-gray-300 rounded-md' type='checkbox' name='hardware[]' value='${input.value.trim()}' checked>
                <label class='ml-2 text-sm text-gray-900 cursor-pointer'>${input.value.trim()}</label>
            </div>`);
          input.value = '';
      }
  };

  // Funciones Add New Software
  document.getElementById('addSoftwareButton').onclick = () => {
      const input = document.getElementById('newSoftwareInput');
      if (input.value.trim()) {
          document.getElementById('softwareCheckboxes').insertAdjacentHTML('beforeend', 
            `<div class='flex items-center dynamic-checkbox-item'>
                <input class='form-checkbox h-5 w-5 text-black border-gray-300 rounded-md' type='checkbox' name='software[]' value='${input.value.trim()}' checked>
                <label class='ml-2 text-sm text-gray-900 cursor-pointer'>${input.value.trim()}</label>
            </div>`);
          input.value = '';
      }
  };
</script>