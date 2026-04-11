<div class="w-[95%] sm:w-[95%] max-h-[99vh] overflow-y-auto rounded-lg shadow-lg relative z-50 bg-gray-50 text-gray-800 text-sm leading-relaxed" hx-boost="true">
    <!-- Botón de Cierre -->
    <button id="closeNewModal"
        class="absolute top-0 right-0 m-3 text-gray-900 hover:text-gray-700"
        @click="showModal = !showModal; document.getElementById('myModal').innerHTML = '';"
    >
        <i class="ri-close-line text-2xl"></i>
    </button>

    <div class="p-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-5 pb-2 border-b border-gray-200">
            <h1 class="text-xl font-extrabold text-gray-800 mb-2 sm:mb-0">
                <?= '<b>ID:<b> '.$id->id ?>
            </h1>

            <?php if ($id->status === 'available' or $id->status === 'assigned') { ?>
            <div x-data="{ open: false }" class="relative mr-10">
                <button @click="open = !open"
                    class="text-sm px-4 py-2 bg-black text-white rounded-md shadow hover:opacity-80 focus:outline-none">
                    <i class="ri-menu-line"></i> Options
                </button>

                <div x-show="open" @click.outside="open = false"
                    class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                    <ul class="py-1 text-sm text-gray-700">
                        <li>
                            <a href="#" @click='nestedModal = true' 
                                hx-get="?c=Assets&a=DetailModal&modal=edit&id=<?= $id->id ?>"
                                hx-target="#nestedModal"
                                hx-indicator="#loading"
                                class="block px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                                <i class="ri-check-double-line"></i> Edit
                            </a>
                        </li>
                        <li>
                            <a href="#" @click='nestedModal = true' 
                                hx-get="?c=Assets&a=DetailModal&modal=dispose&id=<?= $id->id ?>"
                                hx-target="#nestedModal"
                                hx-indicator="#loading"
                                class="block px-4 py-2 hover:bg-gray-100 flex items-center gap-2">
                                <i class="ri-check-double-line"></i> Dispose
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <?php } ?>
        </div>

        <!-- <?php if ($id->status === 'available') { ?>
            <div class="tab-alert w-full bg-red-100 text-red-800 px-4 py-2 mb-4 rounded-md border border-red-300 mt-2 text-sm font-semibold">
                <i class="ri-error-warning-line mr-1"></i> This asset is pending assignment
            </div>
        <?php } ?> -->

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow-md overflow-hidden lg:col-span-1">
                <div class="w-full h-60 bg-gray-100 flex items-center justify-center border-b border-gray-200">
                    <img src='<?= $qrcode ?>' alt='QR Code' width='160' height='160' onclick="printImage(this)" style="cursor:pointer;">
                </div>
                <div class="p-4">
                    <div class="flex justify-center mb-4">
                        <div class="inline-block px-4 py-1.5 rounded-full text-sm font-bold shadow-md border-2 <?php if ($id->status != 'available' or $id->status != 'assigned') {
                            echo 'text-red-500';
                        } ?>">
                            <?php echo ucwords($id->status) ?>
                        </div>
                    </div>
                    <div class="mb-4 pb-2.5 border-b border-dashed border-gray-200 last:border-b-0 last:mb-0 last:pb-0">
                        <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1.5 mb-2"><i class="ri-information-line text-xl"></i> <span>Basic Information</span></h3>
                        <div class="flex text-xs mb-1">
                            <div class="w-24 text-gray-600">Serial:</div>
                            <div class="font-medium text-gray-900" id="sidebarAssetSerial"><?= ucwords($id->serial) ?></div>
                        </div>
                        <div class="flex text-xs mb-1">
                            <div class="w-24 text-gray-600">SAP:</div>
                            <div class="font-medium text-gray-900" id="sidebarAssetSerial"><?= ucwords($id->sap) ?></div>
                        </div>
                        <div class="flex text-xs mb-1">
                            <div class="w-24 text-gray-600">Hostname:</div>
                            <div class="font-medium text-gray-900" id="sidebarAssetSerial"><?= ucwords($id->hostname) ?></div>
                        </div>
                        <div class="flex text-xs mb-1">
                            <div class="w-24 text-gray-600">Warranty Until:</div>
                            <div class="font-medium text-gray-900" id="sidebarAssetWarranty"><?= $id->warranty ?></div>
                        </div>
                    </div>

                    <div class="mb-4 pb-2.5 border-b border-dashed border-gray-200 last:border-b-0 last:mb-0 last:pb-0">
                        <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1.5 mb-2"><i class="ri-shield-user-line text-xl"></i> <span>Current Assignment</span></h3>
                        <div class="flex text-xs mb-1">
                            <div class="w-24 text-gray-600">Assigned to:</div>
                            <div class="font-medium text-gray-900" id="sidebarAssignedTo"><?= $id->assignee !== null ? mb_convert_case($id->assignee, MB_CASE_TITLE, 'UTF-8') : '' ?></div>
                        </div>
                        <!-- <div class="flex text-xs mb-1">
                            <div class="w-24 text-gray-600">Department:</div>
                            <div class="font-medium text-gray-900" id="sidebarAssignedDept"><?= $id->division ?? '' ?></div>
                        </div> -->
                        <div class="flex text-xs mb-1">
                            <div class="w-24 text-gray-600">Date:</div>
                            <div class="font-medium text-gray-900" id="sidebarAssignedDate"><?= $id->assigned_at ?? '' ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md overflow-hidden lg:col-span-3">
                <div class="flex border-b border-gray-200 bg-white px-3 flex-wrap">

                    <div class="tab active text-gray-800 border-gray-800 border-b-2 px-3 py-2.5 cursor-pointer font-medium text-gray-500 transition-colors duration-200 hover:text-gray-800 whitespace-nowrap"
                        hx-get="?c=Assets&a=DetailTab&tab=details&id=<?= $id->id ?>"
                        data-tab="details"
                        hx-target="#tabContentContainer">Details</div>

                    <div class="tab px-3 py-2.5 cursor-pointer font-medium text-gray-500 transition-colors duration-200 hover:text-gray-800 whitespace-nowrap"
                        hx-get="?c=Assets&a=DetailTab&tab=assignments&id=<?= $id->id ?>"
                        data-tab="assignments"
                        hx-target="#tabContentContainer">Assignments
                        <!-- <?php if ($id->status === 'available') { ?>
                            <i class="tab-alert ri-error-warning-fill text-red-600 text-base" title="Pending assignment"></i>
                        <?php } ?> -->
                    </div>

                    <div class="tab px-3 py-2.5 cursor-pointer font-medium text-gray-500 transition-colors duration-200 hover:text-gray-800 whitespace-nowrap"
                        hx-get="?c=Assets&a=DetailTab&tab=returns&id=<?= $id->id ?>"
                        data-tab="returns"
                        hx-target="#tabContentContainer">Returns</div>

                    <div class="tab px-3 py-2.5 cursor-pointer font-medium text-gray-500 transition-colors duration-200 hover:text-gray-800 whitespace-nowrap"
                        hx-get="?c=Assets&a=DetailTab&tab=documents&id=<?= $id->id ?>"
                        data-tab="documents"
                        hx-target="#tabContentContainer">Documents</div>

                    <div class="tab px-3 py-2.5 cursor-pointer font-medium text-gray-500 transition-colors duration-200 hover:text-gray-800 whitespace-nowrap"
                        hx-get="?c=Assets&a=DetailTab&tab=automations&id=<?= $id->id ?>"
                        data-tab="automations"
                        hx-target="#tabContentContainer">Automations</div>

                    <div class="tab px-3 py-2.5 cursor-pointer font-medium text-gray-500 transition-colors duration-200 hover:text-gray-800 whitespace-nowrap"
                        hx-get="?c=Assets&a=DetailTab&tab=comments&id=<?= $id->id ?>"
                        data-tab="comments"
                        hx-target="#tabContentContainer">Comments</div>

                    <div class="tab px-3 py-2.5 cursor-pointer font-medium text-gray-500 transition-colors duration-200 hover:text-gray-800 whitespace-nowrap"
                        hx-get="?c=Assets&a=DetailTab&tab=maintenances&id=<?= $id->id ?>"
                        data-tab="maintenances"
                        hx-target="#tabContentContainer">Maintenances</div>
                </div>

                <div id="tabContentContainer" class="p-4"
                    hx-get="?c=Assets&a=DetailTab&tab=details&id=<?= $id->id ?>"
                    hx-trigger="load,refresh"
                    hx-target="this">
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Script para tabs
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
