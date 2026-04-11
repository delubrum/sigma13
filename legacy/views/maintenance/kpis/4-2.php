<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-start">
    <div class="overflow-auto bg-white p-4 shadow-lg rounded-xl"> 
        <h3 class="text-base font-semibold mb-3 text-gray-800"><i class="ri-tools-line mr-2"></i> Cantidad de Registros Mensuales</h3>
        <table class="w-full text-[10px] text-left text-gray-700">
            <thead class="text-xxs uppercase bg-gray-50 border-b">
                <tr>
                    <th class="px-2 py-2">Mes</th>
                    <th class="px-1 py-2 text-center">Corr.</th>
                    <th class="px-1 py-2 text-center">Prod.</th>
                    <th class="px-1 py-2 text-center">Infr.</th>
                    <th class="px-1 py-2 text-center">P. MNT</th>
                    <th class="px-1 py-2 text-center">P. Form</th>
                    <th class="px-1 py-2 text-center bg-amber-50 text-amber-600">S/C</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($indbCantidad as $r) { ?>
                <tr class="border-b hover:bg-gray-50 transition">
                    <th class="px-2 py-1.5 font-bold"><?= $r['dateStr'] ?></th>
                    <td class="px-1 py-1.5 text-center"><?= $r['corrective'] ?></td>
                    <td class="px-1 py-1.5 text-center"><?= $r['production'] ?></td>
                    <td class="px-1 py-1.5 text-center"><?= $r['infrastructure'] ?></td>
                    <td class="px-1 py-1.5 text-center"><?= $r['preventive'] ?></td>
                    <td class="px-1 py-1.5 text-center"><?= $r['preventive_form'] ?></td>
                    <td class="px-1 py-1.5 text-center bg-amber-50 text-amber-700 font-bold"><?= $r['unclassified'] ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="bg-white shadow-lg rounded-xl p-4">
        <h3 class="text-base font-semibold mb-3 text-gray-800"><i class="ri-line-chart-fill mr-2"></i> Evolución Cantidades (<?= date('Y', strtotime($date)) ?>)</h3>
        <canvas id="myChart42" height="180"></canvas>
    </div>
</div>

<script>
new Chart(document.getElementById('myChart42').getContext('2d'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_column($indbCantidad, 'dateStr')); ?>,
        datasets: [
            { label: 'Corr.', data: <?php echo json_encode(array_column($indbCantidad, 'corrective')); ?>, borderColor: '#ef4444', tension: 0.3, fill: false },
            { label: 'Prod.', data: <?php echo json_encode(array_column($indbCantidad, 'production')); ?>, borderColor: '#3b82f6', tension: 0.3, fill: false },
            { label: 'Infr.', data: <?php echo json_encode(array_column($indbCantidad, 'infrastructure')); ?>, borderColor: '#f59e0b', tension: 0.3, fill: false },
            { label: 'P. MNT', data: <?php echo json_encode(array_column($indbCantidad, 'preventive')); ?>, borderColor: '#10b981', tension: 0.3, fill: false },
            { label: 'P. Form', data: <?php echo json_encode(array_column($indbCantidad, 'preventive_form')); ?>, borderColor: '#8b5cf6', tension: 0.3, fill: false },
            { label: 'S/C', data: <?php echo json_encode(array_column($indbCantidad, 'unclassified')); ?>, borderColor: '#d97706', borderDash: [4, 4], tension: 0.3, fill: false }
        ]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 9 } } } } }
});
</script>