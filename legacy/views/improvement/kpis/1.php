<?php
// Preparamos los datos JSON desde PHP
$js_months = json_encode($months);
$js_result1 = json_encode($result1);
?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-2">

    <div class="bg-white p-5 shadow-lg rounded-xl border border-gray-100">
        <h3 class="text-lg font-bold mb-4 text-gray-800 flex items-center">
            <i class="ri-table-line mr-2 text-blue-600"></i> Closed
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="text-xs uppercase bg-blue-600 text-white">
                    <tr>
                        <th class="px-4 py-3">Month</th>
                        <th class="px-4 py-3 text-center">Total</th>
                        <th class="px-4 py-3 text-center">Closed</th>
                        <th class="px-4 py-3 text-center">%</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($result as $r) { ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-semibold"><?= $r['month'] ?></td>
                        <td onclick="openIndicatorDetail('<?= $r['month'] ?>', 'total')" 
                            class="px-4 py-3 text-center cursor-pointer text-blue-600 font-bold hover:underline">
                            <?= $r['total'] ?>
                        </td>
                        <td onclick="openIndicatorDetail('<?= $r['month'] ?>', 'closed')" 
                            class="px-4 py-3 text-center cursor-pointer text-blue-600 font-bold hover:underline">
                            <?= $r['closed'] ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold <?= $r['result'] >= 80 ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?>">
                                <?= $r['result'] ?>%
                            </span>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white shadow-lg rounded-xl p-5 border border-gray-100">
        <h3 class="text-lg font-bold mb-4 text-gray-800 flex items-center">
            <i class="ri-line-chart-line mr-2"></i> Closed
        </h3>
        <div class="w-full" style="height: 350px;">
            <canvas id="kpiChartCanvas"></canvas>
        </div>
    </div>
</div>

<script>
    // 1. Datos e identificación del año
    const dashboardYear = '<?= $year ?>';
    const chartLabels = <?= $js_months ?>;
    const chartDataValues = <?= $js_result1 ?>;

    // 2. Función para los detalles (Detail)
    function openIndicatorDetail(month, type) {
        const url = `?c=Improvement&a=KpisDetail&year=${dashboardYear}&month=${month}&type=${type}`;
        window.open(url, '_blank');
    }

    // 3. Inicialización de Chart.js al cargar el DOM
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('kpiChartCanvas');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Cumplimiento %',
                    data: chartDataValues,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: true,
                    pointRadius: 5,
                    pointBackgroundColor: '#2563eb'
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
                        max: 100,
                        ticks: {
                            callback: function(value) { return value + '%'; }
                        }
                    }
                }
            }
        });
    });
</script>