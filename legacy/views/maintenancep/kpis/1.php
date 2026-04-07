<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-start">
    <div class="overflow-auto bg-white p-4 shadow-lg rounded-xl"> 
        <h3 class="text-base font-semibold mb-3 text-gray-800"><i class="ri-calendar-check-line mr-2"></i>Cumplimiento del Plan</h3>
        <table class="w-full text-xs text-left text-gray-700">
            <thead class="text-xxs uppercase bg-gray-50 border-b text-center">
                <tr>
                    <th class="px-2 py-2 text-left">Mes</th>
                    <th class="px-2 py-2">Prog.</th>
                    <th class="px-2 py-2 text-gray-400">Mora</th>
                    <th class="px-2 py-2 font-bold">Carga</th>
                    <th class="px-2 py-2 text-orange-600 bg-orange-50">Atend. Mora</th>
                    <th class="px-2 py-2 bg-blue-50 text-blue-700">Ejecutados</th>
                    <th class="px-2 py-2">% Cumpl.</th>
                </tr>
            </thead>
            <tbody class="text-center">
                <?php
                $st = 0;
                $sm = 0;
                $stp = 0;
                $stat = 0;
                $sam_mora = 0;
                $r1s = 0;
                $rc = count($result);
                $c = fn ($p) => match (true) {
                    $p >= 90 => 'bg-green-100 text-green-800',$p >= 75 => 'bg-yellow-100 text-yellow-800',default => 'bg-red-100 text-red-800',
                };

                foreach ($result as $r) {
                    $st += $r['total'];
                    $sm += $r['mora'];
                    $stp += $r['carga_total'];
                    $stat += $r['total_at'];
                    $sam_mora += $r['at_mora'];
                    $r1s += $r['result1'];
                    ?>
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-2 py-1.5 text-left font-medium"><?= $r['dateStr']?></td>
                    <td class="px-2 py-1.5 detail cursor-pointer" data-id="total" data-date="<?= $r['date']?>"><?= $r['total']?></td>
                    <td class="px-2 py-1.5 text-gray-400"><?= $r['mora']?></td>
                    <td class="px-2 py-1.5 font-medium"><?= $r['carga_total']?></td>
                    <td class="px-2 py-1.5 text-orange-700 bg-orange-50/50 font-semibold"><?= $r['at_mora']?></td>
                    <td class="px-2 py-1.5 bg-blue-50/30 text-blue-700 font-bold detail cursor-pointer" data-id="total_at" data-date="<?= $r['date']?>"><?= $r['total_at']?></td>
                    <td class="px-2 py-1.5">
                        <span class="inline-block px-2 py-0.5 text-xxs font-semibold rounded-full <?= $c($r['result1'])?>"><?= $r['result1']?>%</span>
                    </td>
                </tr>
                <?php }?>
            </tbody>
            <tfoot class="text-center font-bold border-t-2 border-gray-200">
                <tr class="bg-gray-50/50 text-gray-600">
                    <td class="px-2 py-2 text-left italic">PROMEDIO</td>
                    <td class="px-2 py-2"><?= ($rc > 0) ? round($st / $rc, 1) : 0?></td>
                    <td class="px-2 py-2 text-gray-300"><?= ($rc > 0) ? round($sm / $rc, 1) : 0?></td>
                    <td class="px-2 py-2"><?= ($rc > 0) ? round($stp / $rc, 1) : 0?></td>
                    <td class="px-2 py-2 text-orange-800"><?= ($rc > 0) ? round($sam_mora / $rc, 1) : 0?></td>
                    <td class="px-2 py-2 bg-blue-50/50 text-blue-700"><?= ($rc > 0) ? round($stat / $rc, 1) : 0?></td>
                    <td class="px-2 py-2">
                        <?php $promedio = ($rc > 0) ? round($r1s / $rc) : 0; ?>
                        <span class="inline-block px-2 py-0.5 text-xxs font-bold rounded-full <?= $c($promedio)?>"><?= $promedio?>%</span>
                    </td>
                </tr>
                <tr class="bg-gray-100 text-gray-800">
                    <td class="px-2 py-2 text-left">TOTALES</td>
                    <td class="px-2 py-2"><?= $st?></td>
                    <td class="px-2 py-2 text-gray-400"><?= $sm?></td>
                    <td class="px-2 py-2"><?= $stp?></td>
                    <td class="px-2 py-2 text-orange-800 bg-orange-200/50"><?= $sam_mora?></td>
                    <td class="px-2 py-2 bg-blue-100 text-blue-800"><?= $stat?></td>
                    <td class="px-2 py-2">
                        <?php $pctTotal = ($stp > 0) ? round(($stat / $stp) * 100) : 0; ?>
                        <span class="inline-block px-2 py-0.5 text-xxs font-bold rounded-full <?= $c($pctTotal)?>"><?= $pctTotal?>%</span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="bg-white shadow-lg rounded-xl p-4">
        <canvas id="myChart" height="180"></canvas>
    </div>
</div>

<script>
new Chart(document.getElementById('myChart'),{
    type:'line',
    data:{
        labels:<?= json_encode($dates)?>,
        datasets:[{
            label:'Cumplimiento',
            data:<?= json_encode($result1)?>,
            borderColor:'#10b981',
            fill:true,
            backgroundColor:'rgba(16,185,129,0.1)',
            tension:.3
        }]
    },
    options:{ 
        plugins:{legend:{display:false}}, 
        scales:{y:{min:0,max:100,ticks:{callback:(v)=>v+"%"}}} 
    }
});
</script>