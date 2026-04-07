<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-start">
    
    <div class="bg-white p-4 shadow-lg rounded-xl overflow-auto">
        <h3 class="text-base font-semibold mb-3 text-gray-800"><i class="ri-time-line mr-2"></i> Distribución del Tiempo por Áreas</h3>
        
        <?php
        // 1. PREPARACIÓN DE DATOS
        $areas_list = [];
        if (! empty($areas_by_month)) {
            foreach ($areas_by_month as $areas_data) {
                $areas_list = array_merge($areas_list, array_keys($areas_data));
            }
            $areas_list = array_unique($areas_list);
            sort($areas_list);
        }

        // 2. GENERACIÓN DE TABLA
        if (empty($areas_by_month)) { ?>
            <div class="text-yellow-600 text-center py-4 text-sm"><i class="ri-alert-line mr-1"></i> No hay datos disponibles para mostrar.</div>
        <?php } else { ?>
            <table class="w-full text-xs text-left text-gray-700">
                <thead class="text-xxs uppercase bg-gray-50 border-b">
                    <tr>
                        <th class="px-3 py-2 text-center">Mes</th>
                        <?php foreach ($areas_list as $area) { ?>
                            <th class="px-3 py-2 text-center"><?= htmlspecialchars($area) ?></th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($areas_by_month as $month => $areas_data) {
                        $areas_data_json = json_encode($areas_data);
                        ?>
                    <tr class="border-b hover:bg-gray-50 transition cursor-pointer" 
                        data-month="<?= htmlspecialchars($month) ?>" 
                        data-areas='<?= htmlspecialchars($areas_data_json) ?>'>
                        
                        <th class="px-3 py-1.5"><?= htmlspecialchars($month) ?></th>

                        <?php foreach ($areas_list as $area) { ?>
                            <td class="px-3 py-1.5 text-center">
                                <?= isset($areas_data[$area]) ? htmlspecialchars($areas_data[$area]) : 0 ?>
                            </td>
                        <?php } ?>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>
    </div>

    <div class="bg-white shadow-lg rounded-xl p-4">
        <h3 class="text-base font-semibold mb-3 text-gray-800"><i class="ri-pie-chart-2-line mr-2"></i> Gráfico de Distribución (Clic en Fila)</h3>
        <div id="pieChartContainer" style="width:100%; height:300px;"></div>
    </div>

</div>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
    // 1. Función para actualizar la gráfica
    function showPieChart(month, data) {
        Highcharts.chart('pieChartContainer', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Distribución del Tiempo por Áreas - ' + month
            },
            plotOptions: {
                pie: {
                    dataLabels: {
                        enabled: true,
                        // Formato compacto
                        format: '<b>{point.name}</b>: {point.y} ({point.percentage:.1f}%)', 
                        style: {
                            fontWeight: '600',
                            fontSize: '10px' // Fuente más pequeña
                        }
                    },
                    showInLegend: true
                }
            },
            series: [{
                name: 'Tiempo',
                colorByPoint: true,
                data: Object.keys(data).map(area => ({
                    name: area,
                    y: parseFloat(data[area]),
                }))
            }]
        });
    }

    // 2. Lógica de inicialización y eventos de clic
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('tr[data-month]');
        
        rows.forEach(function(row) {
            row.addEventListener('click', function() {
                const month = this.getAttribute('data-month');
                const areas = JSON.parse(this.getAttribute('data-areas'));
                showPieChart(month, areas); 
            });
        });

        // Inicializar con el primer mes disponible
        if (rows.length > 0) {
            const firstRow = rows[0];
            const month = firstRow.getAttribute('data-month');
            const areas = JSON.parse(firstRow.getAttribute('data-areas'));
            showPieChart(month, areas);
        }
    });
</script>