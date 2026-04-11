<div class="w-[95%] max-h-[98vh] sm:w-[50%] bg-white rounded-2xl shadow-lg relative z-50 flex flex-col">
    <div class="p-6 border-b flex justify-between items-center bg-white rounded-t-2xl">
        <h1 class="text-gray-800 flex items-center space-x-2">
            <i class="ri-add-box-line text-2xl"></i>
            <span class="text-2xl font-semibold">New Delivery</span>
        </h1>
        <button type="button" class="text-gray-400 hover:text-gray-700"
            @click="showModal = false; document.getElementById('myModal').innerHTML = '';">
            <i class="ri-close-line text-2xl"></i>
        </button>
    </div>

    <div class="p-6 overflow-y-auto flex-grow">
        <form id="new_delivery_form" hx-post="?c=EquipmentDeliveries&a=Save" hx-swap="none" hx-indicator="#loading" autocomplete="off" class="space-y-6">
            
            <div>
                <label class="block text-sm font-bold text-gray-700 uppercase mb-2">* Empleado:</label>
                <select name="employee_id" required id="employee_select" class="w-full">
                    <option value="">Seleccione un empleado...</option>
                    <?php foreach ($this->model->list('*', 'employees', ' ORDER BY name,id') as $r) { ?>
                        <option value="<?= $r->id ?>"><?= $r->id ?> || <?= mb_convert_case($r->name, MB_CASE_TITLE, 'UTF-8') ?></option>
                    <?php } ?>
                </select>
            </div>

            <hr class="border-gray-100">

            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-800">Equipment</h2>
                    <button type="button" id="btn_add_line" 
                            class="bg-black text-white px-4 py-2 rounded-lg text-sm font-bold transition-all flex items-center gap-2 shadow-sm">
                        <i class="ri-add-line"></i> Add Item
                    </button>
                </div>

                <div id="items_container" class="space-y-4">
                    </div>
            </div>

            <div class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 rounded-xl bg-gray-50">
                <label class="block text-sm font-bold text-gray-600 mb-2 uppercase">Employee Signature</label>
                <div class="bg-white border border-gray-400 rounded-md overflow-hidden">
                    <canvas id="canvas_signature" style="touch-action: none; width: 400px; height: 180px;"></canvas>
                </div>
                <button type="button" id="btn_clear_sig" class="mt-2 text-sm text-red-500 flex items-center gap-1">
                    <i class="ri-refresh-line"></i> Clean Signature
                </button>
            </div>

            <div class="flex justify-end pt-4 border-t">
                <button type="submit" class="bg-black text-white px-8 py-3 rounded-xl font-bold flex items-center gap-2 hover:bg-gray-800 transition shadow-lg">
                    <i class="ri-save-line"></i> Save Delivery
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .ts-wrapper .ts-control { border: 1px solid #d1d5db !important; border-radius: 0.5rem !important; padding: 8px 12px !important; min-height: 42px !important; display: flex !important; align-items: center !important; }
    .ts-dropdown { border-radius: 0.5rem !important; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important; z-index: 100 !important; }
</style>

<script>
(function() {
    let sigPad;
    let tsEmployee;
    const container = document.getElementById('items_container');
    
    // Datos de productos desde PHP
    const productsJson = <?= json_encode($this->model->list('id,name', 'equipment_db', ' ORDER BY name')) ?>;

    function createRow() {
        const id = 'row_' + Date.now();
        const row = document.createElement('div');
        row.className = "flex items-start gap-3 p-4 bg-gray-50 rounded-xl border border-gray-200 item-row";
        
        row.innerHTML = `
            <div class="flex-1 min-w-0">
                <label class="text-[10px] font-bold text-gray-500 uppercase mb-1 block">Producto</label>
                <select class="product-select" required></select>
            </div>
            <div class="w-20 md:w-24">
                <label class="text-[10px] font-bold text-gray-500 uppercase mb-1 block text-center">Cant.</label>
                <input type="number" class="product-qty w-full bg-white border border-gray-300 rounded-lg p-2 text-sm text-center font-bold h-[42px]" value="1" min="1">
            </div>
            <div class="pt-6">
                <button type="button" onclick="this.closest('.item-row').remove()" class="text-gray-300 hover:text-red-500">
                    <i class="ri-delete-bin-6-line text-xl"></i>
                </button>
            </div>
        `;
        
        container.appendChild(row);
        
        // Inicializar TomSelect para la nueva fila
        new TomSelect(row.querySelector('.product-select'), {
            options: productsJson.map(p => ({value: p.id, text: p.name.toUpperCase()})),
            placeholder: "Buscar equipo...",
            maxOptions: null
        });
    }

    const init = () => {
        // 1. Inicializar Empleado
        tsEmployee = new TomSelect("#employee_select", { placeholder: "Seleccione empleado...", maxOptions: null });

        // 2. Crear primera fila
        createRow();

        // 3. Inicializar SignaturePad
        const canvas = document.getElementById("canvas_signature");
        sigPad = new SignaturePad(canvas, { backgroundColor: 'rgb(255, 255, 255)' });

        const resize = () => {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            sigPad.clear();
        };
        window.addEventListener("resize", resize);
        setTimeout(resize, 400);

        // Eventos de botones
        document.getElementById('btn_add_line').onclick = createRow;
        document.getElementById('btn_clear_sig').onclick = () => sigPad.clear();

        // 4. Integración con HTMX
        const form = document.getElementById('new_delivery_form');
        
        form.addEventListener('htmx:configRequest', (e) => {
            // Limpiar parámetros para evitar duplicados si HTMX ya leyó el form
            const params = e.detail.parameters;

            // Inyectar Empleado (asegura el valor de TomSelect)
            params['employee_id'] = tsEmployee.getValue();

            // Inyectar Items Dinámicos
            const rows = container.querySelectorAll('.item-row');
            rows.forEach((row, index) => {
                const prodId = row.querySelector('.product-select').value;
                const qty = row.querySelector('.product-qty').value;
                if(prodId) {
                    params[`items[${index}][id]`] = prodId;
                    params[`items[${index}][qty]`] = qty;
                }
            });

            // Inyectar Firma
            if (!sigPad.isEmpty()) {
                params['signature_base64'] = sigPad.toDataURL();
            }
        });

        form.addEventListener('htmx:afterRequest', (e) => {
            if (e.detail.successful) {
                window.location.reload();
            } else {
                const errorData = JSON.parse(e.detail.xhr.response);
                alert(errorData.message || "Error desconocido");
            }
        });
    };

    init();
})();
</script>