<?php
// Decoding the simple array [1,2,3,4,5]
$analysis_data = isset($id->content) ? json_decode($id->content) : null;
$has_analysis = (! empty($id->method_id) || ! empty($id->probable));
$current_method = $id->method_id ?? '';
?>

<div class="w-[95%] max-h-[98vh] sm:w-[60%] p-4 bg-white rounded-xl shadow-2xl relative z-50 overflow-y-auto"
     x-data="{ method_id: '<?= $current_method ?>', isExisting: <?= $has_analysis ? 'true' : 'false' ?> }">
    
    <button id="closeNestedModal"
        class="text-black hover:text-gray-600 absolute top-0 right-0 m-5"
        @click="nestedModal = !nestedModal; document.getElementById('nestedModal').innerHTML = '';">
        <i class="ri-close-circle-fill text-3xl"></i>
    </button>

    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
        <div class="bg-black p-2 rounded-lg shadow-md shrink-0">
            <i class="ri-microscope-line text-white text-xl"></i>
        </div>
        <div>
            <h1 class="text-xl font-extrabold text-gray-900">New Cause</h1>
            <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Improvement Plan</p>
        </div>
    </div>

    <template x-if="isExisting">
        <div class="space-y-6 px-2 animate-in fade-in duration-300">
            <div class="inline-block bg-gray-100 px-3 py-1 rounded-full">
                <span class="text-[10px] font-black uppercase tracking-widest text-black" 
                      x-text="method_id == '1' ? '5 Whys Technique' : 'Attached File'"></span>
            </div>

            <template x-if="method_id == '1'">
                <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-3">
                        <?php
                        if (is_array($analysis_data)) {
                            foreach ($analysis_data as $index => $why) { ?>
                                <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg border border-gray-100">
                                    <span class="text-xs font-bold text-gray-400"><?= $index + 1 ?></span>
                                    <p class="text-sm text-gray-700"><?= htmlspecialchars($why) ?></p>
                                </div>
                            <?php }
                            } ?>
                    </div>
                </div>
            </template>

            <div class="mt-4 p-4 bg-gray-900 rounded-xl text-white">
                <span class="block text-[9px] font-bold text-gray-400 uppercase mb-1">Final Summary / Probable Cause</span>
                <p class="text-sm italic font-light leading-relaxed">
                    "<?= htmlspecialchars($id->probable ?? 'No summary provided.') ?>"
                </p>
            </div>
        </div>
    </template>

    <template x-if="!isExisting">
        <form id="formAnalysis" 
              hx-post="?c=Improvement&a=CauseSave&id=<?= $id->id ?>" 
              hx-encoding="multipart/form-data"
              hx-swap="none"
              class="px-2 space-y-6">
            
            <div class="animate-in fade-in">
                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">General Cause:</label>
                <input type="text" name="reason" required placeholder="Main category..."
                    class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:outline-none text-sm shadow-sm">
            </div>

            <div>
                <label class="text-xs font-bold text-gray-500 uppercase mb-2 block">Analysis Method:</label>
                <select x-model="method_id" name="method_id" required
                    class="w-full p-2.5 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-black outline-none shadow-sm transition-all">
                    <option value="">-- Select a technique --</option>
                    <option value="1">5 Whys</option>
                    <option value="2">File Upload</option>
                </select>
            </div>
            
            <div class="space-y-4">
                <div x-show="method_id == '1'" class="space-y-2 animate-in slide-in-from-top-2">
                    <div class="grid grid-cols-1 gap-2">
                        <template x-for="i in [1, 2, 3, 4, 5]" :key="i">
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-bold text-gray-400 w-4" x-text="i"></span>
                                <input type="text" name="whys[]" :required="method_id == '1'"
                                    class="flex-1 p-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-black outline-none transition-all"
                                    :placeholder="'Step ' + i + '...'">
                            </div>
                        </template>
                    </div>
                </div>

                <div x-show="method_id == '2'" class="p-4 border border-gray-200 rounded-xl bg-gray-50 flex items-center gap-4 animate-in slide-in-from-top-2">
                    <i class="ri-upload-cloud-2-line text-2xl text-gray-400"></i>
                    <input type="file" name="files[]" :required="method_id == '2'"
                        class="block w-full text-xs text-gray-500 
                        file:mr-4 file:py-1.5 file:px-4 
                        file:rounded-lg file:border-0 
                        file:text-xs file:font-bold 
                        file:bg-black file:text-white 
                        hover:file:bg-gray-800 cursor-pointer">
                </div>

                <div x-show="method_id != ''">
                    <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Probable Cause / Summary:</label>
                    <textarea name="probable" rows="3" required
                        class="w-full p-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-black outline-none" 
                        placeholder="State the final conclusion..."></textarea>
                </div>

                <div class="flex justify-end pt-6 border-t" x-show="method_id != ''">
                    <button type="submit" 
                        class="flex items-center justify-center bg-black text-white hover:bg-gray-800 px-10 py-3 rounded-xl text-xs font-bold shadow-lg active:scale-95 transition-all">
                        <i class="ri-save-line text-lg mr-2"></i> 
                        SAVE
                    </button>
                </div>
            </div>
        </form>
    </template>
</div>