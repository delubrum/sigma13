<div class="p-4">
    <div class="mb-4 pb-2.5 border-b border-dashed border-gray-200">
        <div class="flex items-center space-x-2 mb-2">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1">
                <i class="ri-information-line text-xl"></i>
                <span>Basic Information</span>
            </h3>

        <?php if ($canEdit) { ?>
            <i class="ri-edit-line text-lg text-yellow-400 hover:text-yellow-500 cursor-pointer"
            @click="nestedModal = true"
            hx-get="?c=JP&a=New&modal=edit&id=<?= $id->id ?>"
            hx-target="#nestedModal"
            hx-indicator="#loading">
            </i>
        <?php } ?>

        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Nombre:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->name ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Área:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->area ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">División:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->division ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Reporta a:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->reports_to ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Le reportan:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->reports_names ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Modalidad:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->work_mode ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Nivel:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->rank ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Viajar:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->travel ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Cambio residencia:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->relocation ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Fecha Creación:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->created_at ?></div>
        </div>
    </div>

    <div class="mb-4 pb-2.5 border-b border-dashed border-gray-200">
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Idioma:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->lang ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Experiencia:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->experience ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Convalidaciones:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->obs ?></div>
        </div>
    </div>

    <div class="mb-4 pb-2.5">
        <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1.5 mb-2">
            <i class="ri-file-text-line text-xl"></i> <span>Misión</span>
        </h3>
        <div class="flex text-xs mb-1"><div class="font-medium text-gray-900"><?= $id->mission ?></div></div>
    </div>
</div>
