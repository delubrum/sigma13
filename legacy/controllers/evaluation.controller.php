<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

class EvaluationController
{
    public function __construct(private readonly Model $model, private readonly AuthService $auth) {}

    public function Index(): void
    {
        $user = $this->auth->authorize(75);
        $tabulator = true;
        $title = 'Suppliers Evaluation';
        $content = 'app/components/list.php';

        // CORRECCIÓN: Los "field" deben coincidir con los alias del SQL (date y user)
        $columns = '[
            { "title": "Date", "field": "date", "headerHozAlign": "center", "headerFilter": "input" },
            { "title": "User", "field": "user", "headerHozAlign": "center", "headerFilter": "input" },
            { "title": "Nit", "field": "nit", "headerHozAlign": "center", "headerFilter": "input" },
            { "title": "Supplier", "field": "supplier", "headerHozAlign": "center", "headerFilter": "input" },
            { "title": "Type", "field": "type", "headerHozAlign": "center", "headerFilter": "list" },
            { "title": "Result", "field": "result", "headerHozAlign": "center", "headerFilter": "list" }
        ]';
        require_once 'app/views/index.php';
    }

    public function Data(): void
    {
        $this->auth->authorize(147);
        header('Content-Type: application/json');

        $page = (int) ($_GET['page'] ?? 1);
        $size = (int) ($_GET['size'] ?? 15);
        $offset = ($page - 1) * $size;

        $fieldMap = [
            'id' => 'id',
            'date' => 'created_at',
            'nit' => 'nit',
            'supplier' => 'supplier',
            'user' => 'username',
            'type' => 'kind',
            'result' => 'result',
        ];

        $where = '';
        if (isset($_GET['filter'])) {
            foreach ($_GET['filter'] as $f) {
                if ($dbField = ($fieldMap[$f['field']] ?? null)) {
                    $where .= " AND $dbField LIKE '%{$f['value']}%'";
                }
            }
        }

        $total = $this->model->get('COUNT(id) AS total', 'suppliers_evaluation', $where)->total;

        $fields = "a.*, 
                   a.created_at AS date, 
                   b.username AS user, 
                   ROUND(((SUM(jt.val = '1') + SUM(jt.val = '2') * 0.5) / 12) * 100) AS result";

        $table = 'suppliers_evaluation a';

        $params = "$where GROUP BY a.id ORDER BY a.created_at DESC LIMIT $offset, $size";

        $joins = "LEFT JOIN users b ON b.id = a.user_id 
                  LEFT JOIN JSON_TABLE(IFNULL(a.answers,'{}'), '$.*' COLUMNS (val VARCHAR(1) PATH '$')) AS jt ON TRUE";

        $rows = $this->model->list($fields, $table, $params, $joins);

        echo json_encode([
            'data' => $rows,
            'last_page' => ceil($total / $size),
        ]);
    }

    public function Detail(): void
    {
        $id = $this->model->get('*', 'suppliers_evaluation', 'and id = '.(int) $_REQUEST['id']);
        require_once 'app/views/evaluation/detail.php';
    }

    public function Stats(): void
    {
        // Implementación de estadísticas pendiente
    }
}
