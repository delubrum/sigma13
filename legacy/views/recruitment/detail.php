<div class="w-[98%] sm:w-[98%] h-[98vh] flex flex-col rounded-lg shadow-lg relative z-50 bg-gray-50 text-gray-800 text-sm leading-relaxed overflow-hidden" hx-boost="true">
    
    <div class="flex items-center justify-between w-full p-4 border-b border-gray-200 bg-white shrink-0">
        <div class="flex items-center gap-2 sm:gap-3 min-w-0">
            <div class="bg-black p-2.5 rounded-lg shadow-md shrink-0 flex items-center justify-center">
                <i class="ri-user-search-fill text-white text-lg sm:text-xl"></i>
            </div>
            <div class="min-w-0">
                <h1 class="text-sm sm:text-xl font-extrabold text-gray-900 truncate">
                    <?= $id->id ?>
                    <span class="text-gray-400 mx-1">|</span>
                    <span>Recruitment</span>
                </h1>
                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest leading-none mt-1">
                    Process Management
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <?php if ($id->status != '' and $user->id != 505) { ?>
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
                        <!-- <li>
                            <a href="#"
                                hx-get="?c=Recruitment&a=Close&id=<?= $id->id ?>"
                                hx-confirm='Are you sure you wish to close this Recruitment?'
                                hx-swap="none"
                                hx-indicator="#loading"
                                class="block px-4 py-3 hover:bg-gray-50 flex items-center gap-2 transition">
                                <i class="ri-check-double-line text-black text-lg"></i> Close
                            </a>
                        </li> -->
                        <li>
                            <a href="#"
                            hx-get="?c=Recruitment&a=Reject&id=<?= $id->id ?>"
                            hx-on:htmx:config-request="
                                const reason = prompt('¿Reject Cause?');
                                if(!reason) return event.preventDefault();
                                event.detail.parameters['reason'] = reason;
                            "
                            hx-swap="none"
                            hx-indicator="#loading"
                            class="block px-4 py-3 hover:bg-gray-0 flex items-center gap-2 transition">
                                <i class="ri-close-circle-line text-black text-lg"></i> Reject
                            </a>
                        </li>                      
                        <li>
                            <a href="#"
                                hx-get="?c=Recruitment&a=ResendApproval&id=<?= $id->id ?>"
                                hx-confirm='Resend Approval Email?'
                                hx-swap="none"
                                hx-indicator="#loading"
                                class="block px-4 py-3 hover:bg-gray-50 flex items-center gap-2 border-t border-gray-100 transition">
                                <i class="ri-mail-send-line text-black text-lg"></i> Resend Approval
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <?php } ?>

            <button id="closeNewModal"
                class="p-1 text-black hover:text-gray-600 transition-colors active:scale-90"
                @click="showModal = !showModal; document.getElementById('myModal').innerHTML = '';">
                <i class="ri-close-circle-fill text-3xl"></i>
            </button>
        </div>
    </div>

    <div class="p-4 flex-grow overflow-y-auto">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            <div id="info" class="bg-white rounded-lg shadow-md lg:col-span-1"
                hx-get="?c=Recruitment&a=Info&id=<?= $id->id ?>"
                hx-trigger="load,refresh"
                hx-target="this">
            </div>

            <div class="bg-white rounded-lg shadow-md lg:col-span-3">
                <div id="tabContentContainer" class="p-4"
                    hx-get="?c=Recruitment&a=DetailTab&tab=appointments&id=<?= $id->id ?>"
                    hx-trigger="load"
                    hx-target="this">
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Script para tabs (mantenido original)
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.tab').forEach(t => {
                t.classList.remove('active', 'text-gray-800', 'border-gray-800', 'border-b-2');
            });
            tab.classList.add('active', 'text-gray-800', 'border-gray-800', 'border-b-2');
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        const firstTab = document.querySelector('.tab');
        if (firstTab) {
            firstTab.classList.add('active', 'text-gray-800', 'border-gray-800', 'border-b-2');
        }
    });
</script>