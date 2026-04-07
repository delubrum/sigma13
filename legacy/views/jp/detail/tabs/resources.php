<?php
// $data viene de la BD (string JSON)
$dataDecoded = json_decode($data, true);
if (! is_array($dataDecoded)) {
    $dataDecoded = [];
}

function isActive($group, $name, $dataDecoded)
{
    if (isset($dataDecoded[$group]['items']) && is_array($dataDecoded[$group]['items'])) {
        return in_array($name, $dataDecoded[$group]['items']);
    }
    if (isset($dataDecoded['items']) && is_array($dataDecoded['items'])) {
        return in_array($name, $dataDecoded['items']);
    }
    if (array_values($dataDecoded) === $dataDecoded) {
        return in_array($name, $dataDecoded);
    }

    return false;
}

function getOtro($group, $dataDecoded)
{
    return $dataDecoded[$group]['otro'] ?? '';
}
?>

<!-- RECURSOS -->
<div class="mb-5 w-full">
    <h2 class="text-base font-bold text-gray-900 mb-3 flex items-center space-x-1.5">
        <i class="ri-file-text-line text-xl"></i>
        <span>Recursos</span>
    </h2>
    <div class="flex flex-wrap gap-2">
        <?php foreach ($this->model->list('*', 'hr_db', " AND kind = 'asset'") as $p) {
            $activo = isActive('Recursos', $p->name, $dataDecoded);
            $hxvals = htmlspecialchars(json_encode(['id' => $id->id, 'group' => 'Recursos', 'value' => $p->name]), ENT_QUOTES);
            ?>
            <button 
                type="button"
                class="px-3 py-1 rounded-lg border <?= $activo ? '!bg-black !text-white' : 'bg-gray-100' ?> <?= $canEdit ? 'cursor-pointer' : 'cursor-not-allowed opacity-70' ?>"
                <?php if ($canEdit) { ?>
                    hx-post="?c=JP&a=SaveResource"
                    hx-vals='<?= $hxvals ?>'
                    hx-swap="none"
                    onclick="toggleActive(this)"
                <?php } ?>
            >
                <?= htmlspecialchars($p->name) ?>
            </button>
        <?php } ?>
    </div>
</div>

<!-- INFORMACIÓN CONFIDENCIAL -->
<div class="mb-5 w-full">
    <h2 class="text-base font-bold text-gray-900 mb-3 flex items-center space-x-1.5">
        <i class="ri-lock-line text-xl"></i>
        <span>Información Confidencial</span>
    </h2>
    <div class="flex flex-wrap items-center gap-2">
        <?php
            $opciones = ['Administrativa', 'Comercio exterior', 'Compras', 'Tesorería'];
foreach ($opciones as $info) {
    $activo = isActive('InformacionConfidencial', $info, $dataDecoded);
    $hxvals = htmlspecialchars(json_encode(['id' => $id->id, 'group' => 'InformacionConfidencial', 'value' => $info]), ENT_QUOTES);
    ?>
            <button 
                type="button"
                class="px-3 py-1 rounded-lg border <?= $activo ? '!bg-black !text-white' : 'bg-gray-100' ?> <?= $canEdit ? 'cursor-pointer' : 'cursor-not-allowed opacity-70' ?>"
                <?php if ($canEdit) { ?>
                    hx-post="?c=JP&a=SaveResource"
                    hx-vals='<?= $hxvals ?>'
                    hx-swap="none"
                    onclick="toggleActive(this)"
                <?php } ?>
            >
                <?= htmlspecialchars($info) ?>
            </button>
        <?php } ?>

        <label class="flex items-center gap-2">
            <span class="text-gray-600 text-sm font-bold">Otros:</span>
            <input 
                type="text"
                placeholder="Especificar..." 
                value="<?= htmlspecialchars(getOtro('InformacionConfidencial', $dataDecoded)) ?>"
                class="px-3 py-1 rounded-lg border bg-gray-100 focus:outline-none focus:ring-1 focus:ring-black <?= $canEdit ? '' : 'cursor-not-allowed opacity-70' ?>"
                <?php if ($canEdit) { ?>
                    hx-post="?c=JP&a=SaveResource"
                    hx-trigger="change, keyup delay:800ms"
                    hx-swap="none"
                    hx-vals='<?= htmlspecialchars(json_encode(['id' => $id->id, 'group' => 'InformacionConfidencial', 'is_input' => true, 'value' => getOtro('InformacionConfidencial', $dataDecoded)]), ENT_QUOTES) ?>'
                    oninput="this.setAttribute('hx-vals', JSON.stringify({id: '<?= $id->id ?>', group: 'InformacionConfidencial', value: this.value, is_input: true}))"
                <?php } else { ?>disabled<?php } ?>
            >
        </label>
    </div>
