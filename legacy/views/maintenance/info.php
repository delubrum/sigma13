<div class="p-4">
    <div class="flex justify-center mb-4">
        <div class="inline-block px-4 py-1.5 rounded-full text-sm font-bold shadow-md border-2">
            <?php echo ucwords($id->status) ?>
        </div>
    </div>
    <div class="mb-4 pb-2.5 border-b border-dashed border-gray-200 last:border-b-0 last:mb-0 last:pb-0">
        <div class="flex items-center space-x-2 mb-2">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1">
                <i class="ri-information-line text-xl"></i>
                <span>Basic Information</span>
            </h3>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">User:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->username ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Date:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->created_at ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Facility:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->facility ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Started:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->started_at ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Closed:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->closed_at ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Rating:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= ! empty($id->rating) && $id->rating != 0 ? $id->rating : '' ?></div>
        </div>

        <?php
        $directorio = "uploads/mnt/userpics/$id->id/";
            $files = glob($directorio.'*');

            if (! empty($files)) {
                sort($files);
                ?>
            <div class="flex text-xs mb-1 items-start">
                <div class="w-24 text-gray-600 shrink-0">Evidence:</div>
                <div class="font-medium text-blue-500 flex-1 break-words space-y-1">
                    <?php
                            foreach ($files as $file) {
                                if (is_file($file)) {
                                    $fileName = basename($file);
                                    echo "<a class='block' target='_blank' href='{$file}'>
                                    <i class='ri-file-line'></i> Evidence
                                </a>";
                                }
                            }
                ?>
                </div>
            </div>
        <?php } ?>

    </div>

    <div class="mb-4 pb-2.5">
        <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1.5 mb-2">
            <i class="ri-file-text-line text-xl"></i> <span>Description</span>
        </h3>
        <div class="flex text-xs mb-1"><div class="font-medium text-gray-900"><?= $id->description ?></div></div>
    </div>

    <div class="mb-4 pb-2.5 border-b border-dashed border-gray-200 last:border-b-0 last:mb-0 last:pb-0">
        <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1.5 mb-2">
            <i class="ri-shield-user-line text-xl"></i>
            <span>Asset</span>
        </h3>

        <div class="flex text-xs mb-1">
            <?php if ($canClose) { ?>
                <div class="w-full">
                    <select 
                        required 
                        name="asset_id" 
                        class="tomselect w-full p-2 border border-gray-300 rounded-md"
                        hx-post="?c=Maintenance&a=Update"
                        hx-trigger="change"
                        hx-vals='{"id":<?= $id->id ?>,"field": "asset_id"}'
                    >
                        <option value=""></option>
                        <?php
                    foreach ($this->model->list('id,hostname,serial,sap', 'assets') as $r) {
                        $selected = ($r->id == $id->asset_id) ? 'selected' : '';
                        ?>
                            <option value="<?= $r->id ?>" <?= $selected ?>>
                                <?= mb_convert_case($r->hostname, MB_CASE_TITLE, 'UTF-8').' | '.$r->serial.' | '.$r->sap  ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            <?php } elseif ($id->asset_id != null) { ?>
                <div class="font-medium text-gray-900">
                    <?= mb_convert_case($id->asset_id, MB_CASE_TITLE, 'UTF-8') ?>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
        
        <div class="mb-4 pb-2.5 border-b border-dashed border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1.5 mb-2">
                <i class="ri-flag-line text-xl"></i>
                <span>Priority</span>
            </h3>
            <div class="flex text-xs mb-1">
                <?php if ($canClose) { ?>
                    <div class="w-full">
                        <select required name="priority" class="w-full p-2 border border-gray-300 rounded-md" hx-post="?c=Maintenance&a=Update" hx-trigger="change" hx-vals='{"id":<?= $id->id ?>,"field": "priority"}'>
                            <option value=""></option>
                            <option value="High"   <?= ($id->priority === 'High') ? 'selected' : '' ?>>High</option>
                            <option value="Medium" <?= ($id->priority === 'Medium') ? 'selected' : '' ?>>Medium</option>
                            <option value="Low"    <?= ($id->priority === 'Low') ? 'selected' : '' ?>>Low</option>
                        </select>
                    </div>
                <?php } else { ?>
                    <div class="font-medium text-gray-900"><?= htmlspecialchars($id->priority ?? '-') ?></div>
                <?php } ?>
            </div>
        </div>

<div class="mb-4 pb-2.5 border-b border-dashed border-gray-200">
    <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1.5 mb-2">
        <i class="ri-shield-user-line text-xl"></i>
        <span>Assignment</span>
    </h3>
    <div class="flex text-xs mb-1">
        <?php if ($canClose) { ?>
            <div class="w-full">
                <select required name="assignee_id" class="w-full p-2 border border-gray-300 rounded-md" hx-post="?c=Maintenance&a=Update" hx-trigger="change" hx-vals='{"id":<?= $id->id ?>,"field": "assignee_id"}'>
                    <option value=""></option>
                    <?php
                    // 1. Cambiamos active = 1 por active = true
                    // 2. Cambiamos JSON_CONTAINS por LIKE con casteo a text
                    $where_users = " AND active = true AND permissions::text LIKE '%\"35\"%' ORDER BY username ASC";

            foreach ($this->model->list('id,username', 'users', $where_users) as $r) {
                $selected = ($r->id == $id->assignee_id) ? 'selected' : ''; ?>
                        <option value="<?= $r->id ?>" <?= $selected ?>><?= htmlspecialchars($r->username) ?></option>
                    <?php } ?>
                </select>
            </div>
        <?php } elseif ($id->assignee_id != null) { ?>
            <div class="font-medium text-gray-900"><?= mb_convert_case($id->assignee_name ?? 'Not Assigned', MB_CASE_TITLE, 'UTF-8') ?></div>
        <?php } ?>
    </div>
</div>

        <div class="mb-4 pb-2.5 border-b border-dashed border-gray-200 md:border-b-0">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1.5 mb-2">
                <i class="ri-settings-3-line text-xl"></i>
                <span>SGC</span>
            </h3>
            <div class="flex text-xs mb-1">
                <div class="w-full">
                    <select required name="sgc" class="w-full p-2 border border-gray-300 rounded-md" hx-post="?c=Maintenance&a=Update" hx-trigger="change" hx-vals='{"id":<?= $id->id ?>,"field": "sgc"}'>
                        <option value=""></option>
                        <?php
                $sgc_options = ['Corrective', 'Preventive', 'Production', 'Infrastructure'];
            foreach ($sgc_options as $option) {
                $selected = ($id->sgc == $option) ? 'selected' : ''; ?>
                            <option value="<?= $option ?>" <?= $selected ?>><?= $option ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="mb-4 pb-2.5 border-b border-dashed border-gray-200 md:border-b-0">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1.5 mb-2">
                <i class="ri-error-warning-line text-xl"></i>
                <span>Cause</span>
            </h3>
            <div class="flex text-xs mb-1">
                <div class="w-full">
                    <select required name="root_cause" class="w-full p-2 border border-gray-300 rounded-md" hx-post="?c=Maintenance&a=Update" hx-trigger="change" hx-vals='{"id":<?= $id->id ?>,"field": "root_cause"}'>
                        <option value=""></option>
                        <?php
                        $cause_options = ['N/A', 'Habituales', 'Falla Eléctrica', 'Falta Capacitación', 'Desgaste', 'Mal Uso'];
            foreach ($cause_options as $option) {
                $selected = ($id->root_cause == $option) ? 'selected' : ''; ?>
                            <option value="<?= $option ?>" <?= $selected ?>><?= $option ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
  document.querySelectorAll('.tomselect').forEach(el => {
    new TomSelect(el);
  });
</script>