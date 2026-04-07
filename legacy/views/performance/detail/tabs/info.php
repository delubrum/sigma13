<div class="p-4">
    <div class="mb-4 pb-2.5 border-b border-dashed border-gray-200 last:border-b-0 last:mb-0 last:pb-0">
        <div class="flex items-center space-x-2 mb-2">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1">
                <i class="ri-information-line text-xl"></i>
                <span>Basic Information</span>
            </h3>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Name:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= mb_convert_case($id->name, MB_CASE_TITLE, 'UTF-8') ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Charge:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->charge ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Area:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->area ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Division:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->division ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Leader:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= mb_convert_case($this->model->get('name', 'test_db', "and id = $id->user_id")->name, MB_CASE_TITLE, 'UTF-8') ?></div>
        </div>
    </div>
</div>