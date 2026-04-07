<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-start">
    
    <div class="bg-white p-4 shadow-lg rounded-xl border border-gray-100"> 
        <div class="flex justify-between items-center mb-4">
            <div class="text-right">
                <span class="block text-xxs uppercase text-gray-400 font-bold">Annual Avg</span>
                <span class="text-lg font-black text-orange-600"><?= $annual_avg_time?> <small class="text-xs font-normal">days</small></span>
            </div>
        </div>
        
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-xxs uppercase text-gray-400 border-b border-gray-50">
                    <th class="px-2 py-2 font-medium">Month</th>
                    <th class="px-2 py-2 text-center font-medium">Min</th>
                    <th class="px-2 py-2 text-center font-medium">Max</th>
                    <th class="px-2 py-2 text-right font-medium">Avg Time</th>
                </tr>
            </thead>
            <tbody class="text-xxs">
                <?php foreach ($result4 as $r) { ?>
                <tr class="border-b border-gray-50/50 hover:bg-orange-50/30 transition">
                    <td class="px-2 py-2 font-medium text-gray-500"><?= $r['dateStr']?></td>
                    <td class="px-2 py-2 text-center text-gray-300"><?= $r['min']?> d</td>
                    <td class="px-2 py-2 text-center text-gray-300"><?= $r['max']?> d</td>
                    <td class="px-2 py-2 text-right font-black text-orange-600 bg-orange-50/20">
                        <?= $r['avg']?> days
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="bg-white shadow-lg rounded-xl p-4 border border-gray-100">
        <div class="h-64">
            <canvas id="chart_speed"></canvas>
        </div>
    </div>

</div>

<script>
(function() {
    const ctx = document.getElementById('chart_speed').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($result4, 'dateStr'))?>,
            datasets: [{
                label: 'Avg Days to Close',
                data: <?= json_encode($data_days)?>,
                backgroundColor: '#f97316', // orange-500
                borderRadius: 4,
                hoverBackgroundColor: '#ea580c'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    grid: { color: "#f3f4f6" },
                    title: { display: true, text: 'Days', font: { size: 10 } }
                },
                x: { grid: { display: false } }
            }
        }
    });
})();
</script>