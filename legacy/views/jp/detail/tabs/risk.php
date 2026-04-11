<div class="mb-5">
    <h2 class="text-base font-bold text-gray-900 mb-3 flex items-center justify-between">
        <div class="flex items-center space-x-1.5">
            <i class="ri-layout-line text-xl"></i>
            <span>Riesgos</span>
        </div>

        <!-- Botón/link al Excel -->
        <a href="https://sigma.es-metals.com/sigma/uploads/pc/Matriz.xlsx" target="_blank" class="text-green-600 hover:text-green-800 flex items-center space-x-1">
            <i class="ri-file-excel-2-line text-xl"></i>
            <span class="text-sm font-normal">Matriz</span>
        </a>
    </h2>

    <div class="overflow-auto max-h-[65vh]">
        <table class="w-full border-collapse rounded-md overflow-hidden border border-gray-200">
            <tbody>
                <?php $area = $this->model->get('area', 'hr_db', " and id = '$id->division_id'")->area;
                foreach ($this->model->list('*', 'hr_db', " and kind = 'risk' and area = '$area' ORDER BY name ASC") as $r) { ?>     
                    <tr class="hover:bg-gray-100">
                        <td class="px-3 py-2 border-b border-gray-200 text-xs font-semibold"><?= $r->name?></td>
                    </tr>
                <?php }
                foreach ($this->model->list('*', 'hr_db', " and kind = 'risk' and area = 'ALL' ORDER BY name ASC") as $r) { ?>     
                    <tr class="hover:bg-gray-100">
                        <td class="px-3 py-2 border-b border-gray-200 text-xs font-semibold"><?= $r->name?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
