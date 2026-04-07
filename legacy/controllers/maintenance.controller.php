<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MaintenanceController
{
    public function __construct(public Model $model, private AuthService $auth) {}

    public function Index()
    {
        $user = $this->auth->authorize(93);
        $tabulator = true;
        $jspreadsheet = false;
        $kpis = true;
        $button = 'New Ticket';
        $content = 'app/components/list.php';
        $title = 'Infrastructure / Machinery / Service Desk';

        $columns = '[
            { "title": "ID", "field": "id", "width": 60, "sorter": "number", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Date", "field": "created_at", headerHozAlign: "center", headerFilter: customDateRangeFilter, headerFilterFunc: customDateFilterFunc, headerFilterLiveFilter: false },
            { "title": "User", "field": "user", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Facility", "field": "facility", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Asset", "field": "asset", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Priority", "field": "priority", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Status", "field": "status", headerHozAlign: "center", hozAlign:"center", headerFilter:"list",
            headerFilterParams:{ values: {"Open": "Open", "Started": "Started", "Attended": "Attended", "Closed": "Closed", "Rated": "Rated", "Rejected": "Rejected"}, clearable:true}},
            { "title": "Description", "field": "description", "formatter": "html", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Assignee", "field": "assignee", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Days", "field": "days", "sorter": "number", "width": 100, "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Started", "field": "started", headerHozAlign: "center", headerFilter: customDateRangeFilter, headerFilterFunc: customDateFilterFunc, headerFilterLiveFilter: false },
            { "title": "Attended", "field": "attended", headerHozAlign: "center", headerFilter: customDateRangeFilter, headerFilterFunc: customDateFilterFunc, headerFilterLiveFilter: false },
            { "title": "Closed", "field": "closed", headerHozAlign: "center", headerFilter: customDateRangeFilter, headerFilterFunc: customDateFilterFunc, headerFilterLiveFilter: false },
            { "title": "Hours Worked", "field": "time", "sorter": "number", "width": 100, "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "SGC", "field": "sgc", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Cause", "field": "cause", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Rating", "field": "rating", "sorter": "number", "headerHozAlign": "left", "headerFilter": "number"},
        ]';

        require_once 'app/views/index.php';
    }

    public function New()
    {
        $user = $this->auth->authorize(93);
        require_once 'app/views/maintenance/new.php';
    }

    public function Data()
    {
        // 1. Seguridad
        $user = $this->auth->authorize(93);
        $isExport = isset($_GET['export']);

        if (! $isExport) {
            header('Content-Type: application/json');
        }

        // 2. Parámetros de Paginación
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $size = isset($_GET['size']) ? (int) $_GET['size'] : 50;
        $offset = ($page - 1) * $size;

        // 3. Mapeo de campos técnicos (Postgres ILIKE para evitar líos de mayúsculas)
        $fieldMap = [
            'id' => 'a.id',
            'created_at' => 'a.created_at',
            'user' => 'b.username',
            'facility' => 'a.facility',
            'asset' => "concat(c.hostname, ' | ', c.serial, ' | ', c.sap)",
            'priority' => 'a.priority',
            'description' => 'a.description',
            'assignee' => 'd.username',
            'status' => 'a.status',
            'sgc' => 'a.sgc',
            'cause' => 'a.root_cause',
            'rating' => 'a.rating',
        ];

        // 4. Lógica de Filtros y Permisos
        $permissions = json_decode($user->permissions ?? '[]', true);
        $canViewAll = ! empty(array_intersect([35, 44], $permissions));

        $where = " AND a.kind = 'Machinery'";
        if (! $canViewAll) {
            $where .= " AND a.user_id = {$user->id}";
        }

        if (isset($_GET['filter']) && is_array($_GET['filter'])) {
            foreach ($_GET['filter'] as $key => $f) {
                $field = is_array($f) ? ($f['field'] ?? '') : $key;
                $value = is_array($f) ? ($f['value'] ?? '') : $f;
                $value = addslashes((string) $value);

                if (isset($fieldMap[$field])) {
                    $dbField = $fieldMap[$field];
                    if ($field === 'created_at') {
                        if (strpos($value, ' to ') !== false) {
                            [$from, $to] = explode(' to ', $value);
                            $where .= " AND a.created_at::date BETWEEN '$from' AND '$to'";
                        } else {
                            $where .= " AND a.created_at::text LIKE '%$value%'";
                        }
                    } else {
                        $where .= " AND $dbField ILIKE '%$value%'";
                    }
                }
            }
        }

        // --- ORDEN JERÁRQUICO (Homologado Postgres) ---
        $customOrder = "CASE a.status 
        WHEN 'Open' THEN 1 
        WHEN 'Started' THEN 2 
        WHEN 'Attended' THEN 3 
        WHEN 'Closed' THEN 4 
        WHEN 'Rated' THEN 5 
        WHEN 'Rejected' THEN 6 
        ELSE 7 END, a.created_at DESC";

        // 5. Query principal
        $joins = '
        LEFT JOIN users b on a.user_id = b.id 
        LEFT JOIN assets c on a.asset_id = c.id
        LEFT JOIN users d on a.assignee_id = d.id 
        LEFT JOIN (
            SELECT mnt_id, SUM(duration) as total_time 
            FROM mnt_items 
            GROUP BY mnt_id
        ) e ON a.id = e.mnt_id
    ';

        $select = "a.*, b.username, d.username as assignee_name, 
            concat(c.hostname, ' | ', c.serial,' | ', c.sap) as asset_full,
            COALESCE(e.total_time, 0) as time_sum";

        // 6. Lógica de Exportación
        if ($isExport) {
            setcookie('download_complete', 'true', time() + 30, '/');

            // El nuevo list añade el ORDER BY por ti si pasas la opción
            $rows = $this->model->list($select, 'mnt a', $where, $joins, ['order' => $customOrder]);

            $spreadsheet = new Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();
            $headers = ['ID', 'Tipo', 'Fecha', 'Usuario', 'Sede', 'Activo', 'Prioridad', 'Descripción', 'Asignado', 'Días', 'Inicio', 'Atendido', 'Cierre', 'Horas', 'Estado', 'SGC', 'Causa', 'Rating'];
            $sheet->fromArray($headers, null, 'A1');

            $exportData = [];
            $now = new DateTime;

            foreach ($rows as $r) {
                $dateCreated = new DateTime($r->created_at);
                // Postgres = NULL, no ceros
                $dateClosed = (! empty($r->closed_at)) ? new DateTime($r->closed_at) : $now;

                $exportData[] = [
                    $r->id,
                    $r->subtype,
                    $r->created_at ? Date::PHPToExcel(strtotime($r->created_at)) : '',
                    $r->username,
                    $r->facility,
                    mb_convert_case($r->asset_full ?? '', MB_CASE_TITLE, 'UTF-8'),
                    $r->priority,
                    $r->description,
                    $r->assignee_name,
                    $dateCreated->diff($dateClosed)->days,
                    (! empty($r->started_at)) ? Date::PHPToExcel(strtotime($r->started_at)) : '',
                    (! empty($r->ended_at)) ? Date::PHPToExcel(strtotime($r->ended_at)) : '',
                    (! empty($r->closed_at)) ? Date::PHPToExcel(strtotime($r->closed_at)) : '',
                    (float) $r->time_sum,
                    $r->status,
                    $r->sgc,
                    $r->root_cause,
                    $r->rating,
                ];
            }

            $sheet->fromArray($exportData, null, 'A2');
            $lastRow = count($exportData) + 1;
            $sheet->getStyle("C2:C$lastRow")->getNumberFormat()->setFormatCode('yyyy-mm-dd');
            $sheet->getStyle("K2:M$lastRow")->getNumberFormat()->setFormatCode('yyyy-mm-dd');
            foreach (range('A', 'R') as $col) {
                $sheet->getColumnDimension($col)->setWidth(16);
            }

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="Reporte_Machinery_'.date('dmY').'.xlsx"');
            $writer = new Xlsx($spreadsheet);
            $writer->setPreCalculateFormulas(false);
            $writer->save('php://output');
            exit;
        }

        // 7. Respuesta JSON Normal (Tabulator)
        $totalRes = $this->model->get('COUNT(a.id) AS total', 'mnt a', $where, $joins);
        $totalCount = $totalRes ? $totalRes->total : 0;

        // Pasamos paginación como pide el nuevo Model
        $options = [
            'order' => $customOrder,
            'limit' => $size,
            'offset' => $offset,
        ];

        $rows = $this->model->list($select, 'mnt a', $where, $joins, $options);

        $data = [];
        $now = new DateTime;
        foreach ($rows as $r) {
            $dateCreated = new DateTime($r->created_at);
            $dateClosed = (! empty($r->closed_at)) ? new DateTime($r->closed_at) : $now;

            $data[] = [
                'id' => $r->id,
                'type' => $r->subtype,
                'created_at' => $dateCreated->format('Y-m-d'),
                'user' => $r->username,
                'facility' => $r->facility,
                'asset' => mb_convert_case($r->asset_full ?? '', MB_CASE_TITLE, 'UTF-8'),
                'priority' => $r->priority,
                'description' => $r->description,
                'assignee' => $r->assignee_name,
                'days' => $dateCreated->diff($dateClosed)->days,
                'started' => $r->started_at,
                'attended' => $r->ended_at,
                'closed' => $r->closed_at,
                'time' => $r->time_sum,
                'status' => $r->status,
                'sgc' => $r->sgc,
                'cause' => $r->root_cause,
                'rating' => $r->rating,
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
        require_once 'app/views/maintenance/stats.php';
    }

    public function Save()
    {
        try {
            $user = $this->auth->authorize(93);
            header('Content-Type: application/json');

            // Crear objeto con datos del formulario
            $item = new stdClass;
            foreach ($_POST as $k => $val) {
                if (! empty($val) && $k !== 'id') {
                    $item->{$k} = htmlspecialchars(trim($val));
                }
            }

            // Datos fijos
            $item->user_id = $_SESSION['id-SIGMA'] ?? null;
            $item->status = 'Open';

            // Guardar registro principal
            $id = $this->model->save('mnt', $item);
            if (! $id) {
                throw new Exception('Error saving maintenance record');
            }

            /* ==========================
            MANEJO DE ARCHIVOS
            ========================== */
            if (! empty($_FILES['files']['name'][0])) {

                $carpeta = "uploads/mnt/userpics/$id/";
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
            $message = [
                'type' => 'success',
                'message' => 'Maintenance saved',
                'close' => 'closeNewModal',
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
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function Detail()
    {
        $user = $this->auth->authorize(93);
        require_once 'app/views/maintenance/detail.php';
    }

    public function Head()
    {
        $user = $this->auth->authorize(93);
        $canClose = ! empty(array_intersect(['44'], json_decode($user->permissions ?? '[]', true)));
        $canEdit = ! empty(array_intersect(['35'], json_decode($user->permissions ?? '[]', true)));
        if (! empty($_REQUEST['id'])) {
            $filters = 'and id = '.$_REQUEST['id'];
            $id = $this->model->get(
                '*',
                'mnt',
                $filters,
            );
        }
        require_once 'app/views/maintenance/head.php';
    }

    public function Info()
    {
        $user = $this->auth->authorize(93);
        $canClose = ! empty(array_intersect(['44'], json_decode($user->permissions ?? '[]', true)));
        if (! empty($_REQUEST['id'])) {
            $filters = 'and a.id = '.$_REQUEST['id'];
            $id = $this->model->get(
                'a.*, b.hostname,c.username,d.username as assignee',
                'mnt a',
                $filters,
                'LEFT JOIN assets b ON a.asset_id = b.id
                LEFT JOIN users c ON a.user_id = c.id
                LEFT JOIN users d ON a.assignee_id = d.id'
            );
        }
        require_once 'app/views/maintenance/info.php';
    }

    public function Tab()
    {
        $user = $this->auth->authorize(93);

        if (! empty($_REQUEST['id'])) {
            $filters = 'and id = '.(int) $_REQUEST['id'];
            $id = $this->model->get('*', 'mnt', $filters);
        }

        // permisos
        $hasPermission = ! empty(
            array_intersect(['35'], json_decode($user->permissions ?? '[]', true))
        );

        // lógica final
        if (empty($id->assignee_id)) {
            // no hay asignado → cualquiera con permisos
            $canEdit = $hasPermission;
        } else {
            // hay asignado → debe ser el mismo usuario + permisos
            $canEdit = ($id->assignee_id == $user->id) && $hasPermission;
        }

        require_once 'app/views/maintenance/tab.php';
    }

    public function Task()
    {
        $user = $this->auth->authorize(93);
        header('Content-Type: application/json');

        $mntId = (int) ($_GET['id'] ?? 0);

        $rows = $this->model->list(
            'a.*, b.username',
            'mnt_items a',
            "and a.mnt_id = $mntId",
            'LEFT JOIN users b ON a.user_id = b.id'
        );

        $data = [];

        foreach ($rows as $r) {

            // --- Evidences (MISMA lógica original, optimizada) ---
            $action = '';
            $directorio = "uploads/mnt/pics/$r->id/";
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
        $id = $this->model->get('*', 'mnt', $filters);
        require_once "app/views/maintenance/$modal.php";
    }

    public function SaveTask()
    {
        try {
            // Autorización
            $user = $this->auth->authorize(93);
            header('Content-Type: application/json');

            /* ==========================
            CREAR OBJETO ITEM
            ========================== */
            $item = new stdClass;

            foreach ($_POST as $k => $val) {
                if (! empty($val) && $k !== 'id') {
                    $item->{$k} = htmlspecialchars(trim($val));
                }
            }

            // Datos fijos
            $item->mnt_id = $_REQUEST['id'] ?? null;

            $item->user_id = $_SESSION['id-SIGMA'] ?? null;

            if (empty($item->mnt_id)) {
                throw new Exception('Maintenance ID not provided');
            }

            /* ==========================
            VALIDAR / ACTUALIZAR MNT
            ========================== */
            $mntId = $item->mnt_id;
            $exists = $this->model->get('*', 'mnt_items', "AND mnt_id = $mntId");

            if (empty($exists->id)) {
                $mnt = new stdClass;
                $mnt->started_at = date('Y-m-d H:i:s');
                $mnt->status = 'Started';
                $mnt->assignee_id = $_SESSION['id-SIGMA'];
                $this->model->update('mnt', $mnt, $mntId);
            }

            /* ==========================
            GUARDAR ITEM
            ========================== */
            $id = $this->model->save('mnt_items', $item);
            if (! $id) {
                throw new Exception('Error saving maintenance item');
            }

            /* ==========================
            MANEJO DE ARCHIVOS
            ========================== */
            if (! empty($_FILES['files']['name'][0])) {

                $carpeta = "uploads/mnt/pics/$id/";
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
            $close = '';
            $id = $_REQUEST['id'];
            $save = new stdClass;
            $update = new stdClass;

            $save->user_id = $_SESSION['id-SIGMA'];

            if ($_REQUEST['field'] == 'priority') {
                $value = $_REQUEST[$_REQUEST['field']];
                $save->mnt_id = $id;
                $save->notes = "priority updated to $value";
                $update->{$_REQUEST['field']} = htmlspecialchars(trim($value));
            }

            if ($_REQUEST['field'] == 'asset_id') {
                $value = $_REQUEST[$_REQUEST['field']];
                $update->{$_REQUEST['field']} = htmlspecialchars(trim($value));
            }

            if ($_REQUEST['field'] == 'sgc') {
                $value = $_REQUEST[$_REQUEST['field']];
                $update->{$_REQUEST['field']} = htmlspecialchars(trim($value));
            }

            if ($_REQUEST['field'] == 'root_cause') {
                $value = $_REQUEST[$_REQUEST['field']];
                $update->{$_REQUEST['field']} = htmlspecialchars(trim($value));
            }

            if ($_REQUEST['field'] == 'assignee_id') {
                $value = $_REQUEST[$_REQUEST['field']];
                $asignee = $this->model->get('username', 'users', "and id = $value")->username;
                $save->notes = "assignee updated to $asignee";
                $save->mnt_id = $id;
                $update->{$_REQUEST['field']} = htmlspecialchars(trim($value));
            }

            if ($_REQUEST['field'] == 'reason') {
                $value = $_REQUEST[$_REQUEST['field']];
                $save->notes = 'reject reason:'.$_REQUEST['reason'];
                $save->mnt_id = $id;
                $update->status = 'Rejected';
                $update->ended_at = null;
                $close = 'closeNewModal';
                $update->{$_REQUEST['field']} = htmlspecialchars(trim($value));
            }

            if ($_REQUEST['field'] == 'rating') {
                $value = $_REQUEST[$_REQUEST['field']];
                $save->notes = 'rating notes:'.$_REQUEST['notes'];
                $save->mnt_id = $id;
                $update->status = 'Rated';
                $close = 'closeNewModal';
                $update->{$_REQUEST['field']} = htmlspecialchars(trim($value));
            }

            if ($_REQUEST['field'] == 'closed_at') {

                if (! $this->model->get('sgc', 'mnt', " and id = $id and sgc is not null")) {
                    $message = '{"type": "error", "message": "SGC Missing", "close" : ""}';
                    header('HX-Trigger: '.json_encode(['showMessage' => $message]));
                    http_response_code(400);
                    exit;
                }

                if (! $this->model->get('root_cause', 'mnt', " and id = $id and root_cause is not null")) {
                    $message = '{"type": "error", "message": "Cause Missing", "close" : ""}';
                    header('HX-Trigger: '.json_encode(['showMessage' => $message]));
                    http_response_code(400);
                    exit;
                }

                $update->{$_REQUEST['field']} = date('Y-m-d H:i:s');
                $update->status = 'Closed';
                $close = 'closeNewModal';
            }

            if ($_REQUEST['field'] == 'ended_at') {

                if (! $this->model->get('*', 'mnt_items', " and mnt_id = $id LIMIT 1")) {
                    $message = '{"type": "error", "message": "Add at least One task", "close" : ""}';
                    header('HX-Trigger: '.json_encode(['showMessage' => $message]));
                    http_response_code(400);
                    exit;
                }

                $update->{$_REQUEST['field']} = date('Y-m-d H:i:s');
                $update->status = 'Attended';
                $save->notes = 'Ticket set as Attended';
                $save->mnt_id = $id;
                $close = 'closeNewModal';
            }
            $this->model->save('mnt_items', $save);
            $id = $this->model->update('mnt', $update, $id);
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
        $user = $this->auth->authorize(93);
        $result = [];
        (! empty($_REQUEST['year'])) ? $year = $_REQUEST['year'] : $year = date('Y');

        // Definimos $date para que la vista no de error
        $date = $year.'-01-01';

        $dates = [];
        $result1 = [];
        $result2 = [];
        $result3 = [];

        $indb = [];
        $indbCantidad = [];
        $i = 0;

        $locFilter = "AND kind = 'Machinery'";

        for ($m = 1; $m <= 12; $m++) {
            $mes_fmt = str_pad($m, 2, '0', STR_PAD_LEFT);
            $firstDay = "$year-$mes_fmt-01 00:00:00";
            $lastDay = date('Y-m-t', strtotime($firstDay)).' 23:59:59';
            $dateStr = date('M', strtotime($firstDay));

            /** 1. INDICADORES DE ATENCIÓN **/
            // TOTAL CREADOS
            $resT = $this->model->get('COUNT(id) as n', 'mnt', "AND created_at >= '$firstDay' AND created_at <= '$lastDay' $locFilter");
            $totalMes = (int) $resT->n;

            // MORA ENTRADA (92)
            $resM = $this->model->get('COUNT(id) as n', 'mnt', "AND created_at < '$firstDay' AND (ended_at IS NULL OR ended_at >= '$firstDay') $locFilter");
            $moraEntrada = (int) $resM->n;

            // --- NUEVO DESGLOSE DE ATENDIDOS ---
            // Atendidos que venían de MORA (Cerrados este mes pero creados antes)
            $resAtMora = $this->model->get('COUNT(id) as n', 'mnt', "AND ended_at >= '$firstDay' AND ended_at <= '$lastDay' AND created_at < '$firstDay' $locFilter");
            $atMoraReal = (int) $resAtMora->n;

            // ATENDIDOS TOTALES: Todo lo cerrado en el mes independientemente de su creación
            $resAtTotal = $this->model->get('COUNT(id) as n', 'mnt', "AND ended_at >= '$firstDay' AND ended_at <= '$lastDay' $locFilter");
            $totalAt = (int) $resAtTotal->n;

            $atMesTabla = $totalAt;

            $cargaTotal = $totalMes + $moraEntrada;
            $pctAtencion = ($cargaTotal > 0) ? round(($totalAt / $cargaTotal) * 100) : 0;

            // Externos
            $resExt = $this->model->get('COUNT(DISTINCT a.id) as total', 'mnt a', "AND a.created_at >= '$firstDay' AND a.created_at <= '$lastDay' AND a.kind = 'Machinery' AND b.attends = 'External'", 'INNER JOIN mnt_items b ON a.id = b.mnt_id');
            $externalReal = (int) $resExt->total;
            $pctExterno = ($totalMes > 0) ? round(($externalReal / $totalMes) * 100) : 0;

            // Acumulados
            $resTotalAcum = $this->model->get('COUNT(id) as total', 'mnt', "AND created_at <= '$lastDay' $locFilter");
            $totalAcum = (int) $resTotalAcum->total;
            $resOpenAcum = $this->model->get('COUNT(id) as abiertos', 'mnt', "AND created_at <= '$lastDay' AND (ended_at IS NULL OR ended_at > '$lastDay') $locFilter");
            $openAcum = (int) $resOpenAcum->abiertos;
            $pctAbiertos = ($totalAcum > 0) ? round(($openAcum / $totalAcum) * 100) : 0;

            /** 2. CANTIDADES POR CATEGORÍA **/
            $cCorr = (int) $this->model->get('COUNT(id) as n', 'mnt', "AND created_at >= '$firstDay' AND created_at <= '$lastDay' AND sgc = 'corrective' $locFilter")->n;
            $cProd = (int) $this->model->get('COUNT(id) as n', 'mnt', "AND created_at >= '$firstDay' AND created_at <= '$lastDay' AND sgc = 'production' $locFilter")->n;
            $cPrev = (int) $this->model->get('COUNT(id) as n', 'mnt', "AND created_at >= '$firstDay' AND created_at <= '$lastDay' AND sgc = 'preventive' $locFilter")->n;
            $cInfr = (int) $this->model->get('COUNT(id) as n', 'mnt', "AND created_at >= '$firstDay' AND created_at <= '$lastDay' AND kind = 'Infrastructure'")->n;
            $cUncl = (int) $this->model->get('COUNT(id) as n', 'mnt', "AND created_at >= '$firstDay' AND created_at <= '$lastDay' AND (sgc IS NULL OR sgc = '') $locFilter")->n;
            $cForm = (int) $this->model->get('COUNT(id) as n', 'mnt_preventive', "AND created_at >= '$firstDay' AND created_at <= '$lastDay'")->n;

            $indbCantidad[] = [
                'dateStr' => $dateStr, 'corrective' => $cCorr, 'production' => $cProd, 'infrastructure' => $cInfr, 'preventive' => $cPrev, 'preventive_form' => $cForm, 'unclassified' => $cUncl,
            ];

            /** 3. TIEMPOS POR CATEGORÍA **/
            $tWhere = "AND b.created_at >= '$firstDay' AND b.created_at <= '$lastDay'";
            $indb[] = [
                'dateStr' => $dateStr,
                'corrective' => number_format(((float) $this->model->get('SUM(a.duration) as s', 'mnt_items a', "$tWhere AND b.sgc='corrective' $locFilter", 'LEFT JOIN mnt b ON a.mnt_id=b.id')->s) / 60, 1),
                'production' => number_format(((float) $this->model->get('SUM(a.duration) as s', 'mnt_items a', "$tWhere AND b.sgc='production' $locFilter", 'LEFT JOIN mnt b ON a.mnt_id=b.id')->s) / 60, 1),
                'infrastructure' => number_format(((float) $this->model->get('SUM(a.duration) as s', 'mnt_items a', "AND b.created_at >= '$firstDay' AND b.created_at <= '$lastDay' AND b.kind='Infrastructure'", 'LEFT JOIN mnt b ON a.mnt_id=b.id')->s) / 60, 1),
                'preventive' => number_format(((float) $this->model->get('SUM(a.duration) as s', 'mnt_items a', "$tWhere AND b.sgc='preventive' $locFilter", 'LEFT JOIN mnt b ON a.mnt_id=b.id')->s) / 60, 1),
                'preventive_form' => number_format(((float) $this->model->get('SUM(a.duration) as s', 'mntp_items a', "AND b.created_at >= '$firstDay' AND b.created_at <= '$lastDay'", 'LEFT JOIN mnt_preventive b ON a.mntp_id=b.id')->s) / 60, 1),
                'unclassified' => number_format(((float) $this->model->get('SUM(a.duration) as s', 'mnt_items a', "$tWhere AND (b.sgc IS NULL OR b.sgc='') $locFilter", 'LEFT JOIN mnt b ON a.mnt_id=b.id')->s) / 60, 1),
            ];

            $dates[] = $dateStr;
            $result[$i] = [
                'dateStr' => $dateStr,
                'date' => "$year-$mes_fmt-01",
                'total' => $totalMes,
                'mora' => $moraEntrada,
                'carga_total' => $cargaTotal,
                'at_mora' => $atMoraReal, // <-- NUEVO
                'at_mes' => $atMesTabla,
                'at_pnd' => 0,
                'total_at' => $totalAt,
                'attended' => $totalAt,
                'external' => $externalReal,
                'totall' => $totalAcum,
                'open' => $openAcum,
                'result1' => $pctAtencion,
                'result2' => $pctExterno,
                'result3' => $pctAbiertos,
            ];
            $result1[] = $pctAtencion;
            $result2[] = $pctExterno;
            $result3[] = $pctAbiertos;
            $i++;
        }

        // --- MANTENEMOS LOS DEMÁS REPORTES INTACTOS ---
        $areas = $this->model->list("c.so, DATE_FORMAT(a.created_at, '%b') AS dateStr, SUM(a.duration) AS total", 'mnt_items a', " and so is not null AND YEAR(a.created_at) = '$year' AND b.kind = 'Machinery' GROUP BY c.so, DATE_FORMAT(a.created_at, '%b') ORDER BY a.created_at ASC", 'LEFT JOIN mnt b ON mnt_id = b.id LEFT JOIN assets c ON b.asset_id = c.id');
        $areas_by_month = [];
        foreach ($areas as $r) {
            $areas_by_month[$r->dateStr][$r->so] = number_format($r->total / 60, 1);
        }

        $areasCantidad = $this->model->list("c.so, DATE_FORMAT(a.created_at, '%b') AS dateStr, COUNT(a.id) AS total", 'mnt_items a', " and so is not null AND YEAR(a.created_at) = '$year' AND b.kind = 'Machinery' GROUP BY c.so, DATE_FORMAT(a.created_at, '%b') ORDER BY a.created_at ASC", 'LEFT JOIN mnt b ON mnt_id = b.id LEFT JOIN assets c ON b.asset_id = c.id');
        $areas_by_month_cantidad = [];
        foreach ($areasCantidad as $r) {
            $areas_by_month_cantidad[$r->dateStr][$r->so] = $r->total;
        }

        $causes = $this->model->list("root_cause, DATE_FORMAT(a.created_at, '%b') AS dateStr, SUM(a.duration) AS total", 'mnt_items a', " and root_cause is not null and root_cause <> 'N/A' AND YEAR(a.created_at) = '$year' AND b.kind = 'Machinery' GROUP BY root_cause, DATE_FORMAT(a.created_at, '%b') ORDER BY a.created_at ASC", 'LEFT JOIN mnt b ON mnt_id = b.id');
        $causes_by_month = [];
        foreach ($causes as $r) {
            $causes_by_month[$r->dateStr][$r->root_cause] = number_format($r->total / 60, 1);
        }

        $machinesTime = $this->model->list("DATE_FORMAT(a.created_at, '%Y-%m') AS month, c.hostname as machine, sum(a.duration) as total", 'mnt_items a', " and a.duration is not null and c.hostname is not null and b.kind = 'Machinery' and year(a.created_at) >= 2024 GROUP BY month, machine ORDER BY month, machine", ' LEFT JOIN mnt b ON mnt_id = b.id LEFT JOIN assets c on b.asset_id = c.id');
        $months = [];
        $machines = [];
        $data = [];
        foreach ($machinesTime as $r) {
            if (! in_array($r->month, $months)) {
                $months[] = $r->month;
            }
            if (! in_array($r->machine, $machines)) {
                $machines[] = $r->machine;
            }
            $available = (24 * 30) - ($r->total / 60);
            $data[$r->month][$r->machine] = "<span title='".number_format($r->total / 60, 1).' horas Ocupada, '.number_format($available, 1)." Disponible'>".number_format($available * 100 / (24 * 30), 1).'%</span>';
        }

        $in = isset($_REQUEST['in']) ? $_REQUEST['in'] : 1;
        require_once 'app/views/maintenance/kpis/index.php';
    }
}
