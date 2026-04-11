<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LocativeController
{
    public function __construct(public Model $model, private AuthService $auth) {}

    public function Index()
    {
        $user = $this->auth->authorize(34);
        $tabulator = true;
        $jspreadsheet = false;
        $kpis = true;
        $button = 'New Ticket';
        $content = 'app/components/list.php';
        $title = 'Infrastructure / Locative / Service Desk';

        $columns = '[
            { "title": "ID", "field": "id", "width": 60, "sorter": "number", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Date", "field": "date", headerHozAlign: "center", headerFilter: customDateRangeFilter, headerFilterFunc: customDateFilterFunc, headerFilterLiveFilter: false },
            { "title": "User", "field": "user", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Facility", "field": "facility", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Asset", "field": "asset", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Priority", "field": "priority", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Status", "field": "status", headerHozAlign: "center", hozAlign:"center", headerFilter:"list",
            headerFilterParams:{ values: {"Open": "Open", "Started": "Started", "Attended": "Attended", "Closed": "Closed", "Rated": "Rated", "Rejected": "Rejected"}, clearable:true}},
            { "title": "Description", "field": "description", "formatter": "textarea", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Assignee", "field": "assignee", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Days", "field": "days", "sorter": "number", "width": 100, "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Started", "field": "started", headerHozAlign: "center", headerFilter: customDateRangeFilter, headerFilterFunc: customDateFilterFunc, headerFilterLiveFilter: false },
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
        $user = $this->auth->authorize(34);
        require_once 'app/views/locative/new.php';
    }

    public function Data()
    {
        // 1. Seguridad
        $user = $this->auth->authorize(34);
        $isExport = isset($_GET['export']);

        if (! $isExport) {
            header('Content-Type: application/json');
        }

        // 2. Parámetros de Paginación
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $size = isset($_GET['size']) ? (int) $_GET['size'] : 50;
        $offset = ($page - 1) * $size;

        // 3. Mapeo Seguro de Campos
        $fieldMap = [
            'id' => 'a.id',
            'date' => 'a.created_at',
            'user' => 'b.username',
            'facility' => 'a.facility',
            'asset' => 'c.hostname',
            'priority' => 'a.priority',
            'description' => 'a.description',
            'assignee' => 'd.username',
            'status' => 'a.status',
            'sgc' => 'a.sgc',
            'cause' => 'a.root_cause',
            'rating' => 'a.rating',
        ];

        // 4. Lógica de Permisos (Locative)
        $permissions = json_decode($user->permissions ?? '[]', true);
        $canViewAll = ! empty(array_intersect([68, 69], $permissions));

        $where = " AND a.kind = 'Locative'";
        if (! $canViewAll) {
            $where .= " AND a.user_id = {$user->id}";
        }

        // 5. Procesamiento de Filtros
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

        // --- 6. Ordenamiento Personalizado ---
        // Definimos el orden por defecto: Jerarquía de Status y luego fecha DESC
        $defaultOrder = "FIELD(a.status, 'Open', 'Started', 'Attended', 'Closed', 'Rated', 'Rejected'), a.created_at DESC";
        $orderBy = $defaultOrder;

        // Si Tabulator envía un orden específico por clic en columna, lo respetamos
        if (isset($_GET['sort'][0]['field'])) {
            $sortField = $_GET['sort'][0]['field'];
            $sortDir = strtoupper($_GET['sort'][0]['dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
            if (isset($fieldMap[$sortField])) {
                $orderBy = $fieldMap[$sortField]." $sortDir";
            }
        }

        // 7. Definición de JOINs
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

        $selectFields = '
            a.*, b.username, d.username as assignee_name, 
            c.hostname as assetname,
            COALESCE(e.total_time, 0) as time_sum
        ';

        // 8. Ejecución y Salida
        if ($isExport) {
            setcookie('download_complete', 'true', time() + 30, '/');
            // En export siempre usamos el orden jerárquico solicitado
            $rows = $this->model->list($selectFields, 'mnt a', "$where ORDER BY $defaultOrder", $joins);

            $spreadsheet = new Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();

            // Cabeceras
            $headers = ['ID', 'Tipo', 'Fecha', 'Usuario', 'Sede', 'Activo', 'Prioridad', 'Descripción', 'Asignado', 'Días', 'Inicio', 'Atendido', 'Cierre', 'Horas', 'Estado', 'SGC', 'Causa', 'Rating'];
            $sheet->fromArray($headers, null, 'A1');

            $exportData = [];
            $now = new DateTime;

            foreach ($rows as $r) {
                $dateClosed = (! empty($r->closed_at) && $r->closed_at != '0000-00-00 00:00:00') ? new DateTime($r->closed_at) : $now;
                $dateCreated = new DateTime($r->created_at);

                $exportData[] = [
                    $r->id,
                    $r->subtype,
                    $r->created_at ? Date::PHPToExcel(strtotime($r->created_at)) : '',
                    $r->username,
                    $r->facility,
                    mb_convert_case($r->assetname ?? '', MB_CASE_TITLE, 'UTF-8'),
                    $r->priority,
                    $r->description,
                    $r->assignee_name,
                    $dateCreated->diff($dateClosed)->days,
                    ($r->started_at && $r->started_at != '0000-00-00 00:00:00') ? Date::PHPToExcel(strtotime($r->started_at)) : '',
                    ($r->ended_at && $r->ended_at != '0000-00-00 00:00:00') ? Date::PHPToExcel(strtotime($r->ended_at)) : '',
                    ($r->closed_at && $r->closed_at != '0000-00-00 00:00:00') ? Date::PHPToExcel(strtotime($r->closed_at)) : '',
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
            header('Content-Disposition: attachment; filename="Reporte_Locative_'.date('dmY').'.xlsx"');

            $writer = new Xlsx($spreadsheet);
            $writer->setPreCalculateFormulas(false);
            $writer->save('php://output');
            exit;
        }

        // Respuesta para Tabulator
        $totalCount = $this->model->get('COUNT(a.id) AS total', 'mnt a', $where, $joins)->total;
        $rows = $this->model->list($selectFields, 'mnt a', "$where ORDER BY $orderBy LIMIT $offset, $size", $joins);

        $data = [];
        $now = new DateTime;
        foreach ($rows as $r) {
            $dateClosed = (! empty($r->closed_at) && $r->closed_at != '0000-00-00 00:00:00') ? new DateTime($r->closed_at) : $now;
            $data[] = [
                'id' => $r->id,
                'type' => $r->subtype,
                'date' => date('Y-m-d', strtotime($r->created_at)),
                'user' => $r->username,
                'facility' => $r->facility,
                'asset' => mb_convert_case($r->assetname ?? '', MB_CASE_TITLE, 'UTF-8'),
                'priority' => $r->priority,
                'description' => $r->description,
                'assignee' => $r->assignee_name,
                'days' => (new DateTime($r->created_at))->diff($dateClosed)->days,
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
        require_once 'app/views/locative/stats.php';
    }

    public function Save()
    {
        try {
            $user = $this->auth->authorize(34);
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
        $user = $this->auth->authorize(34);
        require_once 'app/views/locative/detail.php';
    }

    public function Head()
    {
        $user = $this->auth->authorize(34);
        $canClose = ! empty(array_intersect(['69'], json_decode($user->permissions ?? '[]', true)));
        $canEdit = ! empty(array_intersect(['68'], json_decode($user->permissions ?? '[]', true)));
        if (! empty($_REQUEST['id'])) {
            $filters = 'and id = '.$_REQUEST['id'];
            $id = $this->model->get(
                '*',
                'mnt',
                $filters,
            );
        }
        require_once 'app/views/locative/head.php';
    }

    public function Info()
    {
        $user = $this->auth->authorize(34);
        $canClose = ! empty(array_intersect(['69'], json_decode($user->permissions ?? '[]', true)));
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
        require_once 'app/views/locative/info.php';
    }

    public function Tab()
    {
        $user = $this->auth->authorize(34);

        if (! empty($_REQUEST['id'])) {
            $filters = 'and id = '.(int) $_REQUEST['id'];
            $id = $this->model->get('*', 'mnt', $filters);
        }

        // permisos
        $hasPermission = ! empty(
            array_intersect(['68'], json_decode($user->permissions ?? '[]', true))
        );

        // lógica final
        if (empty($id->assignee_id)) {
            // no hay asignado → cualquiera con permisos
            $canEdit = $hasPermission;
        } else {
            // hay asignado → debe ser el mismo usuario + permisos
            $canEdit = ($id->assignee_id == $user->id) && $hasPermission;
        }

        require_once 'app/views/locative/tab.php';
    }

    public function Task()
    {
        $user = $this->auth->authorize(34);
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
        $user = $this->auth->authorize([68]);
        $modal = $_REQUEST['modal'];
        $filters = 'and id = '.$_REQUEST['id'];
        $id = $this->model->get('*', 'mnt', $filters);
        require_once "app/views/locative/$modal.php";
    }

    public function SaveTask()
    {
        try {
            // Autorización
            $user = $this->auth->authorize(34);
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
        $user = $this->auth->authorize(34);
        $result = [];
        (! empty($_REQUEST['year'])) ? $year = $_REQUEST['year'] : $year = date('Y');

        // Definimos $date para consistencia con la vista
        $date = $year.'-01-01';

        $dates = [];
        $result1 = [];
        $result2 = [];
        $result3 = [];
        $i = 0;

        // Filtro para Mantenimiento Locativo
        $locFilter = "AND kind = 'Locative'";

        for ($m = 1; $m <= 12; $m++) {
            $mes_fmt = str_pad($m, 2, '0', STR_PAD_LEFT);
            $firstDay = "$year-$mes_fmt-01";
            $lastDay = date('Y-m-t', strtotime($firstDay));
            $dateStr = date('M', strtotime($firstDay));

            /** 1. INDICADORES DE ATENCIÓN (Homologados) **/

            // TOTAL CREADOS: Tickets Locative abiertos en este mes
            $resT = $this->model->get('COUNT(id) as n', 'mnt', "AND DATE(created_at) >= '$firstDay' AND DATE(created_at) <= '$lastDay' $locFilter");
            $totalMes = (int) $resT->n;

            // MORA (PENDIENTES INICIALES):
            // 1. Creados antes del mes y atendidos en este mes o después
            // 2. Creados antes del mes y que siguen vacíos (sin fecha end)
            $resM = $this->model->get(
                'COUNT(id) as n',
                'mnt',
                "AND DATE(created_at) < '$firstDay' 
                AND (DATE(ended_at) >= '$firstDay' OR end IS NULL OR ended_at = '0000-00-00') 
                $locFilter"
            );
            $moraEntrada = (int) $resM->n;

            // ATENDIDOS: Estrictamente los que tienen fecha 'end' en este mes
            $resAtTotal = $this->model->get('COUNT(id) as n', 'mnt', "AND DATE(ended_at) >= '$firstDay' AND DATE(ended_at) <= '$lastDay' $locFilter");
            $totalAt = (int) $resAtTotal->n;

            $cargaTotal = $totalMes + $moraEntrada;

            // Porcentaje de atención respecto a la carga total
            $pctAtencion = ($cargaTotal > 0) ? round(($totalAt / $cargaTotal) * 100) : 0;

            /** 2. LÓGICA DE EXTERNOS **/
            $resExt = $this->model->get(
                'COUNT(DISTINCT a.id) as total',
                'mnt a',
                "AND DATE(a.created_at) >= '$firstDay' AND DATE(a.created_at) <= '$lastDay' AND a.kind = 'Locative' AND b.attends = 'External'",
                'INNER JOIN mnt_items b ON a.id = b.mnt_id'
            );
            $externalReal = (int) $resExt->total;
            $pctExterno = ($totalMes > 0) ? round(($externalReal / $totalMes) * 100) : 0;

            /** 3. LÓGICA DE ACUMULADOS **/
            $resTotalAcum = $this->model->get('COUNT(id) as total', 'mnt', "AND DATE(created_at) <= '$lastDay' $locFilter");
            $totalAcum = (int) $resTotalAcum->total;

            $resOpenAcum = $this->model->get('COUNT(id) as abiertos', 'mnt', "AND DATE(created_at) <= '$lastDay' AND (ended_at IS NULL OR ended_at = '0000-00-00' OR DATE(ended_at) > '$lastDay') $locFilter");
            $openAcum = (int) $resOpenAcum->abiertos;

            $pctAbiertos = ($totalAcum > 0) ? round(($openAcum / $totalAcum) * 100) : 0;

            // MAPEO DE RESULTADOS
            $dates[] = $dateStr;
            $result[$i] = [
                'dateStr' => $dateStr,
                'date' => "$year-$mes_fmt-01",
                'total' => $totalMes,
                'mora' => $moraEntrada,
                'carga_total' => $cargaTotal,
                'at_mes' => $totalAt,
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

        $no_data = empty($result);
        $in = isset($_REQUEST['in']) ? $_REQUEST['in'] : 1;
        require_once 'app/views/locative/kpis/index.php';
    }
}
