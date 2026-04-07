<?php
// pf_panel.php — partial cargado por PFResult()
?>
<div class="p-4">
    <div class="flex items-center gap-2 mb-4 border-b pb-2">
        <i class="ri-shield-user-line text-purple-500"></i>
        <h3 class="font-semibold text-gray-800">PF <span class="text-xs text-gray-400 font-normal">(Factores de Personalidad)</span></h3>
    </div>

    <?php if ($resultados) { ?>
        <div class="space-y-3">
            <?php foreach ($resultados as $f => $r) {
                $color = ($r['nivel'] == 'Alto') ? 'green' : (($r['nivel'] == 'Bajo') ? 'red' : 'yellow');
                ?>
                <div class="p-3 rounded border bg-<?= $color ?>-50 border-<?= $color ?>-200">
                    <div class="flex justify-between items-baseline mb-1">
                        <span class="font-bold text-sm"><?= htmlspecialchars($r['nombre']) ?> (<?= htmlspecialchars($f) ?>)</span>
                        <span class="text-sm">Nivel: <?= $r['nivel'] ?> | Prom: <?= number_format($r['prom'], 2) ?></span>
                    </div>
                    <div class="mt-2 bg-gray-200 h-2 rounded-full overflow-hidden">
                        <div class="h-full bg-<?= $color ?>-500" style="width:<?= ($r['prom'] / 3 * 100) ?>%"></div>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div class="flex flex-col items-center justify-center py-8 text-center">
            <i class="ri-mail-send-line text-5xl text-blue-600 mb-3"></i>
            <h2 class="text-lg font-semibold text-gray-800">Prueba PF pendiente</h2>
        </div>
    <?php } ?>
</div>