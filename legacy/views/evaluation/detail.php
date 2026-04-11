<div class="w-[98vw] sm:w-[83vw] max-h-[98vh] bg-white rounded-lg flex flex-col overflow-hidden shadow-2xl">
    
    <div class="p-4 border-b flex justify-between items-center bg-gray-50 rounded-t-lg">
        <div class="flex items-center gap-4">
            <div class="bg-black p-2 rounded-lg shadow-md shrink-0">
                <i class="ri-list-check text-white text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-gray-900 uppercase tracking-tight">Suppliers Evaluation</h1>
                <p class="text-sm text-gray-500 font-bold tracking-widest uppercase">
                    Code: F01-PRPS-03 | Date: 2023-06-06 | Version: 02
                </p>
            </div>
        </div>
        <button @click="showModal = false" class="ri-close-circle-fill text-3xl text-black hover:text-gray-700 transition-colors"></button>
    </div>

    <div class="p-6 bg-white overflow-y-auto flex-grow">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 items-center border-b pb-6">
            
            <div class="space-y-4">
                <div>
                    <label class="text-xs font-black text-gray-400 uppercase tracking-wider block mb-1">Tipo de Evaluación:</label>
                    <?= $id->kind ?>
                </div>
                <div>
                    <label class="text-xs font-black text-gray-400 uppercase tracking-wider block mb-1">Proveedor:</label>
                    <?= $id->supplier ?>
                </div>
            </div>

            <div class="h-full">
                <label class="text-xs font-black text-gray-400 uppercase tracking-wider block mb-1">Notas del Evaluador:</label>
                <div class="text-xs text-gray-600 bg-yellow-50 p-3 rounded-lg border border-yellow-100 italic h-[100px] overflow-y-auto">
                    <?= isset($id->notes) ? $id->notes : 'Sin observaciones adicionales.' ?>
                </div>
            </div>
        </div>

        <div class="space-y-8">
            <?php
            $answers = json_decode($id->answers, true);
                    foreach ($this->model->list('DISTINCT(kind)', 'suppliers_questions', 'ORDER BY id ASC') as $typeGroup) {
                        ?>
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 shadow-sm">
                    <h3 class="text-md font-black text-black uppercase mb-4 flex items-center gap-2 border-b border-gray-200 pb-2">
                        <i class="ri-checkbox-list-line text-blue-600"></i>
                        <?= $typeGroup->kind ?>
                    </h3>

                    <div class="grid grid-cols-1 gap-3">
                        <?php foreach ($this->model->list('*', 'suppliers_questions', " and kind = '$typeGroup->kind'") as $q) { ?>
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-3 rounded-lg border border-gray-200 hover:shadow-md transition-shadow gap-3">
                                <span class="text-sm font-medium text-gray-700 flex-grow">
                                    <?= $q->question ?>
                                </span>
                                
                                <div class="w-full sm:w-[150px] shrink-0 flex justify-end">
                                    <?php
                                                $val = $answers[$q->id] ?? null;

                            if ($val === '1') { ?>
                                        <span class="px-3 py-1 rounded-full text-sm font-black bg-green-100 text-green-700 border border-green-200 uppercase tracking-tighter">
                                            <i class="ri-checkbox-circle-fill mr-1"></i> SI
                                        </span>
                                    <?php } elseif ($val === '2') { ?>
                                        <span class="px-3 py-1 rounded-full text-sm font-black bg-orange-100 text-orange-700 border border-orange-200 uppercase tracking-tighter">
                                            <i class="ri-Subtract-fill mr-1"></i> PARCIAL
                                        </span>
                                    <?php } elseif ($val === '0') { ?>
                                        <span class="px-3 py-1 rounded-full text-sm font-black bg-red-100 text-red-700 border border-red-200 uppercase tracking-tighter">
                                            <i class="ri-close-circle-fill mr-1"></i> NO
                                        </span>
                                    <?php } else { ?>
                                        <span class="px-3 py-1 rounded-full text-sm font-black bg-gray-100 text-gray-400 border border-gray-200 uppercase tracking-tighter">
                                            N/A
                                        </span>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>