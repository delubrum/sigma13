<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

class EquipmentDeliveriesController
{
    public function __construct(public Model $model, private AuthService $auth) {}

    public function Index()
    {
        $user = $this->auth->authorize(155);
        $tabulator = true;
        $jspreadsheet = true;
        $title = 'OHS / Equipment / Deliveries';
        $button = 'New Delivery';
        $content = 'app/components/list.php';
        $columns = '[
            { title: "ID", field: "id", headerHozAlign: "center", headerFilter: "input", width: 70 },
            { title: "Fecha", field: "date", headerHozAlign: "center", headerFilter: "input" },
            { title: "Nombre", field: "name", headerHozAlign: "center", headerFilter: "input" },
            { title: "Empleado", field: "employee", headerHozAlign: "center", headerFilter: "input" },
            { title: "Cantidad", field: "qty", headerHozAlign: "center", headerFilter: "input" },
            { title: "Usuario", field: "user", headerHozAlign: "center", headerFilter: "input" },
        ]';
        require_once 'app/views/index.php';
    }

    public function Data()
    {
        $user = $this->auth->authorize(155);

        header('Content-Type: application/json');

        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $size = isset($_GET['size']) ? (int) $_GET['size'] : 15;
        $offset = ($page - 1) * $size;

        /**
         * Campos reales en BD (para filtros y orden)
         */
        $fieldMap = [
            'id' => 'a.id',
            'date' => 'a.created_at',
            'name' => 'a.name',
            'employee' => 'c.name',
            'qty' => 'a.qty',
            'user' => 'b.username',
            'notes' => 'a.notes',
        ];

        /**
         * Campos del SELECT con alias (evita colisiones)
         */
        $selectMap = [
            'id' => 'a.id AS id',
            'date' => 'a.created_at AS date',
            'name' => 'a.name AS name',
            'employee' => 'c.name AS employee',
            'qty' => 'a.qty AS qty',
            'user' => 'b.username AS user',
            'notes' => 'a.notes AS notes',
        ];

        $where = '';

        /**
         * Filtros
         */
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

        /**
         * Ordenamiento
         */
        $orderBy = 'a.id DESC';
        if (isset($_GET['sort'][0]['field'], $_GET['sort'][0]['dir'])) {
            $sortField = $_GET['sort'][0]['field'];
            $sortDir = strtoupper($_GET['sort'][0]['dir']) === 'ASC' ? 'ASC' : 'DESC';

            if (isset($fieldMap[$sortField])) {
                $orderBy = $fieldMap[$sortField]." $sortDir";
            }
        }

        /**
         * Joins
         */
        $joins = '
            LEFT JOIN users b ON a.user_id = b.id
            LEFT JOIN employees c ON a.employee_id = c.id
        ';

        /**
         * Total de registros
         */
        $total = $this->model
            ->get('COUNT(a.id) AS total', 'equipment a', $where, $joins)
            ->total;

        /**
         * Obtener datos
         */
        $selectFields = implode(', ', $selectMap);

        $rows = $this->model->list(
            $selectFields,
            'equipment a',
            "$where ORDER BY $orderBy LIMIT $offset, $size",
            $joins
        );

        /**
         * Formatear respuesta
         */
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
        $a = $this->model->get('count(a.id) as total', 'equipment a', $filters)->total;
        require_once 'app/views/equipment/stats.php';
    }

    public function New()
    {
        $user = $this->auth->authorize(155);
        require_once 'app/views/equipment/new-delivery.php';
    }

    public function Save()
    {
        // 1. Autorización y Cabeceras
        $user = $this->auth->authorize(155);
        header('Content-Type: application/json');

        // 2. Captura de datos desde el POST
        $employee_id = $_POST['employee_id'] ?? null;
        $items = $_POST['items'] ?? [];
        $sigBase64 = $_POST['signature_base64'] ?? '';

        // 3. Validaciones específicas para depuración
        if (empty($employee_id)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Seleccione un empleado.']);
            exit;
        }

        if (empty($items)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Debe agregar al menos un equipo a la lista.']);
            exit;
        }

        if (strlen($sigBase64) < 1000) { // Una firma válida suele ser > 1000 caracteres en Base64
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'La firma es obligatoria o no es válida.']);
            exit;
        }

        // 4. Procesar la firma (Base64 a Binario)
        $imgData = str_replace(['data:image/png;base64,', ' '], ['', '+'], $sigBase64);
        $signatureBlob = base64_decode($imgData);

        $success = false;

        // 5. Iterar y guardar cada equipo
        foreach ($items as $data) {
            if (empty($data['id'])) {
                continue;
            }

            $item = new stdClass;
            $item->user_id = $_SESSION['id-SIGMA'];
            $item->employee_id = $employee_id;
            $item->qty = (int) ($data['qty'] ?? 1);
            $item->img = $signatureBlob; // Binario de la firma
            $item->notes = $_POST['notes'] ?? '';

            // Buscar el nombre del equipo para el registro histórico
            $equipmentId = (int) $data['id'];
            $dbData = $this->model->get('name', 'equipment_db', "AND id = $equipmentId");
            $item->name = ($dbData) ? $dbData->name : 'Unknown Item';

            // Guardar en la tabla 'equipment' usando el método PDO saveblob
            if ($this->model->saveblob('equipment', $item)) {
                $success = true;
            }
        }

        // 6. Respuesta final para HTMX
        if ($success) {
            $hxTrigger = json_encode([
                'eventChanged' => true,
                'showMessage' => ['type' => 'success', 'message' => 'Entrega registrada correctamente'],
            ]);
            header('HX-Trigger: '.$hxTrigger);
            echo json_encode(['status' => 'success']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Error al guardar en la base de datos.']);
        }
        exit;
    }

    public function Detail()
    {
        $user = $this->auth->authorize(155);
        $img = $this->model->get('*', 'equipment', 'and id = '.$_REQUEST['id'])->img;

        $base64Img = 'data:image/png;base64,'.base64_encode($img);

        // Salimos de PHP para escribir HTML limpio
        ?>
            <div class="w-[95%] max-h-[98vh] sm:w-[25%] bg-white p-6 rounded-2xl shadow-lg relative z-50 overflow-y-auto">
                <button id="closeNewModal"
                    class="absolute top-0 right-0 m-3 text-gray-900 hover:text-gray-700"
                    @click="showModal = false; document.getElementById('myModal').innerHTML = '';"
                >
                    <i class="ri-close-line text-2xl"></i>
                </button>
                <div class="pt-4 justify-center">
                    <img src="<?= $base64Img ?>" 
                        class="w-full h-auto max-h-[70vh] object-contain" 
                        alt="Vista previa">
                </div>
            </div>
            <?php
    }
}
