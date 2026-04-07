<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ImprovementController
{
    public function __construct(public Model $model, private AuthService $auth) {}

    public function Index()
    {
        $user = $this->auth->authorize(110);
        $tabulator = true;
        $kpis = true;
        $button = 'New Improvement Plan';
        $content = 'app/components/list.php';
        $title = 'SGC / Improvement';
        $columns = '[
            { "title": "ID", "field": "id", "width": 60, "sorter": "number", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Date", "field": "occurrence_date", headerHozAlign: "center", headerFilter: customDateRangeFilter, headerFilterFunc: customDateFilterFunc, headerFilterLiveFilter: false },
            { "title": "Code", "field": "code", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Creator", "field": "creator", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Responsible", "field": "responsible", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Process", "field": "process", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Status", "field": "status", "headerHozAlign": "center", "hozAlign":"center", "headerFilter":"list", 
                "headerFilterParams":{ 
                    "values": {"Open": "Open", "Plan": "Plan", "Analysis": "Analysis", "Closure": "Closure", "Closed": "Closed"}, 
                    "clearable":true
                }
            },
            { "title": "Description", "field": "description", "formatter": "textarea", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Type", "field": "kind", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Source", "field": "source", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Perspective", "field": "perspective", "headerHozAlign": "left", "headerFilter": "input"},
            
        ]';

        require_once 'app/views/index.php';
    }

    public function New()
    {
        $user = $this->auth->authorize(110);
        require_once 'app/views/improvement/new.php';
    }

    public function Data()
    {
        // 1. Seguridad
        $user = $this->auth->authorize(110); // Permiso original 110
        $isExport = isset($_GET['export']);

        if (! $isExport) {
            header('Content-Type: application/json');
        }

        // 2. Parámetros de Paginación (Tabulator envía page y size)
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $size = isset($_GET['size']) ? (int) $_GET['size'] : 50;
        $offset = ($page - 1) * $size;

        // 3. Mapeo de campos para filtros remotos
        $fieldMap = [
            'id' => 'a.id',
            'occurrence_date' => 'a.occurrence_date',
            'kind' => 'a.kind',
            'creator' => 'b.username',
            'responsible' => 'c.username',
            'status' => 'a.status',
            'process' => 'a.process',
        ];

        $canEdit = ! empty(array_intersect([134], json_decode($user->permissions ?? '[]', true)));

        $where = '';
        if (! $canEdit) {
            $where .= " AND (a.user_id = {$user->id} or a.responsible_id = {$user->id})";
        }

        if (isset($_GET['filter']) && is_array($_GET['filter'])) {
            foreach ($_GET['filter'] as $f) {
                $field = $f['field'] ?? '';
                $value = addslashes($f['value'] ?? '');

                if (isset($fieldMap[$field])) {
                    $dbField = $fieldMap[$field];
                    if ($field === 'occurrence_date') {
                        if (strpos($value, ' to ') !== false) {
                            [$from, $to] = explode(' to ', $value);
                            $where .= " AND DATE($dbField) BETWEEN '$from' AND '$to'";
                        } else {
                            $where .= " AND DATE($dbField) LIKE '%$value%'";
                        }
                    } else {
                        $where .= " AND $dbField LIKE '%$value%'";
                    }
                }
            }
        }

        // --- ORDEN Y QUERY ---
        $customOrder = 'ORDER BY a.id DESC';
        $joins = 'LEFT JOIN users b on a.user_id = b.id LEFT JOIN users c on a.responsible_id = c.id';

        // Select con la subquery para el número correlativo (manteniendo tu lógica original)
        $select = "a.*, b.username as creator_name, c.username as responsible_name,
                SUBSTRING_INDEX(a.process, ' || ', 1) AS code_prefix,
                (SELECT COUNT(*) FROM improvement AS t2 
                WHERE SUBSTRING_INDEX(t2.process, ' || ', 1) = SUBSTRING_INDEX(a.process, ' || ', 1) 
                AND t2.id <= a.id) AS seq_number";

        // 5. Lógica de Exportación (Excel)
        if ($isExport) {
            setcookie('download_complete', 'true', time() + 30, '/');
            $rows = $this->model->list($select, 'improvement a', "$where $customOrder", $joins);

            $spreadsheet = new Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();

            $headers = ['ID', 'Fecha', 'Código', 'Creador', 'Responsable', 'Tipo', 'Origen', 'Proceso', 'Perspectiva', 'Estado'];
            $sheet->fromArray($headers, null, 'A1');

            $exportData = [];
            foreach ($rows as $r) {
                $exportData[] = [
                    $r->id, $r->created_at, "$r->code_prefix-$r->seq_number", $r->creator_name,
                    $r->responsible_name, $r->kind, $r->source, $r->process, $r->perspective, $r->status,
                ];
            }
            $sheet->fromArray($exportData, null, 'A2');

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="Mejoras_'.date('dmY').'.xlsx"');
            (new Xlsx($spreadsheet))->save('php://output');
            exit;
        }

        // 6. Respuesta JSON para Tabulator
        $totalCount = $this->model->get('COUNT(a.id) AS total', 'improvement a', $where, $joins)->total;
        $rows = $this->model->list($select, 'improvement a', "$where $customOrder LIMIT $offset, $size", $joins);

        $data = [];
        foreach ($rows as $r) {
            $data[] = [
                'id' => $r->id,
                'occurrence_date' => $r->occurrence_date,
                'code' => "$r->code_prefix-$r->seq_number",
                'creator' => $r->creator_name,
                'responsible' => $r->responsible_name,
                'kind' => $r->kind,
                'source' => $r->source,
                'process' => $r->process,
                'perspective' => $r->perspective,
                'description' => $r->description,
                'status' => $r->status,
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
        require_once 'app/views/improvement/stats.php';
    }

    public function Save()
    {
        try {
            // 1. Seguridad y Cabeceras
            $user = $this->auth->authorize(110);
            header('Content-Type: application/json');

            // 2. Construcción del Objeto
            $item = new stdClass;
            $table = 'improvement';

            foreach ($_POST as $k => $val) {
                // Limpieza básica y omisión de campos especiales
                if (! empty($val) && ! in_array($k, ['id', 'other', 'files'])) {
                    $item->{$k} = htmlspecialchars(trim($val));
                }
            }

            // Lógica específica de Improvement: Fuente "Otras"
            $item->source = ($_POST['source'] === 'Otras') ? ($_POST['other'] ?? 'Otras') : $_POST['source'];

            // Datos de auditoría y estado inicial
            $item->user_id = $_SESSION['id-SIGMA'] ?? null;
            $item->status = 'Analysis'; // Estado inicial según tu flujo original

            // 3. Guardar en Base de Datos
            $id = $this->model->save($table, $item);
            if (! $id) {
                throw new Exception('Error saving improvement record');
            }

            // Respuesta HTMX
            $message = '{"type": "success", "message": "Saved", "close": "closeNewModal"}';
            $hxTriggerData = json_encode([
                'eventChanged' => true,
                'showMessage' => $message,
            ]);
            header('HX-Trigger: '.$hxTriggerData);
            http_response_code(204);

        } catch (Exception $e) {
            // Error de servidor
            http_response_code(500);
            echo json_encode([
                'type' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function Detail()
    {
        $user = $this->auth->authorize(110);
        $id = $this->model->get('*', 'improvement', 'and id = '.$_REQUEST['id']);
        $canEdit = ! empty(array_intersect([134], json_decode($user->permissions ?? '[]', true)));
        require_once 'app/views/improvement/detail.php';
    }

    public function Head()
    {
        $user = $this->auth->authorize(110);
        $id = $this->model->get('*', 'improvement', 'and id = '.$_REQUEST['id']);
        $canClose = $this->model->get(
            'i.*',
            'improvement i',
            "AND i.id = {$id->id} 
            AND i.user_ids IS NOT NULL AND i.user_ids != '' 
            AND i.aim IS NOT NULL AND i.aim != '' 
            AND i.goal IS NOT NULL AND i.goal != '' 
            AND ia.done IS NOT NULL 
            LIMIT 1",
            'INNER JOIN improvement_activities ia ON i.id = ia.improvement_id '
        );
        $canEdit = ! empty(array_intersect([134], json_decode($user->permissions ?? '[]', true)));
        require_once 'app/views/improvement/head.php';
    }

    public function Info()
    {
        $user = $this->auth->authorize(110);
        $id = $this->model->get('a.*,b.username', 'improvement a', 'and a.id = '.$_REQUEST['id'], 'LEFT JOIN users b ON a.responsible_id = b.id');
        $canEdit = ! empty(array_intersect([134], json_decode($user->permissions ?? '[]', true)));
        require_once 'app/views/improvement/info.php';
    }

    public function Tab()
    {
        $user = $this->auth->authorize(110);
        $id = $this->model->get('*', 'improvement', 'and id = '.$_REQUEST['id']);
        require_once 'app/views/improvement/tab.php';
    }

    public function Tabs()
    {
        $user = $this->auth->authorize(110);
        $canEdit = ! empty(array_intersect([134], json_decode($user->permissions ?? '[]', true)));
        $tab = $_REQUEST['tab'];
        $id = $this->model->get('*', 'improvement', 'and id = '.$_REQUEST['id']);
        require_once "app/views/improvement/detail/tabs/$tab.php";
    }

    public function Modal()
    {
        $user = $this->auth->authorize(134);
        $modal = $_REQUEST['modal'];
        $id = $this->model->get('*', 'improvement', 'and id = '.$_REQUEST['id']);
        require_once "app/views/improvement/detail/modals/$modal.php";
    }

    public function Cause()
    {
        $user = $this->auth->authorize(110);
        $id = $this->model->get('*', 'improvement_causes', 'and id = '.$_REQUEST['id']);
        require_once 'app/views/improvement/detail/modals/cause.php';
    }

    public function DeleteCause()
    {
        $id = $_REQUEST['id'];
        $this->model->delete('improvement_causes', " id = '$id'");
        $msg_content = '{"type": "success", "message": "Delete", "close" : ""}';
        $hxTriggerData = json_encode([
            'eventChanged' => true,
            'showMessage' => $msg_content,
        ]);

        header('HX-Trigger: '.$hxTriggerData);
        http_response_code(204);

    }

    public function DeleteActivity()
    {
        $id = $_REQUEST['id'];
        $this->model->delete('improvement_activities', " id = '$id'");
        $msg_content = '{"type": "success", "message": "Delete", "close" : ""}';
        $hxTriggerData = json_encode([
            'eventChanged' => true,
            'showMessage' => $msg_content,
        ]);

        header('HX-Trigger: '.$hxTriggerData);
        http_response_code(204);

    }

    public function Reject()
    {
        $id = $_REQUEST['id'];
        $item = new stdClass;
        $item->status = 'Rejected';
        $item->reason = $_SERVER['HTTP_HX_PROMPT'];
        $this->model->update('improvement', $item, $id);
        $msg_content = '{"type": "success", "message": "Rejected", "close" : "closeNewModal"}';
        $hxTriggerData = json_encode([
            'eventChanged' => true,
            'showMessage' => $msg_content,
        ]);

        header('HX-Trigger: '.$hxTriggerData);
        http_response_code(204);

    }

    public function Cancel()
    {
        $id = $_REQUEST['id'];
        $item = new stdClass;
        $item->status = 'Canceled';
        $item->reason = $_SERVER['HTTP_HX_PROMPT'];
        $this->model->update('improvement', $item, $id);
        $msg_content = '{"type": "success", "message": "Canceled", "close" : "closeNewModal"}';
        $hxTriggerData = json_encode([
            'eventChanged' => true,
            'showMessage' => $msg_content,
        ]);

        header('HX-Trigger: '.$hxTriggerData);
        http_response_code(204);

    }

    public function Activity()
    {
        $user = $this->auth->authorize(110);
        $id = (int) $_REQUEST['id'];
        $activity_id = (int) $_REQUEST['activity_id'] ?? null;
        require_once 'app/views/improvement/detail/modals/activity-close.php';
    }

    public function ActivitySave()
    {
        try {
            $user = $this->auth->authorize(110);
            header('Content-Type: application/json');

            $improvement_id = intval($_REQUEST['id']);
            $activity_id = isset($_REQUEST['activity_id']) ? intval($_REQUEST['activity_id']) : null;

            $date = $_REQUEST['date'] ?? date('Y-m-d');
            $resultsText = $_REQUEST['results'] ?? '';

            // --- LÓGICA DE ARCHIVO ÚNICO ---
            $filePath = '';
            $carpeta = 'uploads/improvement/result';

            if (! file_exists($carpeta)) {
                mkdir($carpeta, 0777, true);
            }

            // Validación para un solo archivo
            if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
                $tmpName = $_FILES['file']['tmp_name'];
                // Limpiamos el nombre y agregamos prefijo para evitar duplicados
                $fileName = time().'_'.basename($_FILES['file']['name']);
                $destination = $carpeta.'/'.$fileName;

                if (move_uploaded_file($tmpName, $destination)) {
                    $filePath = $destination;
                }
            }

            // Estructura: [fecha, resultados, ruta_archivo]
            $newEntry = '['.$date.','.$resultsText.','.$filePath.']';

            // 2. BUSCAR SI YA EXISTE LA ACTIVIDAD
            $existing = null;
            if ($activity_id) {
                $existing = $this->model->get('results', 'improvement_activities', " and id = $activity_id");
            }

            $item = new stdClass;
            $item->user_id = $user->id;

            if ($existing) {
                $resultPrevio = $existing->results ?? '';
                $item->results = ($resultPrevio != '') ? $resultPrevio.','.$newEntry : $newEntry;

                if (isset($_REQUEST['fulfill']) && $_REQUEST['fulfill'] == 1) {
                    $item->done = $date;
                }
                $this->model->update('improvement_activities', $item, $activity_id);
            } else {
                foreach ($_POST as $k => $val) {
                    if (! empty($val) && ! in_array($k, ['id', 'activity_id'])) {
                        $item->{$k} = htmlspecialchars(trim($val));
                    }
                }
                $item->improvement_id = $improvement_id;
                $item->results = $newEntry;

                $this->model->save('improvement_activities', $item);

                $itemc = new stdClass;
                $responsible_id = $_POST['responsible_id'];
                $email = $this->model->get('email', 'users', " and id= $responsible_id")->email;
                $itemc->to = ['mario.gonzalez@es-metals.com', 'esteban.arteta@es-metals.com', $email];
                $itemc->type = 'componenti';
                $itemc->email = 'sigma@es-metals.com';
                $itemc->id = $_POST['id'];
                $planId = $_POST['id'];
                $itemc->subject = 'SIGMA - New Improvement plan';
                $itemc->body = "Hi! this an email notification for you to check for a new improvement plan id: $planId. Thanks!";
                $this->model->sendEmail($itemc);
            }

            $message = ['type' => 'success', 'message' => 'Saved', 'close' => 'closeNestedModal'];
            header('HX-Trigger: '.json_encode([
                'eventChanged' => true,
                'showMessage' => json_encode($message),
            ]));
            http_response_code(204);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function ActivitiesData()
    {
        $user = $this->auth->authorize(110);
        header('Content-Type: application/json');

        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $size = isset($_GET['size']) ? (int) $_GET['size'] : 15;
        $offset = ($page - 1) * $size;

        $fieldMap = [
            'id' => 'a.id',
            'created_at' => 'a.created_at',
            'creator' => 'c.username as creator',
            'activity' => 'a.activity',
            'how' => 'a.how',
            'responsible' => 'b.username',
            'when' => 'a.whenn',
            'result' => 'a.results',
        ];

        // Filtro por improvement_id
        $id_improvement = intval($_REQUEST['id'] ?? 0);
        $where = "AND a.improvement_id = $id_improvement";

        // Filtros dinámicos
        if (isset($_GET['filter']) && is_array($_GET['filter'])) {
            foreach ($_GET['filter'] as $f) {
                if (! isset($f['field'], $f['value'])) {
                    continue;
                }
                $field = $f['field'];
                $value = addslashes($f['value']);
                if (isset($fieldMap[$field])) {
                    $where .= ' AND '.$fieldMap[$field]." LIKE '%$value%'";
                }
            }
        }

        $orderBy = 'a.id DESC';
        if (isset($_GET['sort'][0]['field'], $_GET['sort'][0]['dir'])) {
            $sortField = $_GET['sort'][0]['field'];
            $sortDir = strtoupper($_GET['sort'][0]['dir']) === 'ASC' ? 'ASC' : 'DESC';
            if (isset($fieldMap[$sortField])) {
                $orderBy = $fieldMap[$sortField]." $sortDir";
            }
        }

        $joins = 'LEFT JOIN users b on a.responsible_id = b.id LEFT JOIN users c on a.user_id = c.id';

        // Cambiado 'improvement_activities' a 'improvement_activities' según el primer código
        $total = $this->model->get('COUNT(a.id) AS total', 'improvement_activities a', $where, $joins)->total;
        $rows = $this->model->list('a.*, b.username, c.username as creator', 'improvement_activities a', "$where ORDER BY $orderBy LIMIT $offset, $size", $joins);

        $data = [];
        $canEdit = ! empty(array_intersect([134], json_decode($user->permissions ?? '[]', true)));

        foreach ($rows as $r) {

            $log = '';
            if (! empty($r->results)) {

                $records = explode('],[', trim($r->results, '[]'));

                foreach ($records as $recordStr) {

                    $parts = explode(',', $recordStr, 3);
                    if (count($parts) < 2) {
                        continue;
                    }

                    $fecha = trim($parts[0]);
                    $texto = trim($parts[1] ?? '');
                    $filePath = trim($parts[2] ?? '');

                    $link = '';

                    if (! empty($filePath) && file_exists($filePath)) {
                        $link = "<a href=\"$filePath\" target=\"_blank\" title=\"Ver archivo\" class=\"inline-flex items-center ml-1 text-gray-500 hover:text-gray-700\"><i class=\"ri-file-line\"></i></a>";
                    }

                    $log .= "<strong>$fecha:</strong> $texto $link<br>";
                }
            }

            $edit = (! $r->done)
                ? "<i @click='nestedModal=true' 
                    hx-get='?c=Improvement&a=Activity&activity_id=$r->id&id=$r->improvement_id' 
                    hx-target='#nestedModal' 
                    hx-indicator='#loading' 
                    class='ri-edit-line text-success cursor-pointer' 
                    title='Edit/Close'></i>"
                : '';

            $status = $this->model->get('status', 'improvement', "and id = $r->improvement_id")->status;
            $delete = ($canEdit and $status != 'Closed' and $status != 'Canceled') ? "<i hx-get='?c=Improvement&a=DeleteActivity&id=$r->id' hx-confirm='Are you sure you wish to delete this activity?' hx-indicator='#loading' class='ml-3 ri-delete-bin-line' title='Delete'></i>" : '';

            $data[] = [
                'id' => $r->id,
                'created_at' => $r->created_at,
                'creator' => $r->creator,
                'activity' => $r->activity,
                'how' => $r->how,
                'responsible' => $r->username,
                'when' => $r->whenn,
                'done' => $r->done,
                'results' => $log,
                'actions' => "$edit $delete",
            ];
        }

        echo json_encode([
            'data' => $data,
            'last_page' => ceil($total / $size),
            'last_row' => $total,
        ]);
    }

    public function CausesData()
    {
        $user = $this->auth->authorize(110);
        header('Content-Type: application/json');

        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $size = isset($_GET['size']) ? (int) $_GET['size'] : 15;
        $offset = ($page - 1) * $size;

        $fieldMap = [
            'id' => 'a.id',
            'created_at' => 'a.created_at',
            'creator' => 'b.username',
            'reason' => 'a.reason',
            'method' => "(CASE WHEN a.method_id = '1' THEN '5 Whys' ELSE 'File' END)",
            'probable' => 'a.probable',
        ];

        // Filtro básico por requisitionId
        $where = 'AND a.improvement_id = '.intval($_REQUEST['id'] ?? 0);

        // Filtros dinámicos
        if (isset($_GET['filter']) && is_array($_GET['filter'])) {
            foreach ($_GET['filter'] as $f) {
                if (! isset($f['field'], $f['value'])) {
                    continue;
                }
                $field = $f['field'];
                $value = addslashes($f['value']);
                if (! isset($fieldMap[$field])) {
                    continue;
                }
                $where .= ' AND '.$fieldMap[$field]." LIKE '%$value%'";
            }
        }

        // Ordenamiento
        $orderBy = 'a.id DESC';
        if (isset($_GET['sort'][0]['field'], $_GET['sort'][0]['dir'])) {
            $sortField = $_GET['sort'][0]['field'];
            $sortDir = strtoupper($_GET['sort'][0]['dir']) === 'ASC' ? 'ASC' : 'DESC';
            if (isset($fieldMap[$sortField])) {
                $orderBy = $fieldMap[$sortField]." $sortDir";
            }
        }

        $joins = 'LEFT JOIN users b on a.user_id = b.id';

        // Total de registros (el JOIN no afecta el conteo de la tabla principal)
        $total = $this->model->get('COUNT(a.id) AS total', 'improvement_causes a', $where, $joins)->total;

        // Traer los registros
        $rows = $this->model->list("a.*, b.username as creator, CASE  WHEN a.method_id = '1' THEN '5 Whys'  ELSE 'File' END as method_name", 'improvement_causes a', "$where ORDER BY $orderBy LIMIT $offset, $size", $joins);

        $data = [];
        $canEdit = ! empty(array_intersect([134], json_decode($user->permissions ?? '[]', true)));

        foreach ($rows as $r) {
            $view = ($r->method_id == 1)
            ? "<i @click='nestedModal=true' hx-get='?c=Improvement&a=Cause&id=$r->id' hx-target='#nestedModal' hx-indicator='#loading' class='ri-eye-line' title='View'></i>"
            : "<a href='$r->content' target='_blank' title='View'><i class='ri-eye-line'></i></a>";
            $status = $this->model->get('status', 'improvement', "and id = $r->improvement_id")->status;
            $delete = ($canEdit and $status != 'Closed' and $status != 'Canceled') ? "<i hx-get='?c=Improvement&a=DeleteCause&id=$r->id' hx-confirm='Are you sure you wish to delete this cause?' hx-indicator='#loading' class='ml-3 ri-delete-bin-line' title='Delete'></i>" : '';
            $data[] = [
                'id' => $r->id,
                'created_at' => $r->created_at,
                'creator' => $r->creator,
                'reason' => $r->reason,
                'method' => $r->method_name,
                'probable' => $r->probable,
                'actions' => "$view $delete",
            ];
        }

        echo json_encode([
            'data' => $data,
            'last_page' => ceil($total / $size),
            'last_row' => $total,
        ]);
    }

    public function CauseSave()
    {
        try {
            $user = $this->auth->authorize(134);
            header('Content-Type: application/json');

            $method_id = $_REQUEST['method_id'] ?? null;
            $id = $_REQUEST['id'] ?? null;

            if (! $method_id && $method_id !== '0') {
                throw new Exception('Please select an analysis method.');
            }

            $item = new stdClass;

            $item->method_id = $method_id;

            $item->reason = $_REQUEST['reason'];
            $item->probable = $_REQUEST['probable'];
            $item->user_id = $user->id;
            $item->improvement_id = $id;

            // --- OPTION A: FILE UPLOAD (Method 1) ---
            if ($method_id === '2' && isset($_FILES['files'])) {
                if (! $id) {
                    throw new Exception('Record ID is required for file uploads.');
                }

                $file = $_FILES['files'];
                // Dynamic path: uploads/improvement/other/{id}/
                $folder = 'uploads/improvement/other/';

                if (! is_dir($folder)) {
                    mkdir($folder, 0777, true);
                }

                $fileName = time().'_'.basename($file['name'][0]);
                $targetPath = $folder.'/'.$fileName;

                if (move_uploaded_file($file['tmp_name'][0], $targetPath)) {
                    // Save the link in analysis_url
                    $item->content = $targetPath;
                } else {
                    throw new Exception('Failed to move uploaded file.');
                }
            }
            // --- OPTION B: 5 WHYS JSON (Method 0) ---
            elseif ($method_id === '1') {
                $item->content = json_encode($_REQUEST['whys'], JSON_UNESCAPED_UNICODE);
            }

            // Save to Database
            $savedId = $this->model->save('improvement_causes', $item);
            if (! $savedId) {
                throw new Exception('Error saving to database.');
            }

            $update = new stdClass;
            $update->status = 'Plan';
            $this->model->update('improvement', $update, $id);

            // Respuesta HTMX
            $message = '{"type": "success", "message": "Saved", "close": "closeNestedModal"}';
            $hxTriggerData = json_encode([
                'eventChanged' => true,
                'showMessage' => $message,
            ]);
            header('HX-Trigger: '.$hxTriggerData);
            http_response_code(204);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function Close()
    {
        $user = $this->auth->authorize(110);
        $id = (int) $_REQUEST['id'];
        if (! $this->model->get('*', 'improvement_activities', " and improvement_id = $id LIMIT 1")) {
            $message = '{"type": "error", "message": "Add at least one activity", "close" : ""}';
            header('HX-Trigger: '.json_encode(['showMessage' => $message]));
            http_response_code(400);
            exit;
        }
        require_once 'app/views/improvement/detail/modals/close.php';
    }

    public function CloseSave()
    {
        try {

            $id = $_REQUEST['id'];

            $item = new stdClass;

            foreach ($_POST as $k => $val) {
                if (! empty($val) && ! in_array($k, ['id'])) {
                    $item->{$k} = htmlspecialchars(trim($val));
                }
            }
            $item->closed_at = date('Y-m-d H:i:s');
            $item->status = 'Closed';
            $this->model->update('improvement', $item, $id);

            $message = '{"type": "success", "message": "Closed", "close": "closeNestedModal"}';
            $hxTriggerData = json_encode([
                'eventChanged' => true,
                'showMessage' => $message,
            ]);
            header('HX-Trigger: '.$hxTriggerData);
            http_response_code(204);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function Kpis()
    {
        $user = $this->auth->authorize(110);
        $in = $_REQUEST['in'] ?? '';
        $year = $_REQUEST['year'] ?? date('Y');

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $result = [];
        $result1 = [];

        foreach ($months as $i => $month) {
            // Consultas optimizadas
            $total = (int) $this->model->get('count(*) as total', 'improvement_activities', " AND DATE_FORMAT(whenn, '%b') = '$month' AND YEAR(whenn) = '$year'")->total;
            $closed = (int) $this->model->get('count(*) as total', 'improvement_activities', " AND DATE_FORMAT(done, '%b') = '$month' AND YEAR(done) = '$year'")->total;

            $percentage = ($total > 0) ? round(($closed / $total) * 100) : 100;

            $result[] = [
                'month' => $month,
                'total' => $total,
                'closed' => $closed,
                'result' => $percentage,
            ];
            $result1[] = $percentage;
        }

        require_once 'app/views/improvement/kpis/index.php';
    }

    public function KpisDetail()
    {
        (! empty($_REQUEST['year'])) ? $year = $_REQUEST['year'] : $year = date('Y');
        $date = $year.'-01-01';
        $month = $_REQUEST['month'];
        if ($_REQUEST['type'] === 'total') {
            $total = $this->model->list('*, a.id as code, u.username as responsiblename', 'improvement_activities a', " and DATE_FORMAT(whenn, '%b') = '$month'  AND YEAR(whenn) = YEAR('$date') ORDER BY a.id ASC", 'LEFT JOIN users u on a.responsible_id = u.id');
        } else {
            $total = $this->model->list('*, a.id as code, u.username as responsiblename', 'improvement_activities a', " and DATE_FORMAT(done, '%b') = '$month'  AND YEAR(done) = YEAR('$date') ORDER BY a.id ASC", 'LEFT JOIN users u on a.responsible_id = u.id');
        }
        require_once 'app/views/improvement/kpis/detail.php';
    }

    public function Notifications()
    {
        foreach ($this->model->list('responsible_id,whenn,improvement_id', 'improvement_activities', ' and done is null') as $r) {
            if (new DateTime($r->whenn) > new DateTime) {
                $itemc = new stdClass;
                $email = $this->model->get('email', 'users', " and id= $r->responsible_id")->email;
                $itemc->to = ['esteban.arteta@es-metals.com', $email];
                $itemc->type = 'componenti';
                $itemc->email = 'sigma@es-metals.com';
                $itemc->id = $r->improvement_id;
                $itemc->subject = 'SIGMA - Expired Improvement plan';
                $itemc->body = "Hi! this an email notification for you to check for an expired improvement plan id: $r->improvement_id. Thanks!";
                $this->model->sendEmail($itemc);
            }
            if ((new DateTime($r->whenn))->modify('-14 days') == new DateTime('today')) {
                $itemc = new stdClass;
                $email = $this->model->get('email', 'users', " and id= $r->responsible_id")->email;
                $itemc->to = ['esteban.arteta@es-metals.com', $email];
                $itemc->type = 'componenti';
                $itemc->email = 'sigma@es-metals.com';
                $itemc->id = $r->improvement_id;
                $itemc->subject = 'SIGMA - 2 Weeks for Improvement plan';
                $itemc->body = "Hi! this an email notification for you to check for plan id: $r->improvement_id. Thanks!";
                $this->model->sendEmail($itemc);
            }
            if ((new DateTime($r->whenn))->modify('-7 days') == new DateTime('today')) {
                $itemc = new stdClass;
                $email = $this->model->get('email', 'users', " and id= $r->responsible_id")->email;
                $itemc->to = ['esteban.arteta@es-metals.com', $email];
                $itemc->type = 'componenti';
                $itemc->email = 'sigma@es-metals.com';
                $itemc->id = $r->improvement_id;
                $itemc->subject = 'SIGMA - 1 Weeks for Improvement plan';
                $itemc->body = "Hi! this an email notification for you to check for plan id: $r->improvement_id. Thanks!";
                $this->model->sendEmail($itemc);
            }
            if ((new DateTime($r->whenn))->modify('-3 days') == new DateTime('today')) {
                $itemc = new stdClass;
                $email = $this->model->get('email', 'users', " and id= $r->responsible_id")->email;
                $itemc->to = ['esteban.arteta@es-metals.com', $email];
                $itemc->type = 'componenti';
                $itemc->email = 'sigma@es-metals.com';
                $itemc->id = $r->improvement_id;
                $itemc->subject = 'SIGMA - 3 days for Improvement plan';
                $itemc->body = "Hi! this an email notification for you to check for plan id: $r->improvement_id. Thanks!";
                $this->model->sendEmail($itemc);
            }
        }
    }

    public function Update()
    {
        try {
            header('Content-Type: application/json');
            $close = '';
            $id = $_REQUEST['id'];
            $field = $_REQUEST['field'];
            $save = new stdClass;
            $update = new stdClass;

            $save->user_id = $_SESSION['id-SIGMA'];

            // Procesamiento para Aim y Goal (Texto simple)
            if ($field == 'aim' || $field == 'goal') {
                $value = $_REQUEST[$field];
                $update->{$field} = htmlspecialchars(trim($value));
            }

            // Procesamiento para Users (Guardado como JSON)
            if ($field == 'user_ids') {
                // Recibimos el array de TomSelect o un array vacío si no hay selección
                $value = $_REQUEST[$field] ?? [];
                // Convertimos el array a formato JSON string para la base de datos
                $update->{$field} = json_encode($value);
            }

            // Actualización en la tabla 'improvement' (o la tabla que corresponda)
            // Se asume que el modelo maneja la seguridad de la consulta
            $result = $this->model->update('improvement', $update, $id);

            if ($result === false) {
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

            // Respondemos con 204 (No Content) para que HTMX no altere el DOM innecesariamente
            http_response_code(204);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'type' => 'error',
                'message' => 'Internal Server Error: '.$e->getMessage(),
            ]);
        }
    }

    public function Print()
    {
        $id = 91;
        $improvement = $this->model->get('*', 'improvement', "and id = $id");
        $causes = $this->model->list('*', 'improvement_causes', "and improvement_id = $id");
        $activities = $this->model->list('*', 'improvement_activities', "and improvement_id = $id");
        print_r($improvement);
        print_r($causes);
        print_r($activities);
    }

    public function PDF()
    {
        $id = $_REQUEST['id'];
        // 1. Obtención de datos con los nombres de tabla correctos
        $improvement = $this->model->get('*', 'improvement', "and id = $id");
        $causes = $this->model->list('*', 'improvement_causes', "and improvement_id = $id");
        $activities = $this->model->list('*', 'improvement_activities', "and improvement_id = $id");

        // 2. Configuración de Dompdf
        $options = new Options;
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <style>
                @page { margin: 1cm 1.2cm; }
                body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #333; line-height: 1.3; }
                .header-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; border: 1.5px solid #000; }
                .header-table td { border: 1px solid #000; padding: 8px; }
                .title { font-size: 13px; font-weight: bold; text-align: center; background: #f2f2f2; text-transform: uppercase; }
                
                .section-title { background: #1a202c; color: white; padding: 4px 10px; font-weight: bold; margin-top: 12px; text-transform: uppercase; font-size: 10px; }
                
                .info-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
                .info-table td { border: 1px solid #ccc; padding: 5px; vertical-align: top; }
                .label { background: #f8fafc; font-weight: bold; width: 20%; color: #2d3748; }

                .why-box { border: 1px solid #e2e8f0; background: #fffaf0; margin: 5px 0; padding: 8px; }
                .why-item { margin-bottom: 3px; border-bottom: 1px solid #fff; padding-bottom: 2px; }
                .why-num { font-weight: bold; color: #7b341e; }

                .act-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
                .act-table th { background: #edf2f7; border: 1px solid #cbd5e0; padding: 5px; font-size: 8.5px; }
                .act-table td { border: 1px solid #cbd5e0; padding: 5px; font-size: 8.5px; }
                
                .badge { padding: 2px 6px; border-radius: 4px; font-weight: bold; border: 1px solid #333; background: #eee; }
            </style>
        </head>
        <body>

            <table class="header-table">
                <tr>
                    <td width="20%" align="center"><strong><img width="90" src="https://sigma.es-metals.com/sigma/app/assets/img/logoES.png"></strong></td>
                    <td width="55%" class="title">REPORTE DE ACCIÓN CORRECTIVA Y MEJORA</td>
                    <td width="25%" style="font-size: 9px;">
                        <strong>ID:</strong> #<?php echo $improvement->id; ?><br>
                        <strong>Estado:</strong> <span class="badge"><?php echo $improvement->status; ?></span><br>
                        <strong>Origen:</strong> <?php echo $improvement->perspective; ?>
                    </td>
                </tr>
            </table>

            <div class="section-title">1. Información General</div>
            <table class="info-table">
                <tr>
                    <td class="label">Proceso:</td>
                    <td><?php echo $improvement->process; ?></td>
                    <td class="label">Fecha Apertura:</td>
                    <td><?php echo $improvement->occurrence_date; ?></td>
                </tr>
                <tr>
                    <td class="label">Fuente:</td>
                    <td colspan="3"><?php echo $improvement->source; ?></td>
                </tr>
                <tr>
                    <td class="label">Objetivo (Aim):</td>
                    <td colspan="3"><?php echo $improvement->aim; ?></td>
                </tr>
                <tr>
                    <td class="label">Meta (Goal):</td>
                    <td colspan="3"><?php echo $improvement->goal; ?></td>
                </tr>
            </table>

            <div class="section-title">2. Descripción del Hallazgo y Acción Inmediata</div>
            <table class="info-table">
                <tr>
                    <td class="label">Hallazgo:</td>
                    <td style="text-align: justify;"><?php echo nl2br($improvement->description); ?></td>
                </tr>
                <tr>
                    <td class="label">Acción Inmediata (ACIM):</td>
                    <td style="font-weight: bold; color: #2c5282;"><?php echo $improvement->acim; ?></td>
                </tr>
            </table>

            <div class="section-title">3. Análisis de Causa Raíz (5 Por qués)</div>
            <?php foreach ($causes as $c) {
                $porques = json_decode($c->content);
                ?>
                <div class="why-box">
                    <?php if (is_array($porques)) {
                        foreach ($porques as $index => $porque) { ?>
                        <div class="why-item">
                            <span class="why-num"><?php echo $index + 1; ?>. ¿Por qué?</span> <?php echo htmlspecialchars($porque); ?>
                        </div>
                    <?php }
                        } ?>
                    <div style="margin-top: 6px; padding-top: 4px; border-top: 1px dashed #7b341e;">
                        <strong>Causa Probable Final:</strong> <?php echo htmlspecialchars($c->probable); ?>
                    </div>
                </div>
            <?php } ?>

            <div class="section-title">4. Plan de Acción y Seguimiento</div>
            <table class="act-table">
                <thead>
                    <tr>
                        <th width="30%">Actividad / Cómo</th>
                        <th width="10%">Límite (Whenn)</th>
                        <th width="10%">Ejecución (Done)</th>
                        <th width="50%">Resultados de Verificación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($activities as $act) {
                        // Limpieza del campo results: [fecha, texto, hash]
                        $resString = trim($act->results, '[]');
                        $resArray = explode(',', $resString);
                        $evidencia = $resArray[1] ?? 'No hay resultados registrados';
                        ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($act->activity); ?></strong><br>
                            <span style="color:#555; font-size: 8px;">Cómo: <?php echo htmlspecialchars($act->how); ?></span>
                        </td>
                        <td align="center"><?php echo $act->whenn; ?></td>
                        <td align="center"><?php echo $act->done; ?></td>
                        <td><?php echo htmlspecialchars($evidencia); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div class="section-title">5. Cierre y Evaluación de Eficacia</div>
            <table class="info-table">
                <tr>
                    <td class="label">Fecha Cierre:</td>
                    <td><?php echo $improvement->cdate; ?></td>
                    <td class="label">Eficacia:</td>
                    <td style="font-weight: bold;"><?php echo $improvement->effectiveness; ?></td>
                </tr>
                <tr>
                    <td class="label">Conveniencia:</td>
                    <td colspan="3"><?php echo $improvement->convenience; ?></td>
                </tr>
                <tr>
                    <td class="label">Adecuación:</td>
                    <td colspan="3"><?php echo $improvement->adequacy; ?></td>
                </tr>
            </table>

            <div style="margin-top: 30px; text-align: center;">
                <div style="display: inline-block; width: 40%; border-top: 1px solid #000; margin-right: 10%;">
                    <br>Responsable del Proceso
                </div>
                <div style="display: inline-block; width: 40%; border-top: 1px solid #000;">
                    <br>Líder de Calidad / SST
                </div>
            </div>

        </body>
        </html>
        <?php
        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('Plan_Mejora_'.$improvement->id.'.pdf', ['Attachment' => false]);
    }
}
