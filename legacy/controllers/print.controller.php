<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PrintController
{
    public function __construct(public Model $model, private AuthService $auth) {}

    public function Index()
    {
        $user = $this->auth->authorize(103);
        $tabulator = true;
        $jspreadsheet = true;
        $button = 'New Label';
        $content = 'app/components/list.php';
        $title = 'Print WO';
        $columns = '[
            { "title": "ID", "field": "id", "headerHozAlign": "center", "headerFilter": "input" },
            { "title": "Date", "field": "date", headerHozAlign: "center", headerFilter: customDateRangeFilter, headerFilterFunc: customDateFilterFunc, headerFilterLiveFilter: false },
            { "title": "Project", "field": "project", "headerHozAlign": "center", "headerFilter": "input" },
            { "title": "User", "field": "user", "headerHozAlign": "center", "headerFilter": "input" },
            { "title": "ES ID", "field": "es", "headerHozAlign": "center", "headerFilter": "input" },
        ]';
        require_once 'app/views/index.php';
    }

    public function Data()
    {
        $user = $this->auth->authorize(103);
        header('Content-Type: application/json');

        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $size = isset($_GET['size']) ? (int) $_GET['size'] : 15;
        $offset = ($page - 1) * $size;

        // ⚠️ CAMBIO: id => a.code (el "id" que ve el usuario es el code)
        $fieldMap = [
            'id' => 'a.code',
            'project' => 'a.project',
            'user' => 'b.username',
            'date' => 'a.created_at',
            'es' => 'a.es_id',
        ];

        $where = '';

        if (isset($_GET['filter']) && is_array($_GET['filter'])) {
            foreach ($_GET['filter'] as $f) {
                if (! isset($f['field'], $f['value'])) {
                    continue;
                }
                $field = $f['field'];
                $value = addslashes($f['value']);

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

        $orderBy = 'a.created_at DESC';
        if (isset($_GET['sort'][0]['field'], $_GET['sort'][0]['dir'])) {
            $sortField = $_GET['sort'][0]['field'];
            $sortDir = strtoupper($_GET['sort'][0]['dir']) === 'ASC' ? 'ASC' : 'DESC';
            if (isset($fieldMap[$sortField])) {
                $orderBy = $fieldMap[$sortField]." $sortDir";
            }
        }

        $joins = 'LEFT JOIN users b ON a.user_id = b.id';
        $selectFields = 'a.*, b.username';

        $total = $this->model->get('COUNT(a.id) AS total', 'wo a', $where, $joins)->total;

        $rows = $this->model->list(
            $selectFields,
            'wo a',
            "$where ORDER BY $orderBy LIMIT $offset, $size",
            $joins
        );

        $data = [];
        foreach ($rows as $r) {
            $data[] = [
                'id' => $r->code,   // ⚠️ CAMBIO: mostramos code como "id" al frontend
                'project' => $r->project,
                'user' => $r->username,
                'date' => $r->created_at,
                'es' => $r->es_id,
            ];
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
        $user = $this->auth->authorize(103);
        require_once 'app/views/print/new.php';
    }

    public function Save()
    {
        $user = $this->auth->authorize(103);
        header('Content-Type: application/json');

        $wo = new stdClass;
        $error = false;
        $message = '';

        if (! empty($_FILES['excel_file']['name'])) {
            $allowed_extension = ['xls', 'xlsx'];
            $file_array = explode('.', $_FILES['excel_file']['name']);
            $file_extension = strtolower(end($file_array));

            if (in_array($file_extension, $allowed_extension)) {
                $reader = IOFactory::createReader('Xlsx');
                $reader->setReadDataOnly(true);
                $reader->setReadEmptyCells(false);

                $spreadsheet = $reader->load($_FILES['excel_file']['tmp_name']);
                $excelSheet = $spreadsheet->getActiveSheet();
                $spreadSheetAry = $excelSheet->toArray();
                $sheetCount = count($spreadSheetAry);

                $wo_code = trim($spreadSheetAry[1][0]);

                // ⚠️ CAMBIO: buscar por code en lugar de id
                $existing = $this->model->get('id', 'wo', "AND code = '".addslashes($wo_code)."'");
                if (isset($existing->id)) {
                    $message = '{"type": "error", "message": "WO is duplicated", "close" : ""}';
                    header('HX-Trigger: '.json_encode(['showMessage' => $message]));
                    http_response_code(400);
                    exit;

                    return;
                }

                // ⚠️ CAMBIO: guardar en ->code en lugar de ->id
                $wo->code = $wo_code;
                $wo->es_id = $_REQUEST['esId'];
                $wo->project = trim($spreadSheetAry[1][4]);
                $wo->user_id = $user->id;

                $save_id = $this->model->save('wo', $wo);

                if ($save_id !== false) {
                    for ($i = 1; $i < $sheetCount; $i++) {
                        if (! empty($spreadSheetAry[$i][1])) {
                            $item = new stdClass;
                            $item->wo_code = addslashes($wo_code);              // ⚠️ CAMBIO: wo_code
                            $item->code = addslashes(trim($spreadSheetAry[$i][1])); // ⚠️ CAMBIO: code
                            $item->description = addslashes(trim($spreadSheetAry[$i][5]));
                            $item->fuc = addslashes(trim($spreadSheetAry[$i][8]));
                            $item->qty = addslashes(trim($spreadSheetAry[$i][9]));

                            $this->model->save('wo_items', $item);
                        }
                    }

                    // ⚠️ CAMBIO: carpeta usa $wo_code
                    $carpeta = "uploads/print/$wo_code";
                    if (! file_exists($carpeta)) {
                        mkdir($carpeta, 0777, true);
                    }

                    if (! empty($_FILES['files']['name'])) {
                        $tmpFilePath = $_FILES['files']['tmp_name'][0];
                        if ($tmpFilePath != '') {
                            move_uploaded_file($tmpFilePath, "$carpeta/qr.png");
                        }
                    }

                    $msg_content = '{"type": "success", "message": "Saved", "close" : "closeNewModal"}';
                    $hxTriggerData = json_encode([
                        'eventChanged' => true,
                        'showMessage' => $msg_content,
                    ]);

                    header('HX-Trigger: '.$hxTriggerData);
                    http_response_code(204);

                    return;
                }
            }
        }

        http_response_code(400);
        echo json_encode(['message' => 'Error processing request']);
    }

    public function Detail()
    {
        $code = $_REQUEST['id']; // El frontend sigue mandando 'id', internamente es el code
        // ⚠️ CAMBIO: buscar por code
        $wo = $this->model->get('*', 'wo', "and code = '$code'");
        $items = $this->model->list('*', 'wo_items', "and wo_code = '$code'"); // ⚠️ CAMBIO: wo_code
        require_once 'app/views/print/detail.php';
    }

    public function ESM()
    {
        $array = $_REQUEST['id'] ?? [];
        $val = $_REQUEST['val'] ?? [];
        $woId = $_REQUEST['woId'] ?? null; // woId sigue siendo el code del WO

        if (! $woId || empty($array) || empty($val)) {
            return;
        }

        echo '<header>
        <link rel="icon" sizes="192x192" href="app/assets/img/logoES.png">
        <title>Sigma | ES-Metals</title>
        <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
        </header>
        <style>
            * { box-sizing: border-box; }
            body { margin: 0; font-family: Arial, sans-serif; }

            .col-12 { width: 100%; display: block; overflow: hidden; }
            .float-right { float: right !important; }
            .m-1 { margin: 4px !important; }
            
            .btn-dark { 
                color: #fff; 
                background-color: #343a40; 
                border: none;
                padding: 8px 12px;
                border-radius: 4px;
                cursor: pointer;
                font-size: 16px;
            }

            @media print {    
                .noprint { display: none !important; }
                .boarding-pass { 
                    display: block !important; 
                    box-shadow: none !important; 
                    border: 1px solid transparent !important; 
                    -webkit-print-color-adjust: exact;
                }
            }

            small {font-weight:bold}
            .boarding-pass {
                position: relative;
                width: 350px;
                height: auto;
                background: #fff;
                box-shadow: 0 5px 30px rgba(0, 0, 0, 0.2);
                overflow: hidden;
                text-transform: uppercase;
            }
            .boarding-pass small {
                display: block;
                font-size: 11px;
                color: #000;
                margin-bottom: 2px;
            }
            .boarding-pass strong {
                font-size: 16px;
                display: block;
            }
            .boarding-pass header {
                background: #FFF;
                padding: 12px 20px;
                height: 53px;
            }
            .boarding-pass header .logo {
                float: left;
                width: 130px;
                height: 31px;
            }
            .boarding-pass header .flight {
                float: right;
                color: #000;
                text-align: right;
            }
            .boarding-pass header .flight small {
                font-size: 8px;
                margin-bottom: 2px;
            }
            .boarding-pass header .flight strong {
                font-size: 18px;
            }
            .boarding-pass .infos {
                display: flex;
                border-top: 1px solid #000;
            }
            .boarding-pass .infos .places,
            .boarding-pass .infos .times {
                width: 50%;
                padding: 10px 0;
            }
            .boarding-pass .infos .places {
                border-right: 1px solid #000;
            }
            .boarding-pass .infos .box {
                padding: 10px 20px 10px;
                width: 100%;
            }
            .boarding-pass .infos .box small {
                font-size: 12px;
            }
            .boarding-pass .strap {
                position: relative;
                border-top: 1px solid #000;
            }
            .boarding-pass .strap .box {
                padding: 23px 0 20px 20px;
            }
            .boarding-pass .strap .box div {
                margin-bottom: 15px;
            }
            .boarding-pass .strap .box div small {
                font-size: 12px;
            }
            .boarding-pass .strap .box div strong {
                font-size: 13px;
            }
            .boarding-pass .strap .qrcode {
                position: absolute;
                top: 20px;
                right: 35px;
                width: 80px;
                height: 80px;
            }

            @page { size: 234px 290px; margin: 0; }

            @media print {
                .boarding-pass { 
                    page-break-before: avoid;
                    page-break-after: avoid;
                    page-break-inside: avoid;
                    margin-bottom:100px;
                }
            }
        </style>
        <div class="col-12">
            <button type="button" class="btn-dark float-right noprint m-1" onclick="window.print();">
                <i class="ri-printer-line"></i>
            </button>
        </div>
        <center class="noprint">
            <b>Inspector:</b> <input id="inspectorName" style="margin:10px">
        </center>

        <script>
        document.addEventListener("input", (e) => {
            if (e.target.id === "inspectorName") {
                document.querySelectorAll(".inspectorTicket").forEach(el => {
                    el.textContent = e.target.value;
                });
            }
        });
        </script>';

        // ⚠️ CAMBIO: buscar por code
        $wo = $this->model->get('*', 'wo', "and code = '$woId'");
        $itemsCache = [];
        $count = count($array);

        for ($i = 0; $i < $count; $i++) {
            $itemCode = $array[$i]; // ⚠️ Es el code del item

            if (! isset($itemsCache[$itemCode])) {
                // ⚠️ CAMBIO: buscar por code y wo_code
                $itemsCache[$itemCode] = $this->model->get('*', 'wo_items', "and code = '$itemCode' and wo_code = '$woId'");
            }

            $id = $itemsCache[$itemCode];
            if (! $id) {
                continue;
            }

            for ($j = 0; $j < $val[$i]; $j++) {
                ?>
                <div class="boarding-pass">
                    <header>
                        <div class="logo">
                            <img style='width:35px' src='app/assets/img/logobw.png'> 
                            <div style="font-size:10px; display:inline-block;vertical-align:middle;font-weight:bold;color:black">F03-PROP-02 V01 <br> 12/07/23 </div>
                        </div>
                        <div class="flight">
                            <small>Part Number</small>
                            <strong><?php echo $id->code ?></strong>  <!-- ⚠️ CAMBIO: ->code -->
                        </div>
                    </header>

                    <section class="infos">
                        <div class="places">
                            <div class="box">
                                <small>Project</small>
                                <strong><em><?php echo $wo->project ?></em></strong>
                            </div>
                            <div class="box">
                                <small>Order</small>
                                <strong><?php echo $wo->code ?></strong>  <!-- ⚠️ CAMBIO: ->code -->
                            </div>
                        </div>
                        <div class="times">
                            <div class="box">
                                <small>Description</small>
                                <strong><em><?php echo $id->description ?></em></strong>
                            </div>
                            <div class="box">
                                <small>Finish & UC</small>
                                <strong><em><?php echo $id->fuc ?></em></strong>
                            </div>
                        </div>
                    </section>
                    
                    <section class="strap">
                        <div class="box">
                            <div class="passenger">
                                <small>INSPECTOR</small>
                                <strong class="inspectorTicket">__________________________</strong>
                            </div>
                            <div class="date">
                                <small>Date</small>
                                <strong><?php echo date('Y-m-d') ?></strong>
                            </div>
                        </div>
                        <div class="qrcode">
                            <img src='uploads/print/<?php echo $wo->code ?>/qr.png' alt='QR Code' width='110' height='110' style="margin:0;padding:0;display:inline">  <!-- ⚠️ CAMBIO: ->code -->
                        </div>
                    </section>
                </div>
                <?php
            }
        }
    }

    public function ESData($id)
    {
        $params = [
            'text' => '',
            'idOrden' => $id,
            'mcdId' => 1,
            'token' => getenv('ES_TOKEN'),
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://portalnova.eswllc.net/sv/api/fussion/OrderDetailESMetals');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Curl error: '.curl_error($ch);
        }

        $data = json_decode($response, true);

        if ($data !== null && isset($data['data'])) {
            return $data['data'];
        } else {
            echo 'Error decoding JSON data or "data" field not found.';
        }
    }

    public function ES()
    {
        ob_start();

        try {
            $idArr = $_REQUEST['id'] ?? [];    // son los codes de items
            $marcaArr = $_REQUEST['marca'] ?? [];
            $esid = $_REQUEST['esid'] ?? [];
            $woId = $_REQUEST['woId'] ?? '';  // es el code del WO

            if (empty($idArr) || empty($marcaArr) || empty($esid) || empty($woId)) {
                throw new Exception('Faltan parámetros requeridos.');
            }

            // ⚠️ CAMBIO: buscar WO por code
            $wo = $this->model->get('*', 'wo', "and code = '$woId'");
            if (! $wo) {
                throw new Exception('No se encontró la orden de trabajo.');
            }

            // ⚠️ CAMBIO: buscar items por code y wo_code
            $itemCodes = implode("','", array_map('addslashes', $idArr));
            $items = $this->model->list('*', 'wo_items', "and code IN ('$itemCodes') and wo_code = '$woId'");

            if (empty($items)) {
                throw new Exception('No se encontraron items para esta orden.');
            }

            $itemsById = [];
            foreach ($items as $item) {
                $itemsById[$item->code] = $item; // ⚠️ CAMBIO: indexar por ->code
            }

            $esData = [];
            $uniqueEsIds = array_unique($esid);

            foreach ($uniqueEsIds as $esId) {
                $data = $this->ESData($esId);
                if (! empty($data)) {
                    $esData[$esId] = $data;
                } else {
                    error_log("No se pudieron obtener datos ES para ID: $esId");
                    $esData[$esId] = [];
                }
            }

            $consecutivo = [];
            $orden = [];
            $marcasSinDatos = [];

            for ($i = 0; $i < count($idArr); $i++) {
                $marca = $marcaArr[$i];
                $currentEsId = $esid[$i];

                if (! isset($esData[$currentEsId]) || empty($esData[$currentEsId])) {
                    if (! isset($consecutivo[$marca])) {
                        $consecutivo[$marca] = ['NO_DATA'];
                        $orden[$marca] = ['SIN DATOS ES'];
                        $marcasSinDatos[] = $marca;
                    }

                    continue;
                }

                $encontrado = false;
                foreach ($esData[$currentEsId] as $item) {
                    if (isset($item['marca']) && $item['marca'] === $marca) {
                        $encontrado = true;
                        if (isset($item['consecutivo'])) {
                            $consecutivo[$marca][] = $item['consecutivo'];
                        }
                        if (isset($item['nombreorden'])) {
                            $orden[$marca][] = $item['nombreorden'];
                        }
                    }
                }

                if (! $encontrado) {
                    if (! isset($consecutivo[$marca])) {
                        $consecutivo[$marca] = ['NO_DATA'];
                        $orden[$marca] = ['SIN DATOS ES'];
                        $marcasSinDatos[] = $marca;
                    }
                }
            }

            // ⚠️ CAMBIO: ruta usa ->code
            $woQrPath = "uploads/print/{$wo->code}/qr.png";
            if (! file_exists($woQrPath)) {
                error_log("QR del WO no encontrado: $woQrPath");
                $woQrPath = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';
            }

            $qrOptions = new QROptions([
                'eccLevel' => QRCode::ECC_L,
                'outputType' => QRCode::OUTPUT_MARKUP_SVG,
                'version' => 5,
            ]);
            $qrGenerator = new QRCode($qrOptions);

            $qrCodes = [];
            foreach ($consecutivo as $marcaConsecutivos) {
                foreach ($marcaConsecutivos as $r) {
                    if (! isset($qrCodes[$r])) {
                        if ($r === 'NO_DATA') {
                            $qrCodes[$r] = '';
                        } else {
                            try {
                                $qrCodes[$r] = $qrGenerator->render("$r");
                            } catch (Exception $e) {
                                error_log("Error generando QR para: $r - ".$e->getMessage());
                                $qrCodes[$r] = '';
                            }
                        }
                    }
                }
            }

            ob_end_clean();
            ob_start();

        } catch (Exception $e) {
            ob_end_clean();
            error_log('Error en ES(): '.$e->getMessage());

            echo '<!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Error - Sigma ES</title>
                <style>
                    body { font-family: Arial, sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; background: #f5f5f5; }
                    .error-box { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 30px rgba(0,0,0,0.1); max-width: 500px; text-align: center; }
                    .error-icon { font-size: 48px; color: #e74c3c; margin-bottom: 20px; }
                    h1 { color: #e74c3c; margin: 0 0 10px 0; }
                    p { color: #666; line-height: 1.6; }
                </style>
            </head>
            <body>
                <div class="error-box">
                    <div class="error-icon">⚠️</div>
                    <h1>Error al generar etiquetas</h1>
                    <p>'.htmlspecialchars($e->getMessage()).'</p>
                    <p style="font-size: 12px; color: #999; margin-top: 20px;">Cierre esta ventana y verifique los datos</p>
                </div>
            </body>
            </html>';

            return;
        }
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <link rel="icon" sizes="192x192" href="app/assets/img/logoES.png">
            <title>Sigma | ES</title>
            <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
            <style>
                * { box-sizing: border-box; }
                body { margin: 0; font-family: Arial, sans-serif; }
                .col-12 { width: 100%; display: block; overflow: hidden; }
                .float-right { float: right !important; }
                .m-1 { margin: 4px !important; }
                .btn-dark { color: #fff; background-color: #343a40; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer; font-size: 16px; }
                @media print {    
                    .noprint { display: none !important; }
                    .boarding-pass { display: block !important; box-shadow: none !important; border: 1px solid transparent !important; -webkit-print-color-adjust: exact; }
                    .warning-badge { display: block !important; }
                }
                small {font-weight:bold}
                .boarding-pass { position: relative; width: 350px; height: auto; background: #fff; box-shadow: 0 5px 30px rgba(0, 0, 0, 0.2); overflow: hidden; text-transform: uppercase; }
                .boarding-pass small { display: block; font-size: 14px; color: #000; margin-bottom: 2px; }
                .boarding-pass strong { font-size: 15px; display: block; }
                .boarding-pass header { background: #FFF; padding: 5px; height: 53px; }
                .boarding-pass header .logo { float: left; width: 130px; height: 31px; }
                .boarding-pass header .flight { float: right; color: #000; text-align: right; }
                .boarding-pass header .flight small { font-size: 8px; margin-bottom: 2px; }
                .boarding-pass header .flight strong { font-size: 18px; }
                .boarding-pass .infos { display: flex; border-top: 1px solid #000; }
                .boarding-pass .infos .places, .boarding-pass .infos .times { width: 50%; padding: 5px 0; }
                .boarding-pass .infos .places { border-right: 1px solid #000; }
                .boarding-pass .infos .box { padding: 5px; width: 47%; float: left; }
                .boarding-pass .infos .box small { font-size: 10px; }
                .warning-badge { background: #ff9800; color: white; padding: 3px 8px; border-radius: 3px; font-size: 10px; font-weight: bold; display: inline-block; margin-top: 5px; }
                .no-data-placeholder { background: #fff3cd; border: 2px dashed #856404; padding: 10px; text-align: center; color: #856404; font-weight: bold; }
                @page { size: 234px 290px; margin: 0; }
                @media print {
                    .boarding-pass { page-break-before: avoid; page-break-after: avoid; page-break-inside: avoid; margin-bottom:100px; }
                }
            </style>
        </head>
        <body>

        <div class="col-12">
            <button type="button" class="btn-dark float-right noprint m-1" onclick="window.print();">
                <i class="ri-printer-line"></i> Imprimir
            </button>
        </div>
        <center class="noprint">
            <b>Inspector:</b> <input id="inspectorName" style="margin:10px">
        </center>

        <?php if (! empty($marcasSinDatos)) { ?>
        <div class="noprint" style="background:#fff3cd;border:1px solid #856404;color:#856404;padding:15px;margin:10px;border-radius:5px;">
            <b><i class="ri-alert-line"></i> Advertencia:</b> Las siguientes marcas no tienen datos ES: 
            <b><?= implode(', ', array_unique($marcasSinDatos)) ?></b>
        </div>
        <?php } ?>

        <script>
        document.addEventListener("input", (e) => {
            if (e.target.id === "inspectorName") {
                document.querySelectorAll(".inspectorTicket").forEach(el => {
                    el.textContent = e.target.value;
                });
            }
        });
        </script>

        <?php
        $ticketsGenerados = 0;

        for ($i = 0; $i < count($idArr); $i++) {
            $marca = $marcaArr[$i];
            $itemCode = $idArr[$i]; // ⚠️ Es el code del item

            if (! isset($itemsById[$itemCode])) {
                error_log("Item no encontrado: $itemCode");

                continue;
            }

            $id = $itemsById[$itemCode];

            if (! isset($consecutivo[$marca]) || empty($consecutivo[$marca])) {
                continue;
            }

            foreach ($consecutivo[$marca] as $idx => $r) {
                $esConDatos = ($r !== 'NO_DATA');
                $qrcodeES = $qrCodes[$r] ?? '';
                $ordenNombre = $orden[$marca][$idx] ?? '';

                $project = isset($wo->project) ? htmlspecialchars($wo->project) : 'N/A';
                $description = isset($id->description) ? htmlspecialchars($id->description) : 'N/A';
                $fuc = isset($id->fuc) ? htmlspecialchars($id->fuc) : 'N/A';
                ?>
                <div class="boarding-pass">
                <header>
                    <div class="logo">
                    <img style='width:35px' src='app/assets/img/logobw.png' onerror="this.style.display='none'"> 
                    <div style="font-size:10px; display:inline-block;vertical-align:middle;font-weight:bold;color:black">F03-PROP-02 V01 <br> 12/07/23 </div>
                    </div>
                    <div class="flight">
                    <small>Part Number</small>
                    <strong><?= $id->code ?></strong>  <!-- ⚠️ CAMBIO: ->code -->
                    <?php if (! $esConDatos) { ?>
                    <span class="warning-badge">SIN DATOS ES</span>
                    <?php } ?>
                    </div>
                </header>

                <section class="infos">
                    <div class="places">
                    <div class="box" style="width:100%">
                        <small>Project</small>
                        <strong><em><?= $project ?></em></strong>
                    </div>
                    <div class="box" style="width:100%">
                        <small>Order</small>
                        <strong><?= $wo->code ?></strong>  <!-- ⚠️ CAMBIO: ->code -->
                    </div>
                    </div>
                    <div class="times">
                    <div class="box" style="width:100%">
                        <small>Description</small>
                        <strong><em><?= $description ?></em></strong>
                    </div>
                    <div class="box" style="width:100%">
                        <small>Finish & UC</small>
                        <strong><em><?= $fuc ?></em></strong>
                    </div>
                    <div class="box" style="width:100%">
                        <small><?= htmlspecialchars($ordenNombre) ?></small>
                    </div>
                    </div>
                </section>

                <section class="infos">
                    <div class="places" style="padding:5px">
                    <center>
                    <small>INSPECTOR</small>
                    <strong class="inspectorTicket" style="font-size:12px">__________________</strong>
                    <small>Date: <?= date('Y-m-d') ?></small>
                    <img src='<?= $woQrPath ?>' alt='QR Code' width='100' height='100' style="margin:0;padding:0;display:inline" onerror="this.style.display='none'">
                    </center>
                    </div>
                    <div class="times" style="padding:40px 0 0 0">
                    <center>
                        <?php if ($esConDatos) { ?>
                            <small>ES: <b><?= htmlspecialchars($r) ?></b></small>
                            <?php if (! empty($qrcodeES)) { ?>
                            <img src='<?= $qrcodeES ?>' alt='QR Code' width='100' height='100' style="margin:0;padding:0;display:inline">
                            <?php } ?>
                        <?php } else { ?>
                            <div class="no-data-placeholder">
                                <small>MARCA: <b><?= htmlspecialchars($marca) ?></b></small><br>
                                <small style="font-size:9px;">SIN CONSECUTIVO ES</small>
                            </div>
                        <?php } ?>
                    </center>
                    </div>
                </section>
                </div>
                <?php
                $ticketsGenerados++;
            }
        }

        if ($ticketsGenerados === 0) {
            echo '<div style="padding:20px;color:red;text-align:center;" class="noprint">
                    <b>Error:</b> No se pudieron generar tickets.
                  </div>';
        } else {
            echo '<div style="padding:10px;color:green;text-align:center;" class="noprint">
                    <b><i class="ri-checkbox-circle-line"></i> '.$ticketsGenerados.' etiqueta(s) generada(s)</b>
                  </div>';
        }
        ?>

        </body>
        </html>
        <?php
        ob_end_flush();
    }

    public function Delete()
    {
        $code = $_REQUEST['id']; // El frontend manda 'id', internamente es el code

        // ⚠️ CAMBIO: delete por code
        $this->model->delete('wo_items', " wo_code = '$code'");
        $this->model->delete('wo', " code = '$code'");
        unlink("uploads/print/$code/qr.png");

        $msg_content = '{"type": "success", "message": "Delete", "close" : "closeNewModal"}';
        $hxTriggerData = json_encode([
            'eventChanged' => true,
            'showMessage' => $msg_content,
        ]);

        header('HX-Trigger: '.$hxTriggerData);
        http_response_code(204);

    }
}
