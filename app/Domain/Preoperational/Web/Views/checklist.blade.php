<?php
if (! function_exists('e')) {
    function e(mixed $v): string
    {
        return htmlspecialchars((string) ($v ?? ''), ENT_QUOTES, 'UTF-8');
    }
}
?>

<input type="hidden" name="id" id="main_preop_id" value="<?= e($id_preop) ?>">

<?php foreach ($checklist_data as $category => $questions) { ?>
    <div class="flex items-center gap-2 mb-4 mt-8 px-2">
        <div class="h-[1px] bg-gray-200 flex-grow"></div>
        <h4 class="text-[12px] font-black uppercase italic text-gray-500"><?= e($category) ?></h4>
        <div class="h-[1px] bg-gray-200 flex-grow"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 p-2">
        <?php foreach ($questions as $q) {
            $saved = $saved_items[$q->id] ?? null;
            $answer = $saved->answer ?? '';
            $hasPhoto = ! empty($saved->url);
            $subtype = (int) ($q->subtype ?? 1);
            ?>
        <div class="preop-card p-5 border border-gray-200 bg-white rounded-3xl flex flex-col shadow-sm"
             data-qid="<?= e($q->id) ?>"
             data-photo="<?= $hasPhoto ? 'true' : 'false' ?>"
             data-subtype="<?= $subtype ?>">
            
            <p class="text-[13px] font-bold mb-4 text-gray-800 leading-tight"><?= e($q->question) ?></p>

            <div class="relative aspect-video bg-gray-50 rounded-2xl mb-4 border-2 border-dashed border-gray-200 flex items-center justify-center overflow-hidden">
                <div id="preview_<?= e($q->id) ?>" class="w-full h-full flex items-center justify-center">
                    <?php if ($hasPhoto) { ?>
                        <img src="<?= e($saved->url) ?>?t=<?= time() ?>" class="w-full h-full object-cover">
                    <?php } else { ?>
                        <i class="ri-camera-fill text-gray-300 text-3xl"></i>
                    <?php } ?>
                </div>
                <input type="file" accept="image/*" onchange="compressAndUpload(this)" data-qid="<?= e($q->id) ?>" class="absolute inset-0 opacity-0 cursor-pointer">
            </div>

            <?php if ($subtype === 3) { ?>
                <input type="number" name="obs_<?= e($q->id) ?>" value="<?= e($saved->obs ?? '') ?>"
                       placeholder="Ingrese valor numérico..."
                       class="w-full p-3 text-center font-bold border rounded-2xl outline-none focus:border-black"
                       hx-post="{{ route('preoperational.save') }}" hx-headers='{"X-CSRF-TOKEN": "{{ csrf_token() }}"}' hx-trigger="keyup changed delay:1s"
                       hx-include="#main_preop_id">
            <?php } else { ?>
                <div class="flex gap-2">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="question_<?= e($q->id) ?>" value="Bien" class="hidden-input peer"
                               onchange="toggleObs(<?= e($q->id) ?>, false)"
                               hx-post="{{ route('preoperational.save') }}" hx-headers='{"X-CSRF-TOKEN": "{{ csrf_token() }}"}' hx-include="#main_preop_id" 
                               <?= $answer === 'Bien' ? 'checked' : '' ?>>
                        <div class="py-2.5 border rounded-xl text-center text-[10px] font-black peer-checked:bg-green-600 peer-checked:text-white uppercase transition-all">Bien</div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="question_<?= e($q->id) ?>" value="Mal" class="hidden-input peer"
                               onchange="toggleObs(<?= e($q->id) ?>, true)"
                               hx-post="{{ route('preoperational.save') }}" hx-headers='{"X-CSRF-TOKEN": "{{ csrf_token() }}"}' hx-include="#main_preop_id" 
                               <?= $answer === 'Mal' ? 'checked' : '' ?>>
                        <div class="py-2.5 border rounded-xl text-center text-[10px] font-black peer-checked:bg-red-600 peer-checked:text-white uppercase transition-all">Mal</div>
                    </label>
                </div>

                <div id="obs_container_<?= e($q->id) ?>" class="mt-4 <?= $answer === 'Mal' ? '' : 'hidden' ?>">
                    <?php if ($subtype === 1) { ?>
                        <textarea name="obs_<?= e($q->id) ?>" placeholder="Especifique la falla..."
                                  class="w-full p-3 text-xs border rounded-xl focus:border-red-500 outline-none"
                                  hx-post="{{ route('preoperational.save') }}" hx-headers='{"X-CSRF-TOKEN": "{{ csrf_token() }}"}' hx-trigger="keyup changed delay:1s"
                                  hx-include="#main_preop_id"><?= e($saved->obs ?? '') ?></textarea>
                    <?php } elseif ($subtype === 2) {
                        $opts = json_decode($q->items ?? '[]', true);
                        $savedObs = explode(', ', $saved->obs ?? '');
                        ?>
                        <div class="flex flex-wrap gap-1.5">
                            <?php foreach ($opts as $o) { ?>
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="obs_<?= e($q->id) ?>[]" value="<?= e($o) ?>" class="hidden-input peer"
                                           hx-post="{{ route('preoperational.save') }}" hx-headers='{"X-CSRF-TOKEN": "{{ csrf_token() }}"}' hx-include="#main_preop_id"
                                           <?= in_array($o, $savedObs) ? 'checked' : '' ?>>
                                    <div class="px-2 py-1.5 border rounded-lg text-[9px] font-bold peer-checked:bg-red-600 peer-checked:text-white transition-all"><?= e($o) ?></div>
                                </label>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
        <?php } ?>
    </div>
<?php } ?>