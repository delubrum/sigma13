<div class="mb-5">
    <h2 class="text-base font-bold text-gray-900 mb-3 flex items-center space-x-1.5">
        <i class="ri-clipboard-line text-xl"></i>
        <span>Received</span>
    </h2>

    <div class="space-y-6">
        <table class="w-full border-collapse rounded-md overflow-hidden border border-gray-200">
            <thead>
                <tr>
                    <th class="bg-gray-100 px-3 py-2 text-left font-medium text-gray-700 text-xs">Evaluador</th>
                    <th class="bg-gray-100 px-3 py-2 text-left font-medium text-gray-700 text-xs">Estado</th>
                    <th class="bg-gray-100 px-3 py-2 text-left font-medium text-gray-700 text-xs">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($evaluatorsCompleted as $evaluator) { ?>
                    <tr class="border-b border-gray-200">
                        <td class="px-3 py-2 text-xs font-semibold"><?= htmlspecialchars($evaluator['evaluatorName']); ?></td>
                        <td class="px-3 py-2 text-xs font-bold flex items-center space-x-1 text-green-500">
                            <i class="ri-check-line"></i>
                            <span>Done</span>
                        </td>
                        <?php if ($leader) { ?>
                        <td class="px-3 py-2 text-xs">
                            <a target="_blank" href="<?= htmlspecialchars($evaluator['detailLink']); ?>" class="px-3 py-1 text-xs font-medium rounded bg-gray-700 text-white hover:bg-gray-800">
                                Ver
                            </a>
                        </td>
                        <?php } ?>
                    </tr>
                <?php } ?>

                <?php foreach ($evaluatorsPending as $evaluator) { ?>
                    <tr class="border-b border-gray-200">
                        <td class="px-3 py-2 text-xs font-semibold"><?= htmlspecialchars($evaluator['evaluatorName']); ?></td>
                        <td class="px-3 py-2 text-xs font-bold flex items-center space-x-1 text-orange-500">
                            <i class="ri-time-line"></i>
                            <span>Pending</span>
                        </td>
                        <td class="px-3 py-2 text-xs">
                        </td>
                    </tr>
                <?php } ?>

                <?php if (empty($evaluatorsCompleted) && empty($evaluatorsPending)) { ?>
                    <tr>
                        <td colspan="3" class="px-3 py-2 text-center text-sm text-gray-600 bg-gray-50">
                            No hay evaluadores registrados.
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
