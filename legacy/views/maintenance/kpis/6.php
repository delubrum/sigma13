<?php
// Agrupamos las áreas por mes en un array
$causes_by_month = [];
foreach ($causes as $r) {
    $causes_by_month[$r->dateStr][$r->cause] = number_format($r->total / 60, 1);
}

// Extraemos todas las áreas únicas de todos los meses
$causes_list = [];
foreach ($causes_by_month as $month => $causes_data) {
    $causes_list = array_merge($causes_list, array_keys($causes_data)); // Agregamos todas las áreas
}
$causes_list = array_unique($causes_list); // Eliminamos duplicados

// Si no hay datos, mostramos un mensaje
if (empty($causes_by_month)) {
    echo '<div class="alert alert-warning text-center">No hay datos disponibles para mostrar.</div>';
} else {
    // Mostramos la tabla
    echo '<table class="table table-hover col-12 bg-white" data-id="3">';
    echo '<thead><tr><th class="text-center">Mes</th>';
    foreach ($causes_list as $cause) {
        echo '<th class="text-center">'.htmlspecialchars($cause).'</th>';
    }
    echo '</tr></thead><tbody>';

    // Mostramos los datos por mes
    foreach ($causes_by_month as $month => $causes_data) {
        // Convertimos los datos del mes en un formato adecuado para JavaScript
        $causes_data_json = json_encode($causes_data);

        // Asignamos los datos de cada fila en atributos `data-*`
        echo '<tr data-month="'.htmlspecialchars($month).'" data-causes="'.htmlspecialchars($causes_data_json).'">';
        echo '<th class="text-center">'.htmlspecialchars($month).'</th>';

        // Mostramos las áreas por cada mes
        foreach ($causes_list as $cause) {
            echo '<td class="text-center">'.(isset($causes_data[$cause]) ? htmlspecialchars($causes_data[$cause]) : 0).'</td>';
        }
        echo '</tr>';
    }

    echo '</tbody></table>';
}
?>

<!-- Div para mostrar el gráfico -->
<div id="pieChartContainer" style="width:100%; height:400px;"></div>

<!-- Carga de Highcharts JS -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
    // Función para actualizar la gráfica
    function showPieChart(month, data) {
        Highcharts.chart('pieChartContainer', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Distribución del Tiempo por Causas - ' + month
            },
            plotOptions: {
                pie: {
                    dataLabels: {
                        enabled: true, // Habilita las etiquetas de datos
                        format: '{point.name}: {point.y} ({point.percentage:.1f}%)', // Muestra nombre, valor y porcentaje
                        style: {
                            fontWeight: 'bold',
                            color: 'black'
                        }
                    }
                }
            },
            series: [{
                name: 'Áreas',
                colorByPoint: true,
                data: Object.keys(data).map(function(cause) {
                    return {
                        name: cause,
                        y: parseFloat(data[cause]),
                    };
                })
            }]
        });
    }

    // Agregar el evento para cada fila de la tabla
    document.querySelectorAll('tr[data-month]').forEach(function(row) {
        row.addEventListener('click', function() {
            var month = row.getAttribute('data-month');
            var causes = JSON.parse(row.getAttribute('data-causes'));
            showPieChart(month, causes); // Llamamos a la función para mostrar el gráfico
        });
    });

    // Si ya tenemos datos, podemos inicializar el gráfico con el mes de enero como predeterminado
    <?php if (! empty($causes_by_month['Jan'])) { ?>
        showPieChart('Jan', <?php echo json_encode($causes_by_month['Jan']); ?>);
    <?php } ?>
</script>
