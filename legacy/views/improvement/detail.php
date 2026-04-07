<div class="w-[98%] sm:w-[98%] h-[98vh] flex flex-col rounded-lg shadow-lg relative z-50 bg-gray-50 text-gray-800 text-sm leading-relaxed overflow-hidden" hx-boost="true">

        <div id='head' class="flex items-center justify-between w-full p-4 border-b border-gray-200 bg-white shrink-0"
            hx-get="?c=Improvement&a=Head&id=<?= $id->id ?>"
            hx-trigger="load,refresh" hx-target="this">>
        </div>

        <?php if (! empty($id->reason)) { ?>
            <div class="mx-4 mt-4 p-3 bg-orange-50 border-l-4 border-orange-400 text-orange-800 flex items-center gap-3 rounded-r shadow-sm shrink-0">
                <i class="ri-error-warning-fill ri-lg text-orange-500"></i>
                <div class="font-medium">
                    <span class="font-bold">Reason:</span> <?= htmlspecialchars($id->reason) ?>
                </div>
            </div>
        <?php } ?>

        <div class="p-4 flex-grow overflow-y-auto">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                <div id="info" class="bg-white rounded-lg shadow-md lg:col-span-1"
                    hx-get="?c=Improvement&a=Info&id=<?= $id->id ?>"
                    hx-trigger="load,refresh"
                    hx-target="this">
                </div>

                <div id="tab" class="bg-white rounded-lg shadow-md lg:col-span-3"
                    hx-get="?c=Improvement&a=Tab&id=<?= $id->id ?>"
                    hx-trigger="load,refresh"
                    hx-target="this">
                </div>
            </div>
        </div>
</div>