<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class InspectionsController
{
    private array $hxTriggers = [];

    public function __construct(public Model $model, private AuthService $auth) {}

    private function sendHeaders(): void
    {
        if (empty($this->hxTriggers)) {
            return;
        }
        $formattedTriggers = [];
        foreach ($this->hxTriggers as $key => $value) {
            $formattedTriggers[$key] = ($key === 'showMessage') ? json_encode($value) : $value;
        }
        header('HX-Trigger: '.json_encode($formattedTriggers));
    }

    private function upsertItem(int $inspection_id, int $activity_id, array $data): void
    {
        $existing = $this->model->get('id', 'inspection_items', " AND inspection_id = $inspection_id AND activity_id = $activity_id");
        if ($existing) {
            $this->model->update('inspection_items', (object) $data, $existing->id);
        } else {
            $data['inspection_id'] = $inspection_id;
            $data['activity_id'] = $activity_id;
            $this->model->save('inspection_items', (object) $data);
        }
    }

    public function Index()
    {
        $user = $this->auth->authorize(166);
        $tabulator = true;
        $jspreadsheet = false;
        $kpis = true;
        $content = 'app/components/list.php';
        $title = 'Inspections';

        $columns = '[
            { "title": "ID", "field": "id", "width": 60, "sorter": "number", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Created", "field": "created_at", headerHozAlign: "center", "headerFilter": "input" },
            { "title": "Due", "field": "due_date", headerHozAlign: "center", headerFilter: customDateRangeFilter, headerFilterFunc: customDateFilterFunc, headerFilterLiveFilter: false },
            { "title": "Asset", "field": "asset", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Status", "field": "status", headerHozAlign: "center", hozAlign:"center", headerFilter:"list",
            headerFilterParams:{ values: {"Open": "Open", "Started": "Started", "Closed": "Closed"}, clearable:true}},
            { "title": "Days", "field": "days", "width": 100, "headerHozAlign": "left", "headerFilter": "input", formatter: "html"},
            { "title": "Frequency", "field": "frequency", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Started", "field": "started", "headerHozAlign": "left", "headerFilter": "input"},
            { "title": "Closed", "field": "closed", "headerHozAlign": "left", "headerFilter": "input"},
        ]';

        require_once 'app/views/index.php';
    }

    public function Data()
    {
        $user = $this->auth->authorize(166);
        $isExport = isset($_GET['export']);
        if (! $isExport) {
            header('Content-Type: application/json');
        }

        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $size = isset($_GET['size']) ? (int) $_GET['size'] : 50;
        $offset = ($page - 1) * $size;

        $fieldMap = [
            'id' => 'a.id',
            'created_at' => 'a.created_at',
            'due_date' => 'a.due_date',
            'asset' => "concat(c.hostname, ' | ', c.serial, ' | ', c.sap)",
            'frequency' => 'b.frequency',
            'started' => 'a.started',
            'closed' => 'a.closed_at',
            'status' => 'a.status',
            'days' => 'DATEDIFF(a.due_date, CURDATE())',
        ];

        $where = ' ';
        if (isset($_GET['filter']) && is_array($_GET['filter'])) {
            foreach ($_GET['filter'] as $key => $f) {
                $field = is_array($f) ? ($f['field'] ?? '') : $key;
                $val = is_array($f) ? ($f['value'] ?? '') : $f;
                if (empty($val) || ! isset($fieldMap[$field])) {
                    continue;
                }
                $dbField = $fieldMap[$field];

                if ($field === 'due_date' && strpos($val, '|') !== false) {
                    [$start, $end] = explode('|', $val);
                    if (! empty($start)) {
                        $where .= " AND $dbField >= '".addslashes($start)."'";
                    }
                    if (! empty($end)) {
                        $where .= " AND $dbField <= '".addslashes($end)."'";
                    }
                } else {
                    $val = addslashes($val);
                    $where .= " AND $dbField LIKE '%$val%'";
                }
            }
        }

        $defaultOrder = "FIELD(a.status, 'Open', 'Started', 'Closed'), a.due_date ASC";
        $orderBy = $defaultOrder;

        if (isset($_GET['sort'][0]['field'])) {
            $sortField = $_GET['sort'][0]['field'];
            $sortDir = strtoupper($_GET['sort'][0]['dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
            if (isset($fieldMap[$sortField])) {
                $orderBy = $fieldMap[$sortField]." $sortDir";
            }
        }

        $joins = 'LEFT JOIN inspection_automations b ON a.automation_id = b.id LEFT JOIN assets c ON b.asset_id = c.id';
        $selectFields = "a.*, b.frequency, concat(c.hostname, ' | ', c.serial,' | ', c.sap) as asset_full, DATEDIFF(a.due_date, CURDATE()) as days_diff";

        if ($isExport) {
            setcookie('download_complete', 'true', time() + 30, '/');
            $rows = $this->model->list($selectFields, 'inspections a', "$where ORDER BY $defaultOrder", $joins);
            $spreadsheet = new Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();
            $headers = ['ID', 'Programado', 'Vencimiento', 'Activo', 'Frecuencia', 'Estado', 'Inicio Real', 'Fin Real', 'Días Restantes'];
            $sheet->fromArray($headers, null, 'A1');
            $exportData = [];
            foreach ($rows as $r) {
                $exportData[] = [$r->id, $r->created_at, $r->due_date, $r->asset_full, $r->frequency, $r->status, $r->started_at, $r->closed_at, (int) $r->days_diff];
            }
            $sheet->fromArray($exportData, null, 'A2');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="Inspections_'.date('dmY').'.xlsx"');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        }

        $totalCount = $this->model->get('COUNT(a.id) AS total', 'inspections a', $where, $joins)->total;
        $rows = $this->model->list($selectFields, 'inspections a', "$where ORDER BY $orderBy LIMIT $offset, $size", $joins);

        $data = [];
        foreach ($rows as $r) {
            $days = (int) $r->days_diff;
            $color = ($days >= 0) ? 'text-green-500' : 'text-red-500';
            if (! empty($r->closed_at) && $r->closed_at != '0000-00-00 00:00:00') {
                $color = 'text-gray-500';
            }
            $data[] = [
                'id' => $r->id,
                'created_at' => $r->created_at,
                'due_date' => $r->due_date,
                'asset' => $r->asset_full,
                'frequency' => $r->frequency,
                'status' => $r->status,
                'started' => $r->started_at,
                'closed' => $r->closed_at,
                'days' => "<span class='font-bold $color'>$days</span>",
            ];
        }

        echo json_encode(['data' => $data, 'last_page' => ceil($totalCount / $size), 'last_row' => (int) $totalCount]);
    }

    public function Checklist(): void
    {
        $asset_id = (int) ($_REQUEST['asset_id'] ?? 0);
        $inspection_id = (int) ($_REQUEST['inspection_id'] ?? 0);
        $saved_items = [];
        $items = $this->model->list('*', 'inspection_items', " AND inspection_id = $inspection_id");
        if ($items) {
            foreach ($items as $si) {
                $saved_items[$si->activity_id] = $si;
            }
        }
        $checklist_data = [];
        $activities = $this->model->list('*', 'inspection_activities', " AND asset_id = $asset_id ORDER BY category");
        foreach ($activities as $r) {
            $checklist_data[$r->category][] = $r;
        }
        require 'app/views/inspections/checklist.php';
    }

    public function QuickSave(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        $q_id = (int) ($_POST['q_id'] ?? 0);
        $field = $_POST['field'] ?? '';

        if ($id && $q_id && $field) {
            $value = ($field === 'answer') ? ($_POST["q_$q_id"] ?? '') : ($_POST["obs_$q_id"] ?? '');
            $this->upsertItem($id, $q_id, [$field => $value]);
            $this->hxTriggers['showMessage'] = ['type' => 'success', 'message' => 'Actualizado', 'close' => ''];
        }
        $this->sendHeaders();
        exit;
    }

    public function CloseInspection(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id) {
            $inspection = $this->model->get('b.asset_id', 'inspections a', "AND a.id = $id", 'LEFT JOIN inspection_automations b ON a.automation_id = b.id');

            if ($inspection) {
                $totalReq = $this->model->get('COUNT(*) as total', 'inspection_activities', "AND asset_id = {$inspection->asset_id}")->total;
                $totalAns = $this->model->get('COUNT(*) as total', 'inspection_items', "AND inspection_id = $id AND answer != ''")->total;
                $missingDetails = $this->model->get('COUNT(*) as total', 'inspection_items', "AND inspection_id = $id AND answer = 'Mal' AND (url IS NULL OR url = '' OR obs IS NULL OR obs = '')")->total;

                if ($totalAns < $totalReq) {
                    $this->hxTriggers['showMessage'] = ['type' => 'error', 'message' => 'Checklist incompleto.', 'close' => ''];
                } elseif ($missingDetails > 0) {
                    $this->hxTriggers['showMessage'] = ['type' => 'error', 'message' => 'Hallazgos marcados como MAL requieren foto y descripción.', 'close' => ''];
                } else {
                    // 1. Cerrar Inspección
                    $this->model->update('inspections', (object) [
                        'status' => 'Closed',
                        'closed_at' => date('Y-m-d H:i:s'),
                    ], $id);

                    // 2. Crear Tickets por cada ítem en "Mal"
                    $findings = $this->model->list('a.*, b.activity', 'inspection_items a', "AND a.inspection_id = $id AND a.answer = 'Mal'", 'LEFT JOIN inspection_activities b ON a.activity_id = b.id');

                    if ($findings) {
                        foreach ($findings as $f) {
                            $ticket = new stdClass;
                            $ticket->asset_id = $inspection->asset_id;
                            $ticket->description = "HALLAZGO INSPECCIÓN #$id | Actividad: {$f->activity} | Obs: {$f->obs}";
                            $ticket->status = 'Open';
                            $ticket->priority = 'Medium';
                            $ticket->user_id = $_SESSION['id-SIGMA'];
                            $ticket->type = 'OHS';
                            $ticket->url = $f->url;
                            $this->model->save('tickets', $ticket);
                        }
                    }

                    $this->hxTriggers['eventChanged'] = true;
                    $this->hxTriggers['showMessage'] = ['type' => 'success', 'message' => 'Inspección Finalizada y Tickets creados', 'close' => 'closeNewModal'];
                }
            }
        }
        $this->sendHeaders();
        exit;
    }

    public function UploadPhoto(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        $q_id = (int) ($_POST['q_id'] ?? 0);
        $file_key = 'foto_'.$q_id;

        if ($id && $q_id && isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] === 0) {
            $dest = 'uploads/inspections/'.$id.'_'.$q_id.'_'.time().'.jpg';
            if (! is_dir('uploads/inspections/')) {
                mkdir('uploads/inspections/', 0777, true);
            }

            if (move_uploaded_file($_FILES[$file_key]['tmp_name'], $dest)) {
                $this->upsertItem($id, $q_id, ['url' => $dest]);
                echo '<img src="'.$dest.'?t='.time().'" class="w-full h-full object-cover">';
                $this->hxTriggers['showMessage'] = ['type' => 'success', 'message' => 'Foto guardada', 'close' => ''];
                $this->sendHeaders();
            }
        }
    }

    public function Detail(): void
    {
        $id_val = (int) ($_REQUEST['id'] ?? 0);
        $id = $this->model->get('a.*,b.asset_id', 'inspections a', "AND a.id = $id_val", 'LEFT JOIN inspection_automations b on a.automation_id = b.id');
        require_once 'app/views/inspections/detail.php';
    }

    public function Stats()
    {
        require_once 'app/views/inspections/stats.php';
    }

    public function Bot()
    {
        $frequencies = ['Daily' => '+1 day', 'Weekly' => '+1 week', 'Monthly' => '+1 month', 'Quarterly' => '+3 months', 'Semiannual' => '+6 months', 'Annual' => '+1 year'];
        foreach ($frequencies as $frequency => $interval) {
            $this->processFrequency($frequency, $interval);
        }
    }

    private function processFrequency($frequency, $interval)
    {
        $inspection = new stdClass;
        $automation = new stdClass;
        $inspection->status = 'Open';
        $today = date('Y-m-d');
        foreach ($this->model->list('*', 'inspection_automations', "and frequency = '$frequency'") as $r) {
            if (empty($r->anchor_date) || $r->anchor_date == $today) {
                $due_date = empty($r->anchor_date) ? date('Y-m-d', strtotime($today.' '.$interval)) : $r->anchor_date;
                $inspection->automation_id = $r->id;
                $inspection->due_date = $due_date;
                $automation->anchor_date = date('Y-m-d', strtotime($due_date.' '.$interval));
                $this->model->save('inspections', $inspection);
                $this->model->update('inspection_automations', $automation, $r->id);
            }
        }
    }

    public function Get()
    {
        $automations = $this->model->list('*', 'inspection_automations', 'AND status = "active"');
        $result = [];
        foreach ($automations as $automation) {
            $inspections = $this->model->list('due_date', 'inspections', "AND automation_id = {$automation->id}", '');
            $createdDates = array_map(function ($insp) {
                return $insp->due_date;
            }, $inspections);
            $result[] = ['id' => $automation->id, 'type' => $automation->type, 'frequency' => $automation->frequency, 'anchor_date' => $automation->anchor_date ?? null, 'created_inspections' => $createdDates];
        }
        echo json_encode($result, JSON_PRETTY_PRINT);
    }

    public function Gant(): void
    {
        require_once 'app/views/inspections/gant.php';
    }
}
