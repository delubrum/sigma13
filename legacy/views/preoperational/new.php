<div class="w-[98vw] sm:w-[90vw] max-h-[98vh] bg-white rounded-lg flex flex-col overflow-hidden">

    <div class="p-4 border-b flex justify-between items-center bg-gray-50 rounded-t-lg">
        <div class="flex items-center gap-3">
            <div class="bg-black p-2 rounded-lg shadow-md shrink-0">
                <i class="ri-truck-line text-white text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-gray-900">Nuevo Preoperacional</h1>
                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">Inspección de Equipo</p>
            </div>
        </div>
        <button id="closeNewModal" @click="showModal = false"
                class="ri-close-circle-fill text-3xl text-black hover:text-gray-700"></button>
    </div>

    <form id="preopForm" onsubmit="return false;" class="flex flex-col flex-grow overflow-hidden">

        <div class="p-6 overflow-y-auto flex-grow" id="form_body">
            <div class="grid grid-cols-1 gap-2 mb-4">
                <label class="text-xs font-bold text-gray-500">* VEHÍCULO:</label>
                <select name="vehicle_id"
                        id="main_vehicle_id"
                        required
                        class="w-full p-2 border border-gray-300 rounded-lg text-sm"
                        hx-get="?c=Preoperational&a=Checklist"
                        hx-target="#checklist_content"
                        hx-indicator="#checklist_loading">
                    <option value="">Seleccione...</option>
                    <?php foreach ($vehiculos as $v) { ?>
                        <option value="<?= htmlspecialchars((string) $v->id) ?>">
                            <?= htmlspecialchars($v->hostname) ?> || <?= htmlspecialchars($v->serial) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div id="checklist_loading" class="htmx-indicator text-center py-4">
                <i class="ri-loader-4-line animate-spin text-2xl text-gray-400"></i>
            </div>

            <div id="checklist_content">
                <p class="text-center text-gray-300 py-20 font-black uppercase text-xs italic">
                    Seleccione un vehículo para comenzar...
                </p>
            </div>
        </div>

        <div id="footer_actions" class="hidden p-4 border-t bg-gray-50 flex justify-end">
            <button type="button" 
                    onclick="handleFinalize()"
                    class="bg-black text-white px-10 py-3 rounded-xl text-sm font-bold shadow-xl active:scale-95 transition-all">
                FINALIZAR
            </button>
        </div>
    </form>

    <div id="silent_sync" class="hidden"></div>
</div>

<style>
    .hidden-input { position: absolute; opacity: 0; width: 1px; height: 1px; pointer-events: none; }
    .preop-card { transition: all 0.2s ease-in-out; border-width: 2px; }
    .error-card { border-color: #ef4444 !important; background-color: #fef2f2 !important; box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.2); }
</style>

<script>
const COMPRESS_CONFIG = { maxWidth: 1280, maxHeight: 1280, quality: 0.75, mimeType: 'image/jpeg' };

/** Compresión */
function compressImage(file, config = COMPRESS_CONFIG) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = ({ target }) => {
            const img = new Image();
            img.onload = () => {
                let { width, height } = img;
                const ratio = Math.min(config.maxWidth / width, config.maxHeight / height, 1);
                const canvas = document.createElement('canvas');
                canvas.width = width * ratio; canvas.height = height * ratio;
                canvas.getContext('2d').drawImage(img, 0, 0, canvas.width, canvas.height);
                canvas.toBlob(b => b ? resolve(b) : reject(), config.mimeType, config.quality);
            };
            img.src = target.result;
        };
        reader.readAsDataURL(file);
    });
}

/** Upload asíncrono */
async function compressAndUpload(input) {
    const file = input.files[0];
    const qId = input.dataset.qid;
    const preopId = document.getElementById('main_preop_id')?.value;
    if (!preopId || !file) return;

    const preview = document.getElementById(`preview_${qId}`);
    const card = input.closest('.preop-card');
    preview.innerHTML = `<i class="ri-loader-4-line animate-spin text-2xl text-black"></i>`;

    try {
        const compressed = await compressImage(file);
        const formData = new FormData();
        formData.append(`foto_${qId}`, compressed, `photo_${qId}.jpg`);
        formData.append('q_id', qId);
        formData.append('id', preopId);

        const resp = await fetch('?c=Preoperational&a=UploadPhoto', { method: 'POST', body: formData });
        const hxTrigger = resp.headers.get('HX-Trigger');
        
        if (hxTrigger) {
            const triggers = JSON.parse(hxTrigger);
            if (triggers['ocr-success']) {
                const targetInput = document.querySelector(`input[name="obs_${triggers['ocr-success'].q_id}"]`);
                if (targetInput) targetInput.value = triggers['ocr-success'].valor;
            }
        }

        preview.innerHTML = await resp.text();
        card.dataset.photo = 'true';
        card.classList.remove('error-card');
    } catch (err) {
        alert('Error al subir foto');
        preview.innerHTML = `<i class="ri-camera-fill text-gray-300 text-3xl"></i>`;
    } finally { input.value = ''; }
}

function toggleObs(id, isMal) {
    const container = document.getElementById(`obs_container_${id}`);
    if (container) container.classList.toggle('hidden', !isMal);
    const card = document.querySelector(`.preop-card[data-qid="${id}"]`);
    if(card) card.classList.remove('error-card');
}

/** VALIDACIÓN DE CADA TARJETA */
function isCardValid(card) {
    const subtype = card.dataset.subtype;
    const hasPhoto = card.dataset.photo === 'true';

    // 1. Debe tener foto
    if (!hasPhoto) return false;

    // 2. Kilometraje (Subtype 3)
    if (subtype === '3') {
        const val = card.querySelector('input[type="number"]')?.value;
        return val !== "" && val !== null && val.trim() !== "";
    }

    // 3. Selección Bien/Mal
    const radioBien = card.querySelector('input[value="Bien"]');
    const radioMal = card.querySelector('input[value="Mal"]');
    if (!radioBien?.checked && !radioMal?.checked) return false;

    // 4. Si es MAL, validar detalles
    if (radioMal?.checked) {
        if (subtype === '1') { // Texto libre
            const txt = card.querySelector('textarea');
            if (!txt || txt.value.trim().length === 0) return false;
        }
        if (subtype === '2') { // Opciones múltiples
            const checks = Array.from(card.querySelectorAll('input[type="checkbox"]'));
            if (!checks.some(c => c.checked)) return false;
        }
    }
    return true;
}

/** FUNCIÓN DE FINALIZACIÓN MANUAL */
function handleFinalize() {
    const cards = document.querySelectorAll('.preop-card');
    let firstError = null;

    cards.forEach(c => c.classList.remove('error-card'));

    for (const card of cards) {
        if (!isCardValid(card)) {
            card.classList.add('error-card');
            if (!firstError) firstError = card;
        }
    }

    if (firstError) {
        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        alert('¡Atención! Faltan respuestas, fotografías o detalles en los ítems marcados en rojo.');
        return; // Detiene la ejecución
    }

    // Si todo es válido, enviamos los datos con HTMX
    const form = document.getElementById('preopForm');
    htmx.ajax('POST', '?c=Preoperational&a=Finalize', {
        target: '#silent_sync',
        values: htmx.values(form)
    });
}

document.body.addEventListener('setPreopId', () => {
    document.getElementById('footer_actions').classList.replace('hidden', 'flex');
});
</script>