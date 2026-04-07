<div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
    <div class="bg-slate-50 px-3 py-1.5 border-b border-slate-200 flex justify-between items-center">
        <span class="text-[11px] font-black text-slate-700 uppercase tracking-widest">Cronograma Maestro <?= date('Y') ?></span>
        <div class="flex gap-2">
            <div class="flex items-center gap-1"><div class="w-2 h-2 bg-slate-300 rounded-full"></div><span class="text-[8px] font-bold text-slate-500">PASADO</span></div>
            <div class="flex items-center gap-1"><div class="w-2 h-2 bg-indigo-600 rounded-full"></div><span class="text-[8px] font-bold text-slate-500">PENDIENTE</span></div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-[1900px] w-full table-fixed border-separate border-spacing-0">
            <thead>
                <tr class="bg-slate-800 text-white">
                    <th class="sticky left-0 z-50 bg-slate-900 w-[250px] p-2 border-r border-slate-700 text-left text-[9px] uppercase font-black">Actividad / Freq</th>
                    <?php
                    $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        foreach ($meses as $m) { ?>
                        <th colspan="4" class="text-[9px] border-r border-slate-700 py-1 uppercase font-black text-center"><?= $m ?></th>
                    <?php } ?>
                    <th colspan="4" class="bg-slate-700"></th>
                </tr>
                <tr class="bg-slate-100 text-slate-500">
                    <th class="sticky left-0 z-50 bg-slate-100 border-r border-b border-slate-200 p-1"></th>
                    <?php $semanaActual = (int) date('W'); ?>
                    <?php for ($i = 1; $i <= 52; $i++) { ?>
                        <th class="w-[30px] text-[8px] font-black border-r border-b border-slate-200 py-0.5 <?= $i == $semanaActual ? 'bg-yellow-300 text-black' : '' ?>">
                            <?= $i ?>
                        </th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($automations as $task) { ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="sticky left-0 z-40 bg-white border-r border-b border-slate-200 p-2 whitespace-nowrap overflow-hidden shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                        <div class="text-[10px] font-bold text-slate-800 uppercase truncate"><?= $task->activity ?></div>
                        <div class="text-[7px] font-black text-indigo-600 uppercase italic leading-none"><?= $task->frequency ?></div>
                    </td>

                    <?php for ($s = 1; $s <= 52; $s++) {
                        // Lógica de marcado según controlador
                        $esMantenimiento = ($task->intervalo_semanas > 0) && (($s - $task->semana_referencia) % $task->intervalo_semanas == 0);

                        // Clases dinámicas
                        $colorClase = $task->color; // bg-indigo-500 o bg-red-500 desde el controlador
                        if ($s < $semanaActual) {
                            $colorClase = 'bg-slate-300';
                        }
                        if ($s == $semanaActual && $esMantenimiento) {
                            $colorClase = 'bg-yellow-500 ring-1 ring-yellow-700 animate-pulse';
                        }
                        ?>
                        <td class="w-[30px] h-7 border-r border-b border-slate-100 relative p-0">
                            <?php if ($esMantenimiento) { ?>
                                <div class="absolute inset-0 flex items-center justify-center p-[2px]">
                                    <div class="w-full h-[18px] rounded-sm flex items-center justify-center text-[8px] font-black text-white <?= $colorClase ?>" title="S<?= $s ?>">
                                        <?= ($s < $semanaActual) ? '✓' : '' ?>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php if ($s == $semanaActual) { ?>
                                <div class="absolute inset-y-0 left-0 w-[1px] bg-red-500/40 z-10 pointer-events-none"></div>
                            <?php } ?>
                        </td>
                    <?php } ?>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>