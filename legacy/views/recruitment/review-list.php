<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>List Review</title>
  </head>

  <body class="bg-gray-100">
    <div class="max-w-md mx-auto mt-10 bg-white p-6 rounded-xl shadow-lg">
      <p class="text-gray-700 mb-6 text-lg font-semibold">List Review</p>

        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Name:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->name ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">CC:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->cc ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Phone:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->phone ?></div>
        </div>
                <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Email:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->email ?></div>
        </div>

      <form id="decisionForm" method="POST" action="?c=Recruitment&a=ProcessDecisionList" class="space-y-4 mt-4">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id->id) ?>">

        <div>
          <label for="list" class="block mb-1 font-medium text-gray-600">
            Review Result:
          </label>
          <select required
            id="list"
            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
          >
            <option value=""></option>
            <option value="1">Aprobado</option>
            <option value="0">Rechazado</option>
          </select>
        </div>

        <!-- Campo de texto oculto inicialmente -->
        <div id="observacionesDiv" class="hidden">
          <label for="observaciones" class="block mb-1 font-medium text-gray-600">
            Observaciones:
          </label>
          <textarea
            id="observaciones"
            rows="3"
            placeholder="Escriba sus observaciones aquí..."
            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"
          ></textarea>
        </div>

        <!-- Campo oculto que se enviará con el valor final -->
        <input type="hidden" name="candidate_list" id="listValue" />

        <div class="text-right">
          <button
            type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow"
          >
            Guardar
          </button>
        </div>
      </form>
    </div>

    <script>
      const select = document.getElementById("list");
      const obsDiv = document.getElementById("observacionesDiv");
      const obsText = document.getElementById("observaciones");
      const listValue = document.getElementById("listValue");

      select.addEventListener("change", () => {
        if (select.value === "Observaciones") {
          obsDiv.classList.remove("hidden");
          listValue.value = ""; // limpiar
        } else {
          obsDiv.classList.add("hidden");
          listValue.value = select.value;
        }
      });

      // Actualiza el valor final antes de enviar
      document.getElementById("decisionForm").addEventListener("submit", (e) => {
        if (select.value === "Observaciones") {
          listValue.value = obsText.value.trim();
        }
      });
    </script>
  </body>
</html>
