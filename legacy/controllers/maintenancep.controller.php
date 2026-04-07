<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MaintenancepController
{
    public function __construct(public Model $model, private AuthService $auth) {}

    public function Index()
    {
        $user = $this->auth->authorize(82);
        $tabulator = true;
        $jspreadsheet = false;
        $kpis = true;
        $content = 'app/components/list.php';
        $title = 'Infrastructure / Machinery / Preventive';

        $columns = '[
            { "title": "ID", "field": "id", "width": 60, "sorter": "number", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Start", "field": "start", headerHozAlign: "center", headerFilter: customDateRangeFilter, headerFilterFunc: customDateFilterFunc, headerFilterLiveFilter: false },
            { "title": "End", "field": "end", headerHozAlign: "center", headerFilter: customDateRangeFilter, headerFilterFunc: customDateFilterFunc, headerFilterLiveFilter: false },
            { "title": "Asset", "field": "asset", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Status", "field": "status", headerHozAlign: "center", hozAlign:"center", headerFilter:"list",
            headerFilterParams:{ values: {"Open": "Open", "Started": "Started", "Attended": "Attended", "Closed": "Closed"}, clearable:true}},
            { "title": "Days", "field": "days", "width": 100, "headerHozAlign": "left", "headerFilter": "input", formatter: "html"},
            { "title": "Activity", "field": "activity", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Frequency", "field": "frequency", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Started", "field": "started", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Attended", "field": "attended", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Closed", "field": "closed", "headerHozAlign": "left", "headerFilter": "input"},
        ]';

        require_once 'app/views/index.php';
    }

    public function Data()
    {
        // 1. Autorización y Detección de Exportación
        $user = $this->auth->authorize(82);
        $isExport = isset($_GET['export']);

        if (! $isExport) {
            header('Content-Type: application/json');
        }

        // 2. Parámetros de Paginación
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $size = isset($_GET['size']) ? (int) $_GET['size'] : 50;
        $offset = ($page - 1) * $size;

        // 3. Mapeo de campos
        $fieldMap = [
            'id' => 'a.id',
            'start' => 'a.scheduled_start',
            'end' => 'a.scheduled_end',
            'asset' => "concat(c.hostname, ' | ', c.serial, ' | ', c.sap)",
            'activity' => "COALESCE(NULLIF(a.activity, ''), b.activity)",
            'frequency' => 'b.frequency',
            'started' => 'a.started',
            'attended' => 'a.attended',
            'closed' => 'a.closed_at',
            'status' => 'a.status',
            'days' => 'DATEDIFF(a.scheduled_end, CURDATE())',
        ];

        // 4. Construcción del WHERE
        $where = " AND a.kind = 'Machinery'";
        if (isset($_GET['filter']) && is_array($_GET['filter'])) {
            foreach ($_GET['filter'] as $key => $f) {
                $field = is_array($f) ? ($f['field'] ?? '') : $key;
                $value = is_array($f) ? ($f['value'] ?? '') : $f;
                // No aplicamos addslashes aquí arriba para no dañar el formato del rango ' to '

                if (isset($fieldMap[$field])) {
                    $dbField = $fieldMap[$field];

                    // --- Lógica de fechas (Igual a la que te funcionó) ---
                    if ($field === 'start' || $field === 'end') {
                        if (strpos($value, ' to ') !== false) {
                            [$from, $to] = explode(' to ', $value);
                            $where .= " AND DATE($dbField) BETWEEN '$from' AND '$to'";
                        } else {
                            $where .= " AND DATE($dbField) LIKE '%".addslashes($value)."%'";
                        }
                    } else {
                        // Filtros normales de texto
                        $where .= " AND $dbField LIKE '%".addslashes($value)."%'";
                    }
                }
            }
        }

        // --- 5. Ordenamiento Personalizado ---
        $defaultOrder = "FIELD(a.status, 'Open', 'Started', 'Attended', 'Closed'), a.scheduled_end ASC";
        $orderBy = $defaultOrder;

        if (isset($_GET['sort'][0]['field'])) {
            $sortField = $_GET['sort'][0]['field'];
            $sortDir = strtoupper($_GET['sort'][0]['dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
            if (isset($fieldMap[$sortField])) {
                $orderBy = $fieldMap[$sortField]." $sortDir";
            }
        }

        $joins = 'LEFT JOIN mnt_preventive_form b ON a.preventive_id = b.id 
                LEFT JOIN assets c ON c.id = COALESCE(NULLIF(a.asset_id, 0), b.asset_id)';

        $selectFields = "a.id, a.scheduled_start, a.scheduled_end, a.started, a.attended, a.closed_at, a.status, 
                        COALESCE(NULLIF(a.asset_id, 0), b.asset_id) AS asset_id, 
                        COALESCE(NULLIF(a.activity, ''), b.activity) AS activity, 
                        b.frequency, 
                        concat(c.hostname, ' | ', c.serial,' | ', c.sap) as asset_full,
                        DATEDIFF(a.scheduled_end, CURDATE()) as days_diff";

        // 7. Ejecución de Consultas e Exportación
        if ($isExport) {
            setcookie('download_complete', 'true', time() + 30, '/');
            $rows = $this->model->list($selectFields, 'mnt_preventive a', "$where ORDER BY $defaultOrder", $joins);

            $spreadsheet = new Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();

            // Ajusté los headers para que coincidan con la cantidad de columnas del foreach
            $headers = ['ID', 'Programado', 'Vencimiento', 'Activo', 'Actividad', 'Frecuencia', 'Estado', 'Inicio Real', 'Atendido', 'Cierre', 'Días Restantes'];
            $sheet->fromArray($headers, null, 'A1');

            $exportData = [];
            foreach ($rows as $r) {
                $exportData[] = [
                    $r->id,
                    $r->scheduled_start ? Date::PHPToExcel(strtotime($r->scheduled_start)) : '',
                    $r->scheduled_end ? Date::PHPToExcel(strtotime($r->scheduled_end)) : '',
                    $r->asset_full,
                    $r->activity,
                    $r->frequency,
                    $r->status,
                    $r->started ? Date::PHPToExcel(strtotime($r->started)) : '',
                    $r->attended ? Date::PHPToExcel(strtotime($r->attended)) : '',
                    ($r->closed_at && $r->closed_at != '0000-00-00 00:00:00') ? Date::PHPToExcel(strtotime($r->closed_at)) : '',
                    (int) $r->days_diff,
                ];
            }

            $sheet->fromArray($exportData, null, 'A2');
            $lastR = count($exportData) + 1;

            // B=Programado, C=Vencimiento, H=Inicio Real, I=Cierre
            foreach (['B', 'C', 'H', 'I'] as $col) {
                $sheet->getStyle($col.'2:'.$col.$lastR)
                    ->getNumberFormat()
                    ->setFormatCode('yyyy-mm-dd');
            }

            foreach (range('A', 'J') as $col) {
                $sheet->getColumnDimension($col)->setWidth(18);
            }

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="Preventivos_Machinery_'.date('dmY').'.xlsx"');

            $writer = new Xlsx($spreadsheet);
            $writer->setPreCalculateFormulas(false);
            $writer->save('php://output');
            exit;
        }

        // 8. Respuesta para Tabulator (Vista Web)
        $totalCount = $this->model->get('COUNT(a.id) AS total', 'mnt_preventive a', $where, $joins)->total;
        $rows = $this->model->list($selectFields, 'mnt_preventive a', "$where ORDER BY $orderBy LIMIT $offset, $size", $joins);

        $data = [];
        foreach ($rows as $r) {
            $days = (int) $r->days_diff;
            $color = ($days >= 0) ? 'text-green-500' : 'text-red-500';
            if (! empty($r->closed_at) && $r->closed_at != '0000-00-00 00:00:00') {
                $color = 'text-gray-500';
            }

            $data[] = [
                'id' => $r->id,
                'start' => $r->scheduled_start,
                'end' => $r->scheduled_end,
                'asset' => $r->asset_full,
                'activity' => $r->activity,
                'frequency' => $r->frequency,
                'status' => $r->status,
                'started' => $r->started,
                'attended' => $r->attended,
                'closed' => $r->closed_at,
                'days' => "<span class='font-bold $color'>$days</span>",
            ];
        }

        echo json_encode([
            'data' => $data,
            'last_page' => ceil($totalCount / $size),
            'last_row' => (int) $totalCount,
        ]);
    }

    public function Stats()
    {
        require_once 'app/views/maintenancep/stats.php';
    }

    public function Detail()
    {
        $user = $this->auth->authorize(82);
        require_once 'app/views/maintenancep/detail.php';
    }

    public function Head()
    {
        $user = $this->auth->authorize(82);
        $canClose = ! empty(array_intersect(['44'], json_decode($user->permissions ?? '[]', true)));
        $canEdit = ! empty(array_intersect(['35'], json_decode($user->permissions ?? '[]', true)));
        if (! empty($_REQUEST['id'])) {
            $filters = 'and id = '.$_REQUEST['id'];
            $id = $this->model->get(
                '*',
                'mnt_preventive',
                $filters,
            );
        }
        require_once 'app/views/maintenancep/head.php';
    }

    public function Info()
    {
        $user = $this->auth->authorize(82);
        $canClose = ! empty(array_intersect(['44'], json_decode($user->permissions ?? '[]', true)));
        if (! empty($_REQUEST['id'])) {
            $filters = 'and a.id = '.$_REQUEST['id'];
            $id = $this->model->get(
                "a.*,b.activity,b.frequency, concat(c.hostname, ' | ', c.serial,' | ', c.sap) as asset",
                'mnt_preventive a',
                $filters,
                'LEFT JOIN mnt_preventive_form b ON a.preventive_id = b.id LEFT JOIN assets c ON b.asset_id = c.id'
            );
        }
        require_once 'app/views/maintenancep/info.php';
    }

    public function Tab()
    {
        $user = $this->auth->authorize(82);

        if (! empty($_REQUEST['id'])) {
            $filters = 'and id = '.(int) $_REQUEST['id'];
            $id = $this->model->get('*', 'mnt_preventive', $filters);
        }

        // permisos
        $canEdit = ! empty(
            array_intersect(['35'], json_decode($user->permissions ?? '[]', true))
        );

        require_once 'app/views/maintenancep/tab.php';
    }

    public function Task()
    {
        $user = $this->auth->authorize(82);
        header('Content-Type: application/json');

        $mntpId = (int) ($_GET['id'] ?? 0);

        $rows = $this->model->list(
            'a.*, b.username',
            'mntp_items a',
            "and a.mntp_id = $mntpId",
            'LEFT JOIN users b ON a.user_id = b.id'
        );

        $data = [];

        foreach ($rows as $r) {

            // --- Evidences (MISMA lógica original, optimizada) ---
            $action = '';
            $directorio = "uploads/mntp/pics/$r->id/";
            $files = glob($directorio.'*');

            if (! empty($files)) {
                sort($files);
                foreach ($files as $file) {
                    if (is_file($file)) {
                        $fileName = basename($file);
                        // ⚠️ el original sobreescribía, por eso queda el último
                        $action = "<a class='text-blue-500' target='_blank' href='$file'>
                                    Evidence
                                </a>";
                    }
                }
            }

            $data[] = [
                'date' => $r->created_at,
                'user' => $r->username,
                'complexity' => $r->complexity,
                'attends' => $r->attends,
                'time' => $r->duration,
                'notes' => $r->notes,
                'file' => $action,
            ];
        }

        echo json_encode([
            'data' => $data,
        ]);
    }

    public function Modal()
    {
        $user = $this->auth->authorize([35]);
        $modal = $_REQUEST['modal'];
        $filters = 'and id = '.$_REQUEST['id'];
        $id = $this->model->get('*', 'mnt_preventive', $filters);
        require_once "app/views/maintenancep/$modal.php";
    }

    public function SaveTask()
    {
        try {
            // Autorización
            $user = $this->auth->authorize(82);
            header('Content-Type: application/json');

            $item = new stdClass;

            foreach ($_POST as $k => $val) {
                if (! empty($val) && $k !== 'id') {
                    $item->{$k} = htmlspecialchars(trim($val));
                }
            }

            // Datos fijos
            $item->mntp_id = $_REQUEST['id'] ?? null;

            $item->user_id = $_SESSION['id-SIGMA'] ?? null;

            if (empty($item->mntp_id)) {
                throw new Exception('Maintenance ID not provided');
            }

            $mntpId = $item->mntp_id;
            $exists = $this->model->get('*', 'mntp_items', "AND mntp_id = $mntpId");

            if (empty($exists->id)) {
                $mnt = new stdClass;
                $mnt->started = date('Y-m-d H:i:s');
                $mnt->status = 'Started';
                $this->model->update('mnt_preventive', $mnt, $mntpId);
            }

            $id = $this->model->save('mntp_items', $item);
            if (! $id) {
                throw new Exception('Error saving maintenance item');
            }

            /* ==========================
            MANEJO DE ARCHIVOS
            ========================== */
            if (! empty($_FILES['files']['name'][0])) {

                $carpeta = "uploads/mntp/pics/$id/";
                if (! is_dir($carpeta)) {
                    mkdir($carpeta, 0777, true);
                }

                $total = count($_FILES['files']['name']);
                $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf', 'jfif'];

                for ($i = 0; $i < $total; $i++) {

                    $tmpFilePath = $_FILES['files']['tmp_name'][$i];
                    if ($tmpFilePath == '') {
                        continue;
                    }

                    $fileName = basename($_FILES['files']['name'][$i]);
                    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                    if (! in_array($fileExt, $allowedTypes)) {
                        throw new Exception("File type not allowed: $fileName");
                    }

                    $destino = $carpeta.uniqid().'.'.$fileExt;

                    if (! move_uploaded_file($tmpFilePath, $destino)) {
                        throw new Exception("Error uploading file: $fileName");
                    }
                }
            }

            /* ==========================
            RESPUESTA HTMX
            ========================== */
            $message = '{"type": "success", "message": "Saved", "close": "closeNestedModal"}';

            header('HX-Trigger: '.json_encode([
                'eventChanged' => true,
                'showMessage' => $message,
            ]));

            http_response_code(204);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'type' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function Update()
    {
        try {
            header('Content-Type: application/json');
            $id = $_REQUEST['id'];
            $update = new stdClass;
            if ($_REQUEST['field'] == 'closed_at') {
                if (! $this->model->get('*', 'mntp_items', " and mntp_id = $id LIMIT 1")) {
                    $message = '{"type": "error", "message": "Add at least One task", "close" : ""}';
                    header('HX-Trigger: '.json_encode(['showMessage' => $message]));
                    http_response_code(400);
                    exit;
                }
                $update->{$_REQUEST['field']} = date('Y-m-d H:i:s');
                $update->status = 'Closed';
                $close = 'closeNewModal';
            }

            if ($_REQUEST['field'] == 'attended') {

                if (! $this->model->get('*', 'mntp_items', " and mntp_id = $id LIMIT 1")) {
                    $message = '{"type": "error", "message": "Add at least One task", "close" : ""}';
                    header('HX-Trigger: '.json_encode(['showMessage' => $message]));
                    http_response_code(400);
                    exit;
                }

                $update->{$_REQUEST['field']} = date('Y-m-d H:i:s');
                $update->status = 'Attended';
                $close = 'closeNewModal';
            }

            $id = $this->model->update('mnt_preventive', $update, $id);
            if ($id === false) {
                http_response_code(500);
                echo json_encode([
                    'type' => 'error',
                    'message' => 'Error saving',
                ]);

                return;
            }

            // ------------------- Respuesta HTMX -------------------

            $message = [
                'type' => 'success',
                'message' => 'Updated',
                'close' => $close,
            ];

            header('HX-Trigger: '.json_encode([
                'eventChanged' => true,
                'showMessage' => json_encode($message),
            ]));
            http_response_code(204);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'type' => 'error',
                'message' => 'Internal Server Error: '.$e->getMessage(),
            ]);
        }
    }

    public function Kpis()
    {
        $user = $this->auth->authorize(82);
        $result = [];

        (! empty($_REQUEST['year'])) ? $year = $_REQUEST['year'] : $year = date('Y');
        $currentYear = date('Y');
        $currentMonth = date('n');

        $dates = [];
        $result1 = [];
        $result2 = [];
        $result3 = [];
        $i = 0;

        $macFilter = "AND kind = 'Machinery'";

        for ($m = 1; $m <= 12; $m++) {
            $mes_fmt = str_pad($m, 2, '0', STR_PAD_LEFT);
            $firstDay = "$year-$mes_fmt-01 00:00:00";
            $lastDay = date('Y-m-t', strtotime($firstDay)).' 23:59:59';
            $dateStr = date('M', strtotime($firstDay));

            if ($year == $currentYear && $m > $currentMonth) {
                $result[$i] = [
                    'dateStr' => $dateStr,
                    'date' => "$year-$mes_fmt-01",
                    'total' => 0, 'mora' => 0, 'carga_total' => 0,
                    'at_mes' => 0, 'at_pnd' => 0, 'total_at' => 0,
                    'at_mora' => 0,
                    'external' => 0, 'totall' => 0, 'open' => 0,
                    'result1' => 0, 'result2' => 0, 'result3' => 0,
                ];
                $result1[] = 0;
                $result2[] = 0;
                $result3[] = 0;
                $i++;

                continue;
            }

            // 1. Total programado para el mes
            $resT = $this->model->get('COUNT(id) as n', 'mnt_preventive', "AND scheduled_end >= '$firstDay' AND scheduled_end <= '$lastDay' $macFilter");
            $totalMes = (int) $resT->n;

            // 2. Mora de entrada: Pendientes de meses anteriores basándose en 'attended'
            $resM = $this->model->get('COUNT(id) as n', 'mnt_preventive', "AND scheduled_end < '$firstDay' AND (attended IS NULL OR attended >= '$firstDay') $macFilter");
            $moraEntrada = (int) $resM->n;

            // 3. Total Atendidos en el mes (Independiente de cuándo vencían)
            $resEjecMes = $this->model->get('COUNT(id) as n', 'mnt_preventive', "AND attended >= '$firstDay' AND attended <= '$lastDay' $macFilter");
            $totalAt = (int) $resEjecMes->n;

            // 4. Atendidos en el mes que venían de mora (vencían antes de este mes)
            $atMoraReal = (int) $this->model->get('COUNT(id) as n', 'mnt_preventive', "AND attended >= '$firstDay' AND attended <= '$lastDay' AND scheduled_end < '$firstDay' $macFilter")->n;

            // 5. Atendidos en el mes que vencían en este mismo mes
            $resAM = $this->model->get('COUNT(id) as n', 'mnt_preventive', "AND scheduled_end >= '$firstDay' AND scheduled_end <= '$lastDay' AND attended >= '$firstDay' AND attended <= '$lastDay' $macFilter");
            $atMes = (int) $resAM->n;

            $atPnd = $totalAt - $atMes;
            $cargaTotal = $totalMes + $moraEntrada;

            // KPI 1: % Cumplimiento (Atendidos vs Carga Total)
            $pctCumplimiento = ($cargaTotal > 0) ? round(($totalAt / $cargaTotal) * 100) : 0;

            // KPI 2: % Externos
            $resExt = $this->model->get('COUNT(DISTINCT a.id) as total', 'mnt_preventive a', "AND a.scheduled_end >= '$firstDay' AND a.scheduled_end <= '$lastDay' AND a.kind = 'Machinery' AND b.attends = 'External'", 'INNER JOIN mntp_items b ON a.id = b.mntp_id');
            $externalReal = (int) $resExt->total;
            $pctExterno = ($totalMes > 0) ? round(($externalReal / $totalMes) * 100) : 0;

            // KPI 3: % Pendientes Acumulados
            $resTotalAcum = $this->model->get('COUNT(id) as total', 'mnt_preventive', "AND scheduled_end <= '$lastDay' $macFilter");
            $totalAcum = (int) $resTotalAcum->total;

            $resOpenAcum = $this->model->get('COUNT(id) as abiertos', 'mnt_preventive', "AND scheduled_end <= '$lastDay' AND (attended IS NULL OR attended > '$lastDay') $macFilter");
            $openAcum = (int) $resOpenAcum->abiertos;

            $pctPendiente = ($totalAcum > 0) ? round(($openAcum / $totalAcum) * 100) : 0;

            $dates[] = $dateStr;
            $result[$i] = [
                'dateStr' => $dateStr,
                'date' => "$year-$mes_fmt-01",
                'total' => $totalMes,
                'mora' => $moraEntrada,
                'carga_total' => $cargaTotal,
                'at_mes' => $atMes,
                'at_pnd' => $atPnd,
                'total_at' => $totalAt,
                'at_mora' => $atMoraReal,
                'external' => $externalReal,
                'totall' => $totalAcum,
                'open' => $openAcum,
                'result1' => $pctCumplimiento,
                'result2' => $pctExterno,
                'result3' => $pctPendiente,
            ];
            $result1[] = $pctCumplimiento;
            $result2[] = $pctExterno;
            $result3[] = $pctPendiente;
            $i++;
        }
        $in = isset($_REQUEST['in']) ? $_REQUEST['in'] : 1;
        require_once 'app/views/maintenancep/kpis/index.php';
    }

    public function Bot()
    {
        $item = new stdClass;
        $itemb = new stdClass;
        $item->status = 'Open';
        foreach ($this->model->list('*', 'mnt_preventive_form', "and frequency = 'Weekly'") as $r) {
            $id = $r->id;
            $item->preventive_id = $id;
            $item->scheduled_start = $r->last_performed_at;
            $item->kind = $r->kind;
            $item->scheduled_end = date('Y-m-d', strtotime($item->scheduled_start.' +1 week'));
            $itemb->last_performed_at = date('Y-m-d', strtotime($item->scheduled_end.' +1 day'));
            $end = (isset($this->model->get('scheduled_end', 'mnt_preventive', " and preventive_id = '$id' ORDER BY scheduled_end DESC")->scheduled_end)) ? $this->model->get('scheduled_end', 'mnt_preventive', " and preventive_id = '$id' ORDER BY scheduled_end DESC")->scheduled_end : false;
            if ($end and $end < $itemb->last_performed_at and $end <= date('Y-m-d')) {
                $this->model->save('mnt_preventive', $item);
                $this->model->update('mnt_preventive_form', $itemb, $id);
            } elseif (! $end and $r->last_performed_at <= date('Y-m-d')) {
                $this->model->save('mnt_preventive', $item);
                $this->model->update('mnt_preventive_form', $itemb, $id);
            }
            echo "$end - $itemb->last_performed_at <br>";
        }

        foreach ($this->model->list('*', 'mnt_preventive_form', "and frequency = 'Monthly'") as $r) {
            $id = $r->id;
            $item->preventive_id = $id;
            $item->scheduled_start = $r->last_performed_at;
            $item->kind = $r->kind;
            $item->scheduled_end = date('Y-m-d', strtotime($item->scheduled_start.' +1 month'));
            $itemb->last_performed_at = date('Y-m-d', strtotime($item->scheduled_end.' +1 day'));
            $end = (isset($this->model->get('scheduled_end', 'mnt_preventive', " and preventive_id = '$id' ORDER BY scheduled_end DESC")->scheduled_end)) ? $this->model->get('scheduled_end', 'mnt_preventive', " and preventive_id = '$id' ORDER BY scheduled_end DESC")->scheduled_end : false;
            if ($end and $end < $itemb->last_performed_at and $end <= date('Y-m-d')) {
                $this->model->save('mnt_preventive', $item);
                $this->model->update('mnt_preventive_form', $itemb, $id);
                echo "$end - $itemb->last_performed_at <br>";
            } elseif (! $end and $r->last_performed_at <= date('Y-m-d')) {
                $this->model->save('mnt_preventive', $item);
                $this->model->update('mnt_preventive_form', $itemb, $id);
                echo "$itemb->last_performed_at <br>";
            }
        }

        foreach ($this->model->list('*', 'mnt_preventive_form', "and frequency = 'Quarterly'") as $r) {
            $id = $r->id;
            $item->preventive_id = $id;
            $item->scheduled_start = $r->last_performed_at;
            $item->kind = $r->kind;
            $item->scheduled_end = date('Y-m-d', strtotime($item->scheduled_start.' +3 month'));
            $itemb->last_performed_at = date('Y-m-d', strtotime($item->scheduled_end.' +1 day'));
            $end = (isset($this->model->get('scheduled_end', 'mnt_preventive', " and preventive_id = '$id' ORDER BY scheduled_end DESC")->scheduled_end)) ? $this->model->get('scheduled_end', 'mnt_preventive', " and preventive_id = '$id' ORDER BY scheduled_end DESC")->scheduled_end : false;
            if ($end and $end < $itemb->last_performed_at and $end <= date('Y-m-d')) {
                $this->model->save('mnt_preventive', $item);
                $this->model->update('mnt_preventive_form', $itemb, $id);
                echo "$end - $itemb->last_performed_at <br>";
            } elseif (! $end and $r->last_performed_at <= date('Y-m-d')) {
                $this->model->save('mnt_preventive', $item);
                $this->model->update('mnt_preventive_form', $itemb, $id);
                echo "$itemb->last_performed_at <br>";
            }
        }

        foreach ($this->model->list('*', 'mnt_preventive_form', "and frequency = 'Semiannual'") as $r) {
            $id = $r->id;
            $item->preventive_id = $id;
            $item->scheduled_start = $r->last_performed_at;
            $item->kind = $r->kind;
            $item->scheduled_end = date('Y-m-d', strtotime($item->scheduled_start.' +6 month'));
            $itemb->last_performed_at = date('Y-m-d', strtotime($item->scheduled_end.' +1 day'));
            $end = (isset($this->model->get('scheduled_end', 'mnt_preventive', " and preventive_id = '$id' ORDER BY scheduled_end DESC")->scheduled_end)) ? $this->model->get('scheduled_end', 'mnt_preventive', " and preventive_id = '$id' ORDER BY scheduled_end DESC")->scheduled_end : false;
            if ($end and $end < $itemb->last_performed_at and $end <= date('Y-m-d')) {
                $this->model->save('mnt_preventive', $item);
                $this->model->update('mnt_preventive_form', $itemb, $id);
                echo "$end - $itemb->last_performed_at <br>";
            } elseif (! $end and $r->last_performed_at <= date('Y-m-d')) {
                $this->model->save('mnt_preventive', $item);
                $this->model->update('mnt_preventive_form', $itemb, $id);
                echo "$itemb->last_performed_at <br>";
            }
        }

        foreach ($this->model->list('*', 'mnt_preventive_form', "and frequency = 'Annual'") as $r) {
            $id = $r->id;
            $item->preventive_id = $id;
            $item->scheduled_start = $r->last_performed_at;
            $item->kind = $r->kind;
            $item->scheduled_end = date('Y-m-d', strtotime($item->scheduled_start.' +1 year'));
            $itemb->last_performed_at = date('Y-m-d', strtotime($item->scheduled_end.' +1 day'));
            $end = (isset($this->model->get('scheduled_end', 'mnt_preventive', " and preventive_id = '$id' ORDER BY scheduled_end DESC")->scheduled_end)) ? $this->model->get('scheduled_end', 'mnt_preventive', " and preventive_id = '$id' ORDER BY scheduled_end DESC")->scheduled_end : false;
            if ($end and $end < $itemb->last_performed_at and $end <= date('Y-m-d')) {
                $this->model->save('mnt_preventive', $item);
                $this->model->update('mnt_preventive_form', $itemb, $id);
                echo "$end - $itemb->last_performed_at <br>";
            } elseif (! $end and $r->last_performed_at <= date('Y-m-d')) {
                $this->model->save('mnt_preventive', $item);
                $this->model->update('mnt_preventive_form', $itemb, $id);
                echo "$itemb->last_performed_at <br>";
            }
        }

        foreach ($this->model->list('*', 'mnt_preventive_form', "and frequency = 'Annualx2'") as $r) {
            $id = $r->id;
            $item->preventive_id = $id;
            $item->scheduled_start = $r->last_performed_at;
            $item->kind = $r->kind;
            $item->scheduled_end = date('Y-m-d', strtotime($item->scheduled_start.' +2 years'));
            $itemb->last_performed_at = date('Y-m-d', strtotime($item->scheduled_end.' +1 day'));
            $end = (isset($this->model->get('scheduled_end', 'mnt_preventive', " and preventive_id = '$id' ORDER BY scheduled_end DESC")->scheduled_end)) ? $this->model->get('scheduled_end', 'mnt_preventive', " and preventive_id = '$id' ORDER BY scheduled_end DESC")->scheduled_end : false;
            if ($end and $end < $itemb->last_performed_at and $end <= date('Y-m-d')) {
                $this->model->save('mnt_preventive', $item);
                $this->model->update('mnt_preventive_form', $itemb, $id);
                echo "$end - $itemb->last_performed_at <br>";
            } elseif (! $end and $r->last_performed_at <= date('Y-m-d')) {
                $this->model->save('mnt_preventive', $item);
                $this->model->update('mnt_preventive_form', $itemb, $id);
                echo "$itemb->last_performed_at <br>";
            }
        }

        foreach ($this->model->list('*', 'mnt_preventive_form', "and frequency = 'Annualx3'") as $r) {
            $id = $r->id;
            $item->preventive_id = $id;
            $item->scheduled_start = $r->last_performed_at;
            $item->kind = $r->kind;
            $item->scheduled_end = date('Y-m-d', strtotime($item->scheduled_start.' +3 years'));
            $itemb->last_performed_at = date('Y-m-d', strtotime($item->scheduled_end.' +1 day'));
            $end = (isset($this->model->get('scheduled_end', 'mnt_preventive', " and preventive_id = '$id' ORDER BY scheduled_end DESC")->scheduled_end)) ? $this->model->get('scheduled_end', 'mnt_preventive', " and preventive_id = '$id' ORDER BY scheduled_end DESC")->scheduled_end : false;
            if ($end and $end < $itemb->last_performed_at and $end <= date('Y-m-d')) {
                $this->model->save('mnt_preventive', $item);
                $this->model->update('mnt_preventive_form', $itemb, $id);
                echo "$end - $itemb->last_performed_at <br>";
            } elseif (! $end and $r->last_performed_at <= date('Y-m-d')) {
                $this->model->save('mnt_preventive', $item);
                $this->model->update('mnt_preventive_form', $itemb, $id);
                echo "$itemb->last_performed_at <br>";
            }
        }

        foreach ($this->model->list('*', 'mnt_preventive_form', "and frequency = 'Annualx5'") as $r) {
            $id = $r->id;
            $item->preventive_id = $id;
            $item->scheduled_start = $r->last_performed_at;
            $item->kind = $r->kind;
            $item->scheduled_end = date('Y-m-d', strtotime($item->scheduled_start.' +5 years'));
            $itemb->last_performed_at = date('Y-m-d', strtotime($item->scheduled_end.' +1 day'));
            $end = (isset($this->model->get('scheduled_end', 'mnt_preventive', " and preventive_id = '$id' ORDER BY scheduled_end DESC")->scheduled_end)) ? $this->model->get('scheduled_end', 'mnt_preventive', " and preventive_id = '$id' ORDER BY scheduled_end DESC")->scheduled_end : false;
            if ($end and $end < $itemb->last_performed_at and $end <= date('Y-m-d')) {
                $this->model->save('mnt_preventive', $item);
                $this->model->update('mnt_preventive_form', $itemb, $id);
                echo "$end - $itemb->last_performed_at <br>";
            } elseif (! $end and $r->last_performed_at <= date('Y-m-d')) {
                $this->model->save('mnt_preventive', $item);
                $this->model->update('mnt_preventive_form', $itemb, $id);
                echo "$itemb->last_performed_at <br>";
            }
        }

    }

    public function Rating()
    {
        // Tablas a procesar
        $tables = ['mnt', 'it'];

        $now = new DateTime;

        foreach ($tables as $table) {
            // Traemos todos los que no estén "Rated" y tengan closedAt
            $rows = $this->model->list('*', $table, "AND status <> 'Rated' AND closed_at IS NOT NULL");

            foreach ($rows as $row) {
                $closedAt = new DateTime($row->closed_at);
                $diff = $now->getTimestamp() - $closedAt->getTimestamp();

                // Si pasaron más de 72 horas
                if ($diff > 72 * 3600) {
                    $this->model->update($table,
                        (object) [
                            'rating' => 5,
                            'status' => 'Rated',
                        ],
                        $row->id
                    );
                }
            }
        }
    }
}
