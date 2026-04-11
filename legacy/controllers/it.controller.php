<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ITController
{
    public function __construct(public Model $model, private AuthService $auth) {}

    public function Index()
    {
        $user = $this->auth->authorize(29);
        $tabulator = true;
        $jspreadsheet = false;
        $kpis = true;
        $button = 'New Ticket';
        $content = 'app/components/list.php';
        $title = 'Infrastructure / IT / Service Desk';

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
            { "title": "Started", "field": "started_at", headerHozAlign: "center", headerFilter: customDateRangeFilter, headerFilterFunc: customDateFilterFunc, headerFilterLiveFilter: false },
            { "title": "Closed", "field": "closed", headerHozAlign: "center", headerFilter: customDateRangeFilter, headerFilterFunc: customDateFilterFunc, headerFilterLiveFilter: false },
            { "title": "Hours Worked", "field": "time", "sorter": "number", "width": 100, "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "SGC", "field": "sgc", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Rating", "field": "rating", "sorter": "number", "headerHozAlign": "left", "headerFilter": "number"},
        ]';

        require_once 'app/views/index.php';
    }

    public function New()
    {
        $user = $this->auth->authorize(29);
        require_once 'app/views/it/new.php';
    }

    public function Data()
    {
        // 1. Seguridad
        $user = $this->auth->authorize(29);
        $isExport = isset($_GET['export']);

        if (! $isExport) {
            header('Content-Type: application/json');
        }

        // 2. Parámetros
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $size = isset($_GET['size']) ? (int) $_GET['size'] : 50;
        $offset = ($page - 1) * $size;

        // 3. Mapeo
        $fieldMap = [
            'id' => 'a.id',
            'date' => 'a.created_at',
            'user' => 'b.username',
            'facility' => 'a.facility',
            'asset' => "concat(c.hostname, ' | ', c.serial, ' | ', c.sap)",
            'priority' => 'a.priority',
            'description' => 'a.description',
            'assignee' => 'd.username',
            'status' => 'a.status',
            'sgc' => 'a.sgc',
            'rating' => 'a.rating',
        ];

        // 4. Permisos y Filtros
        $permissions = json_decode($user->permissions ?? '[]', true);
        $canViewAll = ! empty(array_intersect([35, 44, 29], $permissions));

        $where = '';
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
                    if ($field === 'date' && strpos($value, ' to ') !== false) {
                        [$from, $to] = explode(' to ', $value);
                        $where .= " AND $dbField::date BETWEEN '$from' AND '$to'";
                    } else {
                        // CAST a TEXT para evitar errores de tipos en Postgres
                        $where .= " AND CAST($dbField AS TEXT) LIKE '%$value%'";
                    }
                }
            }
        }

        // 5. Configuración de Query
        // Joins con subquery para el SUM, evitando el GROUP BY global que rompe el a.*
        $joins = 'LEFT JOIN users b on a.user_id = b.id 
            LEFT JOIN assets c on a.asset_id = c.id
            LEFT JOIN users d on a.assignee_id = d.id 
            LEFT JOIN (SELECT it_id, SUM(duration) as total_time FROM it_items GROUP BY it_id) e ON a.id = e.it_id';

        $select = "a.*, b.username, d.username as assignee_name, 
            concat(c.hostname, ' | ', c.serial,' | ', c.sap) as asset_full,
            COALESCE(e.total_time, 0) as time_sum";

        // Preparamos las opciones para el nuevo Model
        $options = [
            'order' => "CASE a.status 
                    WHEN 'Open' THEN 1 WHEN 'Started' THEN 2 WHEN 'Attended' THEN 3 
                    WHEN 'Closed' THEN 4 WHEN 'Rated' THEN 5 WHEN 'Rejected' THEN 6 
                    ELSE 7 END, a.created_at DESC",
        ];

        if ($isExport) {
            setcookie('download_complete', 'true', time() + 30, '/');
            $rows = $this->model->list($select, 'it a', $where, $joins, $options);

            $spreadsheet = new Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();
            $headers = ['ID', 'Fecha', 'Usuario', 'Sede', 'Activo', 'Prioridad', 'Descripción', 'Asignado', 'Días', 'Inicio', 'Atendido', 'Cierre', 'Horas', 'Estado', 'Rating'];
            $sheet->fromArray($headers, null, 'A1');

            $exportData = [];
            foreach ($rows as $r) {
                $dClosed = (! empty($r->closed_at) && $r->closed_at != 'NULL' && $r->closed_at != '') ? new DateTime($r->closed_at) : new DateTime;
                $exportData[] = [
                    $r->id, $r->created_at, $r->username, $r->facility,
                    mb_convert_case($r->asset_full ?? '', MB_CASE_TITLE, 'UTF-8'),
                    $r->priority, $r->description, $r->assignee_name,
                    (new DateTime($r->created_at))->diff($dClosed)->days,
                    ($r->started_at && $r->started_at != 'NULL') ? Date::PHPToExcel(strtotime($r->started_at)) : '',
                    ($r->ended_at && $r->ended_at != 'NULL') ? Date::PHPToExcel(strtotime($r->ended_at)) : '',
                    ($r->closed_at && $r->closed_at != 'NULL') ? Date::PHPToExcel(strtotime($r->closed_at)) : '',
                    (float) $r->time_sum, $r->status, $r->sgc, $r->rating,
                ];
            }

            $sheet->fromArray($exportData, null, 'A2');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="Reporte_IT_'.date('dmY').'.xlsx"');
            (new Xlsx($spreadsheet))->save('php://output');
            exit;
        }

        // 6. Respuesta para Tabulator
        $total = (int) $this->model->get('COUNT(a.id) AS total', 'it a', $where)->total;

        // Agregamos paginación a las opciones
        $options['limit'] = $size;
        $options['offset'] = $offset;

        $rows = $this->model->list($select, 'it a', $where, $joins, $options);

        $data = [];
        foreach ($rows as $r) {
            $dClosed = (! empty($r->closed_at) && $r->closed_at != 'NULL' && $r->closed_at != '') ? new DateTime($r->closed_at) : new DateTime;
            $data[] = [
                'id' => $r->id, 'date' => $r->created_at, 'user' => $r->username,
                'facility' => $r->facility, 'asset' => mb_convert_case($r->asset_full ?? '', MB_CASE_TITLE, 'UTF-8'),
                'priority' => $r->priority, 'description' => $r->description, 'assignee' => $r->assignee_name,
                'days' => (new DateTime($r->created_at))->diff($dClosed)->days,
                'started_at' => $r->started_at, 'attended' => $r->ended_at, 'closed' => $r->closed_at,
                'time' => $r->time_sum, 'status' => $r->status, 'sgc' => $r->sgc, 'rating' => $r->rating,
            ];
        }

        echo json_encode(['data' => $data, 'last_page' => ceil($total / $size), 'last_row' => $total]);
    }

    public function Stats()
    {
        require_once 'app/views/it/stats.php';
    }

    public function Save()
    {
        try {
            $user = $this->auth->authorize(29);
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
            $id = $this->model->save('it', $item);
            if (! $id) {
                throw new Exception('Error saving maintenance record');
            }

            /* ==========================
            MANEJO DE ARCHIVOS
            ========================== */
            if (! empty($_FILES['files']['name'][0])) {

                $carpeta = "uploads/serviceDesk/userpics/$id/";
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
        $user = $this->auth->authorize(29);
        require_once 'app/views/it/detail.php';
    }

    public function Head()
    {
        $user = $this->auth->authorize(29);
        $canClose = ! empty(array_intersect(['104'], json_decode($user->permissions ?? '[]', true)));
        $canEdit = ! empty(array_intersect(['30'], json_decode($user->permissions ?? '[]', true)));
        if (! empty($_REQUEST['id'])) {
            $filters = 'and id = '.$_REQUEST['id'];
            $id = $this->model->get(
                '*',
                'it',
                $filters,
            );
        }
        require_once 'app/views/it/head.php';
    }

    public function Info()
    {
        $user = $this->auth->authorize(29);
        $canClose = ! empty(array_intersect(['104', '102'], json_decode($user->permissions ?? '[]', true)));
        if (! empty($_REQUEST['id'])) {
            $filters = 'and a.id = '.$_REQUEST['id'];
            $id = $this->model->get(
                'a.*, b.hostname,c.username,d.username as assignee',
                'it a',
                $filters,
                'LEFT JOIN assets b ON a.asset_id = b.id
                LEFT JOIN users c ON a.user_id = c.id
                LEFT JOIN users d ON a.assignee_id = d.id'
            );
        }
        require_once 'app/views/it/info.php';
    }

    public function Tab()
    {
        $user = $this->auth->authorize(29);

        if (! empty($_REQUEST['id'])) {
            $filters = 'and id = '.(int) $_REQUEST['id'];
            $id = $this->model->get('*', 'it', $filters);
        }

        // permisos
        $hasPermission = ! empty(
            array_intersect(['30'], json_decode($user->permissions ?? '[]', true))
        );

        // lógica final
        if (empty($id->assignee_id)) {
            // no hay asignado → cualquiera con permisos
            $canEdit = $hasPermission;
        } else {
            // hay asignado → debe ser el mismo usuario + permisos
            $canEdit = ($id->assignee_id == $user->id) && $hasPermission;
        }

        require_once 'app/views/it/tab.php';
    }

    public function Task()
    {
        $user = $this->auth->authorize(29);
        header('Content-Type: application/json');

        $itId = (int) ($_GET['id'] ?? 0);

        $rows = $this->model->list(
            'a.*, b.username',
            'it_items a',
            "and a.it_id = $itId",
            'LEFT JOIN users b ON a.user_id = b.id'
        );

        $data = [];

        foreach ($rows as $r) {

            // --- Evidences (MISMA lógica original, optimizada) ---
            $action = '';
            $directorio = "uploads/serviceDesk/pics/$r->id/";
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
        $user = $this->auth->authorize([30]);
        $modal = $_REQUEST['modal'];
        $filters = 'and id = '.$_REQUEST['id'];
        $id = $this->model->get('*', 'it', $filters);
        require_once "app/views/it/$modal.php";
    }

    public function SaveTask()
    {
        try {
            // Autorización
            $user = $this->auth->authorize(29);
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
            $item->it_id = $_REQUEST['id'] ?? null;

            $item->user_id = $_SESSION['id-SIGMA'] ?? null;

            if (empty($item->it_id)) {
                throw new Exception('Maintenance ID not provided');
            }

            /* ==========================
            VALIDAR / ACTUALIZAR it
            ========================== */

            if ($this->model->get('*', 'it', 'AND id ='.$item->it_id)->status == 'Open') {
                $it = new stdClass;
                $it->started_at = date('Y-m-d H:i:s');
                $it->status = 'Started';
                $it->assignee_id = $_SESSION['id-SIGMA'];
                $this->model->update('it', $it, $item->it_id);
            }

            /* ==========================
            GUARDAR ITEM
            ========================== */
            $id = $this->model->save('it_items', $item);
            if (! $id) {
                throw new Exception('Error saving maintenance item');
            }

            /* ==========================
            MANEJO DE ARCHIVOS
            ========================== */
            if (! empty($_FILES['files']['name'][0])) {

                $carpeta = "uploads/serviceDesk/pics/$id/";
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
                $save->it_id = $id;
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
                $save->it_id = $id;
                $update->{$_REQUEST['field']} = htmlspecialchars(trim($value));
            }

            if ($_REQUEST['field'] == 'reason') {
                $value = $_REQUEST[$_REQUEST['field']];
                $save->notes = 'reject reason:'.$_REQUEST['reason'];
                $save->it_id = $id;
                $update->status = 'Rejected';
                $update->ended_at = null;
                $close = 'closeNewModal';
                $update->{$_REQUEST['field']} = htmlspecialchars(trim($value));
            }

            if ($_REQUEST['field'] == 'rating') {
                $value = $_REQUEST[$_REQUEST['field']];
                $save->notes = 'rating notes:'.$_REQUEST['notes'];
                $save->it_id = $id;
                $update->status = 'Rated';
                $close = 'closeNewModal';
                $update->{$_REQUEST['field']} = htmlspecialchars(trim($value));
            }

            if ($_REQUEST['field'] == 'closed_at') {

                if (! $this->model->get('sgc', 'it', " and id = $id and sgc is not null")) {
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

                if (! $this->model->get('*', 'it_items', " and it_id = $id LIMIT 1")) {
                    $message = '{"type": "error", "message": "Add at least One task", "close" : ""}';
                    header('HX-Trigger: '.json_encode(['showMessage' => $message]));
                    http_response_code(400);
                    exit;
                }

                $update->{$_REQUEST['field']} = date('Y-m-d H:i:s');
                $update->status = 'Attended';
                $save->notes = 'Ticket set as Attended';
                $save->it_id = $id;
                $close = 'closeNewModal';
            }
            $this->model->save('it_items', $save);
            $id = $this->model->update('it', $update, $id);
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
        $user = $this->auth->authorize(29);
        $result = [];
        (! empty($_REQUEST['year'])) ? $year = $_REQUEST['year'] : $year = date('Y');

        $dates = [];
        $result1 = [];
        $result2 = [];
        $result3 = [];
        $i = 0;

        for ($m = 1; $m <= 12; $m++) {
            $mes_fmt = str_pad($m, 2, '0', STR_PAD_LEFT);
            $firstDay = "$year-$mes_fmt-01 00:00:00";
            $lastDay = date('Y-m-t', strtotime($firstDay)).' 23:59:59';
            $dateStr = date('M', strtotime($firstDay));

            /** 1. INDICADORES DE ATENCIÓN **/

            // TOTAL CREADOS - Usamos ?? 0 para evitar el Warning si get() devuelve false
            $resT = $this->model->get('COUNT(id) as n', 'it', " AND created_at >= '$firstDay' AND created_at <= '$lastDay'");
            $totalMes = (int) ($resT->n ?? 0);

            // MORA ENTRADA - Quitamos '0000-00-00' que rompe Postgres
            $resM = $this->model->get('COUNT(id) as n', 'it', " AND created_at < '$firstDay' AND (ended_at IS NULL OR ended_at >= '$firstDay')");
            $moraEntrada = (int) ($resM->n ?? 0);

            // ATENDIDOS QUE VENÍAN DE MORA
            $resAtMora = $this->model->get('COUNT(id) as n', 'it', " AND ended_at >= '$firstDay' AND ended_at <= '$lastDay' AND created_at < '$firstDay'");
            $atMoraReal = (int) ($resAtMora->n ?? 0);

            // ATENDIDOS TOTALES
            $resAtTotal = $this->model->get('COUNT(id) as n', 'it', " AND ended_at >= '$firstDay' AND ended_at <= '$lastDay'");
            $totalAt = (int) ($resAtTotal->n ?? 0);

            $cargaTotal = $totalMes + $moraEntrada;
            $pctAtencion = ($cargaTotal > 0) ? round(($totalAt / $cargaTotal) * 100) : 0;

            /** 2. LÓGICA DE EXTERNOS **/
            $resExt = $this->model->get('COUNT(DISTINCT a.id) as total', 'it a', " AND a.created_at >= '$firstDay' AND a.created_at <= '$lastDay' AND b.attends = 'External'", 'INNER JOIN it_items b ON a.id = b.it_id');
            $externalReal = (int) ($resExt->total ?? 0);
            $pctExterno = ($totalMes > 0) ? round(($externalReal / $totalMes) * 100) : 0;

            /** 3. LÓGICA DE ACUMULADOS **/
            $resTotalAcum = $this->model->get('COUNT(id) as total', 'it', " AND created_at <= '$lastDay'");
            $totalAcum = (int) ($resTotalAcum->total ?? 0);

            $resOpenAcum = $this->model->get('COUNT(id) as abiertos', 'it', " AND created_at <= '$lastDay' AND (ended_at IS NULL OR ended_at > '$lastDay')");
            $openAcum = (int) ($resOpenAcum->abiertos ?? 0);

            $pctAbiertos = ($totalAcum > 0) ? round(($openAcum / $totalAcum) * 100) : 0;

            $dates[] = $dateStr;
            $result[$i] = [
                'dateStr' => $dateStr,
                'date' => "$year-$mes_fmt-01",
                'total' => $totalMes,
                'mora' => $moraEntrada,
                'carga_total' => $cargaTotal,
                'at_mora' => $atMoraReal,
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

        $in = isset($_REQUEST['in']) ? $_REQUEST['in'] : 1;
        require_once 'app/views/it/kpis/index.php';
    }
}
