<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sigma</title>
    <link rel="icon" sizes="192x192" href="app/assets/img/ico.png" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body class="bg-gray-50"></body>

<div class="w-full rounded-lg relative text-gray-800 text-sm">
    <div class="p-4">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow-md overflow-hidden lg:col-span-1">
                <div class="w-full h-60 bg-gray-100 flex items-center justify-center border-b border-gray-200 overflow-hidden">
                    <?php if (! empty($id->url)) { ?>
                        <img src="<?= $id->url ?>?t=<?= time() ?>" 
                            alt="Asset Photo" 
                            class="w-full h-full object-cover">
                    <?php } else { ?>
                        <img src='<?= $qrcode ?>' 
                            alt='QR Code' 
                            width='160' 
                            height='160'
                            class="block">
                    <?php } ?>
                </div>
                <div class="p-4">
                    <div class="flex justify-center mb-4">
                        <div class="inline-block px-4 py-1.5 rounded-full text-sm font-bold shadow-md border-2">
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
                            <div class="w-24 text-gray-600">Work Mode:</div>
                            <div class="font-medium text-gray-900"><?= $id->work_mode ?? '' ?></div>
                        </div>
                        <div class="flex text-xs mb-1">
                            <div class="w-24 text-gray-600">Location:</div>
                            <div class="font-medium text-gray-900"><?= $id->location ?? '' ?></div>
                        </div>
                        <div class="flex text-xs mb-1">
                            <div class="w-24 text-gray-600">Phone:</div>
                            <div class="font-medium text-gray-900"><?= $id->phone ?? '' ?></div>
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
                            <div class="font-medium text-gray-900" id="sidebarAssignedDept"><?= $id->profile ?? '' ?></div>
                        </div> -->
                        <div class="flex text-xs mb-1">
                            <div class="w-24 text-gray-600">Date:</div>
                            <div class="font-medium text-gray-900" id="sidebarAssignedDate"><?= $id->assigned_at ?? '' ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md overflow-hidden lg:col-span-3">
                <div id="tabContentContainer" class="p-4">
                    <div class="mb-5">
                        <h2 class="text-base font-bold text-gray-900 mb-3 flex items-center space-x-1.5">
                            <i class="ri-mac-line text-xl"></i>
                            <span>Technical Specifications</span>
                        </h2>
                        <table class="w-full border-collapse border border-gray-200 rounded-md overflow-hidden">
                            <thead>
                                <tr>
                                    <th class="bg-gray-100 px-3 py-2 text-left font-semibold text-gray-700 text-xs">Feature</th>
                                    <th class="bg-gray-100 px-3 py-2 text-left font-semibold text-gray-700 text-xs">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="odd:bg-white even:bg-gray-50">
                                    <td class="px-3 py-2 border-b border-gray-200 text-xs">Processor</td>
                                    <td class="px-3 py-2 border-b border-gray-200 text-xs" id="specProcessor"><?= $id->cpu ?></td>
                                </tr>
                                <tr class="odd:bg-white even:bg-gray-50">
                                    <td class="px-3 py-2 border-b border-gray-200 text-xs">RAM Memory</td>
                                    <td class="px-3 py-2 border-b border-gray-200 text-xs" id="specRAM"><?= $id->ram ?></td>
                                </tr>
                                <tr class="odd:bg-white even:bg-gray-50">
                                    <td class="px-3 py-2 border-b border-gray-200 text-xs">Storage</td>
                                    <td class="px-3 py-2 border-b border-gray-200 text-xs" id="specStorage">SSD: <?= $id->ssd ?> / SSD2: <?= $id->hdd ?></td>
                                </tr>
                                <tr class="odd:bg-white even:bg-gray-50">
                                    <td class="px-3 py-2 border-b border-gray-200 text-xs">Operating System</td>
                                    <td class="px-3 py-2 border-b border-gray-200 text-xs" id="specOS"><?= mb_convert_case($id->so, MB_CASE_TITLE, 'UTF-8'); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mb-5">
                        <h2 class="text-base font-bold text-gray-900 mb-3 flex items-center space-x-1.5">
                            <i class="ri-history-line text-xl"></i>
                            <span>Last 3 Corrective Maintenances</span>
                        </h2>
                        
                        <?php
                        // Obtenemos los datos filtrando por correctivos terminados
                        $correctives = $this->model->list('*', 'mnt', "AND asset_id = $id->id AND end is not null ORDER BY end DESC LIMIT 3");
                    ?>

                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse border border-gray-200 rounded-md">
                                <thead>
                                    <tr>
                                        <th class="bg-gray-100 px-3 py-2 text-left font-semibold text-gray-700 text-xs border-b border-gray-200">Attended (Date)</th>
                                        <th class="bg-gray-100 px-3 py-2 text-left font-semibold text-gray-700 text-xs border-b border-gray-200">Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (! empty($correctives)) { ?>
                                        <?php foreach ($correctives as $item) { ?>
                                            <tr class="odd:bg-white even:bg-gray-50 hover:bg-gray-100 transition-colors">
                                                <td class="px-3 py-2 border-b border-gray-200 text-xs font-medium text-gray-800">
                                                    <div class="flex flex-col">
                                                        <span><?= date('d/m/Y', strtotime($item->end)) ?></span>
                                                        <span class="text-[10px] text-gray-500"><?= date('H:i', strtotime($item->end)) ?></span>
                                                    </div>
                                                </td>
                                                <td class="px-3 py-2 border-b border-gray-200 text-xs text-gray-600">
                                                    <div class="line-clamp-2" title="<?= htmlspecialchars($item->description) ?>">
                                                        <?= htmlspecialchars($item->description) ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <tr>
                                            <td colspan="4" class="px-3 py-4 text-center text-gray-500 text-xs italic">
                                                No corrective records found for this asset.
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="mb-5">
                        <h2 class="text-base font-bold text-gray-900 mb-3 flex items-center space-x-1.5">
                            <i class="ri-history-line text-xl"></i>
                            <span>Last 3 Preventive Maintenances</span>
                        </h2>
                        
                        <?php
                    // Obtenemos los datos filtrando por correctivos terminados
                    $correctives = $this->model->list('*', 'mnt_preventive_form', "AND asset_id = $id->id AND last is not null ORDER BY last DESC LIMIT 3");
                    ?>

                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse border border-gray-200 rounded-md">
                                <thead>
                                    <tr>
                                        <th class="bg-gray-100 px-3 py-2 text-left font-semibold text-gray-700 text-xs border-b border-gray-200">Attended (Date)</th>
                                        <th class="bg-gray-100 px-3 py-2 text-left font-semibold text-gray-700 text-xs border-b border-gray-200">Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (! empty($correctives)) { ?>
                                        <?php foreach ($correctives as $item) { ?>
                                            <tr class="odd:bg-white even:bg-gray-50 hover:bg-gray-100 transition-colors">
                                                <td class="px-3 py-2 border-b border-gray-200 text-xs font-medium text-gray-800">
                                                    <div class="flex flex-col">
                                                        <span><?= $item->last ?></span>
                                                    </div>
                                                </td>
                                                <td class="px-3 py-2 border-b border-gray-200 text-xs text-gray-600">
                                                    <div class="line-clamp-2" title="<?= htmlspecialchars($item->activity) ?>">
                                                        <?= htmlspecialchars($item->activity) ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <tr>
                                            <td colspan="4" class="px-3 py-4 text-center text-gray-500 text-xs italic">
                                                No corrective records found for this asset.
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
