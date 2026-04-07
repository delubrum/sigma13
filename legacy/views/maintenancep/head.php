<div class="flex-1 min-w-0">
    <h1 class="text-lg sm:text-xl font-extrabold text-gray-800 truncate">
        <?= $id->id ?> - Preventive
    </h1>
</div>

<div class="flex items-center gap-3">
    <?php if ($id->status != 'Closed') { ?>
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                class="text-sm px-3 py-2 bg-black text-white rounded-md shadow hover:opacity-80 focus:outline-none flex items-center gap-2 whitespace-nowrap">
                <i class="ri-menu-line"></i> 
                <span>Options</span>
            </button>

            <div x-show="open" 
                @click.outside="open = false"
                x-transition
                class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-md shadow-xl z-[70]">
                <ul class="py-1 text-sm text-gray-700">
                    <?php if ($canClose and $id->attended and ! $id->closed_at) { ?>
                        <li>
                            <a href="#"
                                hx-get="?c=Maintenancep&a=Update"
                                hx-confirm='Are you sure you wish to close this Ticket?'
                                hx-swap="none"
                                hx-vals='{"id":<?= $id->id ?>,"field": "closed_at"}'
                                hx-indicator="#loading"
                                class="block px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                                <i class="ri-check-double-line"></i> Close
                            </a>
                        </li>
                        <li>
                            <a href="#"
                                hx-indicator="#loading"
                                onclick="event.preventDefault();
                                    const reason = prompt('¿Reject Cause?');
                                    if (reason !== null && reason.trim() !== '') {
                                    htmx.ajax('GET', '?c=Maintenancep&a=Update&id=<?= $id->id ?>&field=reason&reason=' + encodeURIComponent(reason));
                                    }
                                "
                                class="block px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                                <i class="ri-close-circle-line"></i> Reject
                            </a>
                        </li>
                    <?php } ?>

                    <?php if ($canEdit and ! $id->attended) { ?>
                        <li>
                            <a href="#"
                                hx-get="?c=Maintenancep&a=Update"
                                hx-confirm='Are you sure you wish to set this ticket as Attended?'
                                hx-swap="none"
                                hx-vals='{"id":<?= $id->id ?>,"field": "attended"}'
                                hx-indicator="#loading"
                                class="block px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                                <i class="ri-time-line"></i> Attend
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    <?php } ?>

    <button 
        class="p-1 text-gray-500 hover:text-red-600 transition-colors"
        @click="showModal = !showModal; document.getElementById('myModal').innerHTML = '';"
    >
        <i class="ri-close-line text-2xl"></i>
    </button>
</div>