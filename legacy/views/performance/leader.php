<?php
// --- Datos base (SIN CAMBIOS) ---
$competencias = [
    'Responsabilidad',
    'Trabajo Colaborativo',
    'Compromiso',
    'Comunicación Efectiva',
    'Liderazgo',
    'Conocimientos Técnicos',
    'Gestión y Logro de Objetivos',
    'Autonomía',
    'Pensamiento Analítico',
];

$colaboradoresData = [
    [
        'id' => 1,
        'nombre' => 'Ana García',
        'cargo' => 'Analista Senior',
        'evaluaciones' => [
            'Responsabilidad' => ['auto' => 4.5, 'lider' => 4.8],
            'Trabajo Colaborativo' => ['auto' => 4.2, 'lider' => 4.5],
            'Compromiso' => ['auto' => 4.7, 'lider' => 4.9],
            'Comunicación Efectiva' => ['auto' => 4.0, 'lider' => 4.3],
            'Liderazgo' => ['auto' => 4.3, 'lider' => 4.6],
            'Conocimientos Técnicos' => ['auto' => 4.6, 'lider' => 4.7],
            'Gestión y Logro de Objetivos' => ['auto' => 4.4, 'lider' => 4.8],
            'Autonomía' => ['auto' => 4.5, 'lider' => 4.7],
            'Pensamiento Analítico' => ['auto' => 4.3, 'lider' => 4.5],
        ],
    ],
    [
        'id' => 2,
        'nombre' => 'Carlos Mendoza',
        'cargo' => 'Coordinador',
        'evaluaciones' => [
            'Responsabilidad' => ['auto' => 3.8, 'lider' => 3.5],
            'Trabajo Colaborativo' => ['auto' => 3.5, 'lider' => 3.2],
            'Compromiso' => ['auto' => 3.9, 'lider' => 3.6],
            'Comunicación Efectiva' => ['auto' => 3.2, 'lider' => 3.0],
            'Liderazgo' => ['auto' => 3.6, 'lider' => 3.4],
            'Conocimientos Técnicos' => ['auto' => 4.0, 'lider' => 3.8],
            'Gestión y Logro de Objetivos' => ['auto' => 3.4, 'lider' => 3.1],
            'Autonomía' => ['auto' => 3.7, 'lider' => 3.5],
            'Pensamiento Analítico' => ['auto' => 3.3, 'lider' => 3.2],
        ],
    ],
    [
        'id' => 3,
        'nombre' => 'María López',
        'cargo' => 'Especialista',
        'evaluaciones' => [
            'Responsabilidad' => ['auto' => 4.8, 'lider' => 4.9],
            'Trabajo Colaborativo' => ['auto' => 4.9, 'lider' => 5.0],
            'Compromiso' => ['auto' => 4.7, 'lider' => 4.8],
            'Comunicación Efectiva' => ['auto' => 4.6, 'lider' => 4.8],
            'Liderazgo' => ['auto' => 4.5, 'lider' => 4.7],
            'Conocimientos Técnicos' => ['auto' => 4.7, 'lider' => 4.9],
            'Gestión y Logro de Objetivos' => ['auto' => 4.8, 'lider' => 4.9],
            'Autonomía' => ['auto' => 4.6, 'lider' => 4.8],
            'Pensamiento Analítico' => ['auto' => 4.7, 'lider' => 4.8],
        ],
    ],
    [
        'id' => 4,
        'nombre' => 'Pedro Ramírez',
        'cargo' => 'Analista',
        'evaluaciones' => [
            'Responsabilidad' => ['auto' => 4.1, 'lider' => 4.0],
            'Trabajo Colaborativo' => ['auto' => 4.3, 'lider' => 4.2],
            'Compromiso' => ['auto' => 4.0, 'lider' => 3.9],
            'Comunicación Efectiva' => ['auto' => 3.8, 'lider' => 3.7],
            'Liderazgo' => ['auto' => 3.9, 'lider' => 4.0],
            'Conocimientos Técnicos' => ['auto' => 4.2, 'lider' => 4.1],
            'Gestión y Logro de Objetivos' => ['auto' => 4.0, 'lider' => 3.9],
            'Autonomía' => ['auto' => 4.1, 'lider' => 4.0],
            'Pensamiento Analítico' => ['auto' => 4.0, 'lider' => 3.9],
        ],
    ],
    [
        'id' => 5,
        'nombre' => 'Laura Fernández',
        'cargo' => 'Analista Junior',
        'evaluaciones' => [
            'Responsabilidad' => ['auto' => 3.2, 'lider' => 2.9],
            'Trabajo Colaborativo' => ['auto' => 3.4, 'lider' => 3.1],
            'Compromiso' => ['auto' => 3.0, 'lider' => 2.8],
            'Comunicación Efectiva' => ['auto' => 3.3, 'lider' => 3.0],
            'Liderazgo' => ['auto' => 2.8, 'lider' => 2.6],
            'Conocimientos Técnicos' => ['auto' => 3.5, 'lider' => 3.2],
            'Gestión y Logro de Objetivos' => ['auto' => 3.1, 'lider' => 2.7],
            'Autonomía' => ['auto' => 3.0, 'lider' => 2.8],
            'Pensamiento Analítico' => ['auto' => 3.2, 'lider' => 2.9],
        ],
    ],
];

