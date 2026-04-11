<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class JPController
{
    public function __construct(public Model $model, private AuthService $auth) {}

    public function Index()
    {
        $user = $this->auth->authorize([150, 151]);
        $tabulator = true;
        $jspreadsheet = true;
        $title = 'SGC / Job Profiles';
        $button = (array_intersect([150], json_decode($user->permissions ?? '[]', true))) ? 'New Profile' : '';
        $content = 'app/components/list.php';
        $columns = '[
            { "title": "ID", "field": "id", headerHozAlign: "center", headerFilter:"input"},
            { "title": "Code", "field": "code", headerHozAlign: "center", headerFilter:"input"},
            { "title": "Updated", "field": "created_at", headerHozAlign: "center", headerFilter: customDateRangeFilter, headerFilterFunc: customDateFilterFunc, headerFilterLiveFilter: false },
            { "title": "Name", "field": "name", headerHozAlign: "center", headerFilter:"input"},
            { "title": "Division", "field": "division", headerHozAlign: "center", headerFilter:"input"},
            { "title": "Reports To", "field": "reports_to", headerHozAlign: "center", headerFilter:"input"},
            { "title": "Mode", "field": "work_mode", headerHozAlign: "center", headerFilter:"input"},
            { "title": "Schedule", "field": "schedule", headerHozAlign: "center", headerFilter:"input"},
            { "title": "Travel", "field": "travel", headerHozAlign: "center", headerFilter:"input"},
            { "title": "Relocation", "field": "relocation", headerHozAlign: "center", headerFilter:"input"}
        ]';
        require_once 'app/views/index.php';
    }

    public function Data()
    {
        // 1. Seguridad
        $user = $this->auth->authorize([150, 151]);
        $isExport = isset($_GET['export']);

        if (! $isExport) {
            header('Content-Type: application/json');
        }

        // 2. Parámetros de Paginación
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $size = isset($_GET['size']) ? (int) $_GET['size'] : 15;
        $offset = ($page - 1) * $size;

        // 3. Mapeo de campos (Original de job_profiles)
        $fieldMap = [
            'id' => 'a.id',
            'code' => 'a.code',
            'created_at' => 'a.created_at',
            'name' => 'a.name',
            'area' => 'd.area',
            'division' => 'd.name as division',
            'reports_to' => 'e.name as reports_to',
            'work_mode' => 'a.work_mode',
            'rank' => 'a.rank',
            'schedule' => 'a.schedule',
            'travel' => 'a.travel',
            'relocation' => 'a.relocation',
        ];

        // 4. Lógica de Filtros (Siguiendo exactamente tu ejemplo)
        $where = ''; // Sin el 1=1 para evitar el error de sintaxis
        if (isset($_GET['filter']) && is_array($_GET['filter'])) {
            foreach ($_GET['filter'] as $key => $f) {
                $field = is_array($f) ? ($f['field'] ?? '') : $key;
                $value = is_array($f) ? ($f['value'] ?? '') : $f;
                $value = addslashes($value);

                if (isset($fieldMap[$field])) {
                    $dbField = $fieldMap[$field];
                    if ($field === 'created_at') {
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

        // Ordenamiento
        $orderBy = 'a.id DESC';
        if (isset($_GET['sort'][0]['field']) && isset($_GET['sort'][0]['dir'])) {
            $sortField = $_GET['sort'][0]['field'];
            $sortDir = strtoupper($_GET['sort'][0]['dir']) === 'ASC' ? 'ASC' : 'DESC';
            if (isset($fieldMap[$sortField])) {
                $orderBy = $fieldMap[$sortField]." $sortDir";
            }
        }

        // 5. Query principal
        $joins = 'LEFT JOIN hr_db e on a.reports_to = e.id LEFT JOIN hr_db d on a.division_id = d.id';
        $select = implode(', ', array_values($fieldMap));

        // 6. Ejecución y Procesamiento (Exportación)
        if ($isExport) {
            setcookie('download_complete', 'true', time() + 30, '/');
            $rows = $this->model->list($select, 'job_profiles a', "$where ORDER BY $orderBy", $joins);

            $spreadsheet = new Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();

            $headers = ['ID', 'Código', 'Fecha', 'Nombre', 'Área', 'División', 'Reporta a', 'Modalidad', 'Nivel', 'Horario', 'Viajes', 'Relocalización'];
            $sheet->fromArray($headers, null, 'A1');

            $exportData = [];
            foreach ($rows as $r) {
                $exportData[] = [
                    $r->id,
                    $r->code,
                    $r->created_at ? Date::PHPToExcel(strtotime($r->created_at)) : '',
                    $r->name,
                    $r->area,
                    $r->division,
                    $r->reports_to,
                    $r->work_mode,
                    $r->rank,
                    $r->schedule,
                    $r->travel,
                    $r->relocation,
                ];
            }

            $sheet->fromArray($exportData, null, 'A2');
            $lastRow = count($exportData) + 1;

            $sheet->getStyle("C2:C$lastRow")->getNumberFormat()->setFormatCode('yyyy-mm-dd');
            foreach (range('A', 'L') as $col) {
                $sheet->getColumnDimension($col)->setWidth(18);
            }

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="Reporte_Perfiles_'.date('dmY').'.xlsx"');

            $writer = new Xlsx($spreadsheet);
            $writer->setPreCalculateFormulas(false);
            $writer->save('php://output');
            exit;
        }

        // 7. Respuesta JSON Normal (Mantenida como job_profiles)
        $totalCount = $this->model->get('COUNT(a.id) AS total', 'job_profiles a', $where, $joins)->total;
        $rows = $this->model->list($select, 'job_profiles a', "$where ORDER BY $orderBy LIMIT $offset, $size", $joins);

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
            'last_page' => ceil($totalCount / $size),
            'last_row' => (int) $totalCount,
        ]);
    }

    public function Stats()
    {
        require_once 'app/views/jp/stats.php';
    }

    public function New()
    {
        $user = $this->auth->authorize(151);

        if (! empty($_REQUEST['id'])) {
            $filters = 'and id = '.$_REQUEST['id'];
            $id = $this->model->get('*', 'job_profiles', $filters);
        }
        $status = (! empty($_REQUEST['status'])) ? $_REQUEST['status'] : false;
        $isNested = ! empty($_REQUEST['modal']);
        $buttonId = $isNested ? 'closeNestedModal' : 'closeNewModal';
        $modalVar = $isNested ? 'nestedModal' : 'showModal';
        $modalDiv = $isNested ? 'nestedModal' : 'myModal';
        require_once 'app/views/jp/new.php';
    }

    public function Save()
    {
        $user = $this->auth->authorize(151);

        header('Content-Type: application/json');

        $item = new stdClass;
        foreach ($_POST as $k => $val) {
            if (! empty($val)) {
                if ($k != 'id') {
                    $item->{$k} = $val;
                }
            }
        }

        $item->user_id = $_SESSION['id-SIGMA'];
        $item->status = 'open';
        $item->reports = json_encode($_POST['reports']);

        if (! empty($_REQUEST['id'])) {
            $id = $this->model->update('job_profiles', $item, $_REQUEST['id']);
        } else {
            $id = $this->model->save('job_profiles', $item);
        }

        if ($id !== false) {

            $message = empty($_POST['id'])
                ? '{"type": "success", "message": "Profile Saved", "close" : "closeNewModal"}'
                : '{"type": "success", "message": "Profile Updated", "close" : "closeNestedModal"}';

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
        $user = $this->auth->authorize([150, 151]);
        if (! empty($_REQUEST['id'])) {
            $filters = 'and a.id = '.$_REQUEST['id'];
            $id = $this->model->get('a.*, e.name as reports_to, d.name as division, d.area', 'job_profiles a', $filters, 'LEFT JOIN hr_db e on a.reports_to = e.id LEFT JOIN hr_db d on a.division_id = d.id ');
        }
        require_once 'app/views/jp/detail.php';
    }

    public function Info()
    {
        $user = $this->auth->authorize([150, 151]);
        $canEdit = ! empty(array_intersect(['150'], json_decode($user->permissions ?? '[]', true)));

        if (! empty($_REQUEST['id'])) {
            $filters = 'and a.id = '.$_REQUEST['id'];
            $id = $this->model->get('a.*, e.name as reports_to, d.name as division, d.area',
                'job_profiles a',
                $filters,
                'LEFT JOIN hr_db e on a.reports_to = e.id 
                LEFT JOIN hr_db d on a.division_id = d.id'
            );

            // 🔹 Si hay datos en reports, los decodificas y buscas sus nombres
            if (! empty($id->reports)) {
                $reports_ids = json_decode($id->reports, true);
                if (is_array($reports_ids)) {
                    $names = [];
                    foreach ($reports_ids as $rep_id) {
                        $rep = $this->model->get('name', 'hr_db', " and id = '$rep_id'");
                        if ($rep) {
                            $names[] = $rep->name;
                        }
                    }
                    $id->reports_names = implode('<br>', $names); // ej: "Supervisor, Analista, Técnico"
                }
            }
        }
        require_once 'app/views/jp/detail/tabs/info.php';
    }

    public function DetailTab()
    {
        $user = $this->auth->authorize([150, 151]);
        $canEdit = ! empty(array_intersect(['150'], json_decode($user->permissions ?? '[]', true)));
        $tab = $_REQUEST['tab'];
        $type = $_REQUEST['type'] ?? 0;
        $filters = 'and id = '.$_REQUEST['id'];
        $id = $this->model->get('*', 'job_profiles', $filters);
        $data = (! empty($this->model->get('content', 'job_profile_items', " and jp_id = $id->id AND kind = '$type'")->content))
        ? $this->model->get('content', 'job_profile_items', " and jp_id = $id->id AND kind = '$type'")->content
        : '[]';
        require_once "app/views/jp/detail/tabs/$tab.php";
    }

    public function DetailModal()
    {
        $user = $this->auth->authorize(151);
        $modal = $_REQUEST['modal'];
        $filters = 'and id = '.$_REQUEST['id'];
        $id = $this->model->get('*', 'job_profiles', $filters);
        require_once "app/views/jp/detail/modals/$modal.php";
    }

    public function DetailListModal()
    {
        $user = $this->auth->authorize(151);
        $modal = $_REQUEST['modal'];
        $filters = 'and id = '.$_REQUEST['id'];
        $id = $this->model->get('*', 'job_profile_events', $filters);
        require_once "app/views/jp/detail/modals/$modal.php";
    }

    public function SaveItem()
    {
        $user = $this->auth->authorize(151);

        // 1. Obtener y decodificar JSON
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if ($data === null) {
            echo json_encode(['status' => 'error', 'message' => 'JSON inválido']);
            exit;
        }

        $item = new stdClass;
        $item->kind = $data['type'] ?? null;
        $item->jp_id = $data['jp_id'] ?? null;
        $item->user_id = $_SESSION['id-SIGMA'];
        $item->content = json_encode($data['data']);
        try {
            $existing = $this->model->get('id', 'job_profile_items', "AND kind = '$item->type' AND jp_id = $item->jp_id");
            if (! empty($existing) && ! empty($existing->id)) {
                $this->model->update('job_profile_items', $item, $existing->id);
                $msg = 'Updated';
            } else {
                $this->model->save('job_profile_items', $item);
                $msg = 'Saved';
            }
            echo json_encode(['status' => 'success', 'message' => $msg]);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function SaveResource()
    {
        $user = $this->auth->authorize(151);

        $id = $_POST['id'] ?? null;
        $group = $_POST['group'] ?? null;
        $value = trim($_POST['value'] ?? '');
        $isInputRaw = $_POST['is_input'] ?? null;
        $isInput = ($isInputRaw === 'true' || $isInputRaw === '1' || $isInputRaw === 1 || $isInputRaw === true);

        if ($id && $group) {
            $row = $this->model->get('id, content', 'job_profile_items', " AND kind = 'Recursos' AND jp_id = $id") ?? null;
            $data = $row ? json_decode($row->content, true) : [];

            // Asegurar array
            if (! is_array($data)) {
                $data = [];
            }

            // Compatibilidad: si el JSON previo era un array simple (legacy), lo guardamos bajo 'items' para no perderlo
            if (array_values($data) === $data) {
                $data = ['items' => $data];
            }

            // Asegurar estructura del grupo
            if (! isset($data[$group]) || ! is_array($data[$group])) {
                $data[$group] = ['items' => [], 'otro' => ''];
            } else {
                if (! isset($data[$group]['items']) || ! is_array($data[$group]['items'])) {
                    $data[$group]['items'] = is_array($data[$group]['items']) ? $data[$group]['items'] : [];
                }
            }

            // Si la petición viene del input "Otro" (is_input = true)
            if ($isInput) {
                if ($value === '') {
                    // eliminar campo 'otro' si está vacío
                    if (isset($data[$group]['otro'])) {
                        unset($data[$group]['otro']);
                    }
                } else {
                    $data[$group]['otro'] = $value;
                }
            } else {
                // Petición desde botón: alternar en items del grupo
                if ($value !== '') {
                    if (! in_array($value, $data[$group]['items'])) {
                        $data[$group]['items'][] = $value;
                    } else {
                        $data[$group]['items'] = array_values(array_diff($data[$group]['items'], [$value]));
                    }
                }
            }

            // Guardar
            $item = new stdClass;
            $item->jp_id = $id;
            $item->kind = 'Recursos';
            $item->user_id = $_SESSION['id-SIGMA'];
            $item->content = json_encode($data, JSON_UNESCAPED_UNICODE);

            if ($row) {
                $saveId = $this->model->update('job_profile_items', $item, $row->id);
            } else {
                $saveId = $this->model->save('job_profile_items', $item);
            }

            if ($saveId !== false) {
                $message = '{"type": "success", "message": "Updated", "close": ""}';
                $hxTriggerData = json_encode([
                    'eventChanged' => true,
                    'showMessage' => $message,
                ]);
                header('HX-Trigger: '.$hxTriggerData);
                http_response_code(204);
            }
        }
    }
}
