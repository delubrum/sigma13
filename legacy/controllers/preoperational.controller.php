<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use thiagoalessio\TesseractOCR\TesseractOCR;

class PreoperationalController
{
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    private const UPLOAD_DIR = 'uploads/preoperational/';

    private const CHECKLIST_LIMIT = 1;

    private array $hxTriggers = [];

    public function __construct(
        private readonly Model $model,
        private readonly AuthService $auth
    ) {}

    private function sendHeaders(): void
    {
        if (empty($this->hxTriggers)) {
            return;
        }
        header('HX-Trigger: '.json_encode($this->hxTriggers));
    }

    private function triggerSuccess(string $message, string $close = ''): void
    {
        $this->hxTriggers['showMessage'] = [
            'type' => 'success',
            'message' => $message,
            'close' => $close,
        ];
    }

    /* ═══════════════════════════════════════════════════════════
       INDEX / DATA / EXPORT
    ═══════════════════════════════════════════════════════════ */

    public function Index(): void
    {
        $user = $this->auth->authorize(147);
        $tabulator = true;
        $title = 'Infrastructure / Preoperational';
        $button = 'New Preoperational';
        $content = 'app/components/list.php';
        $columns = '[
            { "title": "ID",      "field": "id",      "headerHozAlign": "center", "headerFilter": "input" },
            { "title": "Date",    "field": "date",    "headerHozAlign": "center", "headerFilter": customDateRangeFilter, "headerFilterFunc": customDateFilterFunc, "headerFilterLiveFilter": false },
            { "title": "Vehicle", "field": "vehicle", "headerHozAlign": "center", "headerFilter": "list" },
            { "title": "User",    "field": "user",    "headerHozAlign": "center", "headerFilter": "input" }
        ]';
        require_once 'app/views/index.php';
    }

    public function Data(): void
    {
        $user = $this->auth->authorize(147);
        $isExport = isset($_GET['export']);
        if (! $isExport) {
            header('Content-Type: application/json');
        }

        $page = (int) ($_GET['page'] ?? 1);
        $size = (int) ($_GET['size'] ?? 50);
        $offset = ($page - 1) * $size;

        $fieldMap = [
            'id' => 'a.id',
            'date' => 'a.created_at',
            'vehicle' => "CONCAT(v.hostname, ' || ', v.serial, ' || ', v.sap)",
            'user' => 'u.username',
            'status' => 'a.status',
        ];

        $where = " AND a.status <> 'draft'";
        if (isset($_GET['filter']) && is_array($_GET['filter'])) {
            foreach ($_GET['filter'] as $key => $f) {
                $field = is_array($f) ? ($f['field'] ?? '') : $key;
                $value = is_array($f) ? ($f['value'] ?? '') : $f;
                $value = addslashes((string) $value);
                if (! isset($fieldMap[$field])) {
                    continue;
                }
                $dbField = $fieldMap[$field];
                if ($field === 'date' && str_contains($value, ' to ')) {
                    [$from, $to] = explode(' to ', $value);
                    $where .= " AND DATE($dbField) BETWEEN '$from' AND '$to'";
                } else {
                    $where .= " AND $dbField LIKE '%$value%'";
                }
            }
        }

        $orderBy = 'a.id DESC';
        if (isset($_GET['sort'][0]['field'])) {
            $sortField = $_GET['sort'][0]['field'];
            $sortDir = strtoupper($_GET['sort'][0]['dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
            if (isset($fieldMap[$sortField])) {
                $orderBy = $fieldMap[$sortField]." $sortDir";
            }
        }

        $joins = 'LEFT JOIN users u ON a.user_id = u.id LEFT JOIN assets v ON a.vehicle_id = v.id';
        $select = "a.id, a.created_at AS date, a.status, CONCAT(v.hostname, ' || ', v.serial, ' || ', v.sap) AS vehicle, u.username AS user";

        if ($isExport) {
            $this->exportXlsx($select, $where, $orderBy, $joins);
            exit;
        }

        $total = $this->model->get('COUNT(a.id) AS total', 'preoperational a', $where, $joins)->total;
        $rows = $this->model->list($select, 'preoperational a', "$where ORDER BY $orderBy LIMIT $offset, $size", $joins);

        echo json_encode([
            'data' => $rows,
            'last_page' => (int) ceil(($total ?? 0) / $size),
            'last_row' => (int) ($total ?? 0),
        ]);
    }

    private function exportXlsx(string $select, string $where, string $orderBy, string $joins): void
    {
        setcookie('download_complete', 'true', time() + 30, '/');
        $rows = $this->model->list($select, 'preoperational a', "$where ORDER BY $orderBy", $joins);
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(['ID', 'Fecha Creación', 'Estado', 'Vehículo / Activo', 'Usuario'], null, 'A1');
        $exportData = array_map(fn ($r) => [
            $r->id,
            $r->date ? Date::PHPToExcel(strtotime($r->date)) : '',
            $r->status,
            $r->vehicle,
            $r->user,
        ], $rows);
        $sheet->fromArray($exportData, null, 'A2');
        $lastRow = count($exportData) + 1;
        $sheet->getStyle("B2:B$lastRow")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD2);
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setWidth($col === 'D' ? 40 : 18);
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Reporte_Preoperacionales_'.date('dmY').'.xlsx"');
        $writer = new Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);
        $writer->save('php://output');
    }

    /* ═══════════════════════════════════════════════════════════
       NEW / CHECKLIST
    ═══════════════════════════════════════════════════════════ */

    public function New(): void
    {
        $vehiculos = $this->model->list('*', 'assets', " AND area = 'Vehicles' ORDER BY hostname ASC");
        require_once 'app/views/preoperational/new.php';
    }

    public function Checklist(): void
    {
        $v_id = (int) ($_REQUEST['vehicle_id'] ?? 0);
        if (! $v_id) {
            header('HX-Trigger: {"lockKM": true}');
            echo '<p class="text-center text-gray-400 py-20 font-black uppercase text-xs italic">Seleccione unidad...</p>';

            return;
        }
        $id_preop = $this->getOrCreateDraft($v_id, $draft);
        $saved_items = $this->getSavedItems($id_preop);
        $checklist_data = $this->getChecklistByVehicle($v_id);
        $this->hxTriggers['setPreopId'] = (string) $id_preop;
        $this->hxTriggers['setKM'] = (string) ($draft->km ?? '0');
        $this->sendHeaders();
        require 'app/views/preoperational/checklist.php';
    }

    private function getOrCreateDraft(int $v_id, mixed &$draft): int
    {
        $u_id = (int) $_SESSION['id-SIGMA'];
        $draft = $this->model->get('id, km', 'preoperational', " AND user_id = $u_id AND vehicle_id = $v_id AND status = 'draft' LIMIT 1");
        if ($draft) {
            return (int) $draft->id;
        }

        return (int) $this->model->save('preoperational', (object) ['user_id' => $u_id, 'vehicle_id' => $v_id, 'status' => 'draft', 'created_at' => date('Y-m-d H:i:s'), 'km' => 0]);
    }

    private function getSavedItems(int $preop_id): array
    {
        $saved = [];
        $items = $this->model->list('*', 'preoperational_items', " AND preop_id = $preop_id");
        foreach ($items ?: [] as $item) {
            $saved[$item->question_id] = $item;
        }

        return $saved;
    }

    private function getChecklistByVehicle(int $v_id): array
    {
        $vehicle = $this->model->get('kind', 'assets', " AND id = $v_id");
        $limitSql = self::CHECKLIST_LIMIT > 0 ? (' LIMIT '.self::CHECKLIST_LIMIT) : '';
        $questions = $this->model->list('*', 'preoperational_questions', " AND kind = '{$vehicle->kind}' ORDER BY sort$limitSql");
        $grouped = [];
        foreach ($questions ?: [] as $q) {
            $grouped[$q->category][] = $q;
        }

        return $grouped;
    }

    /* ═══════════════════════════════════════════════════════════
       SAVE / FINALIZE / TICKETS
    ═══════════════════════════════════════════════════════════ */

    public function Save(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        if (! $id) {
            exit;
        }
        $this->saveAnswers($id);
        $this->triggerSuccess('Progreso guardado');
        $this->sendHeaders();
    }

    public function Finalize(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        if (! $id) {
            return;
        }

        $this->saveAnswers($id);
        $preop = $this->model->get('*', 'preoperational', "AND id = $id");
        if (! $preop) {
            return;
        }

        /**
         * CAMBIO CLAVE:
         * Buscamos ítems que estén 'Mal' (Correctivos)
         * O que sean las preguntas especiales 1 y 52 (Preventivos)
         */
        $items_to_process = $this->model->list('*', 'preoperational_items',
            "AND preop_id = $id AND (answer = 'Mal' OR question_id IN (1, 52))"
        );

        foreach ($items_to_process ?: [] as $item) {
            $question = $this->model->get('*', 'preoperational_questions', "AND id = $item->question_id");
            if (! $question) {
                continue;
            }

            $question_id = (int) $question->id;
            $is_corrective = ($question->ticket ?? 0) == 1 && $item->answer === 'Mal';
            $is_preventive = ($question_id === 1 || $question_id === 52);

            // Solo entramos si es un hallazgo "Mal" con ticket habilitado
            // O si es una de nuestras lecturas especiales
            if ($is_corrective || $is_preventive) {
                $mapping = $this->handleTicketCreation(
                    (int) $id,
                    (int) $preop->vehicle_id,
                    $question,
                    (string) ($item->obs ?? '')
                );

                if (! empty($mapping)) {
                    $this->model->update('preoperational_items', (object) ['ticket_ids' => json_encode($mapping)], (int) $item->id);
                }
            }
        }

        // Finalización del flujo
        $this->model->update('preoperational', (object) [
            'status' => 'completed',
            'updated_at' => date('Y-m-d H:i:s'),
        ], $id);

        $this->triggerSuccess('Preoperacional Finalizado', 'closeNewModal');
        $this->sendHeaders();
    }

    private function saveAnswers(int $preop_id): void
    {
        $changes = [];
        foreach ($_POST as $key => $value) {
            if (! preg_match('/^(question|obs)_(\d+)$/', $key, $m)) {
                continue;
            }
            $type = $m[1];
            $q_id = (int) $m[2];

            // FIX: Manejo de múltiples opciones (Checkboxes)
            // Si el valor es un array, lo unimos. Si ya existe algo en $changes para esta pregunta, no lo pisamos, lo acumulamos.
            $val = is_array($value) ? implode(', ', array_filter($value)) : trim((string) $value);

            if ($type === 'question') {
                $changes[$q_id]['answer'] = $val;
                if ($val === 'Bien') {
                    $changes[$q_id]['obs'] = '';
                }
            } else {
                // FIX: Guardar la observación sin importar el orden del POST
                $changes[$q_id]['obs'] = $val;
            }
        }

        foreach ($changes as $q_id => $data) {
            $this->upsertItem($preop_id, $q_id, $data);
        }
    }

    private function handleTicketCreation(int $preop_id, int $asset_id, object $question, string $user_obs): array
    {
        $question_id = (int) $question->id;

        // Si es Kilometraje (1) u Horómetro (52), derivamos a la lógica de preventivos
        if ($question_id === 1 || $question_id === 52) {
            return $this->handlePreventiveCreation($preop_id, $asset_id, $question_id, $user_obs);
        }

        // --- TU LÓGICA ORIGINAL DE CORRECTIVOS EMPIEZA AQUÍ ---
        $new_obs = array_filter(array_map('trim', explode(',', $user_obs)));
        if (empty($new_obs)) {
            $new_obs[] = 'Falla reportada sin detalle específico';
        }

        $ticket_mapping = [];
        $category = strtoupper($question->category ?? 'General');
        $is_option_type = ! empty(trim((string) ($question->items ?? '')));

        $existing = $this->model->list('id, description', 'mnt',
            "AND asset_id = '$asset_id' AND description LIKE '%-Q{$question->id} |%' AND status NOT IN ('Closed', 'Rejected')"
        ) ?: [];

        foreach ($new_obs as $obs) {
            $found_id = null;
            foreach ($existing as $t) {
                if ($is_option_type) {
                    if (str_contains($t->description, "HALLAZGO: $obs")) {
                        $found_id = (int) $t->id;
                        break;
                    }
                } else {
                    $found_id = (int) $t->id;
                    break;
                }
            }

            if (! $found_id) {
                $desc = "P{$preop_id}-Q{$question->id} | {$category} | {$question->question} | HALLAZGO: {$obs}";
                $new_id = $this->model->save('mnt', (object) [
                    'kind' => 'Machinery',
                    'facility' => $_SESSION['facility'] ?? 'ESM1',
                    'asset_id' => $asset_id,
                    'priority' => 'Medium',
                    'description' => $desc,
                    'user_id' => $_SESSION['id-SIGMA'] ?? null,
                    'status' => 'Open',
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                $found_id = (int) $new_id;
                $existing[] = (object) ['id' => $found_id, 'description' => $desc];
            }
            $ticket_mapping[$obs] = $found_id;
        }

        return $ticket_mapping;
    }

    private function handlePreventiveCreation(int $preop_id, int $asset_id, int $q_id, string $user_obs): array
    {
        $ticket_mapping = [];
        $lectura = (float) filter_var($user_obs, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        if ($lectura <= 0) {
            return [];
        }

        $label = ($q_id === 1) ? 'KM' : 'HRS';
        $planes = $this->model->list('*', 'preoperational_activities', "AND q_id = $q_id");

        foreach ($planes as $p) {
            $base = (int) $p->target_usage;
            if ($base <= 0) {
                continue;
            }

            $open = (int) ($p->opened_at ?? 200);
            $due = (int) ($p->due ?? 200);

            // 1. LIMPIEZA TOTAL: Si pasó el 'Vence', se le pone el 'end' sin importar el status.
            $this->closeObsoleteTickets($asset_id, (int) $p->id, $lectura);

            // 2. CICLO ACTUAL: Determinamos el target por rangos de influencia.
            $t1 = (int) ($base * floor($lectura / $base));
            $t2 = (int) ($base * ceil($lectura / $base));
            if ($t1 === 0) {
                $t1 = $base;
            }

            $target = null;
            if ($lectura >= ($t1 - $open) && $lectura <= ($t1 + $due)) {
                $target = $t1;
            } elseif ($lectura >= ($t2 - $open) && $lectura <= ($t2 + $due)) {
                $target = $t2;
            }

            // Si la lectura cae en espacio muerto entre servicios, saltamos.
            if ($target === null) {
                continue;
            }

            $rango_fin = $target + $due;
            $id = $this->syncPreventiveTicket($asset_id, $p, $target, $lectura, $label, $rango_fin);

            if ($id) {
                $ticket_mapping[$p->activity] = $id;
            }
        }

        return $ticket_mapping;
    }

    /**
     * Cierra tickets que se quedaron abiertos en el pasado basándose en el valor real de 'Vence'
     */
    private function closeObsoleteTickets(int $asset_id, int $activity_id, float $lectura_actual): void
    {
        // Eliminado el filtro de status y el string vacío '' para evitar el error 1525 de MySQL
        $condicion = "AND asset_id = $asset_id 
                  AND activity_id = $activity_id 
                  AND (scheduled_end IS NULL OR scheduled_end = '0000-00-00')";

        $pendientes = $this->model->list('id, activity', 'mnt_preventive', $condicion);

        if (empty($pendientes)) {
            return;
        }

        foreach ($pendientes as $t) {
            // Extraemos el límite de vencimiento configurado en el string del ticket
            if (preg_match('/Vence:\s*(\d+)/', $t->activity, $matches)) {
                $vencimiento_ticket = (int) $matches[1];

                // Si la lectura llegó al punto de vencimiento, el ticket se cierra administrativamente
                if ($lectura_actual >= $vencimiento_ticket) {
                    $this->model->update('mnt_preventive', (object) ['scheduled_end' => date('Y-m-d')], $t->id);
                }
            }
        }
    }

    private function syncPreventiveTicket($asset_id, $plan, $target, $lectura, $label, $rango_fin): ?int
    {
        $act_id = (int) $plan->id;
        $identificador = "Próximo: $target $label";

        $existing = $this->model->get('id, scheduled_end', 'mnt_preventive',
            "AND asset_id = $asset_id AND activity_id = $act_id AND activity LIKE '%$identificador%'"
        );

        $limite_alcanzado = ($lectura >= $rango_fin);

        if ($existing) {
            // Si ya tiene fecha de fin válida, no se modifica (Inmutabilidad)
            if (! empty($existing->scheduled_end) && $existing->scheduled_end != '0000-00-00') {
                return (int) $existing->id;
            }

            // Si se pasó del límite, se le pone el end sí o sí (sin importar status)
            if ($limite_alcanzado) {
                $this->model->update('mnt_preventive', (object) ['scheduled_end' => date('Y-m-d')], $existing->id);
            }

            return (int) $existing->id;
        }

        // Creación de ticket para el ciclo detectado
        $data = (object) [
            'preventive_id' => 0,
            'activity_id' => $act_id,
            'status' => 'Open',
            'kind' => 'Machinery',
            'scheduled_start' => date('Y-m-d'),
            // Si nace ya en el límite o por encima, nace cerrado
            'scheduled_end' => $limite_alcanzado ? date('Y-m-d') : null,
            'asset_id' => $asset_id,
            'activity' => "{$plan->activity} | Cada {$plan->target_usage} {$label} | $identificador | Vence: $rango_fin $label",
        ];

        $new_id = $this->model->save('mnt_preventive', $data);

        return $new_id ? (int) $new_id : null;
    }

    /* ═══════════════════════════════════════════════════════════
       UPLOAD / OCR (RESPETADO)
    ═══════════════════════════════════════════════════════════ */

    public function UploadPhoto(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        $q_id = (int) ($_POST['q_id'] ?? 0);
        $file_key = "foto_$q_id";

        // Definimos los IDs que requieren lectura de texto
        $IDS_CON_OCR = [1, 52]; // 1: Kilometraje, 52: Horómetro

        if (! $id || ! $q_id) {
            http_response_code(400);

            return;
        }

        $file = $_FILES[$file_key] ?? null;
        if (! $file || $file['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);

            return;
        }

        $dest = self::UPLOAD_DIR.uniqid('pre_')."_{$id}_{$q_id}.jpg";

        if (move_uploaded_file($file['tmp_name'], $dest)) {
            $dataToUpdate = ['url' => $dest];

            // Verificamos si el q_id actual está en nuestra lista de OCR
            if (in_array($q_id, $IDS_CON_OCR)) {
                try {
                    $tesseract = new TesseractOCR(realpath($dest));
                    if (file_exists('/usr/bin/tesseract')) {
                        $tesseract->executable('/usr/bin/tesseract');
                    }

                    // Ejecutamos OCR
                    $rawText = $tesseract->psm(7)->allowlist('0123456789')->run();
                    $valorDetectado = preg_replace('/\D/', '', $rawText);

                    // Si detectamos al menos 3 dígitos, guardamos en observaciones
                    if (strlen($valorDetectado) >= 1) { // Bajé a 1 por si el horómetro es bajo
                        $dataToUpdate['obs'] = $valorDetectado;
                        $this->hxTriggers['ocr-success'] = [
                            'q_id' => $q_id,
                            'valor' => $valorDetectado,
                            'tipo' => ($q_id === 1 ? 'Kilometraje' : 'Horómetro'),
                        ];
                    }
                } catch (Exception $e) {
                    error_log("Error OCR en ID $q_id: ".$e->getMessage());
                }
            }

            $this->upsertItem($id, $q_id, $dataToUpdate);
            $this->triggerSuccess('Foto procesada');
            $this->sendHeaders();

            echo '<img src="'.htmlspecialchars($dest).'?t='.time().'" class="w-full h-full object-cover">';
            exit;
        }
    }

    private function upsertItem(int $preop_id, int $q_id, array $data): void
    {
        $existing = $this->model->get('id', 'preoperational_items', " AND preop_id = $preop_id AND question_id = $q_id");
        if ($existing) {
            $this->model->update('preoperational_items', (object) $data, (int) $existing->id);
        } else {
            $data['preop_id'] = $preop_id;
            $data['question_id'] = $q_id;
            $this->model->save('preoperational_items', (object) $data);
        }
    }

    public function Detail(): void
    {
        $id = $this->model->get(
            'a.id AS idd, a.*, u.username AS user, v.hostname, v.brand, v.serial',
            'preoperational a',
            'AND a.id = '.(int) $_REQUEST['id'],
            'LEFT JOIN users u ON a.user_id = u.id LEFT JOIN assets v ON a.vehicle_id = v.id'
        );

        if (! $id) {
            exit('Reporte no encontrado.');
        }

        require_once 'app/views/preoperational/detail.php';
    }

    public function Stats(): void {}
}