// --- Funciones de cálculo (SIN CAMBIOS) ---
function promedio($auto, $lider)
{
    return number_format(($auto + $lider) / 2, 1);
}

function promedioGeneral($evaluaciones)
{
    $suma = 0;
    $count = 0;
    foreach ($evaluaciones as $ev) {
        $suma += ($ev['auto'] + $ev['lider']) / 2;
        $count++;
    }

    return number_format($suma / $count, 2);
}

function statusColor($p)
{
    // Retorna clases de color de fondo, texto y borde para la tarjeta de resultado
    if ($p >= 4.5) {
        return 'bg-green-100 text-green-800 border-green-300';
    }
    if ($p >= 4.0) {
        return 'bg-blue-100 text-blue-800 border-blue-300';
    }
    if ($p >= 3.5) {
        return 'bg-yellow-100 text-yellow-800 border-yellow-300';
    }

    return 'bg-red-100 text-red-800 border-red-300';
}

function statusText($p)
{
    if ($p >= 4.5) {
        return 'Excelente';
    }
    if ($p >= 4.0) {
        return 'Bueno';
    }
    if ($p >= 3.5) {
        return 'Mejorable';
    }

    return 'Plan de Mejora';
}

function cardBorderColor($p)
{
    // Retorna la clase para el borde izquierdo principal de la tarjeta
    if ($p >= 4.5) {
        return 'border-green-500';
    }
    if ($p >= 4.0) {
        return 'border-blue-500';
    }
    if ($p >= 3.5) {
        return 'border-yellow-500';
    }

    return 'border-red-500';
}

function cellColor($v)
{
    // Esta función ya no se usa, pero la mantengo
    if ($v >= 4.5) {
        return 'bg-green-50';
    }
    if ($v >= 4.0) {
        return 'bg-blue-50';
    }
    if ($v >= 3.5) {
        return 'bg-yellow-50';
    }

    return 'bg-red-50';
}

// --- Ordenar por desempeño (SIN CAMBIOS) ---
usort($colaboradoresData, function ($a, $b) {
    return promedioGeneral($b['evaluaciones']) <=> promedioGeneral($a['evaluaciones']);
});
?>

    <!-- RESUMEN -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-2 p-4">
        <?php
        $counts = ['exc' => 0, 'bue' => 0, 'mej' => 0, 'pla' => 0];
