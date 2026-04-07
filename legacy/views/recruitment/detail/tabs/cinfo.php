<div class="p-4">
    <div class="flex justify-center mb-4">
        <div class="inline-block px-4 py-1.5 rounded-full text-sm font-bold shadow-md border-2 <?php if ($id->status === 'discarted') {
            echo 'text-red-500';
        } ?>">
            <?php echo ucwords($id->status) ?>
        </div>
    </div>
    
    <div class="mb-4 pb-2.5 border-b border-dashed border-gray-200">
        <div class="flex items-center space-x-2 mb-2">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1">
                <i class="ri-information-line text-xl"></i>
                <span>Basic Information</span>
            </h3>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Name:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->name ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Appointment:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->appointment ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">CC:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->cc ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Email:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->email ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Phone:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->phone ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Wage:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->wage ?></div>
        </div>
    </div>

    <div class="mb-4 pb-2.5 border-b border-dashed border-gray-200">
        <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1.5 mb-2">
            <i class="ri-file-text-line text-xl"></i> <span>Concept</span>
        </h3>
        <div class="flex text-xs mb-1">
            <div class="font-medium text-gray-900"><?= nl2br($id->concept ?? '') ?></div>
        </div>
    </div>

    <div class="mb-4 pb-2.5">
        <div class="flex items-center space-x-2 mb-2">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1">
                <i class="ri-tools-line text-xl"></i>
                <span>Assigned Resources</span>
            </h3>
        </div>
        
        <div class="space-y-1.5">
            <?php
            $can_resources = ! empty($id->resources) ? json_decode($id->resources, true) : [];

        if (! empty($can_resources)) {
            foreach ($can_resources as $res) {
                $t_id = $res['ticket_id'] ?? null;
                $table = $res['table'] ?? 'tickets';
                $controller = (strtolower($table) === 'it') ? 'IT' : 'Tickets';
                ?>
                    <div class="text-xs flex items-center">
                        <i class="ri-arrow-right-s-line text-gray-400 mr-1"></i>
                        <span class="font-medium text-gray-900">
                            <?= htmlspecialchars($res['name']) ?>
                        </span>
                        <?php if ($t_id === 'EMAIL_SENT') { ?>
                            <span class="text-blue-500 italic ml-2">(Email Sent to Mkt)</span>
                        <?php } elseif ($t_id) { ?>
                            <a href="?c=<?= $controller ?>&a=Index&id=<?= $t_id ?>" target="_blank" class="ml-2 text-blue-600 hover:text-blue-800 font-bold hover:underline">
                                [Ticket #<?= $t_id ?>]
                            </a>
                        <?php } ?>
                    </div>
                <?php }
            } else { ?>
                <div class="text-xs text-gray-400 italic">No resources assigned yet.</div>
            <?php } ?>
        </div>
    </div>
</div>