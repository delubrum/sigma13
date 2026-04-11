<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-start">
    
    <div class="overflow-auto bg-white p-4 shadow-lg rounded-xl"> 
        <h3 class="text-base font-semibold mb-3 text-gray-800"><i class="ri-list-settings-line mr-2"></i> Reporte Externos vs. Total</h3>
        <table id="example" class="w-full text-xs text-left text-gray-700">
            <thead class="text-xxs uppercase bg-gray-50 border-b">
                <tr>
                    <th class="px-3 py-2 text-center">Mes</th>
                    <th class="px-3 py-2 text-center">Total</th>
                    <th class="px-3 py-2 text-center">Externos</th>
                    <th class="px-3 py-2 text-center">% Externos</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // CÁLCULOS PHP
                $ts = 0;
                $es = 0;
                $r2s = 0;
                $c = count($result);
                foreach ($result as $r) {
                    $ts += $r['total'];
                    $es += $r['external'];
                    $r2s += $r['result2'];
                }

                $at = $c > 0 ? intval(round($ts / $c)) : 0;
                $ae = $c > 0 ? intval(round($es / $c)) : 0;
                $ar2 = $c > 0 ? intval(round($r2s / $c)) : 0;
                $tr2 = $ts > 0 ? intval(round($es / $ts * 100)) : 0;

                // FUNCIÓN PARA CLASES DE COLOR
                $c = fn ($p) => match (true) {
                    $p >= 80 => 'bg-green-100 text-green-800',$p >= 50 => 'bg-yellow-100 text-yellow-800',default => 'bg-red-100 text-red-800',
                };

                // FILAS POR MES
                foreach ($result as $r) { ?>
                <tr class="border-b hover:bg-gray-50 transition">
                    <th class="px-3 py-1.5 text-center"><?= $r['dateStr']?></th>
                    <td class="text-center px-3 py-1.5 detail" data-id="total" data-date="<?= $r['date']?>"><?= $r['total']?></td>
                    <td class="text-center px-3 py-1.5 detail" data-id="external" data-date="<?= $r['date']?>"><?= $r['external']?></td>
                    <th class="text-center px-3 py-1.5">
                        <span class="inline-block px-2 py-0.5 text-xxs font-semibold rounded-full <?= $c(intval($r['result2']))?>">
                            <?= intval($r['result2'])?>%
                        </span>
                    </th>
                </tr>
                <?php }?>
                
                <tr class="bg-gray-50 font-semibold border-t">
                    <td class="px-3 py-1.5 text-center"><i class="ri-percent-line mr-1"></i>Promedio</td>
                    <td class="text-center px-3 py-1.5"><?= $at?></td>
                    <td class="text-center px-3 py-1.5"><?= $ae?></td>
                    <td class="text-center px-3 py-1.5">
                        <span class="inline-block px-2 py-0.5 text-xxs font-semibold rounded-full <?= $c($ar2)?>"><?= $ar2?>%</span>
                    </td>
                </tr>

                <tr class="bg-gray-100 font-semibold border-b">
                    <td class="px-3 py-1.5 text-center"><i class="ri-earth-line mr-1"></i>Total</td>
                    <td class="text-center px-3 py-1.5"><?= $ts?></td>
                    <td class="text-center px-3 py-1.5"><?= $es?></td>
                    <td class="text-center px-3 py-1.5">
                        <span class="inline-block px-2 py-0.5 text-xxs font-semibold rounded-full <?= $c($tr2)?>"><?= $tr2?>%</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="bg-white shadow-lg rounded-xl p-4">
        <h3 class="text-base font-semibold mb-3 text-gray-800"><i class="ri-global-line mr-2"></i> Porcentaje de Externos Histórico</h3>
        <canvas id="myChart2" height="180"></canvas>
    </div>

</div>

<script>
const ctx2=document.getElementById('myChart2').getContext('2d');
new Chart(ctx2,{
    type:'line',
    data:{
        labels:<?php echo json_encode($dates); ?>,
        datasets:[{
            label:'Porcentaje de Externos',
            data:<?php echo json_encode(array_map('intval', $result2)); ?>,
            borderColor:'#3b82f6',
            backgroundColor:'rgba(59,130,246,0.15)',
            fill:true,
            tension:.3,
            pointRadius:4,
            pointBackgroundColor:'#3b82f6',
            pointBorderColor:'#fff',
            pointHoverRadius:6
        }]
    },
    options:{
        plugins:{legend:{display:false}},
        scales:{
            y:{
                min:0,max:100,
                ticks:{stepSize:20,callback:(v)=>v+"%"},
                grid:{color:"#e5e7eb"}
            },
            x:{grid:{display:false}}
        }
    }
});
</script>