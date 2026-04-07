<?php if (! empty($array)) { ?>
    <div class="relative ml-4 pl-4 before:content-[''] before:absolute before:top-0 before:bottom-0 before:left-0 before:w-0.5 before:bg-gray-200">
        <?php foreach ($array as $r) { ?>
            <div class="relative pb-4 last:pb-0">
                <div class="absolute -left-6 top-0 w-3 h-3 rounded-full bg-white border-2 border-gray-800 z-10"></div>
                <div class="flex justify-between items-center mb-2">
                    <div class="w-full flex space-x-3 mb-2">
                        <div class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 font-semibold flex-shrink-0 text-xs"><?= strtoupper(substr($r->username, 0, 2)) ?></div>
                        <div class="flex-grow bg-gray-50 p-3 rounded-md border border-gray-200">
                            <div class="flex justify-between items-center mb-1.5">
                                <div class="font-semibold text-sm text-gray-900"><?= $r->username ?></div>
                                <div class="text-xs text-gray-600"><?= $r->created_at ?></div>
                            </div>
                            <div class="text-xs leading-relaxed text-gray-800"><?= $r->description ?></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <div 
            hx-post="?c=Infraimprovement&a=GetEvents&kind=follow-plan&page=<?= $page ?>&id=<?= $r->id ?>"
            hx-trigger="revealed"
            hx-swap="beforeend"
            hx-include="#searchEvent"
            class="text-center text-sm text-gray-400 py-4">
        </div>
    </div>



<?php } else { ?>
    <div class="py-4 text-center text-gray-500">
        <?php if (! empty($search)) { ?>
            No more items found matching your search.
        <?php } else { ?>
            No more items available.
        <?php } ?>
    </div>
<?php } ?>