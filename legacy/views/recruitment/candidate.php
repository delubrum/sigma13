<div class="w-[98%] sm:w-[98%] h-[98vh] flex flex-col rounded-lg shadow-lg relative z-50 bg-gray-50 text-gray-800 text-sm leading-relaxed overflow-hidden" hx-boost="true">
    
    <div class="flex items-center justify-between w-full p-4 border-b border-gray-200 bg-white shrink-0">
        <div class="flex items-center gap-2 sm:gap-3 min-w-0">
            <div class="bg-black p-2.5 rounded-lg shadow-md shrink-0 flex items-center justify-center">
                <i class="ri-user-follow-fill text-white text-lg sm:text-xl"></i>
            </div>
            <div class="min-w-0">
                <h1 class="text-sm sm:text-xl font-extrabold text-gray-900 truncate">
                    <?= $id->id ?>
                    <span class="text-gray-400 mx-1">|</span>
                    <span>Candidate</span>
                </h1>
                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest leading-none mt-1">
                    Talent Profile
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <?php if ($id->status != 'hired' && $id->status != 'discarded' and $user->id != 505) { ?>
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
                            <a href="#" @click="nestedModal = true"
                                hx-get="?c=Recruitment&a=DetailModal&modal=reschedule&id=<?= $id->id ?>"
                                hx-target="#nestedModal"
                                hx-indicator="#loading"
                                class="block px-4 py-3 hover:bg-gray-50 flex items-center gap-2 transition border-b border-gray-50">
                                <i class="ri-calendar-event-line text-black text-lg"></i> Reschedule
                            </a>
                        </li>
                        <li>
                            <a href="#"
                                hx-get="?c=Recruitment&a=UpdateField"
                                hx-vals='{
                                    "id": "<?= $id->id ?>",
                                    "field": "status",
                                    "status": "discarded"
                                }'
                                hx-confirm='Are you sure you wish to discard this candidate?'
                                hx-swap="none"
                                hx-indicator="#loading"
                                class="block px-4 py-3 hover:bg-gray-50 flex items-center gap-2 transition">
                                <i class="ri-close-circle-line text-black text-lg"></i> Discard
                            </a>
                        </li> -->
                        <?php if ($id->contract_email) { ?>
                            <li>
                                <a href="#"
                                    hx-get="?c=Recruitment&a=UpdateField"
                                    hx-vals='{
                                        "id": "<?= $id->id ?>",
                                        "field": "status",
                                        "status": "hired"
                                    }'
                                    hx-confirm='Are you sure you wish to hire this candidate?'
                                    hx-swap="none"
                                    hx-indicator="#loading"
                                    class="block px-4 py-3 hover:bg-gray-50 flex items-center gap-2 transition border-t border-gray-100">
                                    <i class="ri-checkbox-circle-line text-black text-lg"></i> Hire
                                </a>
                            </li>
                        <?php } ?>

                        <li>
                            <a href="#"
                            class="block px-4 py-2 hover:bg-gray-100 flex items-center gap-2"
                            hx-indicator="#loading"
                            onclick="
                                    event.preventDefault();
                                    const val = prompt('¿Recruitment Id?');
                                    if (val !== null && val.trim() !== '') {
                                        htmx.ajax('GET', '?c=Recruitment&a=Move&id=<?= $id->id ?>&field=recruitment_id&recruitment_id=' + encodeURIComponent(val));
                                    }
                            ">
                                <i class="ri-check-double-line"></i> Move
                            </a>
                        </li>
                        <li>
                            <a href="#"
                                hx-get="?c=Recruitment&a=ResendInitialEmail&id=<?= $id->id ?>"
                                hx-confirm="Are you sure you want to resend the interview and test instructions?"
                                hx-indicator="#loading"
                                class="block px-4 py-3 hover:bg-gray-50 flex items-center gap-2 transition border-b border-gray-50">
                                <i class="ri-mail-send-line text-black text-lg"></i> Resend Instructions
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <?php } ?>

            <button id="closeNestedModal"
                class="p-1 text-black hover:text-gray-600 transition-colors active:scale-90"
                @click="nestedModal = !nestedModal; document.getElementById('nestedModal').innerHTML = '';">
                <i class="ri-close-circle-fill text-3xl"></i>
            </button>
        </div>
    </div>

    <div class="p-4 flex-grow overflow-y-auto">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            
            <div id="cinfo" class="bg-white rounded-lg shadow-md lg:col-span-1"
                hx-get="?c=Recruitment&a=CandidateInfo&id=<?= $id->id ?>"
                hx-trigger="load,refresh"
                hx-target="this">
            </div>

            <div class="bg-white rounded-lg shadow-md lg:col-span-3">
            
                <div id="ctabs" class="flex border-b border-gray-200 bg-white flex-wrap rounded-lg"
                 hx-get="?c=Recruitment&a=Tabs&id=<?= $id->id ?>"
                    hx-trigger="load,refresh"
                    hx-target="this">
                </div>

                <div id="ctabContentContainer" class="p-4"
                    hx-get="?c=Recruitment&a=DetailCandidate&tab=cv&id=<?= $id->id ?>"
                    hx-trigger="load"
                    hx-target="this">
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function initTabs(root = document) {
        root.querySelectorAll('.ctab').forEach(tab => {
            tab.addEventListener('click', () => {
                root.querySelectorAll('.ctab').forEach(t => {
                    t.classList.remove('cactive', 'text-gray-800', 'border-gray-800', 'border-b-2');
                });
                tab.classList.add('cactive', 'text-gray-800', 'border-gray-800', 'border-b-2');
            });
        });
    }

    document.addEventListener('DOMContentLoaded', () => initTabs());

    document.body.addEventListener('htmx:afterSwap', (event) => {
        const newContent = event.target;
        initTabs(newContent);
    });
</script>