<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Actualización de Datos Personales - Grupo Tecnoglass (Validación de Foto Ultra Tolerante V5)</title>
  
  <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <script src="https://unpkg.com/htmx.org@1.9.4"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <style>
    /* IMPORTANTE: Este bloque solo se usa para forzar la fuente Poppins. */
    body { 
        font-family: 'Poppins', sans-serif; 
    }

    /* Pequeño hack para el asterisco de requerido */
    label.required::after { content: " *"; color: #dc2626; margin-left: 2px; }
  </style>
</head>
<body class="bg-gray-50 p-6 sm:p-10">
  <div class="max-w-6xl mx-auto space-y-6">
    
    <header class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 bg-white rounded-lg shadow-sm border border-gray-100">

      <div class="flex items-center w-full sm:w-auto justify-between sm:justify-start">
        <h1 class="text-xl font-semibold text-gray-900 flex items-center uppercase tracking-wide">
          <i class="ri-file-text-line text-2xl text-black mr-2"></i>
          Actualización de Datos Personales
        </h1>
      </div>

      <div class="flex items-center gap-3 mt-2 sm:mt-0">
        <div class="text-xs text-gray-700 bg-gray-100 px-2 py-1 rounded-full font-semibold border border-gray-300">
          F04-PRRH-01 (Ver. 2 - 3/03/2023)
        </div>

        <!-- BOTÓN DE CIERRE DE SESIÓN -->
        <a href="?c=Employees&a=Logout"
          class="flex items-center gap-1 text-red-600 hover:text-red-700 text-sm font-semibold px-3 py-1.5 border border-red-300 rounded-lg hover:bg-red-50 transition">
          <i class="ri-logout-circle-r-line text-lg"></i>
          Salir
        </a>
      </div>
    </header>


    <form 
      x-data="formApp()" 
      class="space-y-6"
      hx-post="?c=Employees&a=Save"
      hx-encoding="multipart/form-data"
      hx-on="htmx:afterRequest: onAfterSubmit(event)"
    >
      
      <!-- Se usa para enviar los datos del formulario de Alpine a HTMX/Backend -->
      <input type="hidden" name="payload" :value="JSON.stringify(form)">

      <main class="space-y-6">

        <section class="bg-white rounded-lg p-5 shadow-sm border border-gray-100">
          <h2 class="text-lg font-semibold text-gray-900 pb-2 mb-3 border-b-2 border-gray-900 flex items-center">
            <i class="ri-user-line text-xl mr-2"></i>
            1. Información General del Empleado
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="ri-user-2-line text-lg mr-1 text-gray-500"></i> Primer nombre</label>
              <!-- PLACEHOLDER PHP -->
              <div class="bg-gray-100 border border-gray-300 rounded-lg px-3 py-1.5 text-sm text-gray-700 font-semibold"><?= $employee->name ?></div>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="ri-id-card-line text-lg mr-1 text-gray-500"></i> Cédula No.</label>
              <!-- PLACEHOLDER PHP -->
              <div class="bg-gray-100 border border-gray-300 rounded-lg px-3 py-1.5 text-sm text-gray-700 font-semibold"><?= $employee->id ?></div>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="ri-briefcase-line text-lg mr-1 text-gray-500"></i> Cargo</label>
              <!-- PLACEHOLDER PHP -->
              <div class="bg-gray-100 border border-gray-300 rounded-lg px-3 py-1.5 text-sm text-gray-700 font-semibold"><?= $employee->division ?></div>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="ri-building-line text-lg mr-1 text-gray-500"></i> Área</label>
              <!-- PLACEHOLDER PHP -->
              <div class="bg-gray-100 border border-gray-300 rounded-lg px-3 py-1.5 text-sm text-gray-700 font-semibold"><?= $employee->area ?></div>
            </div>
          </div>
          
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
              <label for="tipo_sangre" class="required block text-sm font-semibold text-gray-800 mb-2"><i class="ri-drop-line text-lg mr-1"></i> Tipo y grupo sanguíneo</label>
              <input id="tipo_sangre" x-model="form.tipo_sangre" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" placeholder="Ej: O+" required />
            </div>
            <div>
              <label for="lugar_nacimiento" class="required block text-sm font-semibold text-gray-800 mb-2"><i class="ri-map-pin-line text-lg mr-1"></i> Lugar de nacimiento</label>
              <input id="lugar_nacimiento" x-model="form.lugar_nacimiento" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required />
            </div>
            <div>
              <label for="fecha_nacimiento" class="required block text-sm font-semibold text-gray-800 mb-2"><i class="ri-calendar-line text-lg mr-1"></i> Fecha de nacimiento</label>
              <input id="fecha_nacimiento" x-model="form.fecha_nacimiento" type="date" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required />
            </div>
            <div>
              <label for="sexo" class="required block text-sm font-semibold text-gray-800 mb-2"><i class="ri-genderless-line text-lg mr-1"></i> Género</label>
              <select id="sexo" x-model="form.sexo" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required>
                <option value="">Seleccione</option>
                <option value="M">Masculino (M)</option>
                <option value="F">Femenino (F)</option>
              </select>
            </div>
            <div>
              <label for="estado_civil" class="required block text-sm font-semibold text-gray-800 mb-2">
                <i class="ri-heart-line text-lg mr-1"></i> Estado civil
              </label>
              <select 
                  id="estado_civil" 
                  x-model="form.estado_civil" 
                  class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors"
                  required
              >
                <option value="" disabled selected>Seleccione un estado civil</option>
                <option value="Soltero">Soltero/a</option>
                <option value="Casado">Casado/a</option>
                <option value="Union libre">Unión libre</option>
                <option value="Divorciado">Divorciado/a</option>
                <option value="Viudo">Viudo/a</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mt-4 pt-4 border-t border-gray-200">
            <div>
              <label for="licencia_conduccion" class="required block text-sm font-semibold text-gray-800 mb-2"><i class="ri-steering-line text-lg mr-1"></i> Licencia de conducción</label>
              <select id="licencia_conduccion" x-model="form.licencia_conduccion" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required>
                <option value="">--</option>
                <option value="Si">Si</option>
                <option value="No">No</option>
              </select>
            </div>
            <div x-show="form.licencia_conduccion === 'Si'">
              <label for="categoria_licencia" class="required block text-sm font-semibold text-gray-800 mb-2"><i class="ri-car-line text-lg mr-1"></i> Categoría</label>
              <input id="categoria_licencia" x-model="form.categoria_licencia" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" :required="form.licencia_conduccion === 'Si'" />
            </div>
            <div>
              <label for="libreta_militar" class="required block text-sm font-semibold text-gray-800 mb-2"><i class="ri-booklet-line text-lg mr-1"></i> Libreta Militar</label>
              <select id="libreta_militar" x-model="form.libreta_militar" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required>
                <option value="">--</option>
                <option value="Si">Si</option>
                <option value="No">No</option>
              </select>
            </div>
            <div>
              <label for="tiene_vehiculo" class="required block text-sm font-semibold text-gray-800 mb-2"><i class="ri-roadster-line text-lg mr-1"></i> Tiene vehículo</label>
              <select id="tiene_vehiculo" x-model="form.tiene_vehiculo" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required>
                <option value="">--</option>
                <option value="Si">Si</option>
                <option value="No">No</option>
              </select>
            </div>
            <div x-show="form.tiene_vehiculo === 'Si'">
              <label for="tipo_vehiculo" class="required block text-sm font-semibold text-gray-800 mb-2"><i class="ri-truck-line text-lg mr-1"></i> Tipo de vehículo</label>
              <input id="tipo_vehiculo" x-model="form.tipo_vehiculo" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" :required="form.tiene_vehiculo === 'Si'" />
            </div>
            <div x-show="form.tiene_vehiculo === 'Si'">
              <label for="placa_vehiculo" class="required block text-sm font-semibold text-gray-800 mb-2"><i class="ri-dashboard-line text-lg mr-1"></i> Placa</label>
              <input id="placa_vehiculo" x-model="form.placa_vehiculo" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" :required="form.tiene_vehiculo === 'Si'" />
            </div>
          </div>
        </section>

        <section class="bg-white rounded-lg p-5 shadow-sm border border-gray-100">
          <h2 class="text-lg font-semibold text-gray-900 pb-2 mb-3 border-b-2 border-gray-900 flex items-center">
            <i class="ri-home-4-line text-xl mr-2"></i>
            Dirección y Contacto
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
              <label for="direccion" class="required block text-sm font-semibold text-gray-800 mb-2"><i class="ri-road-map-line text-lg mr-1"></i> Dirección de domicilio</label>
              <input id="direccion" x-model="form.direccion" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required />
            </div>
            <div>
              <label for="barrio" class="required block text-sm font-semibold text-gray-800 mb-2"><i class="ri-community-line text-lg mr-1"></i> Barrio</label>
              <input id="barrio" x-model="form.barrio" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required />
            </div>
            <div>
              <label for="municipio" class="required block text-sm font-semibold text-gray-800 mb-2"><i class="ri-pin-bus-line text-lg mr-1"></i> Municipio</label>
              <input id="municipio" x-model="form.municipio" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required />
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mt-4">
            <div>
              <label for="departamento" class="required block text-sm font-semibold text-gray-800 mb-2"><i class="ri-building-2-line text-lg mr-1"></i> Departamento</label>
              <input id="departamento" x-model="form.departamento" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required />
            </div>
            <div>
              <label for="estrato" class="required block text-sm font-semibold text-gray-800 mb-2"><i class="ri-money-dollar-circle-line text-lg mr-1"></i> Estrato socioeconómico</label>
              <input id="estrato" x-model="form.estrato_socioeconomico" type="number" min="1" max="6" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required />
            </div>
            <div>
              <label for="tiempo_vivienda" class="required block text-sm font-semibold text-gray-800 mb-2"><i class="ri-time-line text-lg mr-1"></i> Tiempo viviendo aquí</label>
              <input id="tiempo_vivienda" x-model="form.tiempo_vivienda" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" placeholder="Ej: 5 años / 8 meses" required />
            </div>
            <div>
              <label for="tenencia_vivienda" class="required block text-sm font-semibold text-gray-800 mb-2"><i class="ri-home-line text-lg mr-1"></i> Tenencia de la vivienda</label>
              <select id="tenencia_vivienda" x-model="form.tenencia_vivienda" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required>
                <option value="">Seleccione</option>
                <option>Propia</option>
                <option>Familiar</option>
                <option>Arrendada</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-4 pt-4 border-t border-gray-200">
            <div>
              <label for="telefono_fijo" class="block text-sm font-semibold text-gray-800 mb-2"><i class="ri-phone-line text-lg mr-1"></i> Teléfono fijo</label>
              <input id="telefono_fijo" x-model="form.telefono_fijo" type="tel" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" />
            </div>
            <div>
              <label for="celular" class="required block text-sm font-semibold text-gray-800 mb-2"><i class="ri-smartphone-line text-lg mr-1"></i> Celular</label>
              <input id="celular" x-model="form.celular" type="tel" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required />
            </div>
            <div>
              <label for="email" class="required block text-sm font-semibold text-gray-800 mb-2"><i class="ri-mail-line text-lg mr-1"></i> Correo Personal</label>
              <input id="email" x-model="form.email" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required />
            </div>
          </div>
        </section>

        <section class="bg-white rounded-lg p-5 shadow-sm border border-gray-100">
          <h2 class="text-lg font-semibold text-gray-900 pb-2 mb-3 border-b-2 border-gray-900 flex items-center">
            <i class="ri-group-line text-xl mr-2"></i>
            Relación con la Empresa
          </h2>

          <div class="p-4 border border-gray-300 rounded-lg bg-gray-50">
              <h3 class="text-base font-semibold text-gray-900 flex items-center mb-2"><i class="ri-global-line mr-2"></i> Relación con el GRUPO TECNOGLASS</h3>
              <p class="text-xs text-gray-700 mb-3">¿Tiene relación de consanguinidad, afinidad, sentimental con algún empleado, directivo, administrador, accionista, empresa de la competencia, cliente o proveedor en C.I. GRUPO TECNOGLASS? GRUPO TECNOGLASS: TECNOGLASS INC., TECNOGLASS S.A.S., ENERGÍA SOLAR, etc.</p>
              <div class="flex gap-6 mt-3">
                  <label class="inline-flex items-center text-sm font-medium text-gray-800">
                      <input type="radio" x-model="form.relacion_tecnoglass" value="Si" name="relacion_tecnoglass" class="h-4 w-4 text-gray-900 border-gray-400 focus:ring-gray-900" required />
                      <span class="ml-2">SÍ</span>
                  </label>
                  <label class="inline-flex items-center text-sm font-medium text-gray-800">
                      <input type="radio" x-model="form.relacion_tecnoglass" value="No" name="relacion_tecnoglass" class="h-4 w-4 text-gray-900 border-gray-400 focus:ring-gray-900" required />
                      <span class="ml-2">NO</span>
                  </label>
              </div>
              <div x-show="form.relacion_tecnoglass === 'Si'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mt-3 p-3 bg-white rounded-lg border border-gray-200">
                  <div><label class="required block text-sm font-semibold text-gray-800 mb-2">Nombre</label><input x-model="form.relacion_tecnoglass_detalle.nombre" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" :required="form.relacion_tecnoglass === 'Si'" /></div>
                  <div><label class="required block text-sm font-semibold text-gray-800 mb-2">Cédula</label><input x-model="form.relacion_tecnoglass_detalle.cedula" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" :required="form.relacion_tecnoglass === 'Si'" /></div>
                  <div><label class="required block text-sm font-semibold text-gray-800 mb-2">Cargo</label><input x-model="form.relacion_tecnoglass_detalle.cargo" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" :required="form.relacion_tecnoglass === 'Si'" /></div>
                  <div><label class="required block text-sm font-semibold text-gray-800 mb-2">Tipo de relación</label><input x-model="form.relacion_tecnoglass_detalle.tipo_relacion" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" :required="form.relacion_tecnoglass === 'Si'" /></div>
              </div>
          </div>
        </section>


        <section class="bg-white rounded-lg p-5 shadow-sm border border-gray-100">
          <h2 class="text-lg font-semibold text-gray-900 pb-2 mb-3 border-b-2 border-gray-900 flex items-center">
            <i class="ri-parent-line text-xl mr-2"></i>
            2. Información Familiar
          </h2>

          <h3 class="text-base font-semibold text-gray-900 flex items-center mb-2"><i class="ri-user-heart-line mr-2"></i> Datos del Cónyuge / Compañero(a)</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
              <label for="nombre_conyuge" class="required block text-sm font-semibold text-gray-800 mb-2">Nombre</label>
              <input id="nombre_conyuge" x-model="form.nombre_conyuge" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" />
            </div>
            <div>
              <label for="cedula_conyuge" class="required block text-sm font-semibold text-gray-800 mb-2">Cédula N°</label>
              <input id="cedula_conyuge" x-model="form.cedula_conyuge" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" />
            </div>
            <div>
              <label for="ocupacion_conyuge" class="required block text-sm font-semibold text-gray-800 mb-2">Ocupación laboral</label>
              <input id="ocupacion_conyuge" x-model="form.ocupacion_conyuge" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" />
            </div>
            <div>
              <label for="email_conyuge" class="required block text-sm font-semibold text-gray-800 mb-2">E-mail cónyuge</label>
              <input id="email_conyuge" x-model="form.email_conyuge" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" />
            </div>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-4">
            <div>
              <label for="telefono_conyuge" class="required block text-sm font-semibold text-gray-800 mb-2">Teléfono cónyuge</label>
              <input id="telefono_conyuge" x-model="form.telefono_conyuge" type="tel" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" />
            </div>
            <div>
              <label for="numero_hijos" class="required block text-sm font-semibold text-gray-800 mb-2">Número de hijos</label>
              <input id="numero_hijos" x-model="form.numero_hijos" type="number" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required />
            </div>
          </div>

          <hr class="my-5 border-gray-300" />

          <h3 class="text-base font-semibold text-gray-900 flex items-center mb-2"><i class="ri-first-aid-kit-line mr-2"></i> Contacto de Emergencia</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
              <label class="required block text-sm font-semibold text-gray-800 mb-2">Nombre y Apellido</label>
              <input x-model="form.contacto.nombre" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required />
            </div>
            <div>
              <label class="required block text-sm font-semibold text-gray-800 mb-2">Identificación</label>
              <input x-model="form.contacto.identificacion" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required />
            </div>
            <div>
              <label class="required block text-sm font-semibold text-gray-800 mb-2">Parentesco</label>
              <input x-model="form.contacto.parentesco" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required />
            </div>
            <div>
              <label class="required block text-sm font-semibold text-gray-800 mb-2">Teléfonos</label>
              <input x-model="form.contacto.telefono" type="tel" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required />
            </div>
            <div>
              <label class="required block text-sm font-semibold text-gray-800 mb-2">Ocupación</label>
              <input x-model="form.contacto.ocupacion" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required />
            </div>
          </div>
        </section>

        <section class="bg-white rounded-lg p-5 shadow-sm border border-gray-100">
          <h2 class="text-lg font-semibold text-gray-900 pb-2 mb-3 border-b-2 border-gray-900 flex items-center">
            <i class="ri-child-line text-xl mr-2"></i>
            Detalle de Hijos
          </h2>
          <div class="space-y-3">
            <template x-for="(hijo, idx) in form.hijos" :key="idx">
              <div class="grid grid-cols-1 sm:grid-cols-7 gap-3 items-end p-3 rounded-lg border border-gray-300 bg-gray-50">
                <div class="col-span-1 sm:col-span-2">
                  <label class="required block text-sm font-semibold text-gray-800 mb-2">Nombre y Apellido</label>
                  <input x-model="hijo.nombre" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required />
                </div>
                <div class="col-span-1">
                  <label class="required block text-sm font-semibold text-gray-800 mb-2">Género</label>
                  <select x-model="hijo.sexo" ="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required>
                    <option value="">--</option>
                    <option value="M">M</option>
                    <option value="F">F</option>
                  </select>
                </div>
                <div class="col-span-1">
                  <label class="required block text-sm font-semibold text-gray-800 mb-2">F. Nacimiento</label>
                  <input x-model="hijo.fecha_nacimiento" type="date" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required />
                </div>
                <div class="col-span-1">
                  <label class="required block text-sm font-semibold text-gray-800 mb-2">Ocupación</label>
                  <select x-model="hijo.ocupacion" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required>
                    <option value="">Selec.</option>
                    <option>Empleado</option>
                    <option>Estudiante</option>
                    <option>Otro</option>
                  </select>
                </div>
                <div class="col-span-1">
                  <label class="required block text-sm font-semibold text-gray-800 mb-2">Tipo (*)</label>
                  <select x-model="hijo.tipo" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required>
                    <option value="">--</option>
                    <option value="Hijo(a)">Hijo(a)</option>
                    <option value="Hijastro(a)">Hijastro(a)</option>
                  </select>
                </div>
                <div class="col-span-1 flex justify-end">
                  <button type="button" @click="removeHijo(idx)" class="w-full px-2 py-1.5 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition duration-150 flex items-center justify-center text-sm font-medium">
                    <i class="ri-delete-bin-line"></i>
                  </button>
                </div>
              </div>
            </template>

            <div>
              <button type="button" @click="addHijo()" class="px-4 py-1.5 bg-black text-white rounded-lg hover:bg-gray-800 transition duration-150 flex items-center font-semibold text-sm shadow-md">
                <i class="ri-add-line mr-1"></i> Agregar hijo
              </button>
            </div>
          </div>
        </section>

        <section class="bg-white rounded-lg p-5 shadow-sm border border-gray-100">
          <h2 class="text-lg font-semibold text-gray-900 pb-2 mb-3 border-b-2 border-gray-900 flex items-center">
            <i class="ri-book-2-line text-xl mr-2"></i>
            3. Estudios
          </h2>
          <p class="text-xs text-gray-700 mb-4">Diligencie su **último nivel de estudio alcanzado (obligatorio)**.</p>

          <h3 class="text-base font-semibold text-gray-900 flex items-center mb-2"><i class="ri-graduation-cap-line mr-2"></i> Último Nivel de Estudio Alcanzado</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end p-4 rounded-lg bg-gray-100 border border-gray-300">
            <div>
              <label class="required block text-sm font-semibold text-gray-800 mb-2">Nivel</label>
              <select x-model="form.ultimo_estudio.nivel" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required>
                <option value="">Seleccione</option>
                <option>Bachiller</option>
                <option>Técnico</option>
                <option>Tecnólogo</option>
                <option>Profesional</option>
                <option>Diplomado</option>
                <option>Especialización</option>
                <option>Maestría</option>
              </select>
            </div>
            <div>
              <label class="required block text-sm font-semibold text-gray-800 mb-2">Énfasis / Título</label>
              <input x-model="form.ultimo_estudio.enfasis" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required />
            </div>
            <div>
              <label class="required block text-sm font-semibold text-gray-800 mb-2">Institución</label>
              <input x-model="form.ultimo_estudio.institucion" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required />
            </div>
          </div>
          <hr class="my-5 border-gray-300" />

          <h3 class="text-base font-semibold text-gray-900 flex items-center mb-2"><i class="ri-book-open-line mr-2"></i> Estudios Posteriores (Opcional)</h3>
          <div class="space-y-3">
            <template x-for="(estudio, idx) in form.otros_estudios_list" :key="idx">
              <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end p-3 rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="sm:col-span-3">
                  <label class="required block text-sm font-semibold text-gray-800 mb-2">Nombre del Estudio / Curso</label>
                  <input x-model="estudio.nombre" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required />
                </div>
                <div class="flex justify-end">
                  <button type="button" @click="removeOtroEstudio(idx)" class="w-full px-2 py-1.5 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition duration-150 flex items-center justify-center text-sm font-medium">
                    <i class="ri-delete-bin-line"></i>
                  </button>
                </div>
              </div>
            </template>
            <div>
              <button type="button" @click="addOtroEstudio()" class="px-4 py-1.5 bg-black text-white rounded-lg hover:bg-gray-800 transition duration-150 flex items-center font-semibold text-sm shadow-md">
                <i class="ri-add-line mr-1"></i> Agregar otro estudio/curso
              </button>
            </div>
          </div>
        </section>

        <section class="bg-white rounded-lg p-5 shadow-sm border border-gray-100">
          <h2 class="text-lg font-semibold text-gray-900 pb-2 mb-3 border-b-2 border-gray-900 flex items-center">
            <i class="ri-pantone-line text-xl mr-2"></i>
            4. Tallas y Afiliaciones
          </h2>
          
          <h3 class="text-base font-semibold text-gray-900 flex items-center mb-2"><i class="ri-t-shirt-line mr-2"></i> Tallas</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
              <label class="required block text-sm font-semibold text-gray-800 mb-2">Talla Pantalón</label>
              <input x-model="form.tallas.pantalon" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required />
            </div>
            <div>
              <label class="required block text-sm font-semibold text-gray-800 mb-2">Talla Camisa</label>
              <input x-model="form.tallas.camisa" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required />
            </div>
            <div>
              <label class="required block text-sm font-semibold text-gray-800 mb-2">Talla Zapatos</label>
              <input x-model="form.tallas.zapatos" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required />
            </div>
            <div>
              <label class="required block text-sm font-semibold text-gray-800 mb-2">¿Tiene carnet de A.R.L?</label>
              <select x-model="form.tiene_carnet_arl" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required>
                <option value="">--</option>
                <option>Si</option>
                <option>No</option>
              </select>
            </div>
          </div>
          
          <hr class="my-5 border-gray-300" />
          
          <h3 class="text-base font-semibold text-gray-900 flex items-center mb-2"><i class="ri-hospital-line mr-2"></i> Afiliaciones</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
              <label class="required block text-sm font-semibold text-gray-800 mb-2">E.P.S</label>
              <input x-model="form.afiliaciones.eps" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required />
            </div>
            <div>
              <label class="required block text-sm font-semibold text-gray-800 mb-2">Fondo de pensiones</label>
              <input x-model="form.afiliaciones.fondo_pensiones" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required />
            </div>
            <div>
              <label class="required block text-sm font-semibold text-gray-800 mb-2">A.R.L</label>
              <input x-model="form.afiliaciones.arl" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required />
            </div>
            <div>
              <label class="required block text-sm font-semibold text-gray-800 mb-2">¿Tiene seguro de exequias?</label>
              <select x-model="form.seguro_exequias" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required>
                <option value="">--</option>
                <option>Si</option>
                <option>No</option>
              </select>
            </div>
          </div>
        </section>

        <section class="bg-white rounded-lg p-5 shadow-sm border border-gray-100">
          <h2 class="text-lg font-semibold text-gray-900 pb-2 mb-3 border-b-2 border-gray-900 flex items-center">
            <i class="ri-health-line text-xl mr-2"></i>
            5. Antecedentes Médicos
          </h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
              <label class="required block text-sm font-semibold text-gray-800 mb-2">Presión Arterial</label>
              <select x-model="form.medicos.presion_arterial_opcion" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required>
                <option value="">--</option>
                <option value="Si">Si</option>
                <option value="No">No</option>
              </select>
            </div>
            <div>
              <label class="required block text-sm font-semibold text-gray-800 mb-2">Diabetes</label>
              <select x-model="form.medicos.diabetes" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required>
                <option value="">--</option>
                <option value="Si">Si</option>
                <option value="No">No</option>
              </select>
            </div>
            <div class="lg:col-span-2">
              <label class="required block text-sm font-semibold text-gray-800 mb-2">Alergias</label>
              <input x-model="form.medicos.alergias" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" placeholder="Especifique si Sí o No y el tipo" required />
            </div>
            <div class="lg:col-span-4">
              <label class="block text-sm font-semibold text-gray-800 mb-2">Otra condición médica</label>
              <input x-model="form.medicos.otra_condicion" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" placeholder="Especifique si Sí o No y cuál" />
            </div>
            <div class="lg:col-span-4">
              <label class="block text-sm font-semibold text-gray-800 mb-2">Observaciones generales sobre la salud</label>
              <textarea x-model="form.medicos.observaciones" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" rows="2" placeholder="Información adicional relevante..."></textarea>
            </div>
          </div>
        </section>

        <section class="bg-white rounded-lg p-5 shadow-sm border border-gray-100">
          <h2 class="text-lg font-semibold text-gray-900 pb-2 mb-3 border-b-2 border-gray-900 flex items-center">
            <i class="ri-file-upload-line text-xl mr-2"></i>
            Documentos
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
            
            <!-- Columna 1: Input de Archivo -->
            <div>
              <label class="required block text-sm font-semibold text-gray-800 mb-2"><i class="ri-image-line text-lg mr-1"></i> Fotografía (máximo 1 mes, tipo carnet 3:4, fondo blanco)</label>
              <input required type="file" x-ref="photoInput" name="fotografia" accept="image/*" @change="previewPhoto($event)" class="w-full mt-1 border border-gray-300 p-1.5 rounded-lg bg-gray-50 text-sm file:py-1 file:px-2 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300 transition-colors" />
            </div>
            
            <!-- Columna 2: Select Carnet -->
            <div>
              <label class="required block text-sm font-semibold text-gray-800 mb-2"><i class="ri-id-card-line text-lg mr-1"></i> Carnet identificación empresa</label>
              <select x-model="form.carnet_empresa" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-normal focus:border-gray-900 focus:ring-1 focus:ring-gray-900 transition-colors" required>
                <option value="">--</option>
                <option>Si</option>
                <option>No</option>
              </select>
            </div>
            
            <!-- Columna 3: Vista Previa y Mensajes de Error -->
            <div class="flex justify-center">
              <div 
                class="h-28 w-28 flex items-center justify-center p-2 rounded-lg shadow-lg border-2 text-center transition-colors duration-300" 
                :class="{
                    'border-4 border-red-600 bg-red-50 text-red-700': photoErrorMessage,
                    'border-4 border-black bg-white': !photoErrorMessage && photoPreview,
                    'border-dashed border-gray-400 bg-gray-100 text-gray-500': !photoErrorMessage && !photoPreview
                }"
              >
                <!-- 1. Si hay vista previa (éxito) -->
                <template x-if="photoPreview && !photoErrorMessage">
                    <img :src="photoPreview" class="h-full w-full object-cover rounded-md" alt="Vista previa de foto" />
                </template>
                <!-- 2. Si hay un mensaje de error (se muestra en el centro) -->
                <template x-if="photoErrorMessage">
                    <div class="text-xs font-semibold leading-tight p-1" x-text="photoErrorMessage"></div>
                </template>
                <!-- 3. Si no hay nada (placeholder) -->
                <template x-if="!photoPreview && !photoErrorMessage">
                    <i class="ri-image-add-line text-4xl"></i>
                </template>
              </div>
            </div>
          </div>
        </section>

        <section class="bg-white rounded-lg p-5 shadow-sm border border-gray-100">
          <h2 class="text-lg font-semibold text-gray-900 pb-2 mb-3 border-b-2 border-gray-900 flex items-center">
            <i class="ri-shield-check-line text-xl mr-2"></i>
            6. Protección de Datos Personales
          </h2>
          <p class="text-sm text-gray-700 mb-4">Mediante su firma en este documento autoriza el tratamiento de sus datos personales conforme a lo establecido en la <a class="text-blue-500 cursor-pointer" href='https://es-metals.com/es/nosotros/'>política interna de C.I. ES METALS S.A.S.</a></p>
          <div class="mt-3">
            <label class="required inline-flex items-center text-sm cursor-pointer font-normal">
              <input type="checkbox" x-model="form.autorizo" class="h-4 w-4 text-gray-900 border-gray-400 rounded focus:ring-gray-900" required /> 
              <span class="ml-3 font-extrabold text-gray-900 text-sm">Acepto y autorizo el tratamiento de mis datos personales.</span>
            </label>
          </div>
        </section>

        <div class="flex justify-end gap-3 pt-5 border-t border-gray-300">
          <button
            class="px-6 py-2.5 bg-black text-white rounded-lg font-extrabold hover:bg-gray-800 transition duration-300 shadow-xl shadow-gray-400/50 flex items-center text-sm uppercase tracking-widest"
            type="submit" >
            <i class="ri-send-plane-line mr-2"></i> ENVIAR ACTUALIZACIÓN
          </button>
        </div>

        <div x-show="serverMessage" class="mt-4 p-3 bg-gray-100 border border-gray-300 text-gray-800 rounded-lg font-medium shadow-sm" x-text="serverMessage"></div>

      </main>
    </form>
    </div>


