<?php

declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PreopController
{
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
        $formattedTriggers = [];
        foreach ($this->hxTriggers as $key => $value) {
            // Formato exacto solicitado
            $formattedTriggers[$key] = ($key === 'showMessage') ? json_encode($value) : $value;
        }
        header('HX-Trigger: '.json_encode($formattedTriggers));
    }

    private function upsertItem(int $preop_id, int $q_id, array $data): void
    {
        $existing = $this->model->get('id', 'preop_items', " AND preop_id = $preop_id AND question_id = $q_id");
        if ($existing) {
            $this->model->update('preop_items', (object) $data, $existing->id);
        } else {
            $data['preop_id'] = $preop_id;
            $data['question_id'] = $q_id;
            $this->model->save('preop_items', (object) $data);
        }
    }

    public function Index(): void
    {
        $user = $this->auth->authorize(147);
        $tabulator = true;
        $title = 'Infrastructure / Preoperational';
        $button = 'New Preoperational';
        $content = 'app/components/list.php';
        $columns = '[
            { "title": "ID", "field": "id", "headerHozAlign": "center", "headerFilter":"input"},
            { "title": "Date", "field": "date", "headerHozAlign": "center", "headerFilter": customDateRangeFilter, "headerFilterFunc": customDateFilterFunc, "headerFilterLiveFilter": false },
            { "title": "Vehicle", "field": "vehicle", "headerHozAlign": "center", "headerFilter":"list"},
            { "title": "User", "field": "user", "headerHozAlign": "center", "headerFilter":"input"}
        ]';
        require_once 'app/views/index.php';
    }

    public function Data(): void
    {
        // 1. Autorización y Detección de Exportación
        $user = $this->auth->authorize(147);
        $isExport = isset($_GET['export']);

        if (! $isExport) {
            header('Content-Type: application/json');
        }

        // 2. Parámetros de Paginación
        $page = (int) ($_GET['page'] ?? 1);
        $size = (int) ($_GET['size'] ?? 50);
        $offset = ($page - 1) * $size;

        // 3. Mapeo de campos
        $fieldMap = [
            'id' => 'a.id',
            'date' => 'a.created_at',
            'vehicle' => "CONCAT(v.hostname, ' || ', v.serial,' || ', v.sap)",
            'user' => 'u.username',
            'status' => 'a.status',
        ];

        // 4. Lógica de Filtros
        $where = " AND a.status <> 'draft'";
        if (isset($_GET['filter']) && is_array($_GET['filter'])) {
            foreach ($_GET['filter'] as $key => $f) {
                $field = is_array($f) ? ($f['field'] ?? '') : $key;
                $value = is_array($f) ? ($f['value'] ?? '') : $f;
                $value = addslashes($value);

                if (isset($fieldMap[$field])) {
                    $dbField = $fieldMap[$field];
                    if ($field === 'date' && strpos($value, ' to ') !== false) {
                        [$from, $to] = explode(' to ', $value);
                        $where .= " AND DATE($dbField) BETWEEN '$from' AND '$to'";
                    } else {
                        $where .= " AND $dbField LIKE '%$value%'";
                    }
                }
            }
        }

        // 5. Ordenamiento
        $orderBy = 'a.id DESC';
        if (isset($_GET['sort'][0]['field'])) {
            $sortField = $_GET['sort'][0]['field'];
            $sortDir = strtoupper($_GET['sort'][0]['dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
            if (isset($fieldMap[$sortField])) {
                $orderBy = $fieldMap[$sortField]." $sortDir";
            }
        }

        // 6. Consultas (Joins y Select)
        $joins = 'LEFT JOIN users u ON a.user_id = u.id LEFT JOIN assets v ON a.vehicle_id = v.id';
        $select = "a.id, a.created_at as date, a.status, (CONCAT(v.hostname, ' || ', v.serial,' || ', v.sap)) AS vehicle, u.username as user";

        if ($isExport) {
            setcookie('download_complete', 'true', time() + 30, '/');
            $rows = $this->model->list($select, 'preop a', "$where ORDER BY $orderBy", $joins);

            // --- OPTIMIZACIÓN XLSX ---
            $spreadsheet = new Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();

            // Cabeceras (traducidas para el reporte)
            $labels = ['ID', 'Fecha Creación', 'Estado', 'Vehículo / Activo', 'Usuario'];
            $sheet->fromArray($labels, null, 'A1');

            $exportData = [];
            foreach ($rows as $r) {
                $exportData[] = [
                    $r->id,
                    $r->date ? Date::PHPToExcel(strtotime($r->date)) : '',
                    $r->status,
                    $r->vehicle,
                    $r->user,
                ];
            }

            // Vuelco masivo de datos
            $sheet->fromArray($exportData, null, 'A2');
            $lastRow = count($exportData) + 1;

            // Formato de fecha para la columna B (Fecha Creación)
            $sheet->getStyle("B2:B$lastRow")
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD2);

            // Ancho de columnas rápido (ajustado a los datos de vehículos que suelen ser largos)
            foreach (range('A', 'E') as $col) {
                $width = ($col === 'D') ? 40 : 18; // Columna D (Vehículo) más ancha
                $sheet->getColumnDimension($col)->setWidth($width);
            }

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="Reporte_Preoperacionales_'.date('dmY').'.xlsx"');

            $writer = new Xlsx($spreadsheet);
            $writer->setPreCalculateFormulas(false);
            $writer->save('php://output');
            exit;
        }

        // Respuesta normal para Tabulator
        $total = $this->model->get('COUNT(a.id) AS total', 'preop a', $where, $joins)->total;
        $rows = $this->model->list($select, 'preop a', "$where ORDER BY $orderBy LIMIT $offset, $size", $joins);

        echo json_encode([
            'data' => $rows,
            'last_page' => ceil(($total ?? 0) / $size),
            'last_row' => (int) ($total ?? 0),
        ]);
    }

    public function New(): void
    {
        $vehiculos = $this->model->list('*', 'assets', " AND area = 'Vehicles' ORDER BY hostname ASC");
        require_once 'app/views/preop/new.php';
    }

    public function Checklist(): void
    {
        $v_id = (int) ($_REQUEST['vehicle_id'] ?? 0);
        if (! $v_id) {
            header('HX-Trigger: {"lockKM": true}');
            echo '<p class="text-center text-gray-400 py-20 font-black uppercase text-xs italic">Seleccione unidad...</p>';
            exit;
        }
        $u_id = $_SESSION['id-SIGMA'];
        $draft = $this->model->get('id, km', 'preop', " AND user_id = $u_id AND vehicle_id = $v_id AND status = 'draft' LIMIT 1");
        $id_preop = $draft ? (int) $draft->id : (int) $this->model->save('preop', (object) ['user_id' => $u_id, 'vehicle_id' => $v_id, 'status' => 'draft', 'created_at' => date('Y-m-d H:i:s'), 'km' => 0]);

        $this->hxTriggers['setPreopId'] = (string) $id_preop;
        $this->hxTriggers['setKM'] = (string) ($draft->km ?? '0');

        $v = $this->model->get('kind', 'assets', " AND id = $v_id");
        $saved_items = [];
        $items = $this->model->list('*', 'preop_items', " AND preop_id = $id_preop");
        if ($items) {
            foreach ($items as $si) {
                $saved_items[$si->question_id] = $si;
            }
        }

        $checklist_data = [];
        $questions = $this->model->list('*', 'preop_questions', " AND kind = '{$v->kind}' ORDER BY category");
        foreach ($questions as $r) {
            $checklist_data[$r->category][] = $r;
        }

        $this->sendHeaders();
        require 'app/views/preop/checklist.php';
    }

    public function Save(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        if (! $id) {
            exit;
        }

        if (isset($_POST['km'])) {
            $this->model->update('preop', (object) ['km' => $_POST['km']], $id);
        }

        foreach ($_POST as $key => $val) {
            if (preg_match('/^(question|obs)_(\d+)$/', $key, $matches)) {
                $field = $matches[1] === 'question' ? 'answer' : 'obs';
                $this->upsertItem($id, (int) $matches[2], [$field => $val]);
            }
        }
        if (($_POST['is_final'] ?? '0') === '1') {
            $this->model->update('preop', (object) ['status' => 'completed', 'updated_at' => date('Y-m-d H:i:s')], $id);
            $this->hxTriggers['eventChanged'] = true;
            $this->hxTriggers['showMessage'] = ['type' => 'success', 'message' => 'Completed', 'close' => 'closeNewModal'];
            $this->sendHeaders();
            exit;
        }

        if (($_POST['trigger_type'] ?? '') === 'radio') {
            $q_id = (int) ($_POST['q_id_trigger'] ?? 0);
            $ans = $_POST["question_$q_id"] ?? '';
            $this->hxTriggers['showMessage'] = ['type' => 'success', 'message' => 'Updated', 'close' => ''];
            $this->sendHeaders();
            echo '<div id="obs_container_'.$q_id.'" hx-swap-oob="true">';
            if ($ans === 'Mal') {
                echo '<div class="mt-4 animate-in slide-in-from-top-2 duration-300">
                    <textarea name="obs_'.$q_id.'" required placeholder="Hallazgo..." class="w-full p-3 text-xs font-bold border-2 border-red-100 rounded-2xl outline-none focus:border-red-500 bg-red-50/30" rows="2" hx-post="?c=Preop&a=Save" hx-trigger="keyup changed delay:1s" hx-target="#silent_sync" hx-include="#main_vehicle_id, #main_preop_id"></textarea>
                </div>';
            }
            echo '</div>';
            exit;
        }

        $this->hxTriggers['showMessage'] = ['type' => 'success', 'message' => 'Updated', 'close' => ''];
        $this->sendHeaders();
    }

    public function UploadPhoto(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        $q_id = (int) ($_POST['q_id'] ?? 0);

        // Construimos el nombre dinámico que viene del frontend
        $file_key = 'foto_'.$q_id;

        // Verificamos si existe el archivo con esa llave dinámica
        if ($id && $q_id && isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] === 0) {

            // Generar nombre de archivo único
            $extension = pathinfo($_FILES[$file_key]['name'], PATHINFO_EXTENSION);
            $dest = 'uploads/preop/'.uniqid('pre_').'_'.$id.'_'.$q_id.'.'.$extension;

            if (! is_dir('uploads/preop/')) {
                mkdir('uploads/preop/', 0777, true);
            }

            if (move_uploaded_file($_FILES[$file_key]['tmp_name'], $dest)) {
                // Guardar en base de datos
                $this->upsertItem($id, $q_id, ['url' => $dest]);

                // Retornar solo el fragmento del HTML para el preview específico
                echo '<img src="'.$dest.'?t='.time().'" class="w-full h-full object-cover">';

                // Enviar triggers de HTMX si los usas
                $this->hxTriggers['showMessage'] = [
                    'type' => 'success',
                    'message' => 'Foto cargada correctamente',
                    'close' => '',
                ];
                $this->sendHeaders();
            } else {
                // Error al mover el archivo
                header('HTTP/1.1 500 Internal Server Error');
                echo 'Error al guardar el archivo en el servidor.';
            }
        } else {
            // Error de validación o archivo no encontrado
            header('HTTP/1.1 400 Bad Request');
            echo 'No se recibió el archivo correctamente o falta ID.';
        }
    }

    public function Detail(): void
    {
        $filters = 'and a.id = '.$_REQUEST['id'];
        $id = $this->model->get('a.id as idd, a.*, u.username as user, v.*', 'preop a', $filters, 'LEFT JOIN users u ON a.user_id = u.id LEFT JOIN assets v ON a.vehicle_id = v.id');
        require_once 'app/views/preop/detail.php';
    }

    public function Stats(): void
    {
        // Implementación de estadísticas pendiente
    }
}
