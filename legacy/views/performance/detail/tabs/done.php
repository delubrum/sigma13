<div class="mb-5">
    <h2 class="text-base font-bold text-gray-900 mb-3 flex items-center space-x-1.5">
        <i class="ri-clipboard-line text-xl"></i>
        <span>Assigned</span>
    </h2>

    <div class="space-y-6">
        <table class="w-full border-collapse rounded-md overflow-hidden border border-gray-200">
            <thead>
                <tr>
                    <th class="bg-gray-100 px-3 py-2 text-left font-medium text-gray-700 text-xs">Evaluado</th>
                    <th class="bg-gray-100 px-3 py-2 text-left font-medium text-gray-700 text-xs">Estado</th>
                    <th class="bg-gray-100 px-3 py-2 text-left font-medium text-gray-700 text-xs">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($testsAssignedByTargetUser as $test) { ?>
                    <?php
                        $done = $test['done'];
                    if ($done) {
                        $color = 'text-green-500';
                        $statusIcon = '<i class="ri-check-line text-green-500"></i>';
                        $statusText = 'Done';
                        $action = ($leader) ? '<a target="_blank" href="'.htmlspecialchars($test['detailLink']).'" class="px-3 py-1 text-xs font-medium rounded bg-gray-700 text-white hover:bg-gray-800">Ver</a>' : '';
                    } else {
                        $color = 'text-orange-500';
                        $statusIcon = '<i class="ri-time-line text-orange-500"></i>';
                        $statusText = 'Pending';
                        $action = '';
                    }
                    ?>
                    <tr class="border-b border-gray-200">
                        <td class="px-3 py-2 text-xs font-semibold"><?= htmlspecialchars($test['evaluatedName']); ?></td>
                        <td class="px-3 py-2 text-xs font-bold flex items-center space-x-1 <?= $color ?>">
                            <?= $statusIcon ?>
                            <span><?= $statusText ?></span>
                        </td>
                        <td class="px-3 py-2 text-xs"><?= $action ?></td>
                    </tr>
                <?php } ?>

                <?php if (empty($testsAssignedByTargetUser)) { ?>
                    <tr>
                        <td colspan="3" class="px-3 py-2 text-center text-sm text-gray-600 bg-gray-50">
                            No hay evaluaciones asignadas.
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
