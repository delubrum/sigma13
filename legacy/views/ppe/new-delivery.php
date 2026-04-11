<div class="w-[95%] max-h-[98vh] sm:w-[50%] bg-white rounded-2xl shadow-lg relative z-50 flex flex-col">
    <div class="p-6 border-b flex justify-between items-center bg-white rounded-t-2xl">
        <h1 class="text-gray-800 flex items-center space-x-2">
            <i class="ri-add-box-line text-2xl"></i>
            <span class="text-2xl font-semibold">New Delivery</span>
        </h1>
        <button id="closeNewModal" class="text-gray-400 hover:text-gray-700 transition-colors"
            @click="showModal = false; document.getElementById('myModal').innerHTML = '';">
            <i class="ri-close-line text-2xl"></i>
        </button>
    </div>

    <div class="p-6 overflow-y-auto flex-grow">
        <form id="new_delivery_form" hx-post="?c=PPEDeliveries&a=Save" hx-swap="none" autocomplete="off" class="space-y-6">
            
            <div>
                <label class="block text-sm text-gray-600 font-medium mb-1">* Employee:</label>
                <select name="employee_id" required class="tomselect_delivery w-full p-2 border border-gray-300 rounded-md">
                    <option value=""></option>
                    <?php foreach ($this->model->list('*', 'employees', ' ORDER BY name,id') as $r) { ?>
                        <option value="<?= $r->id ?>"><?= $r->id ?> || <?= mb_convert_case($r->name, MB_CASE_TITLE, 'UTF-8') ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-gray-700">PPE</th>
                            <th class="px-2 py-2 text-center font-semibold text-gray-700">Equipment</th>
                            <th class="px-2 py-2 text-center font-semibold text-gray-700">Replacement</th>
                            <th class="px-2 py-2 text-center font-semibold text-gray-700">Loss</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <?php foreach ($this->model->list('id,name', 'epp_db', ' ORDER BY name') as $r) {
                            $out = $this->model->get('count(name) as total', 'epp', " AND name = '$r->name'")->total;
                            $in = $this->model->get('sum(qty) as total', 'epp_register', " AND item_id = '$r->id'")->total;
                            $stock = $in - $out;
                            $hasStock = $stock > 0;
                            $rowStyle = $hasStock ? '' : 'bg-gray-50 opacity-60 grayscale';
                            ?>
                        <tr class="<?= $rowStyle ?> transition-colors duration-200">
                            <td class="px-4 py-3">
                                <div class="flex flex-col">
                                    <span class="text-gray-800 font-semibold"><?= mb_convert_case($r->name, MB_CASE_TITLE, 'UTF-8') ?></span>
                                    <span class="text-xs font-medium px-2 py-0.5 rounded-full <?= $hasStock ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?> w-fit">
                                        Stock: <?= $stock ?>
                                    </span>
                                </div>
                            </td>
                            <?php foreach (['Dotación', 'Reposición', 'Perdida'] as $val) { ?>
                            <td class="px-2 py-2 text-center">
                                <input type="radio" name="type[<?= $r->id ?>]" value="<?= $val ?>" <?= ! $hasStock ? 'disabled' : '' ?> 
                                      class="w-4 h-4 <?= $hasStock ? 'text-blue-600 cursor-pointer' : 'text-gray-300 cursor-not-allowed' ?>">
                            </td>
                            <?php } ?>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div>
                <label class="block text-sm text-gray-600 font-medium mb-1">Notes:</label>
                <textarea name="notes" rows="2" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
            </div>

            <div id="sig-container" class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 rounded-xl bg-gray-50">
                <label class="block text-sm text-gray-600 font-medium mb-2 uppercase">Employee Signature</label>
                <div class="bg-white border border-gray-400 rounded-md overflow-hidden">
                    <canvas id="canvas_signature" style="touch-action: none; width: 400px; height: 200px; background-color: #ffffff;"></canvas>
                </div>
                <button type="button" id="btn_clear" class="mt-2 text-sm text-red-500 hover:text-red-700 flex items-center gap-1">
                    <i class="ri-delete-bin-line"></i> Clear Signature
                </button>
            </div>

            <div class="flex justify-end pt-4 border-t">
                <button type="submit" class="bg-gray-900 text-white px-6 py-2 rounded-lg font-semibold flex items-center gap-2 hover:bg-gray-800 transition">
                    <i class="ri-save-line"></i> Save
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    document.querySelectorAll('.tomselect_delivery').forEach(el => {
        if (!el.tomselect) new TomSelect(el, { placeholder: "Select employee...", maxOptions: null });
    });

    const canvas = document.getElementById("canvas_signature");
    const signaturePad = new SignaturePad(canvas, { 
        backgroundColor: 'rgb(255, 255, 255)' // Fondo blanco para JPEG
    });

    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        signaturePad.clear();
    }
    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();

    document.getElementById("btn_clear").addEventListener("click", () => signaturePad.clear());

    const deliveryForm = document.getElementById('new_delivery_form');
    deliveryForm.addEventListener('htmx:confirm', function(evt) {
        if (signaturePad.isEmpty()) {
            evt.preventDefault();
            alert('Please provide a signature.');
        }
    });

    deliveryForm.addEventListener('htmx:configRequest', function(evt) {
        // CAMBIO A JPEG 0.3: Mucho más ligero
        evt.detail.parameters['signature_base64'] = signaturePad.toDataURL('image/jpeg', 0.3);
    });

    deliveryForm.addEventListener('htmx:afterRequest', function(evt) {
        if (evt.detail.successful) window.location.reload();
        else alert('Error saving data.');
    });
})();
</script>