foreach ($colaboradoresData as $c) {
    $p = promedioGeneral($c['evaluaciones']);
    if ($p >= 4.5) {
        $counts['exc']++;
    } elseif ($p >= 4.0) {
        $counts['bue']++;
    } elseif ($p >= 3.5) {
        $counts['mej']++;
    } else {
        $counts['pla']++;
    }
}
$total = array_sum($counts);
?>
        
        <!-- TOTAL -->
        <div class="bg-white shadow rounded-lg p-2 sm:p-4 flex items-center justify-between border border-gray-200">
            <div>
                <div class="text-base sm:text-xl font-bold text-gray-900"><?= $total ?></div>
                <div class="text-sm text-gray-500">Total</div>
            </div>
            <div class="text-gray-700 bg-gray-100 rounded-full py-2 px-3 text-xl">
                <i class="ri-survey-line"></i>
            </div>
        </div>

        <!-- EXCELENTE -->
        <div class="bg-white shadow rounded-lg p-2 sm:p-4 flex items-center justify-between border border-green-200">
            <div>
                <div class="text-base sm:text-xl font-bold text-green-800"><?= $counts['exc'] ?></div>
                <div class="text-sm text-green-700">Excelente (4.5 - 5.0)</div>
            </div>
            <div class="text-green-700 bg-green-50 rounded-full py-2 px-3 text-xl">
                <i class="ri-thumb-up-line"></i>
            </div>
        </div>

        <!-- BUENO -->
        <div class="bg-white shadow rounded-lg p-2 sm:p-4 flex items-center justify-between border border-blue-200">
            <div>
                <div class="text-base sm:text-xl font-bold text-blue-800"><?= $counts['bue'] ?></div>
                <div class="text-sm text-blue-700">Bueno (4.0 - 4.4)</div>
            </div>
            <div class="text-blue-700 bg-blue-50 rounded-full py-2 px-3 text-xl">
                <i class="ri-emotion-happy-line"></i>
            </div>
        </div>

        <!-- MEJORABLE -->
        <div class="bg-white shadow rounded-lg p-2 sm:p-4 flex items-center justify-between border border-yellow-200">
            <div>
                <div class="text-base sm:text-xl font-bold text-yellow-800"><?= $counts['mej'] ?></div>
                <div class="text-sm text-yellow-700">Mejorable (3.5 - 3.9)</div>
            </div>
            <div class="text-yellow-700 bg-yellow-50 rounded-full py-2 px-3 text-xl">
                <i class="ri-error-warning-line"></i>
            </div>
        </div>

        <!-- PLAN DE MEJORA -->
        <div class="bg-white shadow rounded-lg p-2 sm:p-4 flex items-center justify-between border border-red-200">
            <div>
                <div class="text-base sm:text-xl font-bold text-red-800"><?= $counts['pla'] ?></div>
                <div class="text-sm text-red-700">Plan de Mejora (&lt; 3.5)</div>
            </div>
            <div class="text-red-700 bg-red-50 rounded-full py-2 px-3 text-xl">
                <i class="ri-alert-line"></i>
            </div>
        </div>

    </div>



<!-- LISTA SIMPLIFICADA DE COLABORADORES EN GRID (2 COLUMNAS) -->
<div class="m-4">
    <h2 class="text-xl font-bold text-slate-700 border-b pb-2 mb-4">Resultados Individuales</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php foreach ($colaboradoresData as $i => $colaborador) {
            $pg = promedioGeneral($colaborador['evaluaciones']);
            ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
            <div class="p-4 border-l-4 <?= cardBorderColor($pg) ?>">
                <div class="flex justify-between items-center">
                    
                    <!-- Información del Colaborador -->
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 flex items-center justify-center bg-slate-200 rounded-full font-bold text-sm text-slate-700 flex-shrink-0">
                            <?= $i + 1 ?>
                        </div>

                        <div>
                            <p class="text-base font-bold text-slate-800">
                                <?= $colaborador['nombre'] ?>
                            </p>

                            <p class="text-xs text-slate-600"><?= $colaborador['cargo'] ?></p>
                        </div>
                    </div>

                    <!-- Resultado del Semáforo -->
                    <div class="px-3 py-1 rounded-lg border-2 <?= statusColor($pg) ?> text-center flex-shrink-0">
                        <p class="text-xs font-semibold"><?= statusText($pg) ?></p>
                        <p class="text-lg font-bold"><?= $pg ?></p>
                    </div>

                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>



