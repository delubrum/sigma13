<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sigma | Improvement</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-4 font-sans text-gray-800">

    <div class="max-w-[1600px] mx-auto bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
        
        <div class="bg-gray-800 px-6 py-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="text-xl font-bold text-white whitespace-nowrap">Plan de Mejora Continua</h2>
            
            <div class="relative w-full md:w-96">
                <input type="text" id="searchInput" 
                       placeholder="Filtrar por ID, acción, responsable..." 
                       class="w-full px-4 py-2 rounded-md bg-gray-700 text-white placeholder-gray-400 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                <span class="absolute right-3 top-2.5 text-gray-400">🔍</span>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table id="mainTable" class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-200 uppercase text-xs tracking-wider text-gray-600">
                        <th class="px-4 py-3 font-bold w-20">ID</th>
                        <th class="px-4 py-3 font-bold w-64">Acción</th>
                        <th class="px-4 py-3 font-bold w-64">Cómo</th>
                        <th class="px-4 py-3 font-bold text-center">Cuándo</th>
                        <th class="px-4 py-3 font-bold text-center">Estado</th>
                        <th class="px-4 py-3 font-bold">Resultados</th>
                        <th class="px-4 py-3 font-bold">Responsable</th>
                        <th class="px-4 py-3 font-bold text-center">Calif.</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($total as $item) { ?>
                    <tr class="hover:bg-blue-50/50 transition-colors">
                        <td class="px-4 py-3 text-sm font-bold text-blue-600"><?= $item->code ?></td>
                        <td class="px-4 py-3 text-sm leading-snug"><?= $item->action ?></td>
                        <td class="px-4 py-3 text-sm leading-snug text-gray-600"><?= $item->how ?></td>
                        <td class="px-4 py-3 text-sm text-center whitespace-nowrap text-gray-500 font-mono"><?= $item->whenn ?></td>
                        <td class="px-4 py-3 text-center">
                            <?php if ($item->done) { ?>
                                <span class="bg-green-100 text-green-700 px-2.5 py-0.5 rounded-full text-xs font-bold border border-green-200">Terminado</span>
                            <?php } else { ?>
                                <span class="bg-amber-100 text-amber-700 px-2.5 py-0.5 rounded-full text-xs font-bold border border-amber-200">Pendiente</span>
                            <?php } ?>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500 italic max-w-xs truncate" title="<?= htmlspecialchars($item->results) ?>">
                            <?= is_string($item->results) ? trim($item->results, '[]') : '---' ?>
                        </td>
                        <td class="px-4 py-3 text-sm font-medium"><?= $item->responsiblename ?></td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-yellow-500 font-bold"><?= $item->rating ?? '-' ?></span>
                            <span class="text-yellow-400 text-xs">★</span>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        
        <div id="noResults" class="hidden p-8 text-center text-gray-500">
            No se encontraron coincidencias con tu búsqueda.
        </div>
    </div>

    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#mainTable tbody tr');
            let hasResults = false;

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(filter)) {
                    row.style.display = '';
                    hasResults = true;
                } else {
                    row.style.display = 'none';
                }
            });

            // Mostrar mensaje si no hay filas visibles
            const noResultsDiv = document.getElementById('noResults');
            noResultsDiv.classList.toggle('hidden', hasResults);
        });
    </script>

</body>
</html>