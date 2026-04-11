<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class AssetsController
{
    public function __construct(public Model $model, private AuthService $auth)
    {
    }

    public function Index()
    {
        $user = $this->auth->authorize(140);

        $tabulator = true;
        $button  = "New Asset";
        $content = "app/components/list.php";
        $title   = "Infrastructure / Assets";
        columns: [
            ['title' => 'ID', 'field' => 'id', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
            ['title' => 'Área', 'field' => 'area', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
            ['title' => 'SAP', 'field' => 'sap', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
            ['title' => 'Serial', 'field' => 'serial', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
            ['title' => 'Responsable', 'field' => 'assignee', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
            ['title' => 'Hostname', 'field' => 'hostname', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
            ['title' => 'Marca', 'field' => 'brand', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
            ['title' => 'Modelo', 'field' => 'model', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
            ['title' => 'Tipo', 'field' => 'kind', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
            ['title' => 'CPU', 'field' => 'cpu', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
            ['title' => 'RAM', 'field' => 'ram', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
            ['title' => 'SSD', 'field' => 'ssd', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
            ['title' => 'HDD', 'field' => 'hdd', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
            ['title' => 'S.O.', 'field' => 'so', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
            ['title' => 'Precio', 'field' => 'price', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
            ['title' => 'Fecha', 'field' => 'date', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
            ['title' => 'Factura', 'field' => 'invoice', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
            ['title' => 'Proveedor', 'field' => 'supplier', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
            ['title' => 'Garantía', 'field' => 'warranty', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
            ['title' => 'Modo Trabajo', 'field' => 'work_mode', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
            ['title' => 'Ubicación', 'field' => 'location', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
            ['title' => 'Teléfono', 'field' => 'phone', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
            ['title' => 'Operador', 'field' => 'operator', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
            ['title' => 'Estado', 'field' => 'status', 'formatter' => 'html', 'headerHozAlign' => 'center', 'hozAlign' => 'center', 'headerFilter' => 'list', 'headerFilterParams' => ['values' => ['available' => 'Disponible', 'assigned' => 'Asignado', 'maintenance' => 'Mantenimiento', 'retired' => 'Retirado'], 'clearable' => true], 'headerFilterPlaceholder' => 'Filtro...'],
            ['title' => 'Clasificación', 'field' => 'classification', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
            ['title' => 'Conf.', 'field' => 'confidentiality', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
            ['title' => 'Int.', 'field' => 'integrity', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
            ['title' => 'Disp.', 'field' => 'availability', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
            ['title' => 'Criticidad', 'field' => 'criticality', 'formatter' => 'html', 'headerHozAlign' => 'center', 'hozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
        ],
        require_once 'app/views/index.php';
    }

    public function Data()
    {
        // 1. Seguridad y Detección de Exportación
        $user = $this->auth->authorize(140);
        $isExport = isset($_GET['export']);

        if (!$isExport) {
            header('Content-Type: application/json');
        }

        // 2. Parámetros de Paginación
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $size = isset($_GET['size']) ? (int)$_GET['size'] : 50;
        $offset = ($page - 1) * $size;

        // 3. Mapeo Seguro de Campos
        $fieldMap = [
            'id'              => 'a.id',
            'area'            => 'a.area',
            'sap'             => 'a.sap',
            'serial'          => 'a.serial',
            'assignee'        => 'assignee.name',
            'hostname'        => 'a.hostname',
            'brand'           => 'a.brand',
            'model'           => 'a.model',
            'kind'            => 'a.kind',
            'classification'  => 'a.classification',
            'confidentiality' => 'a.confidentiality',
            'integrity'       => 'a.integrity',
            'availability'    => 'a.availability',
            'criticality'     => '(a.confidentiality + a.integrity + a.availability)',
            'cpu'             => 'a.cpu',
            'ram'             => 'a.ram',
            'ssd'             => 'a.ssd',
            'hdd'             => 'a.hdd',
            'so'              => 'a.so',
            'price'           => 'a.price',
            'date'            => 'a.date',
            'invoice'         => 'a.invoice',
            'supplier'        => 'a.supplier',
            'warranty'        => 'a.warranty',
            'work_mode'       => 'a.work_mode',
            'location'        => 'a.location',
            'phone'           => 'a.phone',
            'operator'        => 'a.operator',
            'status'          => 'a.status',
        ];

        $where = " and deleted_at is null";

        // 4. Procesamiento de Filtros
        if (isset($_GET['filter']) && is_array($_GET['filter'])) {
            foreach ($_GET['filter'] as $key => $f) {
                $field = is_array($f) ? ($f['field'] ?? '') : $key;
                $value = is_array($f) ? ($f['value'] ?? '') : $f;
                $value = addslashes($value);

                if (isset($fieldMap[$field])) {
                    $dbField = $fieldMap[$field];
                    if ($field === 'date' && strpos($value, ' to ') !== false) {
                        list($from, $to) = explode(' to ', $value);
                        $where .= " AND DATE($dbField) BETWEEN '$from' AND '$to'";
                    } else {
                        $where .= " AND $dbField LIKE '%$value%'";
                    }
                }
            }
        }

        // 5. Ordenamiento
        $orderBy = "a.id DESC";
        if (isset($_GET['sort'][0]['field'])) {
            $sortField = $_GET['sort'][0]['field'];
            $sortDir = strtoupper($_GET['sort'][0]['dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
            if (isset($fieldMap[$sortField])) {
                $orderBy = $fieldMap[$sortField] . " $sortDir";
            }
        }

        // 6. Joins y Select
        $joinQuery = "LEFT JOIN (
            SELECT ae.asset_id, e.name
            FROM asset_events ae
            JOIN employees e ON e.id = ae.employee_id
            WHERE ae.kind = 'assignment'
            AND ae.id IN (
                SELECT MAX(id) FROM asset_events WHERE kind = 'assignment' GROUP BY asset_id
            )
            GROUP BY ae.asset_id 
        ) AS assignee ON assignee.asset_id = a.id";

        $selectFields = "
            a.*,
            CASE WHEN a.status = 'assigned' THEN assignee.name ELSE NULL END AS assignee_name,
            (a.confidentiality + a.integrity + a.availability) AS criticality_score
        ";

        // 7. Ejecución de Consultas
        if ($isExport) {
            setcookie("download_complete", "true", time() + 30, "/");
            $rows = $this->model->list($selectFields, "assets a", "$where ORDER BY $orderBy", $joinQuery);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Cabeceras Técnicas
            $headers = [
                'ID', 'Área', 'SAP', 'Serial', 'Responsable', 'Hostname', 'Marca', 'Modelo', 'Tipo', 
                'Clasificación', 'Conf.', 'Int.', 'Disp.', 'Criticidad', 'CPU', 'RAM', 'SSD', 'HDD', 
                'S.O.', 'Precio', 'Fecha Compra', 'Factura', 'Proveedor', 'Garantía', 'Modo Trabajo', 
                'Ubicación', 'Extension', 'Estado'
            ];
            $sheet->fromArray($headers, NULL, 'A1');

            $exportData = [];
            foreach ($rows as $r) {
                $exportData[] = [
                    $r->id, $r->area, $r->sap, $r->serial, $r->assignee_name, $r->hostname, $r->brand, $r->model, $r->kind,
                    $r->classification, $r->confidentiality, $r->integrity, $r->availability, $r->criticality_score,
                    $r->cpu, $r->ram, $r->ssd, $r->hdd, $r->so,
                    (float)$r->price,
                    ($r->date && $r->date != '0000-00-00') ? Date::PHPToExcel(strtotime($r->date)) : '',
                    $r->invoice, $r->supplier, $r->warranty, $r->work_mode, $r->location, $r->phone, $r->status
                ];
            }

            // Vuelco masivo
            $sheet->fromArray($exportData, NULL, 'A2');
            $lastR = count($exportData) + 1;

            // Formatos: Precio (T) y Fecha (U)
            $sheet->getStyle("T2:T$lastR")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $sheet->getStyle("U2:U$lastR")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD2);
            
            // Estilo Negrita Cabecera
            $sheet->getStyle('A1:AB1')->getFont()->setBold(true);

            // Anchos predefinidos para velocidad
            $widths = [
                'A'=>8, 'B'=>15, 'C'=>12, 'D'=>20, 'E'=>25, 'F'=>20, 'G'=>12, 'H'=>15, 'I'=>12, 
                'J'=>15, 'K'=>6, 'L'=>6, 'M'=>6, 'N'=>10, 'O'=>15, 'P'=>8, 'Q'=>8, 'R'=>8, 
                'S'=>15, 'T'=>12, 'U'=>12, 'V'=>12, 'W'=>20, 'X'=>12, 'Y'=>15, 'Z'=>15, 'AA'=>10, 'AB'=>12
            ];
            foreach($widths as $col => $w) $sheet->getColumnDimension($col)->setWidth($w);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="Inventario_Activos_'.date('dmY').'.xlsx"');
            
            $writer = new Xlsx($spreadsheet);
            $writer->setPreCalculateFormulas(false);
            $writer->save('php://output');
            exit;
        }

        // 8. Respuesta para Tabulator (Web)
        $totalCount = $this->model->get("COUNT(DISTINCT a.id) AS total", "assets a", $where, $joinQuery)->total;
        $rows = $this->model->list($selectFields, "assets a", "$where ORDER BY $orderBy LIMIT $offset, $size", $joinQuery);
        
        $data = [];
        foreach ($rows as $r) {
            $data[] = [
                'id' => $r->id, 'area' => $r->area, 'sap' => $r->sap, 'serial' => $r->serial,
                'assignee' => $r->assignee_name, 'hostname' => $r->hostname, 'brand' => $r->brand,
                'model' => $r->model, 'kind' => $r->kind, 'classification' => $r->classification,
                'confidentiality'=> $r->confidentiality, 'integrity' => $r->integrity, 'availability' => $r->availability,
                'criticality' => $r->criticality_score, 'cpu' => $r->cpu, 'ram' => $r->ram, 'ssd' => $r->ssd,
                'hdd' => $r->hdd, 'so' => $r->so, 'price' => $r->price, 'date' => $r->date,
                'invoice' => $r->invoice, 'supplier' => $r->supplier, 'warranty' => $r->warranty,
                'work_mode' => $r->work_mode, 'location' => $r->location, 'phone' => $r->phone, 'status' => $r->status,
            ];
        }

        echo json_encode([
            "data"      => $data,
            "last_page" => ceil(($totalCount ?? 0) / $size),
            "last_row"  => (int)($totalCount ?? 0)
        ]);
    }

    public function Stats()
    {
        $user = $this->auth->authorize(140);

        // Mapeo seguro de campos frontend => SQL
        $fieldMap = [
            'id' => 'a.id',
            'created_at' => 'a.created_at',
            'kind' => 'a.kind',
            'priority' => 'a.priority',
            'description' => 'a.description',
            'status' => 'a.status',
            'status_at' => 'a.status_at',
        ];

        $where = "";

        // Filtros
        if (isset($_GET['filter']) && is_array($_GET['filter'])) {
            foreach ($_GET['filter'] as $field => $value) {
                if (!isset($fieldMap[$field])) continue;

                $dbField = $fieldMap[$field];
                $value = addslashes($value);

                if ($field === 'created_at' && strpos($value, ' to ') !== false) {
                    list($from, $to) = explode(' to ', $value);
                    $where .= " AND DATE($dbField) BETWEEN '$from' AND '$to'";
                } else {
                    $where .= " AND $dbField LIKE '%$value%'";
                }
            }
        }

        // Reutilizamos el JOIN de Data()
        $join = '';

        // Consultas
        $a = $this->model->get('COUNT(a.id) as total', "assets a", "$where and status ='available'", $join)->total;
        $b = $this->model->get('COUNT(a.id) as total', "assets a", "$where and status = 'assigned'", $join)->total;
        $c = $this->model->get('COUNT(a.id) as total', "assets a", "$where and status <> 'available' and status <> 'assigned'", $join)->total;

        // Renderizar vista
        require_once 'app/views/assets/stats.php';
    }

    public function New()
    {
        $user = $this->auth->authorize(140);
        
        if (!empty($_REQUEST['id'])) {
            $filters = "and id = " . $_REQUEST['id'];
            $id = $this->model->get('*', 'assets', $filters);
        }
        $status = (!empty($_REQUEST['status'])) ? $_REQUEST['status'] : false;
        require_once "app/views/assets/new.php";
    }

    public function Save()
    {
        $user = $this->auth->authorize(140);

        header('Content-Type: application/json');

        $item = new stdClass();
        foreach ($_POST as $k => $val) {
            if (!empty($val)) {
                if ($k != 'id') {
                    $item->{$k} = $val;
                }
            }
        }
        if (!empty($_REQUEST['id'])) {
            $id = $this->model->update('assets', $item, $_REQUEST['id']);
            $text = 'Updated';
        } else {
            $id = $this->model->save('assets', $item);
            $text = 'Saved';
        }

        if ($id !== false) {

            $message = empty($_POST['id'])
                ? '{"type": "success", "message": "Saved", "close" : "closeNewModal"}'
                : '{"type": "success", "message": "Updated", "close" : "closeNestedModal"}';

            $hxTriggerData = json_encode([
                "eventChanged" => true,
                "showMessage" => $message
            ]);
            header('HX-Trigger: ' . $hxTriggerData);
            http_response_code(204);
        }
    }

    public function Detail()
    {
        $user = $this->auth->authorize(140);

        if (!empty($_REQUEST['id'])) {
            $filters = "and a.id = " . $_REQUEST['id'];
            $id = $this->model->get(
                "a.id, a.sap, a.serial, a.area,
                CASE WHEN a.status = 'assigned' THEN assignee.name ELSE NULL END as assignee, 
                CASE WHEN a.status = 'assigned' THEN assignee.profile ELSE NULL END as profile, 
                CASE WHEN a.status = 'assigned' THEN assignee.assigned_at ELSE NULL END as assigned_at, 
                a.hostname, a.brand, a.model, a.kind, a.cpu, a.ram, a.ssd, a.hdd, a.so, a.price, a.date, a.invoice,a.supplier, a.warranty, a.status",
                "assets a",
                $filters,
                "LEFT JOIN (
                SELECT ae.asset_id, e.name, e.profile, ae.created_at as assigned_at
                FROM asset_events ae
                JOIN employees e ON e.id = ae.employee_id
                WHERE ae.kind = 'assignment'
                AND ae.id IN (
                    SELECT MAX(id)
                    FROM asset_events
                    WHERE kind = 'assignment'
                    GROUP BY asset_id
                )
                ) AS assignee ON assignee.asset_id = a.id"
            );
        }
        require_once "app/views/assets/detail.php";
    }

    public function Info()
    {
        $user = $this->auth->authorize(140);

        if (!empty($_REQUEST['id'])) {
            $filters = "and a.id = " . $_REQUEST['id'];
            $id = $this->model->get(
                "a.id, a.sap, a.serial, a.phone, a.location, a.url,
                CASE WHEN a.status = 'assigned' THEN assignee.name ELSE NULL END as assignee, 
                CASE WHEN a.status = 'assigned' THEN assignee.profile ELSE NULL END as profile, 
                CASE WHEN a.status = 'assigned' THEN assignee.assigned_at ELSE NULL END as assigned_at, 
                a.hostname, a.brand, a.model, a.kind, a.cpu, a.ram, a.ssd, a.hdd, a.so, a.price, a.date, a.invoice,a.supplier, a.warranty, a.work_mode, a.status",
                "assets a",
                $filters,
                "LEFT JOIN (
                SELECT ae.asset_id, e.name, e.profile, ae.created_at as assigned_at
                FROM asset_events ae
                JOIN employees e ON e.id = ae.employee_id
                WHERE ae.kind = 'assignment'
                AND ae.id IN (
                    SELECT MAX(id)
                    FROM asset_events
                    WHERE kind = 'assignment'
                    GROUP BY asset_id
                )
                ) AS assignee ON assignee.asset_id = a.id"
            );
        }

        $options = new QROptions(['eccLevel' => QRCode::ECC_L,'outputType' => QRCode::OUTPUT_MARKUP_SVG,'version' => 5,]);
        $qrcode = (new QRCode($options))->render("https://sigma.es-metals.com/sigma/?c=Assets&a=View&id=$id->id");
        require_once "app/views/assets/detail/tabs/info.php";
    }

    public function View()
    {

        if (!empty($_REQUEST['id'])) {
            $filters = "and a.id = " . $_REQUEST['id'];
            $id = $this->model->get(
                "a.id, a.sap, a.serial, a.url,
                CASE WHEN a.status = 'assigned' THEN assignee.name ELSE NULL END as assignee, 
                CASE WHEN a.status = 'assigned' THEN assignee.profile ELSE NULL END as profile, 
                CASE WHEN a.status = 'assigned' THEN assignee.assigned_at ELSE NULL END as assigned_at, 
                a.hostname, a.brand, a.model, a.kind, a.cpu, a.ram, a.ssd, a.hdd, a.so, a.price, a.date, a.invoice,a.supplier, a.warranty, a.status",
                "assets a",
                $filters,
                "LEFT JOIN (
                SELECT ae.asset_id, e.name, e.profile, ae.created_at as assigned_at
                FROM asset_events ae
                JOIN employees e ON e.id = ae.employee_id
                WHERE ae.kind = 'assignment'
                AND ae.id IN (
                    SELECT MAX(id)
                    FROM asset_events
                    WHERE kind = 'assignment'
                    GROUP BY asset_id
                )
                ) AS assignee ON assignee.asset_id = a.id"
            );
        }

        $options = new QROptions(['eccLevel' => QRCode::ECC_L,'outputType' => QRCode::OUTPUT_MARKUP_SVG,'version' => 5,]);
        $qrcode = (new QRCode($options))->render("https://sigma.es-metals.com/sigma/?c=Assets&a=Index&id=$id->id");
        require_once "app/views/assets/view.php";
    }

    public function DetailTab()
    {
        $user = $this->auth->authorize(140);
        $tab = $_REQUEST['tab'];
        $filters = "and id = " . $_REQUEST['id'];
        $id = $this->model->get('*', 'assets', $filters);
        require_once "app/views/assets/detail/tabs/$tab.php";
    }

    public function DetailModal()
    {
        $user = $this->auth->authorize(140);
        $modal = $_REQUEST['modal'];
        $filters = "and id = " . $_REQUEST['id'];
        $id = $this->model->get('*', 'assets', $filters);
        require_once "app/views/assets/detail/modals/$modal.php";
    }

    public function DetailListModal()
    {
        $user = $this->auth->authorize(140);
        $modal = $_REQUEST['modal'];
        $filters = "and id = " . $_REQUEST['id'];
        $id = $this->model->get('*', 'asset_events', $filters);
        require_once "app/views/assets/detail/modals/$modal.php";
    }

    public function GetEvents() 
    {
        $user = $this->auth->authorize(140);
        $id = $_REQUEST['id'] ?? null;
        $type = $_REQUEST['kind'] ?? null;
        $search = $_REQUEST['search'] ?? null;
        $page = (int) ($_REQUEST['page'] ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $columnNames = ['a.id', 'a.kind', 'e.name', 'a.created_at', 'e.profile', 'a.notes', 'a.software', 'a.hardware', 'a.wipe', 'u.username', 'a.asset_id'];
        $where = '';

        if ($search) {
            $searchValue = addslashes($search);
            $searchParts = [];
            foreach ($columnNames as $colName) {
                $searchParts[] = "$colName LIKE '%$searchValue%'";
            }
            $where .= " AND (" . implode(" OR ", $searchParts) . ")";
        }

        $array = $this->model->list(implode(", ",$columnNames), 'asset_events a'," $where and a.asset_id = $id and a.kind = '$type' ORDER BY a.created_at DESC LIMIT $limit OFFSET $offset", 'LEFT JOIN employees e ON a.employee_id = e.id LEFT JOIN users u ON a.user_id = u.id');
        // Enviar la variable $hasMore para controlar si se sigue paginando

        $hasMore = count($array) === $limit;
        $nextPage = $page + 1;
        include "app/views/assets/detail/tabs/$type-list.php";
    }

    public function DataAssignments()
    {
        $user = $this->auth->authorize(140);
        header('Content-Type: application/json');

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $size = isset($_GET['size']) ? (int)$_GET['size'] : 15;
        $offset = ($page - 1) * $size;

        $fieldMap = [
            'id'          => 'a.id',
            'date'        => 'a.created_at',
            'notes'       => 'a.notes',
            'hardware'    => 'a.hardware',
            'software'    => 'a.software'
        ];

        $where = " and a.kind = 'assignment' and asset_id = ". $_REQUEST['id'];

        if (isset($_GET['filter']) && is_array($_GET['filter'])) {
            foreach ($_GET['filter'] as $f) {
                if (!isset($f['field'], $f['value'])) continue;
                $field = $f['field'];
                $value = addslashes($f['value']);
                if (!isset($fieldMap[$field])) continue;
                $dbField = $fieldMap[$field];
                if ($field === 'date' && strpos($value, ' to ') !== false) {
                    list($from, $to) = explode(' to ', $value);
                    $where .= " AND DATE($dbField) BETWEEN '$from' AND '$to'";
                } else {
                    $where .= " AND $dbField LIKE '%$value%'";
                }
            }
        }

        $orderBy = "a.id DESC";
        if (isset($_GET['sort'][0]['field'], $_GET['sort'][0]['dir'])) {
            $sortField = $_GET['sort'][0]['field'];
            $sortDir   = strtoupper($_GET['sort'][0]['dir']) === 'ASC' ? 'ASC' : 'DESC';
            if (isset($fieldMap[$sortField])) {
                $orderBy = $fieldMap[$sortField] . " $sortDir";
            }
        }

        $selectFields = "
            a.id, 
            a.asset_id,
            a.created_at,
            e.name as assignee,
            a.notes,
            a.hardware,
            a.software
        ";

        $joins = "LEFT JOIN employees e on a.employee_id = e.id";

        $total = $this->model->get("COUNT(a.id) AS total", "asset_events a", $where, $joins)->total;

        $rows = $this->model->list(
            $selectFields,
            "asset_events a",
            "$where ORDER BY $orderBy LIMIT $offset, $size",
            $joins
        );

        $data = [];
        $i = 0; // Contador para identificar la primera fila
        foreach ($rows as $r) {
            
            // --- LÓGICA IS_LATEST ---
            // Es la más reciente si estamos en la página 1 y es el primer registro del loop
            $isLatest = ($offset === 0 && $i === 0) ? true : false;

            $evidence = (file_exists("/var/www/html/sigma/uploads/assets/$r->asset_id/assignment/$r->id.pdf")) 
                ? "<a target='_blank' class='text-blue-500 font-bold' href='https://sigma.es-metals.com/sigma/uploads/assets/$r->asset_id/assignment/$r->id.pdf?t=" . time() . "'><i class='ri-file-line'></i> Minute </a>" 
                : '';

            $data[] = [
                'id'        => $r->id,
                'asset_id'  => $r->asset_id, // Necesario para el hx-get del botón edit
                'is_latest' => $isLatest,    // El booleano que usará Tabulator
                'date'      => $r->created_at,
                'assignee'  => $r->assignee,
                'notes'     => $r->notes,
                'software'  => $r->software ? implode(", ", json_decode($r->software)) : '',
                'hardware'  => $r->hardware ? implode(", ", json_decode($r->hardware)) : '',
                'minute'    => $evidence,
            ];
            $i++;
        }

        echo json_encode([
            "data"      => $data,
            "last_page" => ceil($total / $size),
            "last_row"  => $total
        ]);
    }

    public function DataReturns()
    {
        $user = $this->auth->authorize(140);
        header('Content-Type: application/json');

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $size = isset($_GET['size']) ? (int)$_GET['size'] : 15;
        $offset = ($page - 1) * $size;

        $fieldMap = [
            'id'          => 'a.id',
            'date'        => 'a.created_at',
            'notes'       => 'a.notes',
            'hardware'    => 'a.hardware',
            'software'    => 'a.software'
        ];

        $where = " and a.kind = 'return' and asset_id = ". $_REQUEST['id'];

        if (isset($_GET['filter']) && is_array($_GET['filter'])) {
            foreach ($_GET['filter'] as $f) {
                if (!isset($f['field'], $f['value'])) continue;
                $field = $f['field'];
                $value = addslashes($f['value']);
                if (!isset($fieldMap[$field])) continue;
                $dbField = $fieldMap[$field];
                if ($field === 'date' && strpos($value, ' to ') !== false) {
                    list($from, $to) = explode(' to ', $value);
                    $where .= " AND DATE($dbField) BETWEEN '$from' AND '$to'";
                } else {
                    $where .= " AND $dbField LIKE '%$value%'";
                }
            }
        }

        $orderBy = "a.id DESC";
        if (isset($_GET['sort'][0]['field'], $_GET['sort'][0]['dir'])) {
            $sortField = $_GET['sort'][0]['field'];
            $sortDir   = strtoupper($_GET['sort'][0]['dir']) === 'ASC' ? 'ASC' : 'DESC';
            if (isset($fieldMap[$sortField])) {
                $orderBy = $fieldMap[$sortField] . " $sortDir";
            }
        }

        $selectFields = "
            a.id, 
            a.asset_id,
            a.created_at,
            e.name as assignee,
            a.notes,
            a.hardware,
            a.software
        ";

        $joins = "LEFT JOIN employees e on a.employee_id = e.id";

        $total = $this->model->get("COUNT(a.id) AS total", "asset_events a", $where, $joins)->total;

        $rows = $this->model->list(
            $selectFields,
            "asset_events a",
            "$where ORDER BY $orderBy LIMIT $offset, $size",
            $joins
        );

        $data = [];
        foreach ($rows as $r) {

            $evidence = (file_exists("/var/www/html/sigma/uploads/assets/$r->asset_id/return/$r->id.pdf")) ? "<a target='_blank' class='text-blue-500 font-bold' href='https://sigma.es-metals.com/sigma/uploads/assets/$r->asset_id/return/$r->id.pdf'><i class='ri-file-line'></i> Minute </a></div>" : '';

            $data[] = [
                'id'       => $r->id,
                'date'     => $r->created_at,
                'assignee' => $r->assignee,
                'notes'    => $r->notes,
                'software' => $r->software ? implode(", ", json_decode($r->software)) : '',
                'hardware' => ($hw = json_decode($r->hardware, true)) ? implode(", ", array_map(fn($k,$v) => "$k: $v", array_keys($hw), $hw)) : '',
                'minute' => $evidence,
            ];
        }

        echo json_encode([
            "data"      => $data,
            "last_page" => ceil($total / $size),
            "last_row"  => $total
        ]);
    }

public function SaveEvent()
{
    $user = $this->auth->authorize(140);
    $item = new stdClass();
    $itemb = new stdClass();
    $table = 'asset_events';
    
    foreach ($_POST as $k => $val) {
        if (!empty($val)) {
            if ($k != 'event_id') {
                $item->{$k} = $val;
            }
        }
    }
    
    $item->user_id = $_SESSION["id-SIGMA"];
    $item->hardware = json_encode($_REQUEST['hardware'] ?? []);
    $item->software = json_encode($_REQUEST['software'] ?? []);
    $asset_id = $_REQUEST['asset_id'];

    if (($_REQUEST['kind'] === 'return')) {
        $last = $this->model->get('employee_id','asset_events'," and asset_id = $asset_id AND kind = 'assignment' ORDER BY id DESC LIMIT 1");
        if($last) $item->employee_id = $last->employee_id;
    }

    if (!empty($_REQUEST['event_id'])) {
        $id = $_REQUEST['event_id'];
        $this->model->update($table, $item, $id);
    } else {
        $id = $this->model->save($table, $item);
    }

    // --- PROCESAMIENTO DEL ARCHIVO (REEMPLAZO FORZADO) ---
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $type = $_REQUEST['kind'] ?? 'assignment';
        $carpeta = "uploads/assets/$asset_id/$type";
        $newPath = "$carpeta/$id.pdf";
        
        if (!file_exists($carpeta)) mkdir($carpeta, 0777, true);

        // Si el archivo ya existe, lo eliminamos antes de mover el nuevo para asegurar el reemplazo
        if (file_exists($newPath)) unlink($newPath);

        $tmp = $_FILES['file']['tmp_name'];
        if (mime_content_type($tmp) === 'application/pdf') {
            move_uploaded_file($tmp, $newPath);
        }
    }

    if (($_REQUEST['kind'] === 'assignment')) {
        $itemb->status = 'assigned';
        $this->model->update('assets', $itemb, $asset_id);
    }
    if (($_REQUEST['kind'] === 'return')) {
        $itemb->status = 'available';
        $this->model->update('assets', $itemb, $asset_id);
    }

    if (($_REQUEST['kind'] === 'dispose')) {
        $itemb->status = $_REQUEST['wipe'];
        $this->model->update('assets', $itemb, $asset_id);
        $event = new stdClass();
        $event->user_id = $_SESSION["id-SIGMA"];
        $event->kind = 'comment';
        $event->asset_id = $asset_id;
        $event->notes = "<b>Dispose Cause: </b>" . $_REQUEST['wipe'] . "<br><b>Notes: </b> " . $_REQUEST['notes'];
        $this->model->save('asset_events', $event);
    }

    $type_label = ucwords($_POST["kind"] ?? 'Event');
    $message = '{"type": "success", "message": "' . $type_label . ' Saved", "close" : "closeNestedModal"}';
    header('HX-Trigger: ' . json_encode(["eventChanged" => true, "showMessage" => $message]));
    http_response_code(204);
}

public function DeleteEventFile()
{
    $this->auth->authorize(140);
    $asset_id = $_REQUEST['asset_id'];
    $event_id = $_REQUEST['event_id'];
    $type = $_REQUEST['kind'] ?? 'assignment';

    $path = "uploads/assets/$asset_id/$type/$event_id.pdf";

    if (file_exists($path)) {
        unlink($path);
        $message = json_encode(["type" => "success", "message" => "Minute Deleted"]);
    } else {
        $message = json_encode(["type" => "error", "message" => "File not found"]);
    }

    header('HX-Trigger: ' . json_encode(["eventChanged" => true, "showMessage" => $message]));
    
    // IMPORTANTE: Respondemos con el input limpio para que HTMX lo ponga en el DOM
    echo '<label class="block text-sm font-bold text-gray-700 mb-2">Minute (PDF Only):</label>
          <input type="file" name="file" accept="application/pdf" 
          class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-black file:text-white hover:file:bg-gray-800 cursor-pointer shadow-sm">';
}

    public function SaveAutomation()
    {
        $user = $this->auth->authorize(140);

        $item = new stdClass();
        $table = 'mnt_preventive_form';
        foreach ($_POST as $k => $val) {
            if (!empty($val)) {
                if ($k != 'id') {
                    $item->{$k} = $val;
                }
            }
        }
        if (!empty($_REQUEST['event_id'])) {
            $id = $this->model->update($table, $item, $_REQUEST['id']);
        } else {
            $id = $this->model->save($table, $item);

            $message = '{"type": "success", "message": "Automation Saved", "close" : "closeNestedModal"}';
            $hxTriggerData = json_encode([
                "eventChanged" => true,
                "showMessage" => $message,
            ]);
            header('HX-Trigger: ' . $hxTriggerData);
            http_response_code(204);
        }
    }

public function Preventive() {
    $id = $_REQUEST['id'];
    $automations = $this->model->list('*', 'mnt_preventive_form', "and asset_id = $id");

    foreach ($automations as $task) {
        // 1. Normalizamos el texto (Minúsculas para comparar bien)
        $frecuencia_texto = strtolower(trim($task->frequency));

        // 2. Definimos días para la fecha próxima Y semanas para el cronograma
        // Ajustado según los nombres exactos que me pasaste
        [$dias, $intervalo_semanas] = match($frecuencia_texto) {
            'weekly'     => [7, 1],
            'monthly'    => [30, 4],
            'quarterly'  => [90, 13],
            'semiannual' => [180, 26],
            'annual'     => [365, 52],
            'annualx2'   => [730, 104],
            'annualx3'   => [1095, 156],
            'annualx5'   => [1825, 260],
            default      => is_numeric($task->frequency) ? [intval($task->frequency), ceil(intval($task->frequency)/7)] : [30, 4],
        };

        $task->intervalo_semanas = $intervalo_semanas;

        // 3. Cálculo de fechas
        $fecha_last = new DateTime($task->last); // Última realización
        $fecha_next = clone $fecha_last;
        $fecha_next->modify("+$dias days");
        
        // Guardamos la semana de la última realización como punto de partida
        $task->semana_referencia = (int)$fecha_last->format('W');
        $task->fecha_proxima = $fecha_next->format('d/m/Y');
        
        // 4. Lógica de estado y color
        $hoy = new DateTime();
        $task->is_vencido = ($fecha_next < $hoy);
        $task->color = $task->is_vencido ? 'bg-red-500' : 'bg-indigo-600';
    }

    include "app/views/assets/detail/tabs/preventive.php";
}

    public function Upload()
    {
        $user = $this->auth->authorize(140);

        $id = $_REQUEST['id'];
        $carpeta = "uploads/assets/$id/documents";
        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0777, true);
        }
        $total = count($_FILES['files']['name']);
        for ($i = 0; $i < $total; $i++) {
            $tmpFilePath = $_FILES['files']['tmp_name'][$i];
            if ($tmpFilePath != "") {
                $newFilePath = "$carpeta/" . $_FILES['files']['name'][$i];
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    $message = '{"type": "success", "message": "File Uploaded", "close" : ""}';
                    $hxTriggerData = json_encode([
                        "eventChanged" => true,
                        "showMessage" => $message,
                    ]);

                    header('HX-Trigger: ' . $hxTriggerData);
                    http_response_code(204);
                }
            }
        }        
    }

    public function files() {

        // --- Configuración CRÍTICA ---
        // Directorio base donde se encuentran TODAS las carpetas de assets
        $base_dir_assets = '/var/www/html/sigma/uploads/assets/';

        // Bandera de seguridad: Si se establece a true, el script SÓLO mostrará lo que haría sin hacer cambios.
        // Cámbialo a false una vez que estés seguro de que los paths son correctos.
        $dry_run = false;
        // -----------------------------

        echo "--- Iniciando Proceso de Limpieza Masiva (Dry Run: " . ($dry_run ? "SI" : "NO") . ") ---\n\n";


        // Función para eliminar carpetas y su contenido de forma recursiva
        // (Sólo se ejecuta si $dry_run es false)
        function delete_recursive($dir, $dry_run) {
            if (!is_dir($dir)) {
                return false;
            }

            if ($dry_run) {
                return "Simulación: Eliminación recursiva de: " . $dir;
            }

            $files = array_diff(scandir($dir), array('.', '..'));
            foreach ($files as $file) {
                (is_dir("$dir/$file")) ? delete_recursive("$dir/$file", $dry_run) : unlink("$dir/$file");
            }
            return rmdir($dir);
        }
        // -------------------------------------------------------------


        // 1. Iterar sobre los directorios dentro de assets/ (que son los ID_USUARIO)
        $user_dirs = array_diff(scandir($base_dir_assets), array('.', '..'));

        foreach ($user_dirs as $user_id_dir) {
            $current_assignment_path = $base_dir_assets . $user_id_dir . '/return/';

            // Verificar si la carpeta 'assignment' existe para este ID de usuario
            if (!is_dir($current_assignment_path)) {
                continue; // Saltar si no hay carpeta 'assignment'
            }

            echo "## 📂 Procesando ID de Usuario: **" . $user_id_dir . "**\n";
            echo "Directorio de Assignment: " . $current_assignment_path . "\n";
            
            // 2. Iterar sobre las carpetas dentro de 'assignment/' (que son los ID_TAREA)
            $assignment_dirs = array_diff(scandir($current_assignment_path), array('.', '..'));
            
            foreach ($assignment_dirs as $task_id_dir) {
                $source_dir = $current_assignment_path . $task_id_dir . '/';

                // Solo procesar directorios que contienen los archivos
                if (!is_dir($source_dir)) {
                    continue;
                }

                $new_filename = $task_id_dir . '.pdf';
                $target_path = $current_assignment_path . $new_filename;

                echo "\n### ➡️ Analizando Tarea: **" . $task_id_dir . "**\n";
                echo "  - Origen: " . $source_dir . "\n";
                
                // 3. Buscar el archivo PDF dentro de la carpeta
                $pdf_file = null;
                $files = array_diff(scandir($source_dir), array('.', '..'));

                foreach ($files as $file) {
                    $path = $source_dir . $file;
                    // Solo consideramos archivos, no subdirectorios, y que sean PDF
                    if (is_file($path) && pathinfo($file, PATHINFO_EXTENSION) === 'pdf') {
                        $pdf_file = $path;
                        break; // Asume que solo hay un PDF por carpeta de tarea
                    }
                }

                if (!$pdf_file) {
                    echo "  ❌ PDF NO encontrado en esta carpeta. Saltar.\n";
                    continue;
                }

                echo "  - PDF encontrado: " . basename($pdf_file) . "\n";
                echo "  - Destino: " . $target_path . "\n";

                // 4. Mover y Renombrar (utiliza rename() para mover y renombrar en un solo paso)
                if (!$dry_run) {
                    if (rename($pdf_file, $target_path)) {
                        echo "  ✅ PDF MOVIDO y RENOMBRADO exitosamente.\n";
                    } else {
                        echo "  ❌ ERROR: Falló el movimiento/renombre. Verifique permisos.\n";
                        continue;
                    }
                } else {
                    echo "  Simulación: rename(" . $pdf_file . ", " . $target_path . ")\n";
                }


                // 5. Eliminar la carpeta original de forma recursiva
                $delete_result = delete_recursive($source_dir, $dry_run);

                if ($dry_run) {
                    echo "  ⚠️ " . $delete_result . "\n";
                } elseif ($delete_result) {
                    echo "  ✅ Carpeta de origen **ELIMINADA**.\n";
                } else {
                    echo "  ⚠️ ADVERTENCIA: No se pudo eliminar la carpeta: " . $source_dir . "\n";
                }

            } // Fin del bucle $assignment_dirs

            echo "\n" . str_repeat('-', 40) . "\n";

        } // Fin del bucle $user_dirs

        echo "\n--- Proceso de Limpieza Terminado. ---\n";

        if ($dry_run) {
            echo "\n*** 🛑 MODO SIMULACIÓN ACTIVO. Ningún archivo fue movido o borrado. ***\n";
            echo "Para ejecutar los cambios, cambie \$dry_run a `false` al inicio del script.\n";
        }
    }

    public function DeleteDocument()
    {
        // 1. Autorización
        $user = $this->auth->authorize(140); 

        // 2. Validación y Obtención de Datos
        if (empty($_POST['asset_id']) || empty($_POST['filename'])) {
            http_response_code(400); // Bad Request
            echo "Faltan parámetros necesarios (asset_id o filename).";
            exit;
        }

        $assetId = $_POST['asset_id'];
        
        // Usamos basename() para limpiar el nombre y prevenir ataques de directorios (../)
        $filename = basename($_POST['filename']); 

        // 3. Construcción de la Ruta Segura
        // La carpeta de destino es la misma que usaste en Upload()
        $dir = "uploads/assets/{$assetId}/documents/"; 
        $filePath = $dir . $filename;

        // 4. Verificación y Eliminación
        if (is_file($filePath)) {
            if (unlink($filePath)) {
                
                // --- CÓDIGO HTMX PARA ÉXITO ---
                
                // 1. Prepara el mensaje de éxito
                $message = '{"type": "success", "message": "Documento \''.$filename.'\' eliminado correctamente.", "close" : "'. '' . '"}';
                
                // 2. Define los triggers
                $hxTriggerData = json_encode([
                    // Esto podría ser un trigger específico para actualizar la lista de documentos
                    "eventChanged" => true, 
                    "showMessage" => $message
                ]);

                // 3. Envía el header HX-Trigger
                header('HX-Trigger: ' . $hxTriggerData);
                
                // 4. Devuelve 204 No Content. Esto hará que HTMX elimine la fila o elemento target.
                http_response_code(204); 
                exit;
                
            } else {
                // ERROR: Fallo en la eliminación
                http_response_code(500); // Internal Server Error
                echo "Error: No se pudo eliminar el archivo. Verifique permisos del servidor.";
                exit;
            }
        } else {
            // ERROR: Archivo no encontrado.
            http_response_code(404); // Not Found
            echo "Error: Documento no encontrado o acceso denegado.";
            exit;
        }
    }

    public function Maintenances()
    {
        $id = $_REQUEST['id'];
        $user = $this->auth->authorize(140);
        header('Content-Type: application/json');

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $size = isset($_GET['size']) ? (int)$_GET['size'] : 15;
        $offset = ($page - 1) * $size;

        $fieldMap = [
            'id'          => 'a.id',
            'date'        => 'a.created_at',
            'user'        => 'b.username',
            'facility'    => 'a.facility',
            'asset'       => 'c.hostname',
            'priority'    => 'a.priority',
            'status'      => 'a.status',
            'sgc'         => 'a.sgc',
            'cause'       => 'a.root_cause',
            'rating'      => 'a.rating',
        ];

        $where = "and asset_id = $id";

        if (isset($_GET['filter']) && is_array($_GET['filter'])) {
            foreach ($_GET['filter'] as $f) {
                if (!isset($f['field'], $f['value'])) continue;
                $field = $f['field'];
                $value = addslashes($f['value']);
                if (!isset($fieldMap[$field])) continue;
                $dbField = $fieldMap[$field];
                // --- INICIO: Lógica de filtrado con soporte para conteos
                if ($field === 'created_at' && strpos($value, ' to ') !== false) {
                    list($from, $to) = explode(' to ', $value);
                    $where .= " AND DATE($dbField) BETWEEN '$from' AND '$to'";
                } else {
                    $where .= " AND $dbField LIKE '%$value%'";
                }
            }
        }

        // 5. Manejo de Ordenación
        $orderBy = "a.created_at DESC"; // Orden por defecto (fecha de creación descendente)
        if (isset($_GET['sort'][0]['field'], $_GET['sort'][0]['dir'])) {
            $sortField = $_GET['sort'][0]['field'];
            $sortDir   = strtoupper($_GET['sort'][0]['dir']) === 'ASC' ? 'ASC' : 'DESC';
            if (isset($fieldMap[$sortField])) {
                $orderBy = $fieldMap[$sortField] . " $sortDir";
            }
        }

        // 6. Definición de JOINs y SELECT
        $joins = "
            LEFT JOIN users b on a.user_id = b.id 
            LEFT JOIN assets c on a.asset_id = c.id
        ";

        $selectFields = "
            a.*,
            b.username, 
            c.hostname as assetname
        ";

        $total = $this->model->get("COUNT(a.id) AS total", "mnt a", $where, $joins)->total;

        $rows = $this->model->list(
            $selectFields,
            "mnt a",
            "$where ORDER BY $orderBy LIMIT $offset, $size",
            $joins
        );

        $data = [];
        $now = new DateTime(date("Y-m-d H:i:s"));

        foreach ($rows as $r) {
            // --- 8a. Cálculo de Tiempo (Days/Minutes) ---
            // Usar closedAt si existe, sino, usar 'now'.
            $dateClosed = $r->closed_at ? new DateTime($r->closed_at) : $now;
            $dateCreated = new DateTime($r->created_at);
            $interval = $dateCreated->diff($dateClosed);
            $minutes = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;
            $minutes = round($minutes/24/60,0);
            // --- 8b. Obtener Tiempo Consumido (Time) ---
            $timeData = $this->model->get('sum(duration) as time', 'mnt_items', "and mnt_id = $r->id");
            $time = $timeData->duration ?? 0;
            
            $data[] = [
                'id'          => $r->id,
                'type'        => $r->subtype,
                'date'        => $r->created_at,
                'user'        => $r->username,
                'facility'    => $r->facility,
                'asset'       => mb_convert_case($r->assetname, MB_CASE_TITLE, "UTF-8"),
                'priority'    => $r->priority,
                'description' => $r->description,
                'days'        => $minutes,
                'started'     => $r->started_at,
                'closed'      => $r->closed_at,
                'time'        => $time,
                'status'      => $r->status,
                'sgc'         => $r->sgc,
                'cause'       => $r->cause,
                'rating'      => $r->rating,
            ];
        }

        // 9. Respuesta JSON para Tabulator
        echo json_encode([
            "data"      => $data,
            "last_page" => ceil($total / $size),
            "last_row"  => $total
        ]);
    }

    public function UploadPhoto(): void {
        $id = (int)($_POST['id'] ?? 0);
        
        // Verificamos el archivo 'photo' enviado desde el JS
        if ($id && isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
            
            // 1. Definir la ruta según tu estándar actual
            // Estructura: /var/www/html/sigma/uploads/assets/{id}/
            $basePath = "/var/www/html/sigma/uploads/assets/$id/";
            $relativeUrl = "uploads/assets/$id/profile_photo.jpg"; // URL relativa para el navegador
            $fullPath = $basePath . "profile_photo.jpg"; // Ruta física para el servidor
            
            // 2. Crear el directorio del activo si no existe
            if (!is_dir($basePath)) {
                mkdir($basePath, 0777, true);
            }

            // 3. Mover el archivo (sobreescribimos la foto anterior si existe para ahorrar espacio)
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $fullPath)) {
                try {
                    // Suponiendo que tu modelo de activos se maneja así:
                    $this->model->update('assets', (object) ['url' => $relativeUrl], $id);
                    $urlWithCacheBuster = "https://sigma.es-metals.com/sigma/" . $relativeUrl;
                    echo $urlWithCacheBuster;
                } catch (Exception $e) {
                    header("HTTP/1.1 500 Internal Server Error");
                    echo "Error al actualizar base de datos.";
                }
            } else {
                header("HTTP/1.1 500 Internal Server Error");
                echo "Error al mover el archivo a: " . $fullPath;
            }
        } else {
            header("HTTP/1.1 400 Bad Request");
            echo "No se recibió la imagen o ID inválido.";
        }
    }


        public function SaveDocument()
    {
        try {
            // Autorización
            $user = $this->auth->authorize(123);
            header('Content-Type: application/json');

            /* ==========================
            CREAR OBJETO ITEM
            ========================== */
            $item = new stdClass();

            foreach ($_POST as $k => $val) {
                if (!empty($val) && $k !== 'id') {
                    $item->{$k} = htmlspecialchars(trim($val));
                }
            }

            // Datos fijos
            $item->asset_id  = $_REQUEST['id'] ?? null;
            $item->user_id = $_SESSION["id-SIGMA"] ?? null;

            $id = $this->model->save('asset_documents', $item);

            if (!$id) {
                throw new Exception("Error saving maintenance item");
            }

            /* ==========================
            MANEJO DE ARCHIVOS
            ========================== */
            if (!empty($_FILES['files']['name'][0])) {

                $carpeta = "uploads/assets/documents/$id/";
                if (!is_dir($carpeta)) {
                    mkdir($carpeta, 0755, true);
                }

                // Tomamos solo el primer archivo (ya que el campo 'url' es único)
                $tmpFilePath = $_FILES['files']['tmp_name'][0];
                
                if ($tmpFilePath != '') {
                    $fileName = basename($_FILES['files']['name'][0]);
                    $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf', 'jfif'];

                    if (!in_array($fileExt, $allowedTypes)) {
                        throw new Exception("Tipo de archivo no permitido: $fileExt");
                    }

                    // Generamos un nombre único para evitar conflictos
                    $nombreArchivo = uniqid() . '.' . $fileExt;
                    $destino = $carpeta . $nombreArchivo;

                    if (move_uploaded_file($tmpFilePath, $destino)) {
                        
                        // --- ACTUALIZACIÓN EN SQL ---
                        $updateData = new stdClass();
                        $updateData->url = $destino; // Guardamos la ruta relativa
                        
                        // Usamos tu función update pasándole: tabla, objeto con datos, e ID
                        $this->model->update('asset_documents', $updateData, $id);

                    } else {
                        throw new Exception("Error al mover el archivo al servidor.");
                    }
                }
            }

            /* ==========================
            RESPUESTA HTMX
            ========================== */
            $message = '{"type": "success", "message": "Saved", "close": "closeNestedModal"}';

            header('HX-Trigger: ' . json_encode([
                "eventChanged" => true,
                "showMessage" => $message
            ]));

            http_response_code(204);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "type"    => "error",
                "message" => $e->getMessage()
            ]);
        }
    }

    public function DocumentsData()
    {
        // Autorización
        $user = $this->auth->authorize(140); // Ajustado al permiso de empleados
        header('Content-Type: application/json');

        $assetId = (int)($_GET['id'] ?? 0);

        // Traemos los campos necesarios, incluyendo la 'url' que guardamos en SaveDocument
        $rows = $this->model->list(
            'a.id, a.name, a.code, a.expiry, a.url', 
            'asset_documents a',
            "and a.asset_id = $assetId"
        );

        $data = [];

        foreach ($rows as $r) {
            $fileLink = '';
            
            // Si el campo url no está vacío y el archivo existe, creamos el enlace
            if (!empty($r->url) && file_exists($r->url)) {
                $fileLink = "<a href='{$r->url}' target='_blank' class='flex items-center space-x-1 text-blue-600 hover:underline font-medium'>
                                <i class='ri-external-link-line text-sm'></i>
                                <span>Ver Documento</span>
                            </a>";
            } else {
                $fileLink = "<span class='text-gray-400 italic'>Sin archivo</span>";
            }

            $data[] = [
                'name'   => $r->name,
                'code'   => $r->code,
                'expiry' => $r->expiry,
                'file'   => $fileLink // Este campo lo procesa el formatter: "html" de Tabulator
            ];
        }

        echo json_encode([
            'data' => $data
        ]);
    }

    private function groqCall(string $apiKey, string $model, array $messages, int $timeout = 30): array
    {
        $ch = curl_init("https://api.groq.com/openai/v1/chat/completions");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode([
                'model'       => $model,
                'messages'    => $messages,
                'temperature' => 0.1,
                'max_tokens'  => 8000,
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
            ],
            CURLOPT_TIMEOUT => $timeout,
        ]);

        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new Exception("cURL Error: {$curlError}");
        }

        $data = json_decode($response, true);

        if (isset($data['error'])) {
            $errorMsg = $data['error']['message'] ?? 'Unknown Error';
            throw new Exception("GROQ API Error [{$httpCode}]: {$errorMsg}");
        }

        return [
            'content' => $data['choices'][0]['message']['content'] ?? null,
            'usage'   => $data['usage'] ?? []
        ];
    }

    public function AIEngine()
    {
        try {
            $assetId = $_REQUEST['id'];
            $apiKey  = $_ENV['GROQ_API_KEY'] ?? '';
            
            // Modelos recomendados para Groq
            $modelNorm   = 'meta-llama/llama-4-scout-17b-16e-instruct'; 
            $modelExpert = 'openai/gpt-oss-120b';

            // 1. DATA MINING (Extracción de la fuente de verdad)
            $fields  = "a.description as failure, b.notes as task, b.duration as minutes, c.hostname as machine";
            $table   = "mnt a";
            $joins   = "LEFT JOIN mnt_items b ON a.id = b.mnt_id LEFT JOIN assets c ON a.asset_id = c.id";
            $filters = "AND a.asset_id = $assetId ORDER BY a.created_at DESC";

            $rawData = $this->model->list($fields, $table, $filters, $joins);
            if (empty($rawData)) throw new Exception("No se encontraron registros para este activo.");

            $machineName = $rawData[0]->machine ?? "ASSET $assetId";

            // 2. PRE-AGRUPADO EN PHP (Suma de minutos por token único)
            // Esto reduce 500 filas a quizás 20 tokens únicos, ahorrando 95% de tokens.
            $preGrouped = []; 
            foreach ($rawData as $row) {
                $fullText = ($row->task ?? '') . ' ' . ($row->failure ?? '');
                $token = $this->compressText($fullText);
                
                if (mb_strlen($token) < 3) continue;

                if (!isset($preGrouped[$token])) {
                    $preGrouped[$token] = ['qty' => 0, 'minutes' => 0];
                }
                
                // Sumamos la data real aquí
                $preGrouped[$token]['qty']++;
                $preGrouped[$token]['minutes'] += (int)($row->minutes ?? 0);
            }

            if (empty($preGrouped)) throw new Exception("La data disponible es insuficiente para el análisis.");

            // 3. PASO 1: NORMALIZACIÓN TÉCNICA (IA)
            $uniqueTokens = array_keys($preGrouped);
            $corpusString = implode('|', $uniqueTokens);

            $promptNorm = "Eres un normalizador industrial experto en RCM. 
            Recibes una lista de tokens técnicos separados por |. 
            Tu tarea es convertirlos a TÉRMINOS CANÓNICOS profesionales (Máx 3 palabras, MAYÚSCULAS).
            Responde ÚNICAMENTE un objeto JSON donde la llave es el token original y el valor es la traducción técnica.
            CORPUS: $corpusString";

             echo $promptNorm;exit;


            // Llamada a la IA para normalizar nombres
            $normResp = $this->groqCall($apiKey, $modelNorm, [['role' => 'user', 'content' => $promptNorm]], 30);
            $normMap  = json_decode(trim(preg_replace('/```json|```/i', '', $normResp['content'])), true) ?? [];

            // 4. PASO 2: CONSOLIDACIÓN SEMÁNTICA (PHP)
            // Aquí unimos los tokens que la IA decidió que significan lo mismo
            $finalGroups = [];
            $totalMinutesGlobal = 0;
            $totalEvents = 0;

            foreach ($preGrouped as $token => $data) {
                $canonical = $normMap[$token] ?? 'MANTENIMIENTO GENERAL';
                $key = strtolower($canonical);

                if (!isset($finalGroups[$key])) {
                    $finalGroups[$key] = ['term' => $canonical, 'qty' => 0, 'total_min' => 0];
                }
                $finalGroups[$key]['qty'] += $data['qty'];
                $finalGroups[$key]['total_min'] += $data['minutes'];
                
                $totalMinutesGlobal += $data['minutes'];
                $totalEvents += $data['qty'];
            }

            // Ordenamos por frecuencia (Pareto)
            uasort($finalGroups, fn($a, $b) => $b['qty'] <=> $a['qty']);
            $topGroups = array_slice($finalGroups, 0, 8);
            
            $globalMttr = $totalEvents > 0 ? round($totalMinutesGlobal / $totalEvents, 1) : 0;

            // 5. PASO 3: ANÁLISIS EXPERTO (IA de Confiabilidad)
            $historySummary = "";
            foreach ($topGroups as $g) {
                $mttr = round($g['total_min'] / $g['qty'], 1);
                $historySummary .= "- {$g['term']}: {$g['qty']} eventos, Total {$g['total_min']} min, MTTR {$mttr} min.\n";
            }

            $promptExpert = "Eres Consultor de Confiabilidad Senior. Analiza el historial de $machineName:
            $historySummary
            Estadísticas Globales: MTTR $globalMttr min, Total de eventos analizados: $totalEvents.

            Responde ÚNICAMENTE un JSON con este formato:
            {
              \"health_score\": 0,
              \"criticality\": \"ALTA/MEDIA/BAJA\",
              \"summary\": \"Resumen ejecutivo\",
              \"root_cause\": \"Causa raíz probable\",
              \"risk\": \"Riesgo operativo si no se actúa\",
              \"action_plan\": [\"Paso 1\", \"Paso 2\"],
              \"recommended_kpi\": \"KPI para medir mejora\",
              \"verdict\": \"Veredicto final\"
            }";

            echo $promptExpert;exit;

            $expertResp = $this->groqCall($apiKey, $modelExpert, [['role' => 'user', 'content' => $promptExpert]], 60);
            $analysis   = json_decode(trim(preg_replace('/```json|```/i', '', $expertResp['content'])), true);

            // 6. PREPARACIÓN DE DATA PARA VISTA
            $uniqueId = 'sigma_' . uniqid();
            $ai = [
                'metrics' => [
                    'total_records'     => $totalEvents,
                    'global_mttr'       => $globalMttr,
                    'pareto_percentage' => $totalEvents > 0 ? round((reset($topGroups)['qty'] / $totalEvents) * 100, 1) : 0,
                    'health_score'      => $analysis['health_score'] ?? 50,
                    'criticality'       => $analysis['criticality'] ?? 'INDETERMINADO'
                ],
                'analysis' => $analysis,
                'groups'   => $topGroups
            ];

            $chartData = [
                'labels' => json_encode(array_column($topGroups, 'term')),
                'qty'    => json_encode(array_column($topGroups, 'qty')),
                'mttr'   => json_encode(array_map(fn($g) => round($g['total_min'] / $g['qty'], 1), $topGroups)),
                'accent' => $this->getAccentColor($ai['metrics']['criticality'])
            ];

            include 'views/reports/sigma_god_view.php';

        } catch (Exception $e) {
            $this->renderError($e->getMessage());
        }
    }

    private function compressText(string $text): string
    {
        $t = mb_strtolower($text);
        $t = str_replace(['á','é','í','ó','ú','ü','ñ','à','è','ì','ò','ù'], ['a','e','i','o','u','u','n','a','e','i','o','u'], $t);
        $t = preg_replace('/[^a-z0-9\s]/', ' ', $t);

        $map = [
            'compresor'=>'cmp', 'boquilla'=>'bq', 'nitrogeno'=>'n2', 'oxigeno'=>'o2',
            'lente'=>'ln', 'capacitancia'=>'cap', 'chiller'=>'chl', 'sensor'=>'snr',
            'quemado'=>'qmd', 'contaminado'=>'ctm', 'temperatura'=>'tmp',
            'limpieza'=>'limp', 'limpiar'=>'limp', 'lavar'=>'limp',
            'cambio'=>'cmb', 'cambiar'=>'cmb', 'reemplazo'=>'cmb', 'sustitucion'=>'cmb',
            'ajuste'=>'ajt', 'ajustar'=>'ajt', 'calibracion'=>'cal', 'calibrar'=>'cal'
        ];
        $t = str_ireplace(array_keys($map), array_values($map), $t);

        $noise = [
            'de','el','la','los','las','en','que','por','para','con','se','un','una','al','del','es','no','y','o','su','lo','le','les','me','mi','nos','si','ya','ha','he','tu','mas','pero','como','esta','este','esto',
            'favor','gracias','hola','saludos','ticket','attended','set','buenos','buenas','dias','tardes','noches',
            'tengo','falla','error','revisar','revisando','reporta','presenta','indica','comenta','realiza','procede'
        ];

        $words = explode(' ', $t);
        $filtered = array_filter($words, fn($w) => strlen($w) >= 2 && !in_array($w, $noise));

        $unique = array_unique($filtered);
        sort($unique); // Fundamental para el agrupamiento determinístico

        return trim(preg_replace('/\s+/', ' ', implode(' ', $unique)));
    }

    private function renderError($msg) {
        echo '<div style="max-width:600px;margin:32px auto;padding:24px;background:#0f1825;border:1px solid rgba(239,68,68,0.3);border-radius:12px;font-family:monospace;">
            <div style="color:#ef4444;font-size:10px;font-weight:700;letter-spacing:.15em;text-transform:uppercase;margin-bottom:8px;">⚠ Error — Sigma Engine</div>
            <div style="color:#fca5a5;font-size:13px;">' . htmlspecialchars($msg) . '</div>
        </div>';
    }

    private function getAccentColor($level) {
        if (str_contains($level, 'CRÍTICO')) return '#ef4444';
        if (str_contains($level, 'PRECAUCIÓN')) return '#f59e0b';
        return '#10b981';
    }

    public function Delete()
    {
        try {
            header('Content-Type: application/json');
            $id = $_REQUEST['id'];           
            $item = new stdClass();
            $item->deleted_at = date("Y-m-d H:i:s");
            $id = $this->model->update('assets', $item, $id);

            if ($id === false) {
                http_response_code(500);
                echo json_encode([
                    "type" => "error",
                    "message" => "Error saving appointment"
                ]);
                return;
            }

            $message = '{"type": "success", "message": "Deleted", "close": "closeNewModal"}';
            $hxTriggerData = json_encode([
                "eventChanged" => true,
                "showMessage"  => $message
            ]);
            header('HX-Trigger: ' . $hxTriggerData);
            http_response_code(204);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "type" => "error",
                "message" => "Internal Server Error: " . $e->getMessage()
            ]);
        }
    }

    public function MigrateExistingDocuments()
{
    $this->auth->authorize(140);
    $table = 'asset_documents';
    $pathPattern = "uploads/assets/*/documents/*.*";
    $files = glob($pathPattern);

    if (!$files) {
        return json_encode(["status" => "info", "message" => "No hay archivos para procesar"]);
    }

    $results = ['success' => 0, 'skipped' => 0, 'errors' => []];

    foreach ($files as $filePath) {
        $parts = explode('/', $filePath);
        $assetId = isset($parts[2]) ? (int)$parts[2] : null;
        if (!$assetId) continue;

        // Evitar duplicados por URL exacta
        if ($this->model->get('id', $table, " AND url = '$filePath'")) {
            $results['skipped']++;
            continue;
        }

        $fileNameWithExt = basename($filePath);
        $fileInfo = $this->cleanFileData($fileNameWithExt);

        $doc = new stdClass();
        $doc->name     = $fileInfo->name;   // Nombre sin extensión y SIN el código
        $doc->url      = $filePath;         // Ruta original intacta (IMPORTANTE)
        $doc->asset_id = $assetId;
        $doc->user_id  = $_SESSION["id-SIGMA"] ?? 1;
        $doc->code     = $fileInfo->code;   // El código ISO extraído
        $doc->expiry   = null;

        try {
            if ($this->model->save($table, $doc)) {
                $results['success']++;
            }
        } catch (Exception $e) {
            $results['errors'][] = "Error en {$fileNameWithExt}: " . $e->getMessage();
        }
    }

    header('Content-Type: application/json');
    return json_encode($results);
}

/**
 * Extrae el código, lo quita del nombre y elimina la extensión
 */
private function cleanFileData(string $filename): object 
{
    $res = new stdClass();
    
    // 1. Quitar la extensión del nombre base
    $baseName = pathinfo($filename, PATHINFO_FILENAME);
    
    // 2. Buscar el código al inicio (F02-PRTI-02)
    if (preg_match('/^F\d{2}-PRTI-\d{2}/i', $baseName, $matches)) {
        $res->code = strtoupper($matches[0]);
        // Quitar el código del nombre
        $nameWithoutCode = str_replace($matches[0], '', $baseName);
        // Limpiar espacios, guiones o puntos que queden al inicio/final
        $res->name = trim($nameWithoutCode, " -_."); 
    } else {
        $res->code = '';
        $res->name = trim($baseName);
    }

    return $res;
}


    

}
