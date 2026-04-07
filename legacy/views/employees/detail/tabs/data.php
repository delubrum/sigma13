<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
<?php
function val($v)
{
    $s = trim((string) ($v ?? ''));

    return ($s === '' || strtolower($s) === 'null') ? '—' : htmlspecialchars($s);
}
function emp($v)
{
    $s = trim((string) ($v ?? ''));

    return $s === '' || strtolower($s) === 'null';
}
?>
</head>
<body class="bg-gray-100 text-gray-900 text-sm">

<div class="max-w-5xl mx-auto px-4 py-6 pb-16" id="formulario-empleado">

  <!-- ENCABEZADO: solo cargo + botón imprimir -->
  <div class="flex items-center justify-between mb-5">
    <span class="text-xs font-bold tracking-widest uppercase text-gray-900">
      <?= val($id->name ?? null) ?> || <?= val($id->profile ?? null) ?>
    </span>
    <button onclick="printById('formulario-empleado')"
      class="no-print flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 rounded text-xs font-normal text-gray-900 hover:text-gray-900 hover:border-gray-400 transition-colors">
      <i class="ri-printer-line"></i> Imprimir
    </button>
  </div>

  <!-- 1. INFORMACIÓN GENERAL -->
  <div class="bg-white border border-gray-200 rounded-lg overflow-hidden mb-3">
    <div class="flex items-center gap-2 px-4 py-2 bg-gray-50 border-b border-gray-200">
      <i class="ri-user-line text-gray-900 text-sm"></i>
      <span class="text-xs font-bold tracking-widest uppercase text-gray-900">Información General</span>
    </div>
    <div class="px-4 py-4 space-y-4">

      <div class="grid grid-cols-2 sm:grid-cols-5 gap-x-6 gap-y-4">
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Nombre completo</div>
          <div class="<?= emp($id->name ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->name ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Cédula</div>
          <div class="font-mono <?= emp($id->id ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->id ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Tipo de sangre</div>
          <div class="<?= emp($id->tipo_sangre ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->tipo_sangre ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Fecha de nacimiento</div>
          <div class="font-mono <?= emp($id->fecha_nacimiento ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->fecha_nacimiento ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Lugar de nacimiento</div>
          <div class="<?= emp($id->lugar_nacimiento ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->lugar_nacimiento ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Género</div>
          <div class="<?= emp($id->sexo ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->sexo ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Estado civil</div>
          <div class="<?= emp($id->estado_civil ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->estado_civil ?? null) ?></div>
        </div>
      </div>

      <hr class="border-gray-100">

      <div class="grid grid-cols-2 sm:grid-cols-4 gap-x-6 gap-y-4">
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Licencia conducción</div>
          <div class="<?= emp($id->licencia_conduccion ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->licencia_conduccion ?? null) ?></div>
        </div>
        <?php if (($id->licencia_conduccion ?? '') === 'Si') { ?>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Categoría licencia</div>
          <div class="<?= emp($id->categoria_licencia ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->categoria_licencia ?? null) ?></div>
        </div>
        <?php } ?>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Libreta militar</div>
          <div class="<?= emp($id->libreta_militar ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->libreta_militar ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Tiene vehículo</div>
          <div class="<?= emp($id->tiene_vehiculo ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->tiene_vehiculo ?? null) ?></div>
        </div>
        <?php if (($id->tiene_vehiculo ?? '') === 'Si') { ?>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Tipo de vehículo</div>
          <div class="<?= emp($id->tipo_vehiculo ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->tipo_vehiculo ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Placa</div>
          <div class="font-mono <?= emp($id->placa_vehiculo ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->placa_vehiculo ?? null) ?></div>
        </div>
        <?php } ?>
      </div>

    </div>
  </div>

  <!-- 2. DIRECCIÓN Y CONTACTO -->
  <div class="bg-white border border-gray-200 rounded-lg overflow-hidden mb-3">
    <div class="flex items-center gap-2 px-4 py-2 bg-gray-50 border-b border-gray-200">
      <i class="ri-home-4-line text-gray-900 text-sm"></i>
      <span class="text-xs font-bold tracking-widest uppercase text-gray-900">Dirección y Contacto</span>
    </div>
    <div class="px-4 py-4 space-y-4">

      <div class="grid grid-cols-2 sm:grid-cols-4 gap-x-6 gap-y-4">
        <div class="sm:col-span-2">
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Dirección</div>
          <div class="<?= emp($id->direccion ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->direccion ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Barrio</div>
          <div class="<?= emp($id->barrio ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->barrio ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Municipio</div>
          <div class="<?= emp($id->municipio ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->municipio ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Departamento</div>
          <div class="<?= emp($id->departamento ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->departamento ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Estrato</div>
          <div class="font-mono <?= emp($id->estrato_socioeconomico ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->estrato_socioeconomico ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Tiempo de residencia</div>
          <div class="<?= emp($id->tiempo_vivienda ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->tiempo_vivienda ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Tenencia vivienda</div>
          <div class="<?= emp($id->tenencia_vivienda ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->tenencia_vivienda ?? null) ?></div>
        </div>
      </div>

      <hr class="border-gray-100">

      <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-4">
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Teléfono fijo</div>
          <div class="font-mono <?= emp($id->telefono_fijo ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->telefono_fijo ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Celular</div>
          <div class="font-mono <?= emp($id->celular ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->celular ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Correo personal</div>
          <div class="<?= emp($id->email ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->email ?? null) ?></div>
        </div>
      </div>

    </div>
  </div>

  <!-- 3. RELACIÓN CON LA EMPRESA -->
  <div class="bg-white border border-gray-200 rounded-lg overflow-hidden mb-3">
    <div class="flex items-center gap-2 px-4 py-2 bg-gray-50 border-b border-gray-200">
      <i class="ri-group-line text-gray-900 text-sm"></i>
      <span class="text-xs font-bold tracking-widest uppercase text-gray-900">Relación con la Empresa</span>
    </div>
    <div class="px-4 py-4">
      <p class="text-xs text-gray-900 mb-3">¿Tiene relación de consanguinidad, afinidad o sentimental con algún empleado, directivo, accionista, empresa de la competencia, cliente o proveedor en C.I. Grupo Tecnoglass?</p>
      <div class="flex items-center gap-2 mb-3">
        <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Respuesta:</span>
        <span class="font-semibold text-gray-900"><?= val($id->relacion_tecnoglass ?? null) ?></span>
      </div>
      <?php if (($id->relacion_tecnoglass ?? '') === 'Si') { ?>
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-x-6 gap-y-4 pt-3 border-t border-gray-100">
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Nombre</div>
          <div class="<?= emp($rel->nombre ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($rel->nombre ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Cédula</div>
          <div class="font-mono <?= emp($rel->cedula ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($rel->cedula ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Cargo</div>
          <div class="<?= emp($rel->cargo ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($rel->cargo ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Tipo de relación</div>
          <div class="<?= emp($rel->tipo_relacion ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($rel->tipo_relacion ?? null) ?></div>
        </div>
      </div>
      <?php } ?>
    </div>
  </div>

  <!-- 4. INFORMACIÓN FAMILIAR -->
  <div class="bg-white border border-gray-200 rounded-lg overflow-hidden mb-3">
    <div class="flex items-center gap-2 px-4 py-2 bg-gray-50 border-b border-gray-200">
      <i class="ri-parent-line text-gray-900 text-sm"></i>
      <span class="text-xs font-bold tracking-widest uppercase text-gray-900">Información Familiar</span>
    </div>
    <div class="px-4 py-4 space-y-4">

      <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Cónyuge / Compañero(a)</p>
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-x-6 gap-y-4">
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Nombre</div>
          <div class="<?= emp($id->nombre_conyuge ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->nombre_conyuge ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Cédula</div>
          <div class="font-mono <?= emp($id->cedula_conyuge ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->cedula_conyuge ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Ocupación</div>
          <div class="<?= emp($id->ocupacion_conyuge ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->ocupacion_conyuge ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Correo</div>
          <div class="<?= emp($id->email_conyuge ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->email_conyuge ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Teléfono</div>
          <div class="font-mono <?= emp($id->telefono_conyuge ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->telefono_conyuge ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Número de hijos</div>
          <div class="font-mono <?= emp($id->numero_hijos ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->numero_hijos ?? null) ?></div>
        </div>
      </div>

      <hr class="border-gray-100">

      <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Contacto de Emergencia</p>
      <div class="grid grid-cols-2 sm:grid-cols-5 gap-x-6 gap-y-4">
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Nombre y Apellido</div>
          <div class="<?= emp($contacto->nombre ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($contacto->nombre ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Identificación</div>
          <div class="font-mono <?= emp($contacto->identificacion ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($contacto->identificacion ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Parentesco</div>
          <div class="<?= emp($contacto->parentesco ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($contacto->parentesco ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Teléfono</div>
          <div class="font-mono <?= emp($contacto->telefono ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($contacto->telefono ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Ocupación</div>
          <div class="<?= emp($contacto->ocupacion ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($contacto->ocupacion ?? null) ?></div>
        </div>
      </div>

    </div>
  </div>

  <!-- 5. HIJOS -->
  <div class="bg-white border border-gray-200 rounded-lg overflow-hidden mb-3">
    <div class="flex items-center gap-2 px-4 py-2 bg-gray-50 border-b border-gray-200">
      <i class="ri-child-line text-gray-900 text-sm"></i>
      <span class="text-xs font-bold tracking-widest uppercase text-gray-900">Detalle de Hijos</span>
    </div>
    <div class="px-4 py-4">
      <?php
      $hijos = json_decode($id->hijos_json ?? '[]', true) ?: [];
