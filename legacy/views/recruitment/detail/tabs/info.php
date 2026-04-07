<div class="p-4">
    <div class="mb-4 pb-2.5 border-b border-dashed border-gray-200 last:border-b-0 last:mb-0 last:pb-0">
        <div class="flex items-center space-x-2 mb-2">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1">
                <i class="ri-information-line text-xl"></i>
                <span>Basic Information</span>
            </h3>
        </div>
        <div class="grid grid-cols-1 gap-y-1">
            <?php
            $fields = [
                'Profile' => $id->profile, 'Schedule' => $id->schedule, 'Experience' => $id->experience,
                'Contract' => $id->contract, 'Quantity' => $id->qty, 'Salary' => $id->srange,
                'Start Date' => $id->start_date, 'City' => $id->city, 'Cause' => $id->cause,
                'Replaces' => $id->replaces, 'Others' => $id->others,
            ];
            foreach ($fields as $label => $value) { ?>
                <div class="flex text-xs items-start">
                    <div class="w-24 text-gray-600 shrink-0"><?= $label ?>:</div>
                    <div class="font-medium text-gray-900 flex-1 break-words"><?= $value ?></div>
                </div>
            <?php } ?>
        </div>

        <?php if (file_exists("/var/www/html/sigma/uploads/recruitment/candidates/$id->id.zip")) { ?>
        <div class="flex text-xs mt-2 items-start">
            <div class="w-24 text-gray-600 shrink-0">Candidates:</div>
            <div class="font-medium text-blue-500 flex-1 break-words">
                <a target='_blank' href="https://sigma.es-metals.com/sigma/uploads/recruitment/candidates/<?= $id->id ?>.zip">
                    <i class="ri-file-line"></i> Files
                </a>
            </div>
        </div>
        <?php } ?>
    </div>

    <div class="mb-4 pb-2.5 border-b border-dashed border-gray-200 last:border-b-0 last:mb-0 last:pb-0">
        <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1.5 mb-2">
            <i class="ri-shield-user-line text-xl"></i>
            <span>Current Assignment</span>
        </h3>
        <div class="flex text-xs mb-1">
            <?php if ($canEdit) { ?>
                <select required name="assignee_id" class="w-full p-2 border border-gray-300 rounded-md"
                    hx-post="?c=Recruitment&a=Assign" hx-trigger="change" hx-vals='{"id":<?= $id->id ?>}'>
                    <option value=""></option>
                    <?php foreach ($this->model->list('id,username', 'users', " AND active = true AND JSON_CONTAINS(permissions, '\"86\"') ORDER BY username ASC") as $r) {
                        $selected = ($r->username === $id->assignee) ? 'selected' : ''; ?>
                        <option value="<?= $r->id ?>" <?= $selected ?>><?= htmlspecialchars($r->username) ?></option>
                    <?php } ?>
                </select>
            <?php } elseif ($id->assignee !== null) { ?>
                <div class="font-medium text-gray-900"><?= mb_convert_case($id->assignee, MB_CASE_TITLE, 'UTF-8') ?></div>
            <?php } ?>
        </div>
    </div>

    <div class="mb-4 pb-2.5 border-b border-dashed border-gray-200 last:border-b-0 last:mb-0 last:pb-0">
        <div class="flex items-center space-x-2 mb-2">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1">
                <i class="ri-tools-line text-xl"></i>
                <span>Requested Resources</span>
            </h3>
        </div>
        <div class="space-y-1.5">
            <?php
            $resources_data = (is_object($id) && ! empty($id->resources)) ? json_decode($id->resources, true) : [];
            $hired_candidates = $this->model->list('name, resources', 'recruitment_candidates', " AND recruitment_id = {$id->id} AND status = 'hired'");

            if (! empty($resources_data)) {
                foreach ($resources_data as $res) {
                    $name = is_array($res) ? ($res['name'] ?? 'N/A') : $res;
                    $stage = is_array($res) ? ($res['stage'] ?? null) : null;
                    $s1_ticket = (is_array($res) && ! empty($res['ticket_id'])) ? $res['ticket_id'] : null;
                    ?>
                    <div class="text-xs">
                        <div class="flex items-center">
                            <i class="ri-arrow-right-s-line text-gray-400 mr-1"></i>
                            <span class="font-medium text-gray-900"><?= htmlspecialchars($name) ?></span>
                            
                            <?php if ($stage == 1 && $s1_ticket) {
                                $ctrl = (strtoupper($res['table'] ?? '') === 'IT') ? 'IT' : 'Tickets'; ?>
                                <a href="?c=<?= $ctrl ?>&a=Index&id=<?= $s1_ticket ?>" target="_blank" class="ml-2 text-green-600 font-bold hover:underline">
                                    [S1 Ticket #<?= $s1_ticket ?>]
                                </a>
                            <?php } ?>
                        </div>

                        <?php if ($stage == 2 || empty($stage)) {
                            foreach ($hired_candidates as $can) {
                                $can_res = json_decode($can->resources ?? '[]', true);
                                if (is_array($can_res)) {
                                    foreach ($can_res as $cr) {
                                        if ($cr['name'] === $name && ! empty($cr['ticket_id'])) {
                                            $t_id = $cr['ticket_id'];
                                            $ctrl2 = (strtoupper($cr['table'] ?? '') === 'IT') ? 'IT' : 'Tickets';
                                            $firstName = explode(' ', trim($can->name))[0]; ?>
                                            <div class="ml-5 mt-0.5 flex items-center text-[10px]">
                                                <span class="text-gray-500 mr-1"><?= htmlspecialchars($firstName) ?>:</span>
                                                <?php if ($t_id === 'EMAIL_SENT') { ?>
                                                    <span class="text-blue-500 italic">Email Sent</span>
                                                <?php } else { ?>
                                                    <a href="?c=<?= $ctrl2 ?>&a=Index&id=<?= $t_id ?>" target="_blank" class="text-blue-600 font-bold hover:underline">
                                                        [Ticket #<?= $t_id ?>]
                                                    </a>
                                                <?php } ?>
                                            </div>
                                        <?php }
                                        }
                                }
                            }
                        } ?>
                    </div>
                <?php }
                } else { ?>
                <div class="text-xs text-gray-400 italic">No resources requested.</div>
            <?php } ?>
        </div>
    </div>

    <div class="mb-4 pb-2.5 border-b border-dashed border-gray-200 last:border-b-0 last:mb-0 last:pb-0">
        <div class="flex items-center space-x-2 mb-2">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1">
                <i class="ri-book-line text-xl"></i>
                <span>Education</span>
            </h3>
        </div>
        <?php
        $edu_result = $this->model->get('content', 'job_profile_items', "and jp_id = $id->profile_id and kind = 'Educación'");
            $education = (is_object($edu_result) && ! empty($edu_result->content)) ? json_decode($edu_result->content, true) : [];

            if (! empty($education) && is_array($education)) {
                foreach ($education as $row) {
                    if (! empty(trim($row[1] ?? ''))) { ?>
                    <div class="flex text-xs mb-1 items-start">
                        <div class="w-24 text-gray-600 shrink-0"><?= htmlspecialchars($row[0] ?? '') ?>:</div>
                        <div class="font-medium text-gray-900 flex-1 break-words"><?= htmlspecialchars($row[1]) ?></div>
                    </div>
                <?php }
                    }
            } ?>
    </div>

    <div class="mb-4 pb-2.5 border-b border-dashed border-gray-200 last:border-b-0 last:mb-0 last:pb-0">
        <div class="flex items-center space-x-2 mb-2">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1">
                <i class="ri-graduation-cap-line text-xl"></i>
                <span>Formation</span>
            </h3>
        </div>
        <?php
            $form_result = $this->model->get('content', 'job_profile_items', "and jp_id = $id->profile_id and kind = 'Formación'");
            $formation = (is_object($form_result) && ! empty($form_result->content)) ? json_decode($form_result->content, true) : [];

            if (! empty($formation) && is_array($formation)) {
                foreach ($formation as $row) {
                    if (! empty(trim($row[0] ?? ''))) { ?>
                    <div class="flex text-xs mb-1 items-start">
                        <i class="ri-checkbox-circle-line text-blue-500 mr-1 mt-[2px]"></i>
                        <div class="font-medium text-gray-900 flex-1 break-words"><?= htmlspecialchars($row[0]) ?></div>
                    </div>
                <?php }
                    }
            } ?>
    </div>
</div>