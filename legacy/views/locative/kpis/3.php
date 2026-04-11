<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-start">
    
    <div class="overflow-auto bg-white p-4 shadow-lg rounded-xl"> 
        <h3 class="text-base font-semibold mb-3 text-gray-800"><i class="ri-history-line mr-2"></i> Estado Acumulado (Total vs. Abiertos)</h3>
        <table id="example" class="w-full text-xs text-left text-gray-700">
            <thead class="text-xxs uppercase bg-gray-50 border-b">
                <tr>
                    <th class="px-3 py-2 text-center">Mes</th>
                    <th class="px-3 py-2 text-center">Total (Acumulado)</th>
                    <th class="px-3 py-2 text-center">Abiertos (Acumulado)</th>
                    <th class="px-3 py-2 text-center">% Abiertos</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // FUNCIÓN PARA CLASES DE COLOR
                $c = fn ($p) => match (true) {
                    $p >= 80 => 'bg-green-100 text-green-800', // Verde: Buen cierre (pocos abiertos)
                    $p >= 40 => 'bg-yellow-100 text-yellow-800', // Amarillo: Moderado
                    default => 'bg-red-100 text-red-800', // Rojo: Muchos abiertos
                };

                foreach ($result as $r) { ?>
                <tr class="border-b hover:bg-gray-50 transition">
                    <th class="px-3 py-1.5 text-center"><?= $r['dateStr'] ?></th>
                    <td class="text-center px-3 py-1.5 detail" data-id="totall" data-date="<?= $r['date'] ?>"><?= $r['totall'] ?></td>
                    <td class="text-center px-3 py-1.5 detail" data-id="open" data-date="<?= $r['date'] ?>"><?= $r['open'] ?></td>
                    <th class="text-center px-3 py-1.5">
                        <span class="inline-block px-2 py-0.5 text-xxs font-semibold rounded-full <?= $c($r['result3'])?>">
                            <?= $r['result3'] ?>%
                        </span>
                    </th>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="bg-white shadow-lg rounded-xl p-4">
        <h3 class="text-base font-semibold mb-3 text-gray-800"><i class="ri-task-line mr-2"></i> % de Abiertos Acumulados</h3>
        <canvas id="myChart3" height="180"></canvas>
    </div>

</div>

<script>
const ctx3 = document.getElementById('myChart3').getContext('2d');
new Chart(ctx3, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($dates); ?>,
        datasets: [{
            label: 'Porcentaje de Abiertos',
            data: <?php echo json_encode(array_map('intval', $result3)); ?>,
            borderColor: '#ff9900', // Naranja/Ámbar para estado "Abierto"
            backgroundColor: 'rgba(255, 153, 0, 0.15)',
            fill: true,
            tension: 0.3,
            pointRadius: 4,
            pointBackgroundColor: '#ff9900',
            pointBorderColor: '#fff',
            pointHoverRadius: 6
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: {
            y: {
                min: 0, max: 100,
                ticks: { stepSize: 20, callback: (v) => v + "%" },
                grid: { color: "#e5e7eb" }
            },
            x: { grid: { display: false } }
        }
    }
});
</script>