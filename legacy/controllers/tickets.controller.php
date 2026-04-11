<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

class TicketsController
{
    public function __construct(public Model $model, private AuthService $auth) {}

    public function Index()
    {
        $user = $this->auth->authorize(157);
        $tabulator = true;
        $button = 'New Ticket';
        $content = 'app/components/list.php';
        $title = 'Admin Desk';
        $columns = '[
            { "title": "ID", "field": "id", "width": 60, "sorter": "number", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Type", "field": "type", headerHozAlign: "left","headerFilter": "input"},
            { "title": "Date", "field": "date", headerHozAlign: "center", headerFilter: customDateRangeFilter, headerFilterFunc: customDateFilterFunc, headerFilterLiveFilter: false },
            { "title": "User", "field": "user", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Facility", "field": "facility", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Priority", "field": "priority", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Description", "field": "description", "formatter": "textarea", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Days", "field": "days", "sorter": "number", "width": 100, "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Started", "field": "started", headerHozAlign: "center", headerFilter: customDateRangeFilter, headerFilterFunc: customDateFilterFunc, headerFilterLiveFilter: false },
            { "title": "Closed", "field": "closed", headerHozAlign: "center", headerFilter: customDateRangeFilter, headerFilterFunc: customDateFilterFunc, headerFilterLiveFilter: false },
            { "title": "Status", "field": "status", headerHozAlign: "center", hozAlign:"center", headerFilter:"list",
            headerFilterParams:{ values: {"Open": "Open", "Started": "Started", "Closed": "Closed", "Rejected": "Rejected"}, clearable:true}},
        ]';

        require_once 'app/views/index.php';
    }

    public function New()
    {
        $user = $this->auth->authorize(157);
        require_once 'app/views/tickets/new.php';
    }

