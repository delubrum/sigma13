<div class="mb-5">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-base font-bold text-gray-900 flex items-center space-x-1.5">
            <i class="ri-robot-2-line text-xl"></i>
            <span>Automations</span>
        </h2>

        <button 
            class="px-3 py-1.5 rounded-md text-sm font-medium cursor-pointer transition-all duration-200 ease-in-out flex items-center space-x-1.5 shadow-sm hover:shadow-md hover:-translate-y-0.5 bg-gray-800 text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600 focus:ring-opacity-50"
            hx-get="?c=Assets&a=DetailModal&modal=automation&id=<?= $id->id ?>"
            @click='nestedModal = true'
            hx-target="#nestedModal"            hx-swap="innerHTML"
            hx-indicator="#loading">
            <i class="ri-add-line text-xs"></i>
            <span>Add Automation</span>
        </button>
    </div>
</div>

    <table class="w-full border-collapse rounded-md overflow-hidden border border-gray-200" id="preventiveMaintenanceTable">
        <thead>
            <tr>
                <th class="bg-gray-100 px-3 py-2 text-left font-medium text-gray-700 text-xs">Activity</th>
                <th class="bg-gray-100 px-3 py-2 text-left font-medium text-gray-700 text-xs">Frequency</th>
                <th class="bg-gray-100 px-3 py-2 text-left font-medium text-gray-700 text-xs">Last Done</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->model->list('*', 'mnt_preventive_form', "and asset_id = $id->id") as $r) { ?>
                <tr>
                    <td class="px-3 py-2 border-b border-gray-200 text-xs"><?= ucfirst(mb_convert_case($r->activity, MB_CASE_LOWER, 'UTF-8')) ?></td>
                    <td class="px-3 py-2 border-b border-gray-200 text-xs"><?= $r->frequency ?></td>
                    <td class="px-3 py-2 border-b border-gray-200 text-xs"><?= $r->last_performed_at ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>