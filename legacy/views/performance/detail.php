<div class="w-[98%] sm:w-[98%] h-[98vh] overflow-y-auto rounded-lg shadow-lg relative z-50 bg-gray-50 text-gray-800 text-sm leading-relaxed" hx-boost="true">
    <div class="sticky top-0 z-10 bg-gray-50 p-4 border-b border-gray-200">
        <button id="closeNewModal"
            class="absolute top-0 right-0 m-3 text-gray-900 hover:text-gray-700"
            @click="showModal = !showModal; document.getElementById('myModal').innerHTML = '';"
        >
            <i class="ri-close-line text-2xl"></i>
        </button>
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
            <h1 class="text-xl font-extrabold text-gray-800 mb-0 sm:mb-0">
                Performance Evaluation
            </h1>
        </div>
    </div>

        <div class="p-4 grid grid-cols-1 lg:grid-cols-4 gap-4">
            <!-- Panel Izquierdo -->
            <div id="info" class="bg-white rounded-lg shadow-md lg:col-span-1"
                hx-get="?c=Performance&a=Info&id=<?= $id->id ?>"
                hx-trigger="load,refresh"
                hx-target="this">
            </div>

            <!-- Panel Derecho -->
            <div class="bg-white rounded-lg shadow-md lg:col-span-3">
                <!-- Tabs -->
                <div class="flex border-b border-gray-200 bg-white flex-wrap rounded-lg">

                    <div class="tab active text-gray-800 border-gray-800 border-b-2 px-3 py-2.5 cursor-pointer font-medium text-gray-500 transition-colors duration-200 hover:text-gray-800 whitespace-nowrap"
                        hx-get="?c=Performance&a=Results&id=<?= $id->id ?>"
                        hx-target="#tabContentContainer"
                        hx-indicator="#loading">Results</div>

                    <div class="tab px-3 py-2.5 cursor-pointer font-medium text-gray-500 transition-colors duration-200 hover:text-gray-800 whitespace-nowrap"
                        hx-get="?c=Performance&a=Plans&id=<?= $id->id ?>"
                        hx-target="#tabContentContainer"
                        hx-indicator="#loading">Plans</div>
                        
                    
                        
                        <div class="tab px-3 py-2.5 cursor-pointer font-medium text-gray-500 transition-colors duration-200 hover:text-gray-800 whitespace-nowrap"
                            hx-get="?c=Performance&a=Done&id=<?= $id->id ?>"
                            hx-target="#tabContentContainer"
                            hx-indicator="#loading">Assigned</div>

                        <div class="tab px-3 py-2.5 cursor-pointer font-medium text-gray-500 transition-colors duration-200 hover:text-gray-800 whitespace-nowrap"
                            hx-get="?c=Performance&a=Received&id=<?= $id->id ?>"
                            hx-target="#tabContentContainer"
                            hx-indicator="#loading">Received</div>
                            
                    
                </div>



                <!-- Contenido dinámico -->
                <div id="tabContentContainer" class="p-4"
                    hx-get="?c=Performance&a=Results&id=<?= $id->id ?>"
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
