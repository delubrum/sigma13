<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Test PF en Español</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-inter">

<div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg p-8 mt-10 mb-10">

    <h1 class="text-3xl font-bold text-gray-800 mb-4 flex items-center gap-2">
        <i class="ri-psychotherapy-line text-blue-600 text-4xl"></i>
        <?= $id->name ?> Bienvenido al Test PF
    </h1>

        <form id="discForm" method="POST" action="?c=Recruitment&a=PFSave">

            <input type="hidden" name="id" value="<?= htmlspecialchars($id->id) ?>">

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
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Enviar</button>
            </div>
        </form>

</div>

</body>
</html>
