<?php
if (! function_exists('e')) {
    function e(mixed $v): string
    {
        return htmlspecialchars((string) ($v ?? ''), ENT_QUOTES, 'UTF-8');
    }
}
?>

<div class="w-[98vw] sm:w-[90vw] max-h-[98vh] bg-white rounded-lg flex flex-col overflow-hidden shadow-2xl">

    <div class="p-4 border-b flex justify-between items-center bg-gray-50 rounded-t-lg">
        <div class="flex items-center gap-3">
            <div class="bg-black p-2 rounded-lg shadow-md shrink-0">
                <i class="ri-truck-line text-white text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-gray-900">Reporte Preoperacional #<?= e($id->idd) ?></h1>
                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">
                    Inspección de Equipo | <?= date('d/m/Y H:i', strtotime((string) $id->created_at)) ?>
                </p>
            </div>
        </div>
        <button id="closeNewModal" @click="showModal = false; document.getElementById('myModal').innerHTML = '';"
                class="ri-close-circle-fill text-3xl text-black hover:text-gray-700 transition-colors"></button>
    </div>

    <div class="p-6 overflow-y-auto bg-gray-50/50">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center shadow-inner">
                    <i class="ri-steering-fill text-xl"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black text-gray-400 uppercase">Conductor</p>
                    <p class="font-bold text-gray-700 text-sm"><?= e($id->user) ?></p>
                </div>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-10 h-10 bg-gray-100 text-gray-600 rounded-xl flex items-center justify-center shadow-inner">
                    <i class="ri-truck-fill text-xl"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black text-gray-400 uppercase">Vehículo / Marca</p>
                    <p class="font-bold text-gray-700 text-sm"><?= e($id->hostname) ?> (<?= e($id->brand) ?>)</p>
                </div>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-10 h-10 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center shadow-inner">
                    <i class="ri-barcode-line text-xl"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black text-gray-400 uppercase">Placa</p>
                    <p class="font-bold text-gray-700 text-sm"><?= e($id->serial) ?></p>
                </div>
            </div>
        </div>

        <div class="space-y-12">
            <?php foreach ($items_by_category as $category => $questions) { ?>
                <div>
                    <div class="flex items-center gap-3 mb-6 px-2">
                        <div class="h-[1px] bg-gray-200 flex-grow"></div>
                        <h4 class="text-[12px] font-black uppercase italic text-gray-500 tracking-widest"><?= e($category) ?></h4>
                        <div class="h-[1px] bg-gray-200 flex-grow"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-6 p-2">
                        <?php foreach ($questions as $item) {
                            $isMal = ($item->answer === 'Mal');
                            $ticket_ids = json_decode($item->ticket_ids ?? '[]', true);
                            ?>
                        <div class="preop-card p-5 border <?= $isMal ? 'border-red-200 bg-red-50/40' : 'border-gray-200 bg-white' ?> rounded-3xl flex flex-col shadow-sm hover:shadow-md transition-shadow">
                            
                            <div class="flex justify-between items-start mb-4">
                                <p class="text-[12px] font-bold text-gray-800 leading-tight flex-1"><?= e($item->question) ?></p>
                                <span class="ml-2 px-2 py-1 rounded-lg text-[8px] font-black uppercase <?= $isMal ? 'bg-red-600 text-white' : 'bg-green-600 text-white shadow-sm' ?>">
                                    <?= e($item->answer) ?>
                                </span>
                            </div>

                            <?php if (! empty($item->url)) { ?>
                                <div class="relative aspect-video bg-gray-100 rounded-2xl mb-4 border border-gray-100 overflow-hidden group">
                                    <img src="<?= e($item->url) ?>" class="w-full h-full object-cover transition-transform group-hover:scale-105">
                                    <a href="<?= e($item->url) ?>" target="_blank" class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all">
                                        <i class="ri-external-link-line text-white text-2xl"></i>
                                    </a>
                                </div>
                            <?php } ?>

                            <?php if (! empty($item->obs)) { ?>
                                <div class="mt-auto">
                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-tighter mb-2">Hallazgos y Gestión</p>
                                    <div class="flex flex-wrap gap-2">
                                        <?php
                                                // Dividimos las observaciones (pueden ser separadas por coma en subtype 2)
                                                $obs_array = explode(', ', (string) $item->obs);
                                foreach ($obs_array as $val) {
                                    $t_id = $ticket_ids[$val] ?? null;
                                    // Si no es por opciones, ticket_ids suele tener una sola entrada
                                    if (! $t_id && count($ticket_ids) === 1) {
                                        $t_id = reset($ticket_ids);
                                    }
                                    ?>
                                            <div class="flex items-center gap-1.5 bg-white/70 border border-gray-200 pl-2 pr-1 py-1 rounded-xl shadow-sm max-w-full">
                                                <span class="text-[10px] text-gray-700 font-bold truncate max-w-[100px]"><?= e($val) ?></span>
                                                
                                                <?php if ($t_id) { ?>
                                                    <a href="?c=Maintenance&a=Index&id=<?= $t_id ?>" target="_blank" 
                                                       class="px-2 py-1 bg-black text-[9px] font-black text-white rounded-lg hover:bg-yellow-500 hover:text-black transition-all flex items-center gap-1 shadow-sm">
                                                        <i class="ri-tools-fill text-yellow-400"></i>
                                                        #<?= e($t_id) ?>
                                                    </a>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>

                        </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>