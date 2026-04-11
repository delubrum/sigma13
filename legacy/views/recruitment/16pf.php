<div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow-lg">

<h1 class="text-3xl font-bold mb-4 text-center">Test 16PF (80 Preguntas) - Simulación Profesional</h1>
<p class="mb-6 text-center">Seleccione una opción para cada pregunta: <strong>Nunca (1) / A veces (2) / Siempre (3)</strong></p>

<?php if ($mensaje) { ?>
<div class="mb-4 p-3 bg-red-100 text-red-700 rounded"><?= $mensaje?></div>
<?php } ?>

<form method="post">
<?php foreach ($preguntas as $p) { ?>
<div class="mb-4 border-b pb-2">
    <label class="block mb-1 font-medium"><?= $p['id']?>. <?= $p['texto']?> <span class="text-xs text-gray-500">(Factor: <?= $p['factor']?>)</span></label>
    <div class="flex space-x-6">
        <?php foreach ([1 => 'Nunca', 2 => 'A veces', 3 => 'Siempre'] as $val => $txt) { ?>
        <label class="flex items-center space-x-1">
            <input type="radio" name="<?= $p['id']?>" value="<?= $val?>" <?= (isset($respuestas[$p['id']]) && $respuestas[$p['id']] == $val) ? 'checked' : ''?> required>
            <span><?= $txt?></span>
        </label>
        <?php } ?>
    </div>
</div>
<?php } ?>

<div class="text-center mt-6">
<button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Calcular Resultados</button>
</div>
</form>
</div>