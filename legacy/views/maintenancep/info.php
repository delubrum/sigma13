<div class="p-4">
    <div class="flex justify-center mb-4">
        <div class="inline-block px-4 py-1.5 rounded-full text-sm font-bold shadow-md border-2">
            <?php echo ucwords($id->status) ?>
        </div>
    </div>
    <div class="mb-4 pb-2.5 border-b border-dashed border-gray-200 last:border-b-0 last:mb-0 last:pb-0">
        <div class="flex items-center space-x-2 mb-2">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1">
                <i class="ri-information-line text-xl"></i>
                <span>Basic Information</span>
            </h3>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Date:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->created_at ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Asset:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->asset ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Frequency:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->frequency ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Started:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->scheduled_start ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Closed:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->closed_at ?></div>
        </div>
    </div>

    <div class="mb-4 pb-2.5">
        <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1.5 mb-2">
            <i class="ri-file-text-line text-xl"></i> <span>Description</span>
        </h3>
        <div class="flex text-xs mb-1"><div class="font-medium text-gray-900"><?= $id->activity ?></div></div>
    </div>

</div>

<script>
  document.querySelectorAll('.tomselect').forEach(el => {
    new TomSelect(el);
  });
</script>