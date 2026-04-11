<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Test CISD en Español</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-inter">

<div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg p-8 mt-10 mb-10">

    <h1 class="text-3xl font-bold text-gray-800 mb-4 flex items-center gap-2">
        <i class="ri-psychotherapy-line text-blue-600 text-4xl"></i>
        <?= $id->name ?> Bienvenido al Test CISD
    </h1>

    <p class="text-gray-600 mb-6 leading-relaxed">
        Este test evalúa tu estilo de comportamiento según el modelo <strong>CISD</strong>.
    </p>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-8 text-center">
        <div class="bg-blue-100 p-3 rounded-lg"><strong class="text-blue-700">C</strong><br><span class="text-sm text-gray-600">Conciencia</span></div>
        <div class="bg-yellow-100 p-3 rounded-lg"><strong class="text-yellow-700">I</strong><br><span class="text-sm text-gray-600">Influencia</span></div>
        <div class="bg-red-100 p-3 rounded-lg"><strong class="text-red-700">D</strong><br><span class="text-sm text-gray-600">Dominancia</span></div>
        <div class="bg-green-100 p-3 rounded-lg"><strong class="text-green-700">S</strong><br><span class="text-sm text-gray-600">Estabilidad</span></div>
    </div>

    <p class="text-gray-600 mb-6">Selecciona cuánto te representa cada afirmación (1 = No me describe, 5 = Me describe totalmente).</p>

        <form id="CISDForm" method="POST" action="?c=Recruitment&a=CISDSave">

            <input type="hidden" name="id" value="<?= htmlspecialchars($id->id) ?>">
            <?php for ($i = 1; $i <= 28; $i++) { ?>
                <div class="p-4 bg-gray-50 rounded-xl shadow-sm">
                    <div class="font-medium text-gray-800 mb-3">
                        <span class="text-blue-600 font-semibold">Pregunta <?= $i ?>:</span>
                        <?= e($questions[$i]) ?>
                    </div>

                    <div class="grid grid-cols-5 gap-2">
                        <?php
                        $labels = ['Nada', 'Poco', 'Neutral', 'Bastante', 'Totalmente'];
                foreach ($labels as $v => $label) {
                    ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="q<?= $i ?>" value="<?= $v + 1 ?>" class="peer hidden">
                            <div class="py-2 px-2 border rounded-lg text-center text-sm font-medium text-gray-700 
                                        hover:bg-blue-50 peer-checked:ring-2 peer-checked:ring-blue-500 peer-checked:bg-blue-100 transition">
                                <?= $label ?>
                            </div>
                        </label>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>

            <div class="text-center pt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-lg shadow-md transition">
                    Enviar
                </button>
            </div>
        </form>

        <script>
        document.getElementById('CISDForm').addEventListener('submit', function(e) {
            const total = 28;
            let filled = 0;
            for (let i = 1; i <= total; i++) {
                if (document.querySelector(`input[name="q${i}"]:checked`)) filled++;
            }
            if (filled < total) {
                e.preventDefault();
                alert(`Por favor, responde todas las preguntas antes de continuar. (${filled}/${total} respondidas)`);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
        </script>

</div>

</body>
</html>
