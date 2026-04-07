<!doctype html>
<html>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  </head>

  <body class="bg-gray-100">
    <div class="max-w-full m-4 bg-white p-6 rounded-xl shadow-lg">



<div class="p-4">
    <div class="mb-4 pb-2.5 border-b border-dashed border-gray-200 last:border-b-0 last:mb-0 last:pb-0">
        <div class="flex items-center space-x-2 mb-2">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1">
                <i class="ri-information-line text-xl"></i>
                <span>Detailed Description & Additional Information</span>
            </h3>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->others ?></div>
        </div>
    </div>
    <div class="mb-4 pb-2.5 border-b border-dashed border-gray-200 last:border-b-0 last:mb-0 last:pb-0">
        <div class="flex items-center space-x-2 mb-2">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1">
                <i class="ri-information-line text-xl"></i>
                <span>Basic Information</span>
            </h3>
        </div>

        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Requester:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->username ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Profile:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->profile ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Schedule:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->schedule ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Experience:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->experience ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Qty:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->qty ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Contract:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->contract ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Salary:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->srange ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">City:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->city ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Cause:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->cause ?></div>
        </div>
        <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Replaces:</div>
            <div class="font-medium text-gray-900 flex-1 break-words"><?= $id->replaces ?></div>
        </div>
        <!-- <div class="flex text-xs mb-1 items-start">
            <div class="w-24 text-gray-600 shrink-0">Candidates:</div>
            <div class="font-medium text-blue-500 flex-1 break-words"><a target='_blank' href="https://sigma.es-metals.com/sigma/uploads/recruitment/<?= $id->id ?>/cv.zip"><i class="ri-file-line"></i> Files</a></div>
        </div> -->
    </div>
    <div class="mb-4 pb-2.5 border-b border-dashed border-gray-200 last:border-b-0 last:mb-0 last:pb-0">
        <div class="flex items-center space-x-2 mb-2">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1">
                <i class="ri-book-line text-xl"></i>
                <span>Education</span>
            </h3>
        </div>
        <?php
        $education = json_decode($this->model->get('content', 'job_profile_items', "and jp_id = $id->profile_id and kind = 'Educación'")->content, true);

            if (! empty($education)) { ?>
            <?php foreach ($education as $row) { ?>
                <?php if (! empty(trim($row[1]))) { ?>
                    <div class="flex text-xs mb-1 items-start">
                        <div class="w-24 text-gray-600 shrink-0"><?= htmlspecialchars($row[0]) ?>:</div>
                        <div class="font-medium text-gray-900 flex-1 break-words"><?= htmlspecialchars($row[1]) ?></div>
                    </div>
                <?php } ?>
            <?php } ?>
        <?php } ?>
    </div>

    <div class="mb-4 pb-2.5 border-b border-dashed border-gray-200 last:border-b-0 last:mb-0 last:pb-0">
        <div class="flex items-center space-x-2 mb-2">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1">
                <i class="ri-graduation-cap-line text-xl"></i>
                <span>Formation</span>
            </h3>
        </div>

        <?php
            $formation = json_decode(
                $this->model->get('content', 'job_profile_items', "and jp_id = $id->profile_id and kind = 'Formación'")->content,
                true
            );

            if (! empty($formation)) { ?>
            <?php foreach ($formation as $row) { ?>
                <?php if (! empty(trim($row[0]))) { ?>
                    <div class="flex text-xs mb-1 items-start">
                        <i class="ri-checkbox-circle-line text-blue-500 mr-1 mt-[2px]"></i>
                        <div class="font-medium text-gray-900 flex-1 break-words"><?= htmlspecialchars($row[0]) ?></div>
                    </div>
                <?php } ?>
            <?php } ?>
        <?php } ?>
    </div>
<div class="mb-4 pb-2.5 border-b border-dashed border-gray-200 last:border-b-0 last:mb-0 last:pb-0">
        <div class="flex items-center space-x-2 mb-2">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center space-x-1">
                <i class="ri-tools-line text-xl text-blue-600"></i>
                <span>Requested Resources</span>
            </h3>
        </div>
        
        <div class="text-xs space-y-2">
            <?php
                // Intentamos decodificar el JSON de recursos
                $resources = json_decode($id->resources ?? '[]', true);

            if (! empty($resources) && is_array($resources)) { ?>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($resources as $res) {
                        // Si es el JSON plano del Save, $res es un string.
                        // Si ya pasó por Update, $res es un array con ticket_id.
                        $name = is_array($res) ? $res['name'] : $res;
                        $hasTicket = is_array($res) && ! empty($res['ticket_id']);
                        ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                            <i class="ri-checkbox-circle-line mr-1"></i>
                            <?= htmlspecialchars($name) ?>
                            <?php if ($hasTicket) { ?>
                                <span class="ml-1 text-[10px] text-blue-600 font-bold">(#<?= $res['ticket_id'] ?>)</span>
                            <?php } ?>
                        </span>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <p class="text-gray-400 italic">No resources requested.</p>
            <?php } ?>
        </div>
    </div>

</div>


      <p class="text-gray-700 mb-6">¿Deseas aprobar o rechazar la solicitud?</p>

      <form id="decisionForm" method="POST" action="?c=Recruitment&a=ProcessDecision">
        <input type="hidden" name="recruitmentId" value="<?= htmlspecialchars($id->id) ?>">

        <div class="flex space-x-4">
          <button type="button" id="approveBtn"
            class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700">
            Aprobar
          </button>

          <button type="button" id="rejectBtn"
            class="flex-1 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700">
            Rechazar
          </button>
        </div>

        <div id="rejectNote" class="mt-4 hidden">
          <label class="block mb-2 text-gray-700">Motivo del rechazo:</label>
          <textarea name="note" id="note" rows="3" class="w-full border rounded-lg p-2"></textarea>

          <button type="submit" id="sendReject" name="decision" value="rejected"
            class="mt-3 w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700">
            Enviar rechazo
          </button>
        </div>

        <input type="hidden" name="decision" id="decision" value="">
      </form>
    </div>

    <script>
      const approveBtn = document.getElementById('approveBtn');
      const rejectBtn = document.getElementById('rejectBtn');
      const rejectNote = document.getElementById('rejectNote');
      const sendReject = document.getElementById('sendReject');
      const decisionInput = document.getElementById('decision');
      const form = document.getElementById('decisionForm');
      const note = document.getElementById('note');

      // Aprobar
      approveBtn.addEventListener('click', function () {
        if (confirm('¿Confirmar aprobación?')) {
          decisionInput.value = 'approved';
          form.submit();
        }
      });

      // Mostrar campo de rechazo
      rejectBtn.addEventListener('click', function () {
        rejectNote.classList.remove('hidden');
      });

      // Validar motivo antes de enviar rechazo
      sendReject.addEventListener('click', function (e) {
        if (note.value.trim() === '') {
          e.preventDefault();
          alert('Por favor ingresa el motivo del rechazo antes de enviar.');
        } else {
          decisionInput.value = 'rejected';
        }
      });
    </script>
  </body>
</html>
