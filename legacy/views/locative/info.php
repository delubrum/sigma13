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
            <div class="w-24 text-gray-600 shrink-0">Type:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->subtype ?></div>
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
            <div class="w-24 text-gray-600 shrink-0">Asset:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= mb_convert_case($id->hostname ?? '', MB_CASE_TITLE, 'UTF-8') ?></div>
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


    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="mb-4 pb-2.5 border-b border-dashed border-gray-200 last:border-b-0 last:mb-0 last:pb-0">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1.5 mb-2">
                <i class="ri-flag-line text-xl"></i>
                <span>Priority</span>
            </h3>

            <div class="flex text-xs mb-1">
                <?php if ($canClose) { ?>
                    <div class="w-full">
                        <select
                            required
                            name="priority"
                            class="w-full p-2 border border-gray-300 rounded-md"
                            hx-post="?c=Locative&a=Update"
                            hx-trigger="change"
                            hx-vals='{"id":<?= $id->id ?>,"field": "priority"}'
                        >
                            <option value=""></option>
                            <option value="High"   <?= ($id->priority === 'High') ? 'selected' : '' ?>>High</option>
                            <option value="Medium" <?= ($id->priority === 'Medium') ? 'selected' : '' ?>>Medium</option>
                            <option value="Low"    <?= ($id->priority === 'Low') ? 'selected' : '' ?>>Low</option>
                        </select>
                    </div>
                <?php } else { ?>
                    <div class="font-medium text-gray-900">
                        <?= htmlspecialchars($id->priority ?? '-') ?>
                    </div>
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
                    <select required name="sgc" class="w-full p-2 border border-gray-300 rounded-md" hx-post="?c=Locative&a=Update" hx-trigger="change" hx-vals='{"id":<?= $id->id ?>,"field": "sgc"}'>
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
    </div>



    <div class="mb-4 pb-2.5 border-b border-dashed border-gray-200 last:border-b-0 last:mb-0 last:pb-0">
        <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1.5 mb-2">
            <i class="ri-shield-user-line text-xl"></i>
            <span>Current Assignment</span>
        </h3>

        <div class="flex text-xs mb-1">
            <?php if ($canClose) { ?>
                <div class="w-full">
                    <select 
                        required 
                        name="assignee_id" 
                        class="w-full p-2 border border-gray-300 rounded-md"
                        hx-post="?c=Locative&a=Update"
                        hx-trigger="change"
                        hx-vals='{"id":<?= $id->id ?>,"field": "assignee_id"}'
                    >
                        <option value=""></option>
                        <?php
                        foreach ($this->model->list('id,username', 'users', " AND active = true AND JSON_CONTAINS(permissions, '\"68\"') ORDER BY username ASC") as $r) {
                            $selected = ($r->id === $id->assignee_id) ? 'selected' : '';
                            ?>
                            <option value="<?= $r->id ?>" <?= $selected ?>>
                                <?= htmlspecialchars($r->username) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            <?php } elseif ($id->assignee_id != null) { ?>
                <div class="font-medium text-gray-900">
                    <?= mb_convert_case($id->assignee, MB_CASE_TITLE, 'UTF-8') ?>
                </div>
            <?php } ?>
        </div>
    </div>

</div>