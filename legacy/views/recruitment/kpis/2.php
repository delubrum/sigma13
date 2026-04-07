<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
    
    <div class="bg-white p-4 shadow-lg rounded-xl border border-gray-100"> 
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-xxs uppercase text-gray-400 border-b border-gray-50">
                    <th class="px-2 py-2 font-medium">Rank</th>
                    <th class="px-2 py-2 font-medium">Position</th>
                    <th class="px-2 py-2 text-right font-medium">Total</th>
                </tr>
            </thead>
            <tbody class="text-xxs uppercase">
                <?php if (empty($result2)) { ?>
                    <tr>
                        <td colspan="3" class="px-2 py-8 text-center text-gray-400 italic font-normal normal-case">No data available for this year.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($result2 as $i => $item) { ?>
                    <tr class="border-b border-gray-50/50 hover:bg-blue-50/30 transition">
                        <td class="px-2 py-2.5">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-md bg-slate-100 text-slate-500 font-bold">
                                <?= ($i + 1)?>
                            </span>
                        </td>
                        <td class="px-2 py-2.5 font-bold text-gray-700 truncate max-w-[180px]">
                            <?= $item->job_title?>
                        </td>
                        <td class="px-2 py-2.5 text-right">
                            <span class="text-xs font-black text-blue-600">
                                <?= $item->total?>
                            </span>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="bg-white shadow-lg rounded-xl p-4 border border-gray-100">
        <div class="h-64">
            <canvas id="chart_positions"></canvas>
        </div>
    </div>

</div>

<script>
(function() {
    const ctx = document.getElementById('chart_positions').getContext('2d');
    new Chart(ctx, {
        type: 'pie', // Solid Pie Chart
        data: {
            labels: <?= json_encode(array_column($result2, 'job_title'))?>,
            datasets: [{
                data: <?= json_encode(array_column($result2, 'total'))?>,
                backgroundColor: [
                    '#3b82f6', // blue-500
                    '#10b981', // emerald-500
                    '#f59e0b', // amber-500
                    '#8b5cf6', // violet-500
                    '#ec4899', // pink-500
                    '#64748b'  // slate-500
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        boxWidth: 8,
                        padding: 15,
                        usePointStyle: true,
                        font: { size: 9, weight: '600' }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ` ${context.label}: ${context.raw} Vacancies`;
                        }
                    }
                }
            }
        }
    });
})();
</script>