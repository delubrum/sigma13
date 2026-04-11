<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-start">
    
    <div class="overflow-auto bg-white p-4 shadow-lg rounded-xl"> 
        <h3 class="text-base font-semibold mb-3 text-gray-800"><i class="ri-table-line mr-2"></i>Reporte Mensual de Atención</h3>
        <table class="w-full text-xs text-left text-gray-700">
            <thead class="text-xxs uppercase bg-gray-50 border-b">
                <tr>
                    <th class="px-2 py-2">Mes</th>
                    <th class="px-2 py-2 text-center">Nuevos</th>
                    <th class="px-2 py-2 text-center">Mora</th>
                    <th class="px-2 py-2 text-center">Carga Total</th>
                    <th class="px-2 py-2 text-center bg-blue-50">Atendidos</th>
                    <th class="px-2 py-2 text-center">% Atenc.</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $st = 0;
                $sm = 0;
                $stp = 0;
                $sam = 0;
                $r1s = 0;
                $rc = count($result);
                $c = fn ($p) => match (true) {
                    $p >= 80 => 'bg-green-100 text-green-800',$p >= 50 => 'bg-yellow-100 text-yellow-800',default => 'bg-red-100 text-red-800',
                };

                foreach ($result as $r) {
                    $st += $r['total'];
                    $sm += $r['mora'];
                    $stp += $r['carga_total'];
                    $sam += $r['at_mes'];
                    $r1s += $r['result1'];
                    ?>
                <tr class="border-b hover:bg-gray-50 transition text-center">
                    <td class="px-2 py-1.5 text-left font-medium"><?= $r['dateStr']?></td>
                    <td class="px-2 py-1.5"><?= $r['total']?></td>
                    <td class="px-2 py-1.5 text-gray-400"><?= $r['mora']?></td>
                    <td class="px-2 py-1.5 font-medium text-gray-600"><?= $r['carga_total']?></td>
                    <td class="px-2 py-1.5 text-blue-600 font-bold bg-blue-50/30"><?= $r['at_mes']?></td>
                    <td class="px-2 py-1.5">
                        <span class="inline-block px-2 py-0.5 text-xxs font-semibold rounded-full <?= $c(intval($r['result1']))?>">
                            <?= intval($r['result1'])?>%
                        </span>
                    </td>
                </tr>
                <?php }?>
                
                <tr class="bg-gray-50 font-semibold border-t">
                    <td class="px-2 py-1.5">PROMEDIO</td>
                    <td class="text-center px-2 py-1.5"><?= ($rc > 0 ? round($st / $rc) : 0)?></td>
                    <td class="text-center px-2 py-1.5"><?= ($rc > 0 ? round($sm / $rc) : 0)?></td>
                    <td class="text-center px-2 py-1.5"><?= ($rc > 0 ? round($stp / $rc) : 0)?></td>
                    <td class="text-center px-2 py-1.5 text-blue-700 bg-blue-50/50"><?= ($rc > 0 ? round($sam / $rc) : 0)?></td>
                    <td class="text-center px-2 py-1.5">
                        <?php $aprom = $rc > 0 ? round($r1s / $rc) : 0; ?>
                        <span class="inline-block px-2 py-0.5 text-xxs font-semibold rounded-full <?= $c($aprom)?>"><?= $aprom?>%</span>
                    </td>
                </tr>

                <tr class="bg-gray-100 font-bold border-b">
                    <td class="px-2 py-1.5">TOTAL</td>
                    <td class="text-center px-2 py-1.5"><?= $st?></td>
                    <td class="text-center px-2 py-1.5"><?= $sm?></td>
                    <td class="text-center px-2 py-1.5"><?= $stp?></td>
                    <td class="text-center px-2 py-1.5 text-blue-800 bg-blue-200/50"><?= $sam?></td>
                    <td class="text-center px-2 py-1.5">
                        <?php $tpct = $stp > 0 ? round(($sam / $stp) * 100) : 0; ?>
                        <span class="inline-block px-2 py-0.5 text-xxs font-bold rounded-full <?= $c($tpct)?>"><?= $tpct?>%</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="bg-white shadow-lg rounded-xl p-4">
        <h3 class="text-base font-semibold mb-3 text-gray-800"><i class="ri-line-chart-line mr-2"></i>Porcentaje de Atención Histórico</h3>
        <canvas id="myChart" height="180"></canvas>
    </div>

</div>

<script>
const ctx=document.getElementById('myChart').getContext('2d');
new Chart(ctx,{
    type:'line',
    data:{
        labels:<?= json_encode($dates)?>,
        datasets:[{
            label:'Eficiencia',
            data:<?= json_encode($result1)?>,
            borderColor:'#3b82f6',
            backgroundColor:'rgba(59,130,246,0.1)',
            fill:true,
            tension:.3,
            pointRadius:4
        }]
    },
    options:{
        plugins:{legend:{display:false}},
        scales:{
            y:{ min:0, max:100, ticks:{callback:(v)=>v+"%"}, grid:{color:"#f3f4f6"} },
            x:{ grid:{display:false} }
        }
    }
});
</script>