    public function Data()
    {
        $user = $this->auth->authorize(157);
        header('Content-Type: application/json');

        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $size = isset($_GET['size']) ? (int) $_GET['size'] : 15;
        $offset = ($page - 1) * $size;

        $fieldMap = [
            'id' => 'a.id',
            'type' => 'a.kind',
            'date' => 'a.created_at',
            'user' => 'b.username',
            'facility' => 'a.facility',
            'priority' => 'a.priority',
            'description' => 'a.description',
            'status' => 'a.status',
        ];

        // 1. Obtenemos los permisos del usuario
        $userPermissions = json_decode($user->permissions ?? '[]', true);

        // 2. Definimos qué IDs corresponden a qué categoría
        // Si tiene el 158 O el 159, ve todo HR, etc.
        $categories = [
            'HR' => [158, 159],
            'OHS' => [160, 162],
            'Marketing' => [161, 163],
        ];

        $allowedTypes = [];

        foreach ($categories as $type => $ids) {
            // Si el usuario tiene al menos uno de los IDs de la categoría
            if (! empty(array_intersect($ids, $userPermissions))) {
                $allowedTypes[] = "'$type'";
            }
        }

        // 3. Construcción de la lógica de filtrado ($where)
        if (! empty($allowedTypes)) {
            $typesString = implode(',', $allowedTypes);

            // El usuario ve: registros de su propiedad OR registros de las áreas permitidas
            $where = " AND a.kind IN ($typesString)";
        } else {
            // Si no tiene ningún permiso especial, solo ve lo suyo
            $where = " AND a.user_id = {$user->id}";
        }

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
                $dbField = $fieldMap[$field];
                // --- INICIO: Lógica de filtrado con soporte para conteos
                if ($field === 'created_at' && strpos($value, ' to ') !== false) {
                    [$from, $to] = explode(' to ', $value);
                    $where .= " AND DATE($dbField) BETWEEN '$from' AND '$to'";
                } else {
                    $where .= " AND $dbField LIKE '%$value%'";
                }
            }
        }

        // 5. Manejo de Ordenación
        $orderBy = 'a.created_at DESC'; // Orden por defecto (fecha de creación descendente)
        if (isset($_GET['sort'][0]['field'], $_GET['sort'][0]['dir'])) {
            $sortField = $_GET['sort'][0]['field'];
            $sortDir = strtoupper($_GET['sort'][0]['dir']) === 'ASC' ? 'ASC' : 'DESC';
            if (isset($fieldMap[$sortField])) {
                $orderBy = $fieldMap[$sortField]." $sortDir";
            }
        }

        // 6. Definición de JOINs y SELECT
        $joins = '
            LEFT JOIN users b on a.user_id = b.id 
        ';

        $selectFields = '
            a.*,
            b.username
        ';

        $total = $this->model->get('COUNT(a.id) AS total', 'tickets a', $where, $joins)->total;

        $rows = $this->model->list(
            $selectFields,
            'tickets a',
            "$where ORDER BY $orderBy LIMIT $offset, $size",
            $joins
        );

        $data = [];
        $now = new DateTime(date('Y-m-d H:i:s'));

        foreach ($rows as $r) {
            // --- 8a. Cálculo de Tiempo (Days/Minutes) ---
            // Usar closedAt si existe, sino, usar 'now'.
            $dateClosed = $r->closed_at ? new DateTime($r->closed_at) : $now;
            $dateCreated = new DateTime($r->created_at);
            $interval = $dateCreated->diff($dateClosed);
            $minutes = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;
            $minutes = round($minutes / 24 / 60, 0);

            $data[] = [
                'id' => $r->id,
                'type' => $r->kind,
                'date' => $r->created_at,
                'user' => $r->username,
                'facility' => $r->facility,
                'priority' => $r->priority,
                'description' => $r->description,
                'days' => $minutes,
                'started' => $r->started_at,
                'closed' => $r->closed_at,
                'status' => $r->status,
            ];
        }

        // 9. Respuesta JSON para Tabulator
        echo json_encode([
            'data' => $data,
            'last_page' => ceil($total / $size),
            'last_row' => $total,
        ]);
    }

    public function Stats()
    {
        require_once 'app/views/tickets/stats.php';
    }

    public function Save()
    {
        try {
            $user = $this->auth->authorize(157);
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
            $id = $this->model->save('tickets', $item);
            if (! $id) {
                throw new Exception('Error saving maintenance record');
            }

            /* ==========================
            MANEJO DE ARCHIVOS
            ========================== */
            if (! empty($_FILES['files']['name'][0])) {

                $carpeta = "uploads/tickets/userpics/$id/";
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
                'message' => 'Saved',
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
        $user = $this->auth->authorize(157);
        require_once 'app/views/tickets/detail.php';
    }

    public function Head()
    {
        $user = $this->auth->authorize(157);
        $userPermissions = json_decode($user->permissions ?? '[]', true);

        // Inicializamos como falso
        $canClose = false;

        if (! empty($_REQUEST['id'])) {
            $filters = 'and id = '.$_REQUEST['id'];
            $id = $this->model->get(
                '*',
                'tickets',
                $filters,
            );

            if ($id) {
                $type = $id->kind; // HR, OHS, o Marketing

                // Lógica estricta de Cierre (Close)
                if ($type == 'HR' && in_array(159, $userPermissions)) {
                    $canClose = true;
                }
                if ($type == 'OHS' && in_array(162, $userPermissions)) {
                    $canClose = true;
                }
                if ($type == 'Marketing' && in_array(163, $userPermissions)) {
                    $canClose = true;
                }
            }
        }

        require_once 'app/views/tickets/head.php';
    }

    public function Info()
    {
        $user = $this->auth->authorize(157);
        $userPermissions = json_decode($user->permissions ?? '[]', true);

        $canClose = false;

        if (! empty($_REQUEST['id'])) {
            $filters = 'and a.id = '.$_REQUEST['id'];
            $id = $this->model->get(
                'a.*, c.username',
                'tickets a',
                $filters,
                'LEFT JOIN users c ON a.user_id = c.id'
            );

            if ($id) {
                $type = $id->kind; // HR, OHS, o Marketing

                // 1. Lógica de Cierre (IDs: 159, 162, 163)
                if ($type == 'HR' && in_array(159, $userPermissions)) {
                    $canClose = true;
                } elseif ($type == 'OHS' && in_array(162, $userPermissions)) {
                    $canClose = true;
                } elseif ($type == 'Marketing' && in_array(163, $userPermissions)) {
                    $canClose = true;
                }
            }
        }

        require_once 'app/views/tickets/info.php';
    }

    public function Tab()
    {
        $user = $this->auth->authorize(157);
        $userPermissions = json_decode($user->permissions ?? '[]', true);

        if (! empty($_REQUEST['id'])) {
            $filters = 'and id = '.(int) $_REQUEST['id'];
            $id = $this->model->get('*', 'tickets', $filters);
        }

        if (! empty($_REQUEST['id'])) {
            $filters = 'and a.id = '.$_REQUEST['id'];
            $id = $this->model->get(
                'a.*, c.username',
                'tickets a',
                $filters,
                'LEFT JOIN users c ON a.user_id = c.id'
            );

            if ($id) {
                $type = $id->kind; // HR, OHS, o Marketing

                // 1. Lógica de Cierre (IDs: 159, 162, 163)
                if ($type == 'HR' && in_array(159, $userPermissions)) {
                    $canClose = true;
                } elseif ($type == 'OHS' && in_array(162, $userPermissions)) {
                    $canClose = true;
                } elseif ($type == 'Marketing' && in_array(163, $userPermissions)) {
                    $canClose = true;
                }
            }
        }

        require_once 'app/views/tickets/tab.php';
    }

    public function Task()
    {
        $user = $this->auth->authorize(157);
        header('Content-Type: application/json');

        $ticketId = (int) ($_GET['id'] ?? 0);

        $rows = $this->model->list(
            'a.*, b.username',
            'ticket_items a',
            "and a.ticket_id = $ticketId",
            'LEFT JOIN users b ON a.user_id = b.id'
        );

        $data = [];

        foreach ($rows as $r) {

            // --- Evidences (MISMA lógica original, optimizada) ---
            $action = '';
            $directorio = "uploads/tickets/pics/$r->id/";
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
                'date' => $r->date,
                'user' => $r->username,
                'attends' => $r->attends,
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
        $user = $this->auth->authorize([157]);
        $modal = $_REQUEST['modal'];
        $filters = 'and id = '.$_REQUEST['id'];
        $id = $this->model->get('*', 'tickets', $filters);
        require_once "app/views/tickets/$modal.php";
    }

    public function SaveTask()
    {
        try {
            // Autorización
            $user = $this->auth->authorize(157);
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
            $item->ticket_id = $_REQUEST['id'] ?? null;

            $item->user_id = $_SESSION['id-SIGMA'] ?? null;

            if (empty($item->ticket_id)) {
                throw new Exception('Maintenance ID not provided');
            }

            /* ==========================
            VALIDAR / ACTUALIZAR it
            ========================== */
            $ticketId = $item->ticket_id;
            $exists = $this->model->get('*', 'ticket_items', "AND ticket_id = $ticketId");

            if (empty($exists->id)) {
                $ticket = new stdClass;
                $ticket->started_at = date('Y-m-d H:i:s');
                $ticket->status = 'Started';
                $ticket->assignee_id = $_SESSION['id-SIGMA'];
                $this->model->update('tickets', $ticket, $ticketId);
            }

            /* ==========================
            GUARDAR ITEM
            ========================== */
            $id = $this->model->save('ticket_items', $item);
            if (! $id) {
                throw new Exception('Error saving maintenance item');
            }

            /* ==========================
            MANEJO DE ARCHIVOS
            ========================== */
            if (! empty($_FILES['files']['name'][0])) {

                $carpeta = "uploads/tickets/pics/$id/";
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
                $save->ticket_id = $id;
                $save->notes = "priority updated to $value";
                $update->{$_REQUEST['field']} = htmlspecialchars(trim($value));
            }

            if ($_REQUEST['field'] == 'closed_at') {

                if (! $this->model->get('*', 'ticket_items', " and ticket_id = $id LIMIT 1")) {
                    $message = '{"type": "error", "message": "Add at least One task", "close" : ""}';
                    header('HX-Trigger: '.json_encode(['showMessage' => $message]));
                    http_response_code(400);
                    exit;
                }

                $update->{$_REQUEST['field']} = date('Y-m-d H:i:s');
                $update->status = 'Closed';
                $close = 'closeNewModal';
            }

            if ($_REQUEST['field'] == 'reason') {
                $save->notes = 'reject reason:'.$_REQUEST['reason'];
                $save->ticket_id = $id;
                $update->status = 'Rejected';
                $close = 'closeNewModal';
            }

            $this->model->save('ticket_items', $save);
            $id = $this->model->update('tickets', $update, $id);
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
}
