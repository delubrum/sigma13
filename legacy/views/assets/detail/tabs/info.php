<div class="w-full h-60 bg-gray-100 flex items-center justify-center border-b border-gray-200 relative overflow-hidden">
    
    <div id="asset_photo_preview" class="w-full h-full flex items-center justify-center">
        <?php if (! empty($id->url)) { ?>
            <img src="<?= $id->url ?>?t=<?= time() ?>" class="w-full h-full object-cover">
        <?php } else { ?>
            <div class="flex flex-col items-center gap-2">
                <img src='<?= $qrcode ?>' alt='QR Code' width='160' height='160' class="opacity-70">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest italic">Tocar para subir foto</span>
            </div>
        <?php } ?>
    </div>

    <input type="file" 
           onchange="uploadAssetPhoto(this)" 
           data-asset-id="<?= $id->id ?>"
           class="absolute inset-0 opacity-0 cursor-pointer z-10">

    <div id="asset_upload_loader" class="hidden absolute inset-0 bg-white/80 flex flex-col items-center justify-center z-20">
        <i class="ri-loader-4-line animate-spin text-3xl text-black"></i>
        <span class="text-[10px] font-bold uppercase mt-2">Procesando...</span>
    </div>

    <button onclick="printQR('{{ $data->serial }}','{{ $data->sap }}')"
            class="absolute bottom-3 right-3 z-30 p-2 rounded-xl shadow-lg transition-all hover:scale-110 active:scale-95"
            style="background:var(--ac); color:var(--ac-inv)">
        <i class="ri-qr-code-line text-xl"></i>
    </button>
</div>

<div class="p-4">
    <div class="flex justify-center mb-4">
        <div class="inline-block px-4 py-1.5 rounded-full text-sm font-bold shadow-md border-2 <?php if ($id->status != 'available' && $id->status != 'assigned') {
            echo 'text-red-500 border-red-100 bg-red-50';
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
            <div class="font-medium text-gray-900"><?= ucwords($id->sap) ?></div>
        </div>
        <div class="flex text-xs mb-1">
            <div class="w-24 text-gray-600">Hostname:</div>
            <div class="font-medium text-gray-900"><?= ucwords($id->hostname) ?></div>
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
            <div class="font-medium text-gray-900" id="sidebarAssignedDept"><?= $id->division ?? '' ?></div>
        </div> -->
        <div class="flex text-xs mb-1">
            <div class="w-24 text-gray-600">Date:</div>
            <div class="font-medium text-gray-900" id="sidebarAssignedDate"><?= $id->assigned_at ?? '' ?></div>
        </div>
    </div>
</div>

<iframe id="print_frame" style="display:none;"></iframe>

<script>
async function uploadAssetPhoto(input) {
    const file = input.files[0];
    if (!file) return;

    const assetId = input.getAttribute('data-asset-id');
    const previewDiv = document.getElementById('asset_photo_preview');
    const loader = document.getElementById('asset_upload_loader');

    loader.classList.remove('hidden');

    try {
        const compressedBlob = await new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = (e) => {
                const img = new Image();
                img.src = e.target.result;
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    let w = img.width, h = img.height;
                    const max = 1024;
                    if (w > h && w > max) { h *= max / w; w = max; }
                    else if (h > max) { w *= max / h; h = max; }
                    canvas.width = w; canvas.height = h;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, w, h);
                    canvas.toBlob(b => b ? resolve(b) : reject("Error blob"), 'image/jpeg', 0.6);
                };
            };
        });

        const formData = new FormData();
        formData.append('photo', compressedBlob, "asset_photo.jpg");
        formData.append('id', assetId);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '?c=Assets&a=UploadPhoto', true);
        
        xhr.onload = function() {
            loader.classList.add('hidden');
            if (xhr.status === 200) {
                previewDiv.innerHTML = `<img src="${xhr.responseText.trim()}?t=${new Date().getTime()}" class="w-full h-full object-cover">`;
            } else {
                alert("Error al subir la imagen.");
            }
        };
        xhr.send(formData);
    } catch (e) {
        loader.classList.add('hidden');
        console.error(e);
    }
}

/**
 * Imprime usando un iframe para evitar el bloqueo de la ventana principal
 */
function printQR(serial, sap) {
    const canvas    = document.getElementById('asset-qr-{{ $data->id }}');
    const qrDataUrl = canvas?.toDataURL('image/png') ?? '';
    const iframe    = document.getElementById('print_frame');
    const doc       = iframe.contentWindow.document;
    const assignee  = document.getElementById('sidebarAssignedTo')?.innerText || 'N/A';
    const logoUrl   = 'app/assets/img/logobw.png';
    const anchoTotal = "100mm";

    doc.open();
    doc.write(`
        <html>
            <head>
                <style>
                    @page { size: ${anchoTotal} 25mm; margin: 0; }
                    * { box-sizing: border-box; -webkit-print-color-adjust: exact !important; }
                    html, body {
                        margin: 0 !important; padding: 0 !important;
                        width: ${anchoTotal}; height: 25mm;
                        overflow: hidden; font-family: Arial, sans-serif; background: white;
                    }
                    body { display: flex; flex-direction: row; }
                    .etiqueta-individual { width: 50mm; height: 25mm; padding: 1.5mm 2mm; display: flex; align-items: center; gap: 1mm; }
                    .left-side { flex: 1; display: flex; flex-direction: column; justify-content: center; height: 100%; min-width: 0; }
                    .logo { height: 3.5mm; object-fit: contain; align-self: flex-start; margin-bottom: 0.5mm; }
                    .title-activos { font-size: 5pt; color: #000; margin-bottom: 1mm; letter-spacing: 0.5px; }
                    .info { display: flex; flex-direction: column; gap: 0.5mm; }
                    .info-row { font-size: 7pt; font-weight: bold; line-height: 1; color: #000; white-space: nowrap; }
                    .assignee-row { margin-top: 1.5mm; font-size: 5pt; border-top: 0.2mm solid #000; padding-top: 1mm; font-weight: bold; text-transform: uppercase; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
                    .qr-container { width: 15mm; height: 15mm; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
                    .qr-image { width: 100%; height: 100%; }
                </style>
            </head>
            <body>
                ${Array(2).fill(`
                    <div class="etiqueta-individual">
                        <div class="left-side">
                            <img src="${logoUrl}" class="logo">
                            <div class="title-activos">INVENTARIO ACTIVOS</div>
                            <div class="info">
                                <div class="info-row">SAP: ${sap}</div>
                                <div class="info-row">SER: ${serial}</div>
                                <div class="assignee-row">${assignee}</div>
                            </div>
                        </div>
                        <div class="qr-container">
                            <img src="${qrDataUrl}" class="qr-image">
                        </div>
                    </div>
                `).join('')}
                <script>
                    window.onload = () => setTimeout(() => { window.focus(); window.print(); }, 500);
                <\/script>
            </body>
        </html>
    `);
    doc.close();
}
</script>