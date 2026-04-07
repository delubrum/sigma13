<div class="mb-5">
    <h2 class="text-base font-bold text-gray-900 mb-3 flex items-center space-x-1.5">
        <i class="ri-information-line text-xl"></i>
        <span>Details</span>
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-3">
        <div class="bg-gray-50 rounded-md p-3 border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-600 mb-1">Acquisition Date</div>
            <div class="text-sm font-semibold text-gray-900"><?= $id->date ?></div>
        </div>
        <div class="bg-gray-50 rounded-md p-3 border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-600 mb-1">Acquisition Cost</div>
            <div class="text-sm font-semibold text-gray-900" id="generalAcquisitionCost">$ <?= number_format($id->price) ?></div>
        </div>
        <div class="bg-gray-50 rounded-md p-3 border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-600 mb-1">Supplier</div>
            <div class="text-sm font-semibold text-gray-900" id="generalSupplier"><?= mb_convert_case($id->supplier, MB_CASE_UPPER, 'UTF-8'); ?></div>
        </div>
        <div class="bg-gray-50 rounded-md p-3 border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-600 mb-1">Invoice No.</div>
            <div class="text-sm font-semibold text-gray-900" id="generalInvoiceNo"><?= $id->invoice ?></div>
        </div>
    </div>
</div>
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
                <td class="px-3 py-2 border-b border-gray-200 text-xs" id="specStorage">SSD1: <?= $id->ssd ?> / SSD2: <?= $id->hdd ?></td>
            </tr>
            <tr class="odd:bg-white even:bg-gray-50">
                <td class="px-3 py-2 border-b border-gray-200 text-xs">Operating System</td>
                <td class="px-3 py-2 border-b border-gray-200 text-xs" id="specOS"><?= mb_convert_case($id->so, MB_CASE_TITLE, 'UTF-8'); ?></td>
            </tr>
        </tbody>
    </table>
</div>