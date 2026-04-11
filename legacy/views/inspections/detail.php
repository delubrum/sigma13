<div class="w-[98vw] sm:w-[60vw] max-h-[98vh] bg-white rounded-lg flex flex-col overflow-hidden">
    <div class="p-4 border-b flex justify-between items-center bg-gray-50 rounded-t-lg">
        <div class="flex items-center gap-3">
            <div class="bg-black p-2 rounded-lg shadow-md shrink-0"><i class="ri-survey-line text-white text-xl"></i></div>
            <h1 class="text-xl font-extrabold text-gray-900">Inspección #<?= $id->id ?></h1>
        </div>
        <button id="closeNewModal" @click="showModal = false" class="ri-close-circle-fill text-3xl text-black hover:text-red-500 transition-colors"></button>
    </div>

    <input type="hidden" id="main_inspection_id" value="<?= $id->id ?>">                            

    <div id="checklist_container" class="p-6 overflow-y-auto flex-grow"
         hx-get="?c=Inspections&a=Checklist" 
         hx-trigger="load" 
         hx-target="this" 
         hx-indicator="#loading"
         hx-vals='js:{inspection_id: document.getElementById("main_inspection_id").value, asset_id: "<?= $id->asset_id ?>"}'>
    </div>
    
    <div class="p-4 border-t bg-gray-50 flex justify-end items-center gap-4">
        <div id="loading" class="htmx-indicator">
            <i class="ri-loader-4-line animate-spin text-2xl text-gray-400"></i>
        </div>
        <button type="button" onclick="finishInspection()" class="bg-black text-white px-10 py-3 rounded-xl font-bold uppercase italic text-xs tracking-widest hover:bg-gray-800 transition-all">
            Finalizar Inspección
        </button>
    </div>
</div>

<script>
async function finishInspection() {
    const cards = document.querySelectorAll('.inspection-card');
    let firstError = null;
    let missingAnswer = false;
    let missingDetail = false;

    cards.forEach(card => {
        const checked = card.querySelector('input[type="radio"]:checked');
        const isMal = checked && checked.value === 'Mal';
        const hasPhoto = card.getAttribute('data-photo') === 'true';
        const obsValue = card.querySelector('textarea')?.value.trim();

        card.classList.remove('bg-red-50', 'border-red-200', 'bg-orange-50', 'border-orange-200');

        let hasError = false;

        if (!checked) {
            missingAnswer = true;
            card.classList.add('bg-orange-50', 'border-orange-200');
            hasError = true;
        } else if (isMal && (!hasPhoto || !obsValue)) {
            missingDetail = true;
            card.classList.add('bg-red-50', 'border-red-200');
            hasError = true;
        }

        // Guardamos la primera tarjeta que tenga un error
        if (hasError && !firstError) {
            firstError = card;
        }
    });

    if (firstError) {
        // Scroll suave al primer error encontrado
        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        let msg = missingAnswer ? 'Faltan ítems por responder.' : 'Los hallazgos en MAL requieren foto y hallazgo escrito.';
        
        window.dispatchEvent(new CustomEvent('showMessage', {
            detail: { type: 'error', message: msg }
        }));
        return;
    }

    htmx.ajax('POST', '?c=Inspections&a=CloseInspection', {
        values: { id: document.getElementById("main_inspection_id").value },
        swap: 'none',
        indicator: '#loading'
    });
}

async function compressAndUpload(input) {
    const qId = input.dataset.qid;
    const preview = document.getElementById(`preview_${qId}`);
    const card = input.closest('.inspection-card');
    
    preview.innerHTML = `<i class="ri-loader-4-line animate-spin text-xl text-black"></i>`;
    
    const formData = new FormData();
    formData.append(`foto_${qId}`, input.files[0]);
    formData.append('q_id', qId);
    formData.append('id', document.getElementById("main_inspection_id").value);

    const res = await fetch('?c=Inspections&a=UploadPhoto', { method: 'POST', body: formData });
    if(res.ok) {
        preview.innerHTML = await res.text();
        card.setAttribute('data-photo', 'true');
        card.classList.remove('bg-red-50', 'border-red-200');
    }
}
</script>