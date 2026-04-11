<div class="flex items-center gap-2 sm:gap-3 min-w-0">
    <div class="bg-black p-2.5 rounded-lg shadow-md shrink-0 flex items-center justify-center">
        <i class="ri-user-search-fill text-white text-lg sm:text-xl"></i>
    </div>
    <div class="min-w-0">
        <h1 class="text-sm sm:text-xl font-extrabold text-gray-900 truncate">
            <?= $id->id ?>
            <span class="text-gray-400 mx-1">|</span>
            <span>Improvement Plan</span>
        </h1>
        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest leading-none mt-1">
            Continuous Improvement
        </p>
    </div>
</div>

<div class="flex items-center gap-2 shrink-0">
    <?php if ($canEdit and $id->status != 'Closed' and $id->status != 'Canceled') { ?>
    <div x-data="{ open: false }" class="relative mr-2">
        <button @click="open = !open"
            class="flex items-center justify-center bg-black text-white hover:bg-gray-800
                    px-4 sm:px-6 py-2.5 min-w-[40px] sm:min-w-[140px] rounded-xl 
                    text-xs font-bold shadow-lg active:scale-95 transition-all outline-none">
            <i class="ri-menu-line text-lg sm:mr-2"></i>
            <span class="hidden sm:inline uppercase tracking-widest">Options</span>
        </button>
        <div x-show="open" @click.outside="open = false"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-2xl z-50 overflow-hidden">
            <ul class="py-1 text-sm text-gray-700 font-medium">
                <?php if ($canClose and $canEdit) { ?>
                <li>
                    <a href="#" @click='nestedModal = true'
                        hx-get="?c=Improvement&a=Close&id=<?= $id->id ?>"
                        hx-target="#nestedModal"
                        hx-indicator="#loading"
                        class="block px-4 py-1 hover:bg-gray-50 flex items-center gap-2 transition">
                        <i class="ri-checkbox-circle-line text-black text-lg"></i> Close
                    </a>
                </li> 
                <?php } ?>
                <?php if ($canEdit) { ?>
                <li>
                    <a href="#"
                        hx-get="?c=Improvement&a=Reject&id=<?= $id->id ?>"
                        hx-prompt="¿Reject Cause?"
                        hx-indicator="#loading"
                        class="block px-4 py-1 hover:bg-gray-50 flex items-center gap-2 transition">
                        <i class="ri-close-circle-line text-black text-lg"></i> Reject
                    </a>
                </li>
                <?php } ?>  
                <?php if ($canEdit) { ?>
                <li>
                    <a href="#"
                        hx-get="?c=Improvement&a=Cancel&id=<?= $id->id ?>"
                        hx-prompt="Cancel Cause?"
                        hx-indicator="#loading"
                        class="block px-4 py-1 hover:bg-gray-50 flex items-center gap-2 transition">
                        <i class="ri-forbid-line text-black text-lg"></i> Cancel
                    </a>
                </li> 
                <?php } ?>   
            </ul>
        </div>
    </div>
    <?php } ?>

    <a href="?c=Improvement&a=PDF&id=<?= $id->id ?>" target="_blank"
        class="flex items-center justify-center bg-red-600 text-white hover:bg-red-700
                px-4 py-2.5 rounded-xl text-xs font-bold shadow-lg active:scale-95 transition-all outline-none">
        <i class="ri-file-pdf-2-fill text-lg sm:mr-2"></i>
        <span class="hidden sm:inline uppercase tracking-widest">PDF</span>
    </a>

    <button id="closeNewModal"
        class="p-1 text-black hover:text-gray-600 transition-colors active:scale-90"
        @click="showModal = !showModal; document.getElementById('myModal').innerHTML = '';">
        <i class="ri-close-circle-fill text-3xl"></i>
    </button>
</div>