<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-start">
    
    <div class="overflow-auto bg-white p-4 shadow-lg rounded-xl border border-gray-100"> 
        <table class="w-full text-xs text-left text-gray-700">
            <thead class="text-xxs uppercase bg-gray-50 border-b">
                <tr>
                    <th class="px-2 py-2">Month</th>
                    <th class="px-2 py-2 text-center">Opened</th>
                    <th class="px-2 py-2 text-center text-emerald-600">Closed</th>
                    <th class="px-2 py-2 text-center bg-blue-50/50">%</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $st = 0;
                $sam = 0;
                $r1s = 0;
                $rc = count($result1);
                $c = fn ($p) => match (true) {
                    $p >= 100 => 'bg-green-100 text-green-800',$p >= 70 => 'bg-blue-100 text-blue-800',default => 'bg-red-100 text-red-800',
                };

                foreach ($result1 as $r) {
                    $st += $r['opened'];
                    $sam += $r['closed'];
                    $r1s += $r['result1'];
                    ?>
                <tr class="border-b hover:bg-gray-50 transition text-center">
                    <td class="px-2 py-1.5 text-left font-medium text-gray-500"><?= $r['dateStr']?></td>
                    <td class="px-2 py-1.5"><?= $r['opened']?></td>
                    <td class="px-2 py-1.5 text-emerald-600 font-bold"><?= $r['closed']?></td>
                    <td class="px-2 py-1.5 bg-blue-50/20">
                        <span class="inline-block px-2 py-0.5 text-xxs font-semibold rounded-full <?= $c(intval($r['result1']))?>">
                            <?= intval($r['result1'])?>%
                        </span>
                    </td>
                </tr>
                <?php }?>
                
                <tr class="bg-gray-50 font-semibold border-t">
                    <td class="px-2 py-1.5">AVERAGE</td>
                    <td class="text-center px-2 py-1.5"><?= ($rc > 0 ? round($st / $rc) : 0)?></td>
                    <td class="text-center px-2 py-1.5 text-emerald-700"><?= ($rc > 0 ? round($sam / $rc) : 0)?></td>
                    <td class="text-center px-2 py-1.5">
                        <?php $aprom = $rc > 0 ? round($r1s / $rc) : 0; ?>
                        <span class="inline-block px-2 py-0.5 text-xxs font-semibold rounded-full <?= $c($aprom)?>"><?= $aprom?>%</span>
                    </td>
                </tr>

                <tr class="bg-slate-800 text-white font-bold">
                    <td class="px-2 py-1.5 rounded-bl-lg">ANNUAL TOTAL</td>
                    <td class="text-center px-2 py-1.5"><?= $st?></td>
                    <td class="text-center px-2 py-1.5 text-emerald-400"><?= $sam?></td>
                    <td class="text-center px-2 py-1.5 rounded-br-lg">
                        <?php $tpct = $st > 0 ? round(($sam / $st) * 100) : 0; ?>
                        <span class="text-blue-200"><?= $tpct?>%</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="bg-white shadow-lg rounded-xl p-4 border border-gray-100">
        <canvas id="chart_recruitment" height="180"></canvas>
    </div>

</div>

<script>
(function() {
    const ctx = document.getElementById('chart_recruitment').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($dates)?>,
            datasets: [
                {
                    label: 'Opened',
                    data: <?= json_encode($data_opened)?>,
                    borderColor: '#3b82f6',
                    backgroundColor: 'transparent',
                    borderWidth: 3,
                    tension: .4,
                    pointRadius: 4,
                },
                {
                    label: 'Closed',
                    data: <?= json_encode($data_closed)?>,
                    borderColor: '#10b981',
                    backgroundColor: 'transparent',
                    borderWidth: 3,
                    tension: .4,
                    pointRadius: 4,
                }
            ]
        },
        options: {
            plugins: { 
                legend: { 
                    position: 'top', 
                    align: 'end',
                    labels: { boxWidth: 10, usePointStyle: true, font: { size: 10 } }
                } 
            },
            scales: {
                y: { beginAtZero: true, grid: { color: "#f3f4f6" }, ticks: { stepSize: 1 } },
                x: { grid: { display: false } }
            },
            interaction: { mode: 'index', intersect: false }
        }
    });
})();
</script>