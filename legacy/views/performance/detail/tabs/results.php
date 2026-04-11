<?php
// Inicializar totales
$total_weighted_avg = 0;
$sum_self = 0;
$sum_leader = 0;
$sum_peer = 0;
$sum_weighted = 0;

// Contar resultados
$count = count($resultados_competencia);

// Calcular totales y promedio ponderado
if ($count > 0) {
    foreach ($resultados_competencia as $promedios) {
        $self = isset($promedios['self_avg']) ? $promedios['self_avg'] : 0;
        $leader = isset($promedios['leader_avg']) ? $promedios['leader_avg'] : 0;
        $peer = isset($promedios['peer_avg']) ? $promedios['peer_avg'] : 0;

        $weighted = ($self * 0.05) + ($leader * 0.65) + ($peer * 0.30);

        $sum_self += $self;
        $sum_leader += $leader;
        $sum_peer += $peer;
        $sum_weighted += $weighted;
    }

    $total_weighted_avg = $sum_weighted / $count;
} else {
    $total_weighted_avg = 0;
}

// Mostrar card si el promedio < 3.5
$show_alert_card = $total_weighted_avg < 3.5;
?>

<div class="mb-5">
    <h2 class="text-base font-bold text-gray-900 mb-3 flex items-center space-x-1.5">
        <i class="ri-clipboard-line text-xl"></i>
        <span>Results</span>
    </h2>

    <?php if ($show_alert_card) { ?>
        <div class="mb-4 p-3 bg-red-100 border-l-4 border-red-500 text-red-800 rounded shadow flex items-center">
            <i class="ri-alert-line text-xl mr-2"></i>
            <span>El promedio total ponderado es menor a 3.5. Generar Plan de Mejora.</span>
        </div>
    <?php } ?>

    <div class="space-y-6">
        <table class="w-full border-collapse rounded-md overflow-hidden border border-gray-200">
            <thead>
                <tr>
                    <th class="bg-gray-100 px-3 py-2 text-left font-medium text-gray-700 text-xs">Competency</th>
                    <th class="bg-gray-100 px-3 py-2 text-left font-medium text-gray-700 text-xs">Self-Assessment</th>
                    <th class="bg-gray-100 px-3 py-2 text-left font-medium text-gray-700 text-xs">Leader</th>
                    <th class="bg-gray-100 px-3 py-2 text-left font-medium text-gray-700 text-xs">Peers</th>
                    <th class="bg-gray-100 px-3 py-2 text-left font-medium text-gray-700 text-xs">Average</th>
                    <th class="bg-gray-100 px-3 py-2 text-left font-medium text-gray-700 text-xs">Level</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($resultados_competencia as $competencia => $promedios) { ?>
                    <?php
                        $self = isset($promedios['self_avg']) ? $promedios['self_avg'] : 0;
                    $leader = isset($promedios['leader_avg']) ? $promedios['leader_avg'] : 0;
                    $peer = isset($promedios['peer_avg']) ? $promedios['peer_avg'] : 0;

                    // Promedio ponderado
                    $weighted = ($self * 0.05) + ($leader * 0.65) + ($peer * 0.30);

                    // Color y etiqueta
                    $score = max(1.0, min(5.0, $weighted));
                    if ($score >= 4.5) {
                        $label = 'Excellent';
                        $color = 'green';
                    } elseif ($score >= 4) {
                        $label = 'Good';
                        $color = 'blue';
                    } elseif ($score >= 3.5) {
                        $label = 'Needs Improvement';
                        $color = '#bc9d2eff';
                    } else {
                        $label = 'Action Plan';
                        $color = 'red';
                    }
                    ?>
                    <tr style="color: <?= $color ?>">
                        <td class="px-3 py-2 border-b border-gray-200 text-xs font-semibold"><?= htmlspecialchars($competencia) ?></td>
                        <td class="px-3 py-2 border-b border-gray-200 text-xs font-semibold"><?= number_format($self, 2) ?></td>
                        <td class="px-3 py-2 border-b border-gray-200 text-xs font-semibold"><?= number_format($leader, 2) ?></td>
                        <td class="px-3 py-2 border-b border-gray-200 text-xs font-semibold"><?= number_format($peer, 2) ?></td>
                        <td class="px-3 py-2 border-b border-gray-200 text-xs font-bold"><?= number_format($weighted, 2) ?></td>
                        <td class="px-3 py-2 border-b border-gray-200 text-xs font-bold"><?= $label ?></td>
                    </tr>
                <?php } ?>

                <!-- Fila final de promedios generales -->
                <?php
                    if ($count > 0) {
                        $avg_self = $sum_self / $count;
                        $avg_leader = $sum_leader / $count;
                        $avg_peer = $sum_peer / $count;
                        $avg_weighted = $sum_weighted / $count;
                    } else {
                        $avg_self = $avg_leader = $avg_peer = $avg_weighted = 0;
                    }

$score = max(1.0, min(5.0, $avg_weighted));
if ($score >= 4.5) {
    $label = 'Excellent';
    $color = 'green';
} elseif ($score >= 4) {
    $label = 'Good';
    $color = 'blue';
} elseif ($score >= 3.5) {
    $label = 'Needs Improvement';
    $color = '#bc9d2eff';
} else {
    $label = 'Action Plan';
    $color = 'red';
}
?>
                <tr class="bg-gray-50" style="color: <?= $color ?>">
                    <td class="px-3 py-2 text-xs font-bold">Promedio General</td>
                    <td class="px-3 py-2 text-xs font-bold"><?= number_format($avg_self, 2) ?></td>
                    <td class="px-3 py-2 text-xs font-bold"><?= number_format($avg_leader, 2) ?></td>
                    <td class="px-3 py-2 text-xs font-bold"><?= number_format($avg_peer, 2) ?></td>
                    <td class="px-3 py-2 text-xs font-bold"><?= number_format($avg_weighted, 2) ?></td>
                    <td class="px-3 py-2 text-xs font-bold"><?= $label ?></td>
                </tr>

            </tbody>
        </table>
    </div>
</div>
