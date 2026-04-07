<div class="w-[98%] sm:w-[98%] h-[98vh] overflow-y-auto rounded-lg shadow-lg relative z-50 bg-gray-50 text-gray-800 text-sm leading-relaxed" hx-boost="true">
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
                <?= $id->id ?> - Job Profile <?= $id->code ?> V3 2025-10-07
            </h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            <!-- Panel Izquierdo -->
            <div id="info" class="bg-white rounded-lg shadow-md lg:col-span-1"
                hx-get="?c=JP&a=Info&id=<?= $id->id ?>"
                hx-trigger="load,refresh"
                hx-target="this">
            </div>

            <!-- Panel Derecho -->
            <div class="bg-white rounded-lg shadow-md lg:col-span-3">
                <!-- Tabs -->
                <div class="flex border-b border-gray-200 bg-white flex-wrap rounded-lg">

                    <div class="tab active text-gray-800 border-gray-800 border-b-2 px-3 py-2.5 cursor-pointer font-medium text-gray-500 transition-colors duration-200 hover:text-gray-800 whitespace-nowrap"
                        hx-get="?c=JP&a=DetailTab&tab=descriptions&type=Funciones&id=<?= $id->id ?>"
                        hx-target="#tabContentContainer"
                        hx-indicator="#loading">Funciones</div>

                    <div class="tab px-3 py-2.5 cursor-pointer font-medium text-gray-500 transition-colors duration-200 hover:text-gray-800 whitespace-nowrap"
                        hx-get="?c=JP&a=DetailTab&tab=resources&type=Recursos&id=<?= $id->id ?>"
                        hx-target="#tabContentContainer"
                        hx-indicator="#loading">Recursos</div>

                    <div class="tab px-3 py-2.5 cursor-pointer font-medium text-gray-500 transition-colors duration-200 hover:text-gray-800 whitespace-nowrap"
                        hx-get="?c=JP&a=DetailTab&tab=areas&id=<?= $id->id ?>"
                        hx-target="#tabContentContainer"
                        hx-indicator="#loading">Responsabilidades SGI</div>

                    <div class="tab px-3 py-2.5 cursor-pointer font-medium text-gray-500 transition-colors duration-200 hover:text-gray-800 whitespace-nowrap"
                        hx-get="?c=JP&a=DetailTab&tab=education&type=Educación&id=<?= $id->id ?>"
                        hx-target="#tabContentContainer"
                        hx-indicator="#loading">Educación</div>

                    <div class="tab px-3 py-2.5 cursor-pointer font-medium text-gray-500 transition-colors duration-200 hover:text-gray-800 whitespace-nowrap"
                        hx-get="?c=JP&a=DetailTab&tab=descriptions&type=Formación&id=<?= $id->id ?>"
                        hx-target="#tabContentContainer"
                        hx-indicator="#loading">Formación</div>

                    <div class="tab px-3 py-2.5 cursor-pointer font-medium text-gray-500 transition-colors duration-200 hover:text-gray-800 whitespace-nowrap"
                        hx-get="?c=JP&a=DetailTab&tab=descriptions&type=Entrenamiento&id=<?= $id->id ?>"
                        hx-target="#tabContentContainer"
                        hx-indicator="#loading">Entrenamiento</div>

                    <div class="tab px-3 py-2.5 cursor-pointer font-medium text-gray-500 transition-colors duration-200 hover:text-gray-800 whitespace-nowrap"
                        hx-get="?c=JP&a=DetailTab&tab=skills&type=Competencias&id=<?= $id->id ?>"
                        hx-target="#tabContentContainer"
                        hx-indicator="#loading">Competencias</div>

                    <div class="tab px-3 py-2.5 cursor-pointer font-medium text-gray-500 transition-colors duration-200 hover:text-gray-800 whitespace-nowrap"
                        hx-get="?c=JP&a=DetailTab&tab=risk&type=Riesgos&id=<?= $id->id ?>"
                        hx-target="#tabContentContainer"
                        hx-indicator="#loading">Riesgos</div>
                </div>

                <!-- Contenido dinámico -->
                <div id="tabContentContainer" class="p-4"
                    hx-get="?c=JP&a=DetailTab&tab=descriptions&type=Funciones&id=<?= $id->id ?>"
                    hx-trigger="load"
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
