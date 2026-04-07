<?php if (! empty($array)) { ?>
    <?php foreach ($array as $r) { ?>
        <tr class="hover:bg-gray-100 odd:bg-white even:bg-gray-50">
            <td class="px-3 py-2 border-b border-gray-200 text-xs"><?= $r->user ?></td>
            <td class="px-3 py-2 border-b border-gray-200 text-xs"><?= $r->created_at ?></td>
            <td class="px-3 py-2 border-b border-gray-200 text-xs"><?= $r->description ?></td>
            <td class="px-3 py-2 border-b border-gray-200 text-xs"><?= 'evidence' ?></td>
            <!-- <td class="px-3 py-2 border-b border-gray-200 text-xs">
                <div class="relative inline-block">
                    <i class="ri-more-2-fill cursor-pointer p-1 rounded-md transition-colors duration-200 hover:bg-gray-100"></i>
                    <div class="hidden absolute bg-white min-w-[150px] shadow-lg z-10 rounded-md right-0 top-full mt-1 border border-gray-200">
                        <a href="#" class="text-gray-700 px-3 py-2 text-xs flex items-center space-x-1.5 transition-colors duration-200 hover:bg-gray-100 hover:text-gray-800"><i class="ri-eye-line text-xs"></i> <span>Ver Detalles</span></a>
                        <a href="#" class="text-gray-700 px-3 py-2 text-xs flex items-center space-x-1.5 transition-colors duration-200 hover:bg-gray-100 hover:text-gray-800"><i class="ri-edit-line text-xs"></i> <span>Editar</span></a>
                        <a href="#" class="text-gray-700 px-3 py-2 text-xs flex items-center space-x-1.5 transition-colors duration-200 hover:bg-gray-100 hover:text-gray-800"><i class="ri-check-double-line text-xs"></i> <span>Marcar como Completado</span></a>
                    </div>
                </div>
            </td> -->
            <td class="relative px-3 py-2 border-b border-gray-200 text-xs" x-data="{ open: false, menuPosition: { top: 0, left: 0 } }" @click.away="open = false">
  <div class="inline-block relative">
    <i @click="
          open = !open;
          if (open) {
            const iconRect = $el.getBoundingClientRect();
            menuPosition.top = iconRect.bottom + window.scrollY;
            menuPosition.left = iconRect.right - 150 + window.scrollX;
          }
        "
       class="ri-more-2-fill cursor-pointer p-1 rounded-md transition-colors duration-200 hover:bg-gray-100"></i>
  </div>

  <div x-show="open"
       x-transition:enter="transition ease-out duration-100"
       x-transition:enter-start="opacity-0 scale-95"
       x-transition:enter-end="opacity-100 scale-100"
       x-transition:leave="transition ease-in duration-75"
       x-transition:leave-start="opacity-100 scale-100"
       x-transition:leave-end="opacity-0 scale-95"
       class="fixed bg-white min-w-[150px] shadow-lg z-50 rounded-md border border-gray-200"
       :style="`top: ${menuPosition.top}px; left: ${menuPosition.left}px;`">
    <a href="#"
       class="block text-gray-700 px-3 py-2 text-xs flex items-center space-x-1.5 transition-colors duration-200 hover:bg-gray-100 hover:text-gray-800">
      <i class="ri-edit-line text-xs"></i> <span>Edit</span>
    </a>
    <a href="#"
       class="block text-gray-700 px-3 py-2 text-xs flex items-center space-x-1.5 transition-colors duration-200 hover:bg-gray-100 hover:text-gray-800">
      <i class="ri-delete-bin-line text-xs"></i> <span>Delete</span>
    </a>
  </div>
</td>

        </tr>
    <?php } ?>
    <?php } else { ?>
        <div class="py-4 text-center text-gray-500">
    <?php if (! empty($search)) { ?>
            No more items found matching your search.
    <?php } else { ?>
            No more items available.
    <?php } ?>
    </div>
<?php } ?>