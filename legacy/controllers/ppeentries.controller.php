<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

class PPEEntriesController
{
    public function __construct(public Model $model, private AuthService $auth) {}

    public function Index()
    {
        $user = $this->auth->authorize(121);
        $tabulator = true;
        $jspreadsheet = true;
        $title = 'OHS / PPE / Entries';
        $button = 'New Entry';
        $content = 'app/components/list.php';
        $columns = '[
            { "title": "ID", "field": "id", headerHozAlign: "center", headerFilter:"input"},
            { "title": "Name", "field": "name", headerHozAlign: "center", headerFilter:"input"},
            { "title": "Total", "field": "total", headerHozAlign: "center", headerFilter:"input"},
        ]';
        require_once 'app/views/index.php';
    }

    public function Data()
    {
        // Autorización con el nuevo sistema
        $user = $this->auth->authorize(121);

        header('Content-Type: application/json');

        // Parámetros de paginación (Estándar Tabulator/DataTables)
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $size = isset($_GET['size']) ? (int) $_GET['size'] : 15;
        $offset = ($page - 1) * $size;

        // Mapeo seguro de campos (Frontend => SQL)
        // Nota: 'pp' es Punto de Pedido
        $fieldMap = [
            'item_id' => 'a.item_id',
            'name' => 'b.name',
        ];

        $where = '';

        // Procesamiento de filtros dinámicos
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
                $where .= " AND $dbField LIKE '%$value%'";
            }
        }

        $orderBy = 'b.name ASC';

        // Ordenamiento dinámico
        if (isset($_GET['sort'][0]['field']) && isset($_GET['sort'][0]['dir'])) {
            $sortField = $_GET['sort'][0]['field'];
            $sortDir = strtoupper($_GET['sort'][0]['dir']) === 'ASC' ? 'ASC' : 'DESC';

            if (isset($fieldMap[$sortField])) {
                $orderBy = $fieldMap[$sortField]." $sortDir";
            }
        }

        $totalQuery = $this->model->get(
            'count(DISTINCT a.item_id) as total',
            'epp_register a',
            "$where",
            'LEFT JOIN epp_db b ON a.item_id = b.id'
        );
        $total = $totalQuery->total;

        // 2. Obtener los datos agrupados con el cálculo de stock
        // Replicamos la lógica: SUM(ingresos en epp_register) - COUNT(egresos en epp)
        $rows = $this->model->list(
            'a.item_id, b.name, sum(a.qty) as total_in',
            'epp_register a',
            "$where GROUP BY a.item_id ORDER BY $orderBy LIMIT $offset, $size",
            'LEFT JOIN epp_db b ON a.item_id = b.id'
        );

        $data = [];
        foreach ($rows as $r) {
            // Calculamos las salidas (egresos) de la tabla 'epp'
            $out = $this->model->get('count(name) as total', 'epp', " AND name = '$r->name'")->total;

            $stockActual = $r->total_in - $out;

            $data[] = [
                'id' => $r->item_id,
                'name' => $r->name,
                'total' => $stockActual,
            ];
        }

        // Respuesta en formato JSON para el frontend
        echo json_encode([
            'data' => $data,
            'last_page' => ceil($total / $size),
            'last_row' => $total,
        ]);
    }

    public function Stats() {}

    public function New()
    {
        $user = $this->auth->authorize(121);
        require_once 'app/views/ppe/new-entry.php';
    }

    public function Save()
    {
        $user = $this->auth->authorize(121);

        header('Content-Type: application/json');

        $item = new stdClass;

        foreach ($_POST as $k => $val) {
            if ($val !== '' && $val !== null) {
                if ($k != 'id') {
                    $item->{$k} = $val;
                }
            }
        }

        $item->user_id = $_SESSION['id-SIGMA'];

        $id = $this->model->save('epp_register', $item);

        if ($id !== false) {
            $hxTriggerData = json_encode([
                'eventChanged' => true,
                'showMessage' => '{"type": "success", "message": "Saved Successfully", "close" : "closeNewModal"}',
            ]);
            header('HX-Trigger: '.$hxTriggerData);
            http_response_code(204);
            exit;
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Database error']);
        }
    }

    public function Detail()
    {
        $user = $this->auth->authorize(121);
        require_once 'app/views/ppe/detail.php';
    }

    public function DetailData()
    {
        $user = $this->auth->authorize(121);

        header('Content-Type: application/json');

        // ID obligatorio
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id === 0) {
            echo json_encode([
                'data' => [],
                'last_page' => 0,
                'last_row' => 0,
            ]);

            return;
        }

        // Paginación
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $size = isset($_GET['size']) ? (int) $_GET['size'] : 15;
        $offset = ($page - 1) * $size;

        // Mapeo de campos (frontend => data array)
        $fieldMap = [
            'date' => 'date',
            'type' => 'type',
            'name' => 'name',
            'qty' => 'qty',
            'user' => 'user',
        ];

        $data = [];

        /* =====================
        INGRESOS (In)
        ======================*/
        $entries = $this->model->list(
            'a.created_at, b.name, a.qty, c.username',
            'epp_register a',
            " AND a.item_id = $id",
            'LEFT JOIN epp_db b ON a.item_id = b.id 
            LEFT JOIN users c ON a.user_id = c.id'
        );

        foreach ($entries as $r) {
            $data[] = [
                'date' => $r->created_at,
                'type' => 'In',
                'name' => $r->name,
                'qty' => $r->qty,
                'user' => $r->username,
            ];
        }

        /* =====================
        SALIDAS (Out)
        ======================*/
        $itemInfo = $this->model->get('name', 'epp_db', " AND id = $id");

        if ($itemInfo) {
            $itemName = addslashes($itemInfo->name);

            $outputs = $this->model->list(
                'a.created_at, a.name, b.name AS employeeName',
                'epp a',
                " AND a.name = '$itemName'",
                'LEFT JOIN employees b ON a.employee_id = b.id'
            );

            foreach ($outputs as $r) {
                $data[] = [
                    'date' => $r->created_at,
                    'type' => 'Out',
                    'name' => $r->name,
                    'qty' => 1,
                    'user' => $r->employeeName,
                ];
            }
        }

        /* =====================
        ORDENAMIENTO
        ======================*/
        $orderField = 'date';
        $orderDir = 'DESC';

        if (isset($_GET['sort'][0]['field'], $_GET['sort'][0]['dir'])) {
            if (isset($fieldMap[$_GET['sort'][0]['field']])) {
                $orderField = $_GET['sort'][0]['field'];
                $orderDir = strtoupper($_GET['sort'][0]['dir']) === 'ASC' ? 'ASC' : 'DESC';
            }
        }

        usort($data, function ($a, $b) use ($orderField, $orderDir) {
            $cmp = strtotime($a[$orderField]) <=> strtotime($b[$orderField]);

            return $orderDir === 'ASC' ? $cmp : -$cmp;
        });

        /* =====================
        PAGINACIÓN MANUAL
        ======================*/
        $total = count($data);
        $pagedData = array_slice($data, $offset, $size);

        echo json_encode([
            'data' => $pagedData,
            'last_page' => ceil($total / $size),
            'last_row' => $total,
        ]);
    }
}
