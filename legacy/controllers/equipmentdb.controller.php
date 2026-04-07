<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

class EquipmentDBController
{
    public function __construct(public Model $model, private AuthService $auth) {}

    public function Index()
    {
        $user = $this->auth->authorize(154);
        $tabulator = true;
        $jspreadsheet = true;
        $title = 'OHS / Equipment / Admin';
        $button = 'New Equipment';
        $content = 'app/components/list.php';
        $columns = '[
            { "title": "ID", "field": "id", headerHozAlign: "center", headerFilter:"input"},
            { "title": "Name", "field": "name", headerHozAlign: "center", headerFilter:"input"},
            { "title": "SAP", "field": "code", headerHozAlign: "center", headerFilter:"input"},
            { "title": "Price", "field": "price", headerHozAlign: "center", headerFilter:"input"},
            { "title": "Min", "field": "min_stock", headerHozAlign: "center", headerFilter:"input"},
        ]';
        require_once 'app/views/index.php';
    }

    public function Data()
    {
        $user = $this->auth->authorize(154);

        header('Content-Type: application/json');

        // Parámetros de paginación
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $size = isset($_GET['size']) ? (int) $_GET['size'] : 15;
        $offset = ($page - 1) * $size;

        // Mapeo seguro de campos frontend => SQL
        $fieldMap = [
            'id' => 'a.id',
            'code' => 'a.code',
            'name' => 'a.name',
            'price' => 'a.price',
            'min' => 'a.min_stock',
        ];

        $where = '';

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

                if (($field === 'created_at' || $field === 'status_at') && strpos($value, ' to ') !== false) {
                    [$from, $to] = explode(' to ', $value);
                    $where .= " AND DATE($dbField) BETWEEN '$from' AND '$to'";
                } else {
                    $where .= " AND $dbField LIKE '%$value%'";
                }
            }
        }

        $orderBy = 'a.id DESC';

        if (isset($_GET['sort'][0]['field']) && isset($_GET['sort'][0]['dir'])) {
            $sortField = $_GET['sort'][0]['field'];
            $sortDir = strtoupper($_GET['sort'][0]['dir']) === 'ASC' ? 'ASC' : 'DESC';

            if (isset($fieldMap[$sortField])) {
                $orderBy = $fieldMap[$sortField]." $sortDir";
            }
        }

        // Select fields from map (sin duplicados)
        $selectFields = implode(', ', array_values($fieldMap));

        // Total de registros (se necesita también el JOIN aquí)
        $total = $this->model->get('count(a.id) as total', 'equipment_db a', $where)->total;

        // Datos con paginación
        $rows = $this->model->list(
            $selectFields,
            'equipment_db a',
            "$where ORDER BY $orderBy LIMIT $offset, $size"
        );

        $data = [];

        $data = [];
        foreach ($rows as $r) {
            $item = [];
            foreach (array_keys($fieldMap) as $field) {
                $item[$field] = $r->$field;
            }
            $data[] = $item;
        }

        echo json_encode([
            'data' => $data,
            'last_page' => ceil($total / $size),
            'last_row' => $total,
        ]);
    }

    public function Stats() {}

    public function New()
    {
        $user = $this->auth->authorize(154);
        require_once 'app/views/equipment/new.php';
    }

    public function Save()
    {
        $user = $this->auth->authorize(154);

        header('Content-Type: application/json');

        $item = new stdClass;
        foreach ($_POST as $k => $val) {
            if (! empty($val)) {
                if ($k != 'id') {
                    $item->{$k} = $val;
                }
            }
        }
        if (! empty($_REQUEST['id'])) {
            $id = $this->model->update('equipment_db', $item, $_REQUEST['id']);
        } else {
            $id = $this->model->save('equipment_db', $item);
        }

        if ($id !== false) {

            $message = empty($_POST['id'])
                ? '{"type": "success", "message": "Saved", "close" : "closeNewModal"}'
                : '{"type": "success", "message": "Updated", "close" : "closeNewModal"}';

            $hxTriggerData = json_encode([
                'eventChanged' => true,
                'showMessage' => $message,
            ]);
            header('HX-Trigger: '.$hxTriggerData);
            http_response_code(204);
        }
    }

    public function Detail()
    {
        $user = $this->auth->authorize(154);

        if (! empty($_REQUEST['id'])) {
            $filters = 'and id = '.$_REQUEST['id'];
            $id = $this->model->get('*', 'equipment_db', $filters);
        }
        require_once 'app/views/equipment/new.php';
    }
}
