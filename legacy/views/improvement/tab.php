<div class="flex border-b border-gray-200 bg-white flex-wrap rounded-t-lg">

    <div class="tab active text-gray-800 border-gray-800 border-b-2 px-3 py-1.5 cursor-pointer font-medium text-gray-500 transition-colors duration-200 hover:text-gray-800 whitespace-nowrap"
        hx-get="?c=Improvement&a=Tabs&tab=causes&id=<?= $id->id ?>"
        hx-target="#tabContentContainer"
        hx-indicator="#loading">Causes
    </div>
    <?php if ($id->status != 'Analysis') { ?>
        <div class="tab px-3 py-1.5 cursor-pointer font-medium text-gray-500 transition-colors duration-200 hover:text-gray-800 whitespace-nowrap"
            hx-get="?c=Improvement&a=Tabs&tab=activities&id=<?= $id->id ?>"
            hx-target="#tabContentContainer"
            hx-indicator="#loading">Activities
        </div>
    <?php } ?>
</div>

<!-- Contenido dinámico -->
<div id="tabContentContainer" class="p-4"
    hx-get="?c=Improvement&a=Tabs&tab=causes&id=<?= $id->id ?>"
    hx-trigger="load"
    hx-target="this">
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