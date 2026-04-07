<?php foreach ($checklist_data as $category => $items) { ?>
    <div class="flex items-center gap-3 my-4 px-2">
        <h4 class="text-[12px] font-black text-gray-400 uppercase tracking-tighter shrink-0"><?= $category ?></h4>
        <div class="h-[1px] bg-gray-100 flex-grow"></div>
    </div>
    
    <?php foreach ($items as $i) {
        $s = $saved_items[$i->id] ?? null;
        $ans = $s->answer ?? '';
        ?>
    <div x-data="{ status: '<?= $ans ?>' }" 
         class="inspection-card p-3 mb-2 border rounded-xl transition-all"
         data-photo="<?= ! empty($s->url) ? 'true' : 'false' ?>">
        
        <div class="flex justify-between items-center gap-4 px-1">
            <p class="text-sm font-medium text-gray-800 leading-tight"><?= $i->activity ?></p>

            <div class="flex bg-gray-100 p-1 rounded-lg shrink-0 shadow-inner">
                <label class="cursor-pointer">
                    <input type="radio" name="q_<?= $i->id ?>" value="Bien" x-model="status" class="hidden peer"
                           hx-post="?c=Inspections&a=QuickSave" 
                           hx-vals='js:{id: document.getElementById("main_inspection_id").value, q_id: "<?= $i->id ?>", field: "answer"}' 
                           hx-swap="none" hx-indicator="#loading">
                    <div class="px-4 py-1.5 text-[10px] font-bold rounded-md peer-checked:bg-white peer-checked:text-green-600 text-gray-400 uppercase italic transition-all">Bien</div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="q_<?= $i->id ?>" value="Mal" x-model="status" class="hidden peer"
                           hx-post="?c=Inspections&a=QuickSave" 
                           hx-vals='js:{id: document.getElementById("main_inspection_id").value, q_id: "<?= $i->id ?>", field: "answer"}' 
                           hx-swap="none" hx-indicator="#loading">
                    <div class="px-4 py-1.5 text-[10px] font-bold rounded-md peer-checked:bg-white peer-checked:text-red-600 text-gray-400 uppercase italic transition-all">Mal</div>
                </label>
            </div>
        </div>

        <div x-show="status == 'Mal'" x-transition class="mt-3 flex gap-3">
            <div class="relative w-16 h-12 bg-gray-50 border-2 border-dashed rounded-lg flex items-center justify-center overflow-hidden shrink-0">
                <div id="preview_<?= $i->id ?>" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <?= ! empty($s->url) ? "<img src='{$s->url}?t=".time()."' class='w-full h-full object-cover'>" : "<i class='ri-camera-line text-gray-300'></i>" ?>
                </div>
                <input type="file" onchange="compressAndUpload(this)" data-qid="<?= $i->id ?>" class="absolute inset-0 opacity-0 cursor-pointer">
            </div>
            <textarea name="obs_<?= $i->id ?>" placeholder="Hallazgo..."
                      class="flex-grow p-2 text-xs border rounded-lg focus:ring-1 focus:ring-red-400 outline-none h-12 resize-none transition-all"
                      hx-post="?c=Inspections&a=QuickSave" 
                      hx-trigger="keyup changed delay:1s" 
                      hx-vals='js:{id: document.getElementById("main_inspection_id").value, q_id: "<?= $i->id ?>", field: "obs"}' 
                      hx-swap="none" hx-indicator="#loading"><?= $s->obs ?? '' ?></textarea>
        </div>
    </div>
    <?php } ?>
<?php } ?>