<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Indicadores de Atención</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body class="font-sans bg-gray-50 text-slate-800 p-8 sm:p-10 lg:p-12">

<div class="max-w-7xl mx-auto">

    <div class="text-center mb-10">
        <h1 class="text-2xl font-extrabold text-gray-900 mb-2 tracking-tight">
            <i class="ri-line-chart-line text-blue-600 mr-2"></i>
            Machinery Corrective Indicators
        </h1>
        <div class="h-1 w-32 bg-blue-600 mx-auto rounded-full"></div>
    </div>
    
    <div class="bg-white border border-gray-200 rounded-xl shadow-lg p-6 mb-10 transition duration-300 hover:shadow-xl">
        <?php require_once 'filter.php'; ?>
    </div>

    <div class="grid grid-cols-1 gap-6">

        <div class="bg-white border border-gray-200 rounded-xl shadow-md p-6 transition duration-300 hover:shadow-xl hover:border-blue-300">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                <div class="w-8 h-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-md mr-3 text-lg">
                    <i class="ri-checkbox-circle-line"></i>
                </div>
                Atendidos
            </h2>
            <div class="min-h-48">
                <?php require_once '1.php'; ?>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-md p-6 transition duration-300 hover:shadow-xl hover:border-blue-300">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                <div class="w-8 h-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-md mr-3 text-lg">
                    <i class="ri-user-unfollow-line"></i>
                </div>
                Externos
            </h2>
            <div class="min-h-48">
                <?php require_once '2.php'; ?>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-md p-6 transition duration-300 hover:shadow-xl hover:border-blue-300">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                <div class="w-8 h-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-md mr-3 text-lg">
                    <i class="ri-folder-open-line"></i>
                </div>
                Abiertos
            </h2>
            <div class="min-h-48">
                <?php require_once '3.php'; ?>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-md p-6 transition duration-300 hover:shadow-xl hover:border-blue-300">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                <div class="w-8 h-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-md mr-3 text-lg">
                    <i class="ri-time-line"></i>
                </div>
                Horas por Tipo
            </h2>
            <div class="min-h-48">
                <?php require_once '4.php'; ?>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-md p-6 transition duration-300 hover:shadow-xl hover:border-blue-300">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                <div class="w-8 h-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-md mr-3 text-lg">
                    <i class="ri-sort-number-desc"></i>
                </div>
                Cantidad por Tipo
            </h2>
            <div class="min-h-48">
                <?php require_once '4-2.php'; ?>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-md p-6 transition duration-300 hover:shadow-xl hover:border-blue-300">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                <div class="w-8 h-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-md mr-3 text-lg">
                    <i class="ri-bar-chart-2-line"></i>
                </div>
                Tiempo por Areas
            </h2>
            <div class="min-h-48">
                <?php require_once '5.php'; ?>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-md p-6 transition duration-300 hover:shadow-xl hover:border-blue-300">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                <div class="w-8 h-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-md mr-3 text-lg">
                    <i class="ri-building-line"></i>
                </div>
                Cantidad por Areas
            </h2>
            <div class="min-h-48">
                <?php require_once '5-2.php'; ?>
            </div>
        </div>

    </div>
</div>

<script>
    // Configuración global limpia para Chart.js
    document.addEventListener('DOMContentLoaded', function() {
        if (window.Chart) {
            // Estilos por defecto limpios y consistentes
            Chart.defaults.color = '#475569'; // slate-600
            Chart.defaults.borderColor = '#e2e8f0'; // slate-200
            Chart.defaults.font.family = "'system-ui', sans-serif";
        }
    });
</script>
</body>
</html>