</div>

<!-- MAQUINARIA -->
<div class="mb-5 w-full">
    <h2 class="text-base font-bold text-gray-900 mb-3 flex items-center space-x-1.5">
        <i class="ri-tools-line text-xl"></i>
        <span>Maquinaria</span>
    </h2>
    <div class="flex flex-wrap gap-2">
        <?php foreach (['Punzonadoras', 'Dobladoras', 'Cortadoras', 'Otros'] as $val) {
            $activo = isActive('Maquinaria', $val, $dataDecoded);
            $hxvals = htmlspecialchars(json_encode(['id' => $id->id, 'group' => 'Maquinaria', 'value' => $val]), ENT_QUOTES);
            ?>
            <button 
                type="button"
                class="px-3 py-1 rounded-lg border <?= $activo ? '!bg-black !text-white' : 'bg-gray-100' ?> <?= $canEdit ? 'cursor-pointer' : 'cursor-not-allowed opacity-70' ?>"
                <?php if ($canEdit) { ?>
                    hx-post="?c=JP&a=SaveResource"
                    hx-vals='<?= $hxvals ?>'
                    hx-swap="none"
                    onclick="toggleActive(this)"
                <?php } ?>
            >
                <?= htmlspecialchars($val) ?>
            </button>
        <?php } ?>
    </div>
</div>

<!-- INVENTARIO -->
<div class="mb-5 w-full">
    <h2 class="text-base font-bold text-gray-900 mb-3 flex items-center space-x-1.5">
        <i class="ri-archive-line text-xl"></i>
        <span>Inventario</span>
    </h2>
    <div class="flex flex-wrap gap-2">
        <?php foreach (['Otros'] as $val) {
            $activo = isActive('Inventario', $val, $dataDecoded);
            $hxvals = htmlspecialchars(json_encode(['id' => $id->id, 'group' => 'Inventario', 'value' => $val]), ENT_QUOTES);
            ?>
            <button 
                type="button"
                class="px-3 py-1 rounded-lg border <?= $activo ? '!bg-black !text-white' : 'bg-gray-100' ?> <?= $canEdit ? 'cursor-pointer' : 'cursor-not-allowed opacity-70' ?>"
                <?php if ($canEdit) { ?>
                    hx-post="?c=JP&a=SaveResource"
                    hx-vals='<?= $hxvals ?>'
                    hx-swap="none"
                    onclick="toggleActive(this)"
                <?php } ?>
            >
                <?= htmlspecialchars($val) ?>
            </button>
        <?php } ?>
    </div>
</div>

<!-- MANEJO DE VALORES -->
<div class="mb-5 w-full">
    <h2 class="text-base font-bold text-gray-900 mb-3 flex items-center space-x-1.5">
        <i class="ri-bank-card-line text-xl"></i>
        <span>Manejo de Valores</span>
    </h2>
    <div class="flex flex-wrap gap-2">
        <?php foreach (['Dinero en efectivo', 'Chequeras', 'Tarjetas de Crédito'] as $val) {
            $activo = isActive('ManejoDeValores', $val, $dataDecoded);
            $hxvals = htmlspecialchars(json_encode(['id' => $id->id, 'group' => 'ManejoDeValores', 'value' => $val]), ENT_QUOTES);
            ?>
            <button 
                type="button"
                class="px-3 py-1 rounded-lg border <?= $activo ? '!bg-black !text-white' : 'bg-gray-100' ?> <?= $canEdit ? 'cursor-pointer' : 'cursor-not-allowed opacity-70' ?>"
                <?php if ($canEdit) { ?>
                    hx-post="?c=JP&a=SaveResource"
                    hx-vals='<?= $hxvals ?>'
                    hx-swap="none"
                    onclick="toggleActive(this)"
                <?php } ?>
            >
                <?= htmlspecialchars($val) ?>
            </button>
        <?php } ?>
    </div>
</div>

<script>
function toggleActive(el) {
    if (el.classList.contains('cursor-not-allowed')) return;
    el.classList.toggle("!bg-black");
    el.classList.toggle("!text-white");
}
</script>
