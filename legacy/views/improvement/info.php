<div class="p-4">
    <div class="flex justify-center mb-4">
        <div class="inline-block px-4 py-1.5 rounded-full text-sm font-bold shadow-md border-2">
            <?php echo ucwords($id->status) ?>
        </div>
    </div>

    <div class="mb-4 pb-2.5 border-b border-dashed border-gray-200 last:border-b-0 last:mb-0 last:pb-0">
        <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1.5 mb-2">
            <i class="ri-shield-user-line text-xl"></i>
            <span>Users Involved</span>
        </h3>
        <div class="flex text-xs mb-1">
            <?php
            // Preparamos los datos de usuarios seleccionados
            $selected_users = json_decode($id->user_ids ?? '[]', true);
            if (! is_array($selected_users)) {
                $selected_users = [];
            }

            if ($canEdit and $id->status != 'Closed' and $id->status != 'Canceled') {
                ?>
                <div class="w-full" id="users-preserve-container" hx-preserve>
                    <select id="users-tomselect" required multiple name="user_ids[]" 
                        class="w-full p-2 border border-gray-300 rounded-md" 
                        hx-post="?c=Improvement&a=Update" 
                        hx-trigger="change delay:800ms" 
                        hx-target="this"
                        hx-swap="none"
                        hx-vals='{"id":<?= $id->id ?>,"field": "user_ids"}'>
                        <?php
                            foreach ($this->model->list('id,name', 'employees', 'ORDER BY name ASC') as $r) {
                                $selected = in_array($r->id, $selected_users) ? 'selected' : '';
                                ?>
                            <option value="<?= $r->id ?>" <?= $selected ?>>
                                <?= mb_convert_case($r->name, MB_CASE_TITLE, 'UTF-8') ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            <?php } else { ?>
                <div class="font-medium text-gray-900 w-full border border-transparent">
                    <?php
                    if (! empty($selected_users)) {
                        $names = [];
                        // Buscamos los nombres que coincidan con los IDs guardados
                        foreach ($this->model->list('id,name', 'employees', 'AND id IN ('.implode(',', array_map('intval', $selected_users)).')') as $u) {
                            $names[] = mb_convert_case($u->name, MB_CASE_TITLE, 'UTF-8');
                        }
                        echo implode(', ', $names);
                    } else {
                        echo '<span class="text-gray-400 italic">No users assigned</span>';
                    }
                ?>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="mb-4 pb-2.5 border-b border-dashed border-gray-200">
        <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1.5 mb-2">
            <i class="ri-focus-3-line text-xl"></i>
            <span>Aim</span>
        </h3>
        <div class="flex text-xs mb-1">
            <?php if ($canEdit and $id->status != 'Closed' and $id->status != 'Canceled') { ?>
                <div class="w-full">
                    <textarea name="aim" 
                        oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"
                        placeholder="Define the aim..." 
                        class="w-full p-2 border border-gray-300 rounded-md rows-2" 
                        hx-post="?c=Improvement&a=Update" 
                        hx-trigger="blur" 
                        hx-vals='{"id":<?= $id->id ?>,"field": "aim"}'><?= htmlspecialchars($id->aim ?? '') ?></textarea>
                </div>
            <?php } else { ?>
                <div class="font-medium text-gray-900 whitespace-pre-wrap"><?= htmlspecialchars($id->aim ?? '-') ?></div>
            <?php } ?>
        </div>
    </div>

    <div class="mb-4 pb-2.5 border-b border-dashed border-gray-200">
        <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1.5 mb-2">
            <i class="ri-trophy-line text-xl"></i>
            <span>Goal</span>
        </h3>
        <div class="flex text-xs mb-1">
            <?php if ($canEdit and $id->status != 'Closed' and $id->status != 'Canceled') { ?>
                <div class="w-full">
                    <textarea name="goal" 
                        oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"
                        placeholder="Set a goal..." 
                        class="w-full p-2 border border-gray-300 rounded-md rows-2" 
                        hx-post="?c=Improvement&a=Update" 
                        hx-trigger="blur" 
                        hx-vals='{"id":<?= $id->id ?>,"field": "goal"}'><?= htmlspecialchars($id->goal ?? '') ?></textarea>
                </div>
            <?php } else { ?>
                <div class="font-medium text-gray-900 whitespace-pre-wrap"><?= htmlspecialchars($id->goal ?? '-') ?></div>
            <?php } ?>
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
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->kind ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Source:</div>
            <div class="font-medium text-gray-900 flex-1 break-words">
                <?= $id->source ?> <?= ($id->source == 'Otras' && ! empty($id->other)) ? "($id->other)" : '' ?>
            </div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Date:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->occurrence_date ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Process:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->process ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Perspective:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->perspective ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Repeated:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->is_repeated == 1 ? 'Yes' : 'No' ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Responsible:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->username ?></div>
        </div>
    </div>

    <div class="mb-4 pb-2.5 border-b border-dashed border-gray-200">
        <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1.5 mb-2">
            <i class="ri-file-text-line text-xl"></i> <span>Description</span>
        </h3>
        <div class="flex text-xs mb-1">
            <div class="font-medium text-gray-900 text-justify"><?= nl2br($id->description) ?></div>
        </div>
    </div>

    <div class="mb-4 pb-2.5 border-b border-dashed border-gray-200">
        <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1.5 mb-2">
            <i class="ri-flashlight-line text-xl"></i> <span>Immediate Action</span>
        </h3>
        <div class="flex text-xs mb-1">
            <div class="font-medium text-gray-900"><?= nl2br($id->acim) ?></div>
        </div>
    </div>

    <?php if (! empty($id->cdate)) { ?>
    <div class="mb-4 pb-2.5 border-b border-dashed border-gray-200">
        <div class="flex items-center space-x-2 mb-2">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1">
                <i class="ri-checkbox-circle-line text-xl"></i>
                <span>Closure Details</span>
            </h3>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Close Date:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->cdate ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start mt-1">
            <div class="w-24 text-gray-600 shrink-0">Notes:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= nl2br($id->notes) ?></div>
        </div>
    </div>
    <?php } ?>
</div>

<script>
    function initUsersSelect() {
        const el = document.getElementById('users-tomselect');
        // Si no existe el elemento o ya tiene una instancia activa, no hacemos nada
        if (!el || el.tomselect) return;

        new TomSelect(el, {
            plugins: ['remove_button'],
            persist: false,
            create: false,
            placeholder: 'Seleccione empleados...'
        });
    }

    // Inicializar al cargar
    initUsersSelect();

    // Cuando HTMX hace swap (incluso por el trigger del back), intentamos inicializar
    // pero el check 'el.tomselect' evitará el duplicado.
    document.body.addEventListener('htmx:afterSwap', function(evt) {
        initUsersSelect();
    });
</script>