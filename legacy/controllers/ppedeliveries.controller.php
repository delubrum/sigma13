<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

class PPEDeliveriesController
{
    public function __construct(public Model $model, private AuthService $auth) {}

    public function Index()
    {
        $user = $this->auth->authorize(128);
        $tabulator = true;
        $jspreadsheet = true;
        $title = 'OHS / PPE / Deliveries';
        $button = 'New Delivery';
        $content = 'app/components/list.php';
        $columns = '[
            { title: "ID", field: "id", headerHozAlign: "center", headerFilter: "input", width: 70 },
            { title: "Fecha", field: "date", headerHozAlign: "center", headerFilter: "input" },
            { title: "Nombre", field: "name", headerHozAlign: "center", headerFilter: "input" },
            { title: "Tipo", field: "type", headerHozAlign: "center", headerFilter: "input" },
            { title: "Empleado", field: "employee", headerHozAlign: "center", headerFilter: "input" },
            { title: "Área", field: "area", headerHozAlign: "center", headerFilter: "input" },
            { title: "Usuario", field: "user", headerHozAlign: "center", headerFilter: "input" },
            { title: "Notas", field: "notes", headerHozAlign: "center", headerFilter: "input" },
        ]';
        require_once 'app/views/index.php';
    }

    public function Data()
    {
        $user = $this->auth->authorize(128);
        header('Content-Type: application/json');

        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $size = isset($_GET['size']) ? (int) $_GET['size'] : 15;
        $offset = ($page - 1) * $size;

        $fieldMap = [
            'id' => 'a.id',
            'date' => 'a.created_at',
            'name' => 'a.name',
            'type' => 'a.kind',
            'employee' => 'c.name',
            'area' => 'd.area',
            'user' => 'b.username',
            'notes' => 'a.notes',
        ];

        $selectMap = [
            'id' => 'a.id AS id',
            'date' => 'a.created_at AS date',
            'name' => 'a.name AS name',
            'type' => 'a.kind AS type',
            'employee' => 'c.name AS employee',
            'area' => 'd.area AS area',
            'user' => 'b.username AS user',
            'notes' => 'a.notes AS notes',
        ];

        $where = '';
        if (isset($_GET['filter']) && is_array($_GET['filter'])) {
            foreach ($_GET['filter'] as $f) {
                if (! isset($f['field'], $f['value']) || $f['value'] === '') {
                    continue;
                }
                if (! isset($fieldMap[$f['field']])) {
                    continue;
                }
                $dbField = $fieldMap[$f['field']];
                $value = addslashes($f['value']);
                if ($f['field'] === 'date' && strpos($value, ' to ') !== false) {
                    [$from, $to] = explode(' to ', $value);
                    $where .= " AND DATE($dbField) BETWEEN '$from' AND '$to'";
                } else {
                    $where .= " AND $dbField LIKE '%$value%'";
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

        $joins = '
            LEFT JOIN users b ON a.user_id = b.id
            LEFT JOIN employees c ON a.employee_id = c.id
            LEFT JOIN hr_db d ON c.profile = d.id
        ';

        $total = $this->model->get('COUNT(a.id) AS total', 'epp a', $where, $joins)->total;
        $selectFields = implode(', ', $selectMap);
        $rows = $this->model->list($selectFields, 'epp a', "$where ORDER BY $orderBy LIMIT $offset, $size", $joins);

        $data = [];
        foreach ($rows as $r) {
            $item = [];
            foreach ($selectMap as $key => $_) {
                $item[$key] = $r->$key;
            }
            $data[] = $item;
        }

        echo json_encode([
            'data' => $data,
            'last_page' => ceil($total / $size),
            'last_row' => $total,
        ]);
    }

    public function Stats()
    {
        $filters = '';
        $a = $this->model->get('count(a.id) as total', 'epp a', $filters)->total;
        $b = $this->model->get('count(a.id) as total', 'epp a', "$filters and kind = 'Dotación'")->total;
        $c = $this->model->get('count(a.id) as total', 'epp a', "$filters and kind = 'Perdida'")->total;
        $d = $this->model->get('count(a.id) as total', 'epp a', "$filters and kind = 'Reposición'")->total;
        require_once 'app/views/ppe/stats.php';
    }

    public function New()
    {
        $user = $this->auth->authorize(128);
        require_once 'app/views/ppe/new-delivery.php';
    }

    public function Save()
    {
        $user = $this->auth->authorize(128);
        header('Content-Type: application/json');

        $signatureBlob = null;
        if (! empty($_POST['signature_base64'])) {
            $imgData = $_POST['signature_base64'];
            // El JS ahora envía image/jpeg
            $imgData = str_replace('data:image/jpeg;base64,', '', $imgData);
            $imgData = str_replace(' ', '+', $imgData);
            $signatureBlob = base64_decode($imgData);

            // COMPRESIÓN: Reducimos el binario antes de guardar (ZLIB)
            $signatureBlob = gzcompress($signatureBlob, 9);
        }

        $lastId = false;

        if (isset($_POST['type']) && is_array($_POST['type'])) {
            foreach ($_POST['type'] as $ppeId => $typeValue) {
                if (empty($typeValue)) {
                    continue;
                }

                $item = new stdClass;
                $item->user_id = $_SESSION['id-SIGMA'];
                $item->employee_id = $_POST['employee_id'];
                $item->notes = $_POST['notes'] ?? '';
                $item->kind = $typeValue;

                $nameData = $this->model->get('name', 'epp_db', 'and id = '.(int) $ppeId);
                $item->name = ($nameData) ? $nameData->name : 'Unknown Item';

                $item->img = $signatureBlob;
                $item->is_optimized = 1; // MARCA DE NUEVO FORMATO

                $lastId = $this->model->saveblob('epp', $item);
            }
        }

        if ($lastId !== false) {
            $hxTriggerData = json_encode([
                'eventChanged' => true,
                'showMessage' => ['type' => 'success', 'message' => 'Saved Successfully'],
            ]);
            header('HX-Trigger: '.$hxTriggerData);
            echo json_encode(['status' => 'success']);
            exit;
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error']);
            exit;
        }
    }

    public function Detail()
    {
        $user = $this->auth->authorize(128);
        $record = $this->model->get('*', 'epp', 'and id = '.$_REQUEST['id']);

        $img = $record->img;
        $displayBinary = null;

        // LÓGICA HÍBRIDA: ¿Viene de la nueva columna is_optimized?
        if (isset($record->is_optimized) && $record->is_optimized == 1) {
            $displayBinary = @gzuncompress($img);
        } else {
            // Es formato viejo (posiblemente Base64 o PNG crudo)
            if (strpos($img, 'data:image') !== false) {
                $cleanData = preg_replace('#^data:image/\w+;base64,#i', '', $img);
                $displayBinary = base64_decode($cleanData);
            } else {
                $displayBinary = $img;
            }
        }

        if ($displayBinary === false) {
            $displayBinary = $img;
        }
        $base64Img = 'data:image/jpeg;base64,'.base64_encode($displayBinary);

        ?>
        <div class="w-[95%] max-h-[98vh] sm:w-[25%] bg-white p-6 rounded-2xl shadow-lg relative z-50 overflow-y-auto">
            <button id="closeNewModal"
                class="absolute top-0 right-0 m-3 text-gray-900 hover:text-gray-700"
                @click="showModal = false; document.getElementById('myModal').innerHTML = '';"
            >
                <i class="ri-close-line text-2xl"></i>
            </button>
            <div class="pt-4 justify-center">
                <img src="<?= $base64Img ?>" class="w-full h-auto max-h-[70vh] object-contain" alt="Firma">
            </div>
        </div>
        <?php
    }

    public function MigrarFirmas()
    {
        $this->auth->authorize(128);

        // Traemos 100 registros pendientes
        $rows = $this->model->list('id, img', 'epp', 'AND is_optimized = 0 LIMIT 100');

        if (empty($rows)) {
            exit("<h1 style='color:green; font-family:sans-serif;'>¡Felicidades! Optimización completada.</h1>");
        }

        $procesados = 0;
        foreach ($rows as $r) {
            $data = $r->img;

            // Si es el Base64 viejo, decodificar
            if (strpos($data, 'data:image') !== false) {
                $data = preg_replace('#^data:image/\w+;base64,#i', '', $data);
                $data = base64_decode($data);
            }

            // Comprimir al máximo (Nivel 9)
            $compressed = gzcompress($data, 9);

            // Actualizamos usando el nuevo método updateblob
            $this->model->updateblob('epp', [
                'img' => $compressed,
                'is_optimized' => 1,
            ], "id = {$r->id}");

            $procesados++;
        }

        echo "<div style='font-family:sans-serif; padding:20px; border:1px solid #ccc; border-radius:10px;'>";
        echo "<h2 style='color:#333;'>Migrando Base de Datos...</h2>";
        echo "<p style='font-size:1.2em;'>Procesados en este lote: <b>$procesados</b></p>";
        echo '<p>La página se refrescará automáticamente para el siguiente lote.</p>';
        echo '</div>';

        echo '<script>setTimeout(() => { location.reload(); }, 1200);</script>';
    }
}
