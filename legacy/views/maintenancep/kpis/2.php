<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-start">
    <div class="overflow-auto bg-white p-4 shadow-lg rounded-xl"> 
        <h3 class="text-base font-semibold mb-3 text-gray-800"><i class="ri-tools-line mr-2"></i>Apoyo Externo en Preventivos</h3>
        <table class="w-full text-xs text-left text-gray-700 text-center">
            <thead class="text-xxs uppercase bg-gray-50 border-b">
                <tr>
                    <th class="px-3 py-2 text-left">Mes</th>
                    <th class="px-3 py-2">Programados</th>
                    <th class="px-3 py-2">Externos</th>
                    <th class="px-3 py-2">% Externo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $r) { ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-3 py-1.5 text-left font-medium"><?= $r['dateStr']?></td>
                    <td class="px-3 py-1.5"><?= $r['total']?></td>
                    <td class="px-3 py-1.5 detail cursor-pointer" data-id="external" data-date="<?= $r['date']?>"><?= $r['external']?></td>
                    <td class="px-3 py-1.5">
                        <span class="inline-block px-2 py-0.5 text-xxs font-semibold rounded-full bg-blue-100 text-blue-800"><?= $r['result2']?>%</span>
                    </td>
                </tr>
                <?php }?>
            </tbody>
        </table>
    </div>
    <div class="bg-white shadow-lg rounded-xl p-4">
        <canvas id="myChart2" height="180"></canvas>
    </div>
</div>
<script>
new Chart(document.getElementById('myChart2'),{
    type:'line',
    data:{
        labels:<?= json_encode($dates)?>,
        datasets:[{
            label:'% Externos',
            data:<?= json_encode($result2)?>,
            borderColor:'#3b82f6',
            tension:.3
        }]
    }
});
</script>