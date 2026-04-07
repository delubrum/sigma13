<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-start">
    <div class="overflow-auto bg-white p-4 shadow-lg rounded-xl"> 
        <h3 class="text-base font-semibold mb-3 text-gray-800"><i class="ri-history-line mr-2"></i>Estado del Plan Maestro (Acumulado)</h3>
        <table class="w-full text-xs text-left text-gray-700 text-center">
            <thead class="text-xxs uppercase bg-gray-50 border-b">
                <tr>
                    <th class="px-3 py-2 text-left">Cierre de Mes</th>
                    <th class="px-3 py-2">Total Plan</th>
                    <th class="px-3 py-2">Sin Ejecutar</th>
                    <th class="px-3 py-2">% Pendiente</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $r) { ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-3 py-1.5 text-left font-medium"><?= $r['dateStr']?></td>
                    <td class="px-3 py-1.5"><?= $r['totall']?></td>
                    <td class="px-3 py-1.5 detail cursor-pointer" data-id="open" data-date="<?= $r['date']?>"><?= $r['open']?></td>
                    <td class="px-3 py-1.5">
                        <span class="inline-block px-2 py-0.5 text-xxs font-semibold rounded-full bg-orange-100 text-orange-800"><?= $r['result3']?>%</span>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="bg-white shadow-lg rounded-xl p-4">
        <canvas id="myChart3" height="180"></canvas>
    </div>
</div>
<script>
new Chart(document.getElementById('myChart3'),{
    type:'line',
    data:{
        labels:<?= json_encode($dates)?>,
        datasets:[{
            label:'% Pendiente',
            data:<?= json_encode($result3)?>,
            borderColor:'#f97316',
            fill:true,
            backgroundColor:'rgba(249,115,22,0.1)',
            tension:.3
        }]
    }
});
</script>