<?php
// dame todo el codigo para evitar errores

$dataDecoded = json_decode($data ?? '[]', true);
if (! is_array($dataDecoded)) {
    $dataDecoded = [];
}

if (! function_exists('isActive')) {
    function isActive($group, $name, $dataDecoded)
    {
        if (isset($dataDecoded[$group]['items']) && is_array($dataDecoded[$group]['items'])) {
            return in_array($name, $dataDecoded[$group]['items']);
        }
        if (isset($dataDecoded['items']) && is_array($dataDecoded['items'])) {
            return in_array($name, $dataDecoded['items']);
        }
        if (array_values($dataDecoded) === $dataDecoded) {
            return in_array($name, $dataDecoded);
        }

        return false;
    }
}

$currentCategory = null;
?>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 p-0 bg-gray-50 rounded-xl">
    <div>
        <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1 mb-2">
            <i class="ri-briefcase-line text-xl"></i>
            <span>Experience</span>
        </h3>
        <div class="text-sm"><?= $id->experience ?></div>
    </div>

    <div>
        <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1 mb-2">
            <i class="ri-book-line text-xl"></i>
            <span>Education</span>
        </h3>
        <?php
        $eduItem = $this->model->get('content', 'job_profile_items', "and jp_id = $id->id and kind = 'Educación'");
$education = $eduItem ? json_decode($eduItem->content, true) : [];
if (! empty($education)) { ?>
            <?php foreach ($education as $row) { ?>
                <?php if (! empty(trim($row[1] ?? ''))) { ?>
                    <div class="flex text-xs mb-1 items-start">
                        <div class="w-24 text-gray-600 shrink-0"><?= htmlspecialchars($row[0]) ?>:</div>
                        <div class="font-medium text-gray-900 flex-1 break-words"><?= htmlspecialchars($row[1]) ?></div>
                    </div>
                <?php } ?>
            <?php } ?>
        <?php } ?>
    </div>
</div>

<div class="mb-2 w-full">
    <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1 mb-2">
        <i class="ri-briefcase-line text-xl"></i>
        <span>Resources</span>
    </h3>

    <div class="space-y-3">
    <?php if (! empty($resourcesList)) { ?>
        <?php foreach ($resourcesList as $key => $p) {
            if ($currentCategory !== $p->category) {
                $currentCategory = $p->category;
                ?>
            <div>
                <div class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1">
                    <?= htmlspecialchars($currentCategory ?: 'General') ?>
                </div>
                <div class="flex flex-wrap gap-2">
        <?php } ?>

            <?php
                        $activo = isActive('Recursos', $p->name, $dataDecoded);
            $val = htmlspecialchars($p->name);
            ?>
            
            <div class="relative inline-block">
                <input type="checkbox" 
                       name="resources[]" 
                       value="<?= $val ?>" 
                       id="res_<?= $key ?>"
                       class="hidden peer"
                       <?= $activo ? 'checked' : '' ?>>
                
                <label for="res_<?= $key ?>" 
                       class="text-sm px-3 py-1 rounded-lg border transition-all cursor-pointer inline-block
                              peer-checked:!bg-black peer-checked:!text-white
                              bg-gray-100 text-gray-500 border-transparent hover:border-gray-300">
                    <?= $val ?>
                </label>
            </div>

            <?php
            $nextItem = isset($resourcesList[$key + 1]) ? $resourcesList[$key + 1] : null;
            if (! $nextItem || $nextItem->category !== $currentCategory) { ?>
                </div></div>
            <?php } ?>
            
        <?php } ?>
    <?php } ?>
    </div>
</div>