if (! empty($hijos)) {
    foreach ($hijos as $h) {
        ?>
      <div class="grid grid-cols-2 sm:grid-cols-6 gap-x-5 gap-y-3 p-3 rounded border border-gray-100 bg-gray-50 mb-2 last:mb-0">
        <div class="col-span-2">
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Nombre y Apellido</div>
          <div class="<?= emp($h['nombre'] ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($h['nombre'] ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Género</div>
          <div class="<?= emp($h['sexo'] ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($h['sexo'] ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">F. Nacimiento</div>
          <div class="font-mono <?= emp($h['fecha_nacimiento'] ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($h['fecha_nacimiento'] ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Ocupación</div>
          <div class="<?= emp($h['ocupacion'] ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($h['ocupacion'] ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Tipo</div>
          <div class="<?= emp($h['tipo'] ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($h['tipo'] ?? null) ?></div>
        </div>
      </div>
      <?php
    }
} else {
    ?>
      <p class="text-center text-xs text-gray-900 italic py-4 border border-dashed border-gray-200 rounded">No se registraron hijos.</p>
      <?php } ?>
    </div>
  </div>

  <!-- 6. ESTUDIOS -->
  <div class="bg-white border border-gray-200 rounded-lg overflow-hidden mb-3">
    <div class="flex items-center gap-2 px-4 py-2 bg-gray-50 border-b border-gray-200">
      <i class="ri-book-2-line text-gray-900 text-sm"></i>
      <span class="text-xs font-bold tracking-widest uppercase text-gray-900">Estudios</span>
    </div>
    <div class="px-4 py-4 space-y-4">

      <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Último nivel alcanzado</p>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-6 gap-y-4 p-3 rounded border border-gray-100 bg-gray-50">
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Nivel</div>
          <div class="<?= emp($ultimo_estudio->nivel ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($ultimo_estudio->nivel ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Énfasis / Título</div>
          <div class="<?= emp($ultimo_estudio->enfasis ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($ultimo_estudio->enfasis ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Institución</div>
          <div class="<?= emp($ultimo_estudio->institucion ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($ultimo_estudio->institucion ?? null) ?></div>
        </div>
      </div>

      <hr class="border-gray-100">

      <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Estudios / Cursos adicionales</p>
      <?php
    $otros = json_decode($id->otros_estudios_list_json ?? '[]', true) ?: [];