<script>
function formApp() {
    const baseOtroEstudio = () => ({ nombre: '' });
    const baseHijo = () => ({ nombre:'', sexo:'', fecha_nacimiento:'', ocupacion:'', tipo:'' });

    const getInitialFormState = () => ({
        tipo_sangre: '', lugar_nacimiento: '', fecha_nacimiento: '', sexo: '', 
        licencia_conduccion: 'No', categoria_licencia: '', libreta_militar: 'No', 
        tiene_vehiculo: 'No', tipo_vehiculo: '', placa_vehiculo: '',
        estado_civil: '', direccion: '', barrio: '', municipio: '', departamento: '', 
        estrato_socioeconomico: '', tiempo_vivienda: '', tenencia_vivienda: '', 
        telefono_fijo: '', celular: '', email: '', 
        relacion_tecnoglass: 'No', 
        relacion_tecnoglass_detalle: { nombre: '', cedula: '', cargo: '', tipo_relacion: '' }, 
        nombre_conyuge: '', cedula_conyuge: '', ocupacion_conyuge: '', email_conyuge: '', 
        telefono_conyuge: '', numero_hijos: 0, 
        contacto: { nombre: '', identificacion: '', parentesco: '', telefono: '', ocupacion: '' }, 
        hijos: [], 
        ultimo_estudio: { nivel: '', enfasis: '', institucion: '' },
        otros_estudios_list: [], 
        estudios_post_vinculacion: '', 
        tallas: { pantalon: '', camisa: '', zapatos: '' }, 
        tiene_carnet_arl: '', 
        afiliaciones: { eps: '', fondo_pensiones: '', arl: '' }, 
        seguro_exequias: '', 
        medicos: { alergias: '', diabetes: '', presion_arterial_opcion: '', otra_condicion: '', observaciones: '' }, 
        carnet_empresa: '', 
        autorizo: false
    });

    return {
        photoPreview: null,
        photoErrorMessage: '', 
        serverMessage: '',
        form: getInitialFormState(),
        
        resetPhotoInput(message) {
            this.photoErrorMessage = message;
            this.photoPreview = null;
            if (this.$refs.photoInput) {
                this.$refs.photoInput.value = '';
            }
        },

        previewPhoto(event) {
            this.photoErrorMessage = ''; 
            const file = event.target.files[0];

            if (!file) {
                this.resetPhotoInput('');
                return;
            }

            // Validación mínima de tipo de archivo
            if (!file.type.match('image/jpeg') && !file.type.match('image/png')) {
                this.resetPhotoInput('❌ Solo se permiten imágenes JPG o PNG.');
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                const img = new Image();
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    
                    // Definimos el tamaño final (proporción 3:4 estándar para fotos de perfil)
                    const targetWidth = 300; 
                    const targetHeight = 400;

                    canvas.width = targetWidth;
                    canvas.height = targetHeight;

                    // Lógica para rellenar el canvas (Object-fit: cover)
                    // Esto evita que la imagen se estire o se vea flaca si no es 3:4
                    let scale = Math.max(targetWidth / img.width, targetHeight / img.height);
                    let drawWidth = img.width * scale;
                    let drawHeight = img.height * scale;
                    let offsetX = (targetWidth - drawWidth) / 2;
                    let offsetY = (targetHeight - drawHeight) / 2;
                    
                    // Dibujar la imagen procesada
                    ctx.drawImage(img, offsetX, offsetY, drawWidth, drawHeight);

                    // --- COMPRESIÓN ---
                    // Convertimos a JPEG con calidad 0.8 (80%) para reducir el peso
                    const processedDataUrl = canvas.toDataURL('image/jpeg', 0.8);
                    
                    this.photoPreview = processedDataUrl;
                    this.photoErrorMessage = ''; 
                };
                img.onerror = () => {
                    this.resetPhotoInput('❌ Error al procesar el archivo de imagen.');
                }
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        },

        addHijo() {
            this.form.hijos.push(baseHijo());
        },

        removeHijo(idx) { 
            this.form.hijos.splice(idx, 1);
        },

        addOtroEstudio() {
            this.form.otros_estudios_list.push(baseOtroEstudio());
        },

        removeOtroEstudio(idx) { 
            this.form.otros_estudios_list.splice(idx, 1);
        },

        onAfterSubmit(event) {
            if (event.detail.successful) {
                 this.serverMessage = '✅ ¡Datos actualizados exitosamente!';
            } else {
                 this.serverMessage = '❌ Error al actualizar. Revise los campos.';
            }

            setTimeout(() => {
                this.serverMessage = '';
            }, 6000);
        }
    }
}
</script>
</body>
</html>