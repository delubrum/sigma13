<?php
// disc_panel.php — partial cargado por DiscResult()
?>
<div class="p-4">
    <div class="flex items-center gap-2 mb-4 border-b pb-2">
        <i class="ri-bar-chart-box-line text-blue-500"></i>
        <h3 class="font-semibold text-gray-800">CISD</h3>
    </div>

    <?php if ($results) { ?>
        <?php
        $colors = ['D' => 'red', 'I' => 'yellow', 'S' => 'green', 'C' => 'blue'];
        foreach ($results['percent'] as $dim => $p) {
            $color = $colors[$dim];
            ?>
            <div class="mb-3">
                <div class="flex justify-between text-sm font-medium text-gray-700 mb-1">
                    <span class="text-<?= $color ?>-600 font-semibold"><?= htmlspecialchars($dim) ?></span>
                    <span><?= $p ?>%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                    <div class="h-3 bg-<?= $color ?>-500 rounded-full transition-all duration-700" style="width:<?= $p ?>%"></div>
                </div>
            </div>
        <?php } ?>
        <p class="mt-4 text-xs text-gray-400">
            <strong class="text-red-600">D</strong> Dominancia &nbsp;·&nbsp;
            <strong class="text-yellow-600">I</strong> Influencia &nbsp;·&nbsp;
            <strong class="text-green-600">S</strong> Estabilidad &nbsp;·&nbsp;
            <strong class="text-blue-600">C</strong> Conciencia
        </p>
    <?php } else { ?>
        <div class="flex flex-col items-center justify-center py-8 text-center">
            <i class="ri-mail-send-line text-5xl text-blue-600 mb-3"></i>
            <h2 class="text-lg font-semibold text-gray-800">Prueba CISD pendiente</h2>
        </div>
    <?php } ?>
</div>