if (! empty($otros)) {
    foreach ($otros as $i => $est) {
        ?>
      <div class="<?= $i > 0 ? 'pt-3 border-t border-gray-100' : '' ?>">
        <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Nombre del estudio / curso</div>
        <div class="<?= emp($est['nombre'] ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($est['nombre'] ?? null) ?></div>
      </div>
      <?php
    }
} else {
    ?>
      <p class="text-center text-xs text-gray-900 italic py-4 border border-dashed border-gray-200 rounded">No se registraron estudios o cursos adicionales.</p>
      <?php } ?>
    </div>
  </div>

  <!-- 7. TALLAS Y AFILIACIONES -->
  <div class="bg-white border border-gray-200 rounded-lg overflow-hidden mb-3">
    <div class="flex items-center gap-2 px-4 py-2 bg-gray-50 border-b border-gray-200">
      <i class="ri-pantone-line text-gray-900 text-sm"></i>
      <span class="text-xs font-bold tracking-widest uppercase text-gray-900">Tallas y Afiliaciones</span>
    </div>
    <div class="px-4 py-4 space-y-4">

      <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Tallas</p>
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-x-6 gap-y-4">
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Pantalón</div>
          <div class="font-mono <?= emp($id->talla_pantalon ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->talla_pantalon ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Camisa</div>
          <div class="font-mono <?= emp($id->talla_camisa ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->talla_camisa ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Zapatos</div>
          <div class="font-mono <?= emp($id->talla_zapatos ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->talla_zapatos ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Carnet A.R.L</div>
          <div class="<?= emp($id->tiene_carnet_arl ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->tiene_carnet_arl ?? null) ?></div>
        </div>
      </div>

      <hr class="border-gray-100">

      <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Afiliaciones</p>
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-x-6 gap-y-4">
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">E.P.S</div>
          <div class="<?= emp($id->eps ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->eps ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Fondo de pensiones</div>
          <div class="<?= emp($id->fondo_pensiones ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->fondo_pensiones ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">A.R.L</div>
          <div class="<?= emp($id->arl ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->arl ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Seguro de exequias</div>
          <div class="<?= emp($id->seguro_exequias ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->seguro_exequias ?? null) ?></div>
        </div>
      </div>

    </div>
  </div>

  <!-- 8. ANTECEDENTES MÉDICOS -->
  <div class="bg-white border border-gray-200 rounded-lg overflow-hidden mb-3">
    <div class="flex items-center gap-2 px-4 py-2 bg-gray-50 border-b border-gray-200">
      <i class="ri-health-line text-gray-900 text-sm"></i>
      <span class="text-xs font-bold tracking-widest uppercase text-gray-900">Antecedentes Médicos</span>
    </div>
    <div class="px-4 py-4">
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-x-6 gap-y-4">
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Presión arterial</div>
          <div class="<?= emp($id->medico_presion_arterial ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->medico_presion_arterial ?? null) ?></div>
        </div>
        <div>
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Diabetes</div>
          <div class="<?= emp($id->medico_diabetes ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->medico_diabetes ?? null) ?></div>
        </div>
        <div class="col-span-2">
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Alergias</div>
          <div class="<?= emp($id->medico_alergias ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->medico_alergias ?? null) ?></div>
        </div>
        <div class="col-span-2 sm:col-span-4">
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Otra condición médica</div>
          <div class="<?= emp($id->medico_otra_condicion ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->medico_otra_condicion ?? null) ?></div>
        </div>
        <div class="col-span-2 sm:col-span-4">
          <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-0.5">Observaciones generales</div>
          <div class="whitespace-pre-wrap <?= emp($id->medico_observaciones ?? null) ? 'font-normal text-gray-900 italic' : 'font-normal text-gray-900' ?>"><?= val($id->medico_observaciones ?? null) ?></div>
        </div>
      </div>
    </div>
  </div>

</div>

<script>
function printById(id) {
  const el = document.getElementById(id);
  if (!el) return;
  const twSrc = document.querySelector('script[src*="tailwind"]')?.src ?? '';
  const links = Array.from(document.querySelectorAll('link[rel="stylesheet"]')).map(e => e.outerHTML).join('');
  const w = window.open('', '', 'width=1100,height=900');
  w.document.write(`<!DOCTYPE html><html><head><title>Imprimir</title>
    <script src="${twSrc}"><\/script>${links}
    <style>
      @media print { body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } .no-print { display: none !important; } }
    </style>
    </head><body class="bg-white text-sm">${el.outerHTML}</body></html>`);
  w.document.close();
  w.focus();
  setTimeout(() => { w.print(); w.close(); }, 800);
}
</script>
</body>
</html>