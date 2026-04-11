<!-- LEFT -->
<div class="flex items-center gap-2 sm:gap-3 min-w-0">
    <div class="bg-black p-2.5 rounded-lg shadow-md shrink-0 flex items-center justify-center">
        <i class="ri-customer-service-2-fill text-white text-lg sm:text-xl"></i>
    </div>

    <div class="min-w-0">
        <h1 class="text-sm sm:text-xl font-extrabold text-gray-900 truncate">
            <?= $id->id ?>
            <span class="text-gray-400 mx-1">|</span>
            <span>Ticket</span>
        </h1>
        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest leading-none mt-1">
            Admin Desk
        </p>
    </div>
</div>

<!-- RIGHT -->
<div class="flex items-center gap-2 shrink-0">

    <?php if ($id->status != 'Closed') { ?>
    <div x-data="{ open: false }" class="relative mr-2">
        <button @click="open = !open"
            class="flex items-center justify-center bg-black text-white hover:bg-gray-800
                   px-4 sm:px-6 py-2.5 min-w-[40px] sm:min-w-[140px] rounded-xl 
                   text-xs font-bold shadow-lg active:scale-95 transition-all outline-none">
            <i class="ri-menu-line text-lg sm:mr-2"></i>
            <span class="hidden sm:inline uppercase tracking-widest">Options</span>
        </button>

        <div x-show="open"
            @click.outside="open = false"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-2xl z-[70] overflow-hidden">

            <ul class="py-1 text-sm text-gray-700 font-medium">

                <?php if ($canClose and ! $id->closed_at) { ?>
                <li>
                    <a href="#"
                        hx-get="?c=Tickets&a=Update"
                        hx-confirm="Are you sure you wish to close this Ticket?"
                        hx-swap="none"
                        hx-vals='{"id":<?= $id->id ?>,"field":"closed_at"}'
                        hx-indicator="#loading"
                        class="block px-4 py-3 hover:bg-gray-50 flex items-center gap-2 transition">
                        <i class="ri-check-double-line text-black text-lg"></i> Close
                    </a>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <?php } ?>

    <button
        class="p-1 text-black hover:text-gray-600 transition-colors active:scale-90"
        @click="showModal = !showModal; document.getElementById('myModal').innerHTML = '';">
        <i class="ri-close-circle-fill text-3xl"></i>
    </button>
</div>
