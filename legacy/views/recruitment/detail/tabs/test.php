<style>[x-cloak] { display: none !important; }</style>

<?php
    // Lógica PHP para detectar el estado inicial según datos existentes
    $polyState = '';
if ($id->polygraph_date || $id->polygraph_result !== null) {
    $polyState = '1';
} elseif ($id->security_date || $id->security_result !== null || $id->medical_date || $id->home_date) {
    $polyState = '0';
}
?>

<div class="mb-5" x-data="{ 
    polyRequired: '<?= $polyState ?>', 
    polyRes: '<?= $id->polygraph_result ?? '' ?>',
    secRes: '<?= $id->security_result ?? '' ?>',
    medRes: '<?= $id->medical_result ?? '' ?>',
    homeRes: '<?= $id->home_result ?? '' ?>'
}">
    <h2 class="text-base font-bold text-gray-900 mb-3 flex items-center space-x-1.5">
        <i class="ri-clipboard-line text-xl"></i>
        <span>Screening Process</span>
    </h2>

    <form id="screeningForm" class="space-y-6">

        <div class="border p-4 rounded-md bg-gray-50 border-dashed border-gray-300" 
             x-show="polyRequired === ''" 
             x-transition>
            <label class="block mb-1 font-bold text-indigo-600">Is Polygraph Required for this candidate?</label>
            <select x-model="polyRequired" class="w-full sm:w-1/3 border rounded px-3 py-2 bg-white shadow-sm">
                <option value="">Select an option...</option>
                <option value="1">Yes, Polygraph is required</option>
                <option value="0">No, skip Polygraph</option>
            </select>
        </div>

        <div x-show="polyRequired == '0'" x-cloak x-transition 
             class="p-3 bg-amber-50 border border-amber-200 rounded-md text-amber-700 text-sm flex items-center space-x-2">
            <i class="ri-information-line text-lg"></i>
            <span><strong>Notice:</strong> Polygraph was marked as <strong>Not Required</strong> for this candidate.</span>
        </div>

        <div class="border p-4 rounded-md space-y-4" x-show="polyRequired == '1'" x-cloak x-transition>
            <h3 class="font-semibold text-sm text-gray-500 uppercase tracking-wider">Step 1: Polygraph</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1 font-medium text-gray-600">Polygraph Date:</label>
                    <input type="date" name="polygraph_date" value="<?= $id->polygraph_date ?>"
                        <?= $id->polygraph_date ? 'disabled' : '' ?>
                        hx-indicator="#loading"
                        hx-post="?c=Recruitment&a=UpdateField"
                        hx-vals='{"id":<?= $id->id ?>,"field":"polygraph_date"}'
                        class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-600">Polygraph Result:</label>
                    <select name="polygraph_result" x-model="polyRes"
                        <?= ($id->polygraph_result !== null && $id->polygraph_result !== '') ? 'disabled' : '' ?>
                        hx-indicator="#loading"
                        hx-post="?c=Recruitment&a=UpdateField"
                        hx-vals='{"id":<?= $id->id ?>,"field":"polygraph_result"}'
                        class="w-full border rounded px-3 py-2">
                        <option value=""></option>
                        <option value="1">Approved</option>
                        <option value="0">Rejected</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="border p-4 rounded-md space-y-4" 
             x-show="(polyRequired == '1' && polyRes == '1') || polyRequired == '0'" 
             x-cloak x-transition>
            <h3 class="font-semibold text-sm text-gray-500 uppercase tracking-wider">Step 2: Security Study</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1 font-medium text-gray-600">Security Date:</label>
                    <input type="date" name="security_date" value="<?= $id->security_date ?>"
                        <?= $id->security_date ? 'disabled' : '' ?>
                        hx-indicator="#loading"
                        hx-post="?c=Recruitment&a=UpdateField"
                        hx-vals='{"id":<?= $id->id ?>,"field":"security_date"}'
                        class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-600">Security Result:</label>
                    <select name="security_result" x-model="secRes"
                        <?= ($id->security_result !== null && $id->security_result !== '') ? 'disabled' : '' ?>
                        hx-indicator="#loading"
                        hx-post="?c=Recruitment&a=UpdateField"
                        hx-vals='{"id":<?= $id->id ?>,"field":"security_result"}'
                        class="w-full border rounded px-3 py-2">
                        <option value=""></option>
                        <option value="1">Approved</option>
                        <option value="0">Rejected</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="border p-4 rounded-md space-y-4" x-show="secRes == '1'" x-cloak x-transition>
            <h3 class="font-semibold text-sm text-gray-500 uppercase tracking-wider">Step 3: Medical Exam</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1 font-medium text-gray-600">Medical Date:</label>
                    <input type="date" name="medical_date" value="<?= $id->medical_date ?>"
                        <?= $id->medical_date ? 'disabled' : '' ?>
                        hx-indicator="#loading"
                        hx-post="?c=Recruitment&a=UpdateField"
                        hx-vals='{"id":<?= $id->id ?>,"field":"medical_date"}'
                        class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-600">Medical Result:</label>
                    <select name="medical_result" x-model="medRes"
                        <?= ($id->medical_result !== null && $id->medical_result !== '') ? 'disabled' : '' ?>
                        hx-indicator="#loading"
                        hx-post="?c=Recruitment&a=UpdateField"
                        hx-vals='{"id":<?= $id->id ?>,"field":"medical_result"}'
                        class="w-full border rounded px-3 py-2">
                        <option value=""></option>
                        <option value="1">Approved</option>
                        <option value="0">Rejected</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="border p-4 rounded-md space-y-4" x-show="medRes == '1'" x-cloak x-transition>
            <h3 class="font-semibold text-sm text-gray-500 uppercase tracking-wider">Step 4: Home Visit</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1 font-medium text-gray-600">Home Visit Date:</label>
                    <input type="date" name="home_date" value="<?= $id->home_date ?>"
                        <?= $id->home_date ? 'disabled' : '' ?>
                        hx-indicator="#loading"
                        hx-post="?c=Recruitment&a=UpdateField"
                        hx-vals='{"id":<?= $id->id ?>,"field":"home_date"}'
                        class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-600">Home Visit Result:</label>
                    <select name="home_result" x-model="homeRes"
                        <?= ($id->home_result !== null && $id->home_result !== '') ? 'disabled' : '' ?>
                        hx-indicator="#loading"
                        hx-post="?c=Recruitment&a=UpdateField"
                        hx-vals='{"id":<?= $id->id ?>,"field":"home_result"}'
                        class="w-full border rounded px-3 py-2">
                        <option value=""></option>
                        <option value="1">Approved</option>
                        <option value="0">Rejected</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="border p-4 rounded-md space-y-4 bg-blue-50/30" x-show="homeRes == '1'" x-cloak x-transition>
            <h3 class="font-semibold text-sm text-gray-500 uppercase tracking-wider text-blue-700">Step 5: Lists Review</h3>
            <div>
                <div class="text-lg font-bold min-h-[1.5rem]">
                    <?php
                        // Forzamos a int o null para que el match sea exacto
                        $listStatus = ($id->candidate_list !== null) ? (int) $id->candidate_list : null;

echo match ($listStatus) {
    1 => '<span class="text-green-600 flex items-center space-x-1">
                                    <i class="ri-checkbox-circle-line"></i> 
                                    <span>Approved / Qualified</span>
                                </span>',
    0 => '<span class="text-red-600 flex items-center space-x-1">
                                    <i class="ri-close-circle-line"></i> 
                                    <span>Rejected</span>
                                </span>',
    default => '<span class="text-gray-400 italic font-normal">Pending final review...</span>'
};
?>
                </div>
            </div>
        </div>

    </form>
</div>