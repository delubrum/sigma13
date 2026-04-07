<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EmployeesController
{
    public function __construct(public Model $model, private AuthService $auth) {}

    public function Login()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['user_id'])) {
            header('Location: ?c=Employees&a=PD');
            exit;
        }

        if (empty($_REQUEST['user_id'])) {
            require_once 'app/views/employees/login.php';
            exit;
        }

        $user_id = $_REQUEST['user_id'];

        if (empty($this->model->get('id', 'employees', " and id = '$user_id' and status = '1'")->id)) {
            exit(json_encode(['error' => 'El usuario no existe']));
        }

        $_SESSION['user_id'] = $user_id;
        header('Location: ?c=Test&a=Test');
        exit;
    }

    public function Logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header('Location: ?c=Employees&a=Login');
        exit;
    }

    public function PD()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Verificar si NO hay sesión iniciada (Este bloque se mantiene igual)
        if (! isset($_SESSION['user_id'])) {
            header('Location: ?c=Employees&a=Login');
            exit;
        }

        $user_id = $_SESSION['user_id'];

        // 2. Verificar si hay actualización de datos (Este bloque se mantiene igual)
        $update = $this->model->get('employee_id', 'personal_data_updates', " AND employee_id = '$user_id'");

        // Si hay actualización, mostrar el mensaje de éxito y salir
        if (! empty($update->employee_id)) {
            exit('
                <!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
                    <title>Actualización Exitosa</title>
                    <style>
                        /* ... (Estilos CSS) ... */
                        body {
                            margin: 0;
                            padding: 0;
                            font-family: sans-serif;
                            background-color: #f8f8f8;
                        }
                        .container-full-screen {
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            min-height: 100vh;
                            padding: 20px;
                            box-sizing: border-box;
                        }
                        .success-card {
                            width: 100%;
                            max-width: 400px;
                            padding: 25px 20px;
                            background: #f0f9ff;
                            border: 1px solid #bae6fd;
                            border-radius: 12px;
                            color: #0369a1;
                            font-size: 1rem;
                            text-align: center;
                            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                        }
                        .message {
                            margin-bottom: 20px;
                            font-size: 1.1rem;
                            font-weight: bold;
                            word-wrap: break-word;
                        }
                        .logout-btn {
                            display: block;
                            width: 100%;
                            max-width: 250px;
                            margin: 0 auto;
                            padding: 12px 20px;
                            background: #0369a1;
                            color: white;
                            text-decoration: none;
                            border-radius: 8px;
                            font-size: 1rem;
                            transition: background .2s;
                            box-sizing: border-box;
                        }
                        .logout-btn:hover {
                            background: #02507a;
                        }
                    </style>
                </head>
                <body>
                    <div class="container-full-screen">
                        <div class="success-card">
                            <div class="message">
                                ✔️ Actualización de datos realizada con éxito
                            </div>

                            <a href="?c=Employees&a=Logout" class="logout-btn">
                                Logout
                            </a>
                        </div>
                    </div>
                </body>
                </html>
            ');
        }

        // 3. Buscar los datos del empleado
        $employee = $this->model->get('*', 'employees', " AND id = '$user_id'");

        // 4. Manejar el caso: Empleado Encontrado vs. Empleado No Encontrado
        if ($employee) {
            // El empleado existe, mostrar la vista del formulario
            require_once 'app/views/employees/pd.php';
        } else {
            // ¡MODIFICACIÓN AQUÍ! El usuario inició sesión (tiene $_SESSION['user_id']),
            // pero su registro ya no existe en la base de datos.
            // La mejor opción es destruír la sesión y redirigir al login.

            // Si el usuario no existe, destruimos la sesión y redirigimos al login
            session_unset();
            session_destroy();

            // Redirigimos, posiblemente con un mensaje de error si el framework lo soporta
            header('Location: ?c=Employees&a=Login');
            exit;
        }
    }

    public function Index()
    {
        $user = $this->auth->authorize(123);
        $tabulator = true;
        $jspreadsheet = false; // No necesitamos JSpreadsheet aquí
        $button = 'New Employee';
        $content = 'app/components/list.php';
        $title = 'Employees'; // Título actualizado

        // Definición de columnas para Tabulator
        $columns = '[
            { "title": "ID", "field": "id", headerHozAlign: "left", headerFilter:"input"},
            { "title": "Name", "field": "name", headerHozAlign: "left", headerFilter:"input"},
            { "title": "Division", "field": "division", headerHozAlign: "left", headerFilter:"input"},
            { "title": "Profile", "field": "profile", headerHozAlign: "left", headerFilter:"input"},
            { "title": "City", "field": "city", headerHozAlign: "left", headerFilter:"input"},
            { "title": "Start Date", "field": "start_date", headerHozAlign: "left", headerFilter:"input"},
            { "title": "Status", "field": "status", headerHozAlign: "left", headerFilter:"input"},
            { "title": "Last Update", "field": "updated_at", "headerHozAlign": "left", "headerFilter":"input", "download": true}        ]';
        require_once 'app/views/index.php';
    }

    public function New()
    {
        $user = $this->auth->authorize(123);
        require_once 'app/views/employees/new.php';
    }

    public function Data()
    {
        // 1. Seguridad y Modo Exportación
        $user = $this->auth->authorize(123);
        $isExport = isset($_GET['export']);

        if (! $isExport) {
            header('Content-Type: application/json');
        }

        // 2. Parámetros de Paginación
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $size = isset($_GET['size']) ? (int) $_GET['size'] : 15;
        $offset = ($page - 1) * $size;

        // 3. Mapeo de campos para filtros y ordenación
        $fieldMap = [
            'id' => 'a.id',
            'name' => 'a.name',
            'division' => 'hr.name',
            'profile' => 'jp.name',
            'city' => 'a.city',
            'start_date' => 'a.start_date',
            'status' => 'a.status',
            'updated_at' => 'b.latest_update',
        ];

        // 4. Lógica de Filtros
        $where = ' '; // Cambiado para evitar errores de sintaxis con el primer AND
        if (isset($_GET['filter']) && is_array($_GET['filter'])) {
            foreach ($_GET['filter'] as $key => $f) {
                $field = is_array($f) ? ($f['field'] ?? '') : $key;
                $value = is_array($f) ? ($f['value'] ?? '') : $f;
                if ($value === '') {
                    continue;
                }

                $value = addslashes($value);

                if (isset($fieldMap[$field])) {
                    $dbField = $fieldMap[$field];
                    if ($field === 'start_date' && strpos($value, ' to ') !== false) {
                        [$from, $to] = explode(' to ', $value);
                        $where .= " AND DATE($dbField) BETWEEN '$from' AND '$to'";
                    } elseif ($field === 'status') {
                        $where .= " AND $dbField = '$value'";
                    } else {
                        $where .= " AND $dbField LIKE '%$value%'";
                    }
                }
            }
        }

        // 5. Manejo de ordenación
        $orderBy = 'a.status DESC, a.start_date DESC';
        if (isset($_GET['sort'][0]['field'], $_GET['sort'][0]['dir'])) {
            $sortField = $_GET['sort'][0]['field'];
            $sortDir = strtoupper($_GET['sort'][0]['dir']) === 'ASC' ? 'ASC' : 'DESC';
            if (isset($fieldMap[$sortField])) {
                $orderBy = $fieldMap[$sortField]." $sortDir";
            }
        }

        // 6. Definición de JOINs y SELECT
        $joins = '
            LEFT JOIN (
                SELECT employee_id, MAX(created_at) AS latest_update 
                FROM personal_data_updates 
                GROUP BY employee_id
            ) b ON a.id = b.employee_id
            LEFT JOIN job_profiles jp ON a.profile = jp.id
            LEFT JOIN hr_db hr ON jp.division_id = hr.id
        ';

        // CORRECCIÓN: Se eliminó el error jp.a.city y se agregaron ambos campos con alias claros
        $selectFields = 'a.id, a.name, hr.name AS division_name, jp.name AS profile_name, a.city, a.start_date, a.status, b.latest_update AS updated_at';

        // 7. CASO: EXPORTACIÓN A EXCEL
        if ($isExport) {
            setcookie('download_complete', 'true', time() + 30, '/');
            $rows = $this->model->list($selectFields, 'employees a', "$where ORDER BY $orderBy", $joins);

            $spreadsheet = new Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();

            // Headers actualizados para incluir Perfil y División por separado o combinados
            $headers = ['ID', 'Nombre', 'División', 'Perfil', 'Ciudad', 'Fecha Ingreso', 'Estado', 'Última Actualización'];
            $sheet->fromArray($headers, null, 'A1');

            $exportData = [];
            foreach ($rows as $r) {
                $exportData[] = [
                    $r->id,
                    $r->name,
                    $r->division_name,
                    $r->profile_name,
                    $r->city,
                    $r->start_date, // El formateo se hace abajo con setFormatCode
                    $r->status,
                    $r->updated_at,
                ];
            }

            if (! empty($exportData)) {
                $sheet->fromArray($exportData, null, 'A2');
                $lastRow = count($exportData) + 1;

                // Formateo de columnas de fecha (F y H en este nuevo orden)
                $sheet->getStyle("F2:F$lastRow")->getNumberFormat()->setFormatCode('yyyy-mm-dd');
                $sheet->getStyle("H2:H$lastRow")->getNumberFormat()->setFormatCode('yyyy-mm-dd hh:mm');
            }

            foreach (range('A', 'H') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="Reporte_Empleados_'.date('dmY').'.xlsx"');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        }

        // 8. Respuesta JSON para Tabulator
        $total = $this->model->get('COUNT(a.id) AS total', 'employees a', $where, $joins)->total;
        $rows = $this->model->list($selectFields, 'employees a', "$where ORDER BY $orderBy LIMIT $offset, $size", $joins);

        $data = [];
        foreach ($rows as $r) {
            $data[] = [
                'id' => $r->id,
                'name' => $r->name,
                'division' => $r->division_name,
                'profile' => $r->profile_name,
                'city' => $r->city,
                'start_date' => $r->start_date,
                'status' => $r->status,
                'updated_at' => $r->updated_at ?? '',
            ];
        }

        echo json_encode([
            'data' => $data,
            'last_page' => ceil($total / $size),
            'last_row' => (int) $total,
        ]);
    }

    public function Stats()
    {
        $user = $this->auth->authorize(123);
        header('Content-Type: application/json');

        $a = $this->model->get('COUNT(a.id) as total', 'employees a', 'and a.status = 1')->total;
        $b = $this->model->get('COUNT(a.id) as total', 'employees a', 'and a.status = 0')->total;
        $c = $this->model->get(
            'COUNT(a.id) as total',
            'employees a',
            'AND b.latest_update IS NOT NULL',
            'LEFT JOIN (
                SELECT employee_id, MAX(created_at) AS latest_update 
                FROM personal_data_updates 
                GROUP BY employee_id
            ) b ON a.id = b.employee_id'
        )->total;

        require_once 'app/views/employees/stats.php';
    }

    public function Save()
    {
        // Asegura que la respuesta sea JSON
        header('Content-Type: application/json');

        try {
            // --- 1. Autorización y Extracción de Payload ---
            $payload = json_decode($_POST['payload'] ?? '{}', true);
            $user_id = $_SESSION['user_id'] ?? null;

            if (! $user_id) {
                http_response_code(401);
                echo json_encode(['type' => 'error', 'message' => 'Unauthorized']);

                return;
            }

            // --- 2. CREAR OBJETO PRINCIPAL Y MAPPING DE CAMPOS ---
            $item = new stdClass;
            $item->employee_id = $user_id;

            // ------------------------------
            // CAMPOS SIMPLES
            // ------------------------------
            $simpleFields = [
                // Datos generales
                'tipo_sangre', 'lugar_nacimiento', 'fecha_nacimiento', 'sexo',
                'licencia_conduccion', 'categoria_licencia', 'libreta_militar', 'tiene_vehiculo',
                'tipo_vehiculo', 'placa_vehiculo', 'estado_civil',

                // Dirección
                'direccion', 'barrio', 'municipio', 'departamento',
                'estrato_socioeconomico', 'tiempo_vivienda', 'tenencia_vivienda',
                'telefono_fijo', 'celular', 'email',

                // Relaciones
                'relacion_tecnoglass',

                // Conyuge
                'nombre_conyuge', 'cedula_conyuge', 'ocupacion_conyuge',
                'email_conyuge', 'telefono_conyuge', 'numero_hijos',

                // Datos de seguridad
                'tiene_carnet_arl', 'seguro_exequias', 'carnet_empresa',

                // Campo simple adicional
            ];

            foreach ($simpleFields as $field) {
                $item->{$field} = $payload[$field] ?? null;
            }

            // Valores por defecto
            $item->licencia_conduccion = $item->licencia_conduccion ?? 'No';
            $item->libreta_militar = $item->libreta_militar ?? 'No';
            $item->tiene_vehiculo = $item->tiene_vehiculo ?? 'No';
            $item->relacion_tecnoglass = $item->relacion_tecnoglass ?? 'No';

            // ------------------------------
            // CAMPOS COMPUESTOS
            // ------------------------------

            // Tallas
            $tallas = $payload['tallas'] ?? [];
            $item->talla_pantalon = $tallas['pantalon'] ?? null;
            $item->talla_camisa = $tallas['camisa'] ?? null;
            $item->talla_zapatos = $tallas['zapatos'] ?? null;

            // Afiliaciones
            $afi = $payload['afiliaciones'] ?? [];
            $item->eps = $afi['eps'] ?? null;
            $item->fondo_pensiones = $afi['fondo_pensiones'] ?? null;
            $item->arl = $afi['arl'] ?? null;

            // Antecedentes médicos
            $med = $payload['medicos'] ?? [];
            $item->medico_presion_arterial = $med['presion_arterial_opcion'] ?? 'No';
            $item->medico_diabetes = $med['diabetes'] ?? 'No';
            $item->medico_alergias = $med['alergias'] ?? null;
            $item->medico_otra_condicion = $med['otra_condicion'] ?? null;
            $item->medico_observaciones = $med['observaciones'] ?? null;

            // ------------------------------
            // CAMPOS JSON
            // ------------------------------

            $item->relacion_tecnoglass_detalle_json =
                ($item->relacion_tecnoglass === 'Si')
                    ? json_encode($payload['relacion_tecnoglass_detalle'] ?? new stdClass)
                    : null;

            $item->ultimo_estudio_json = json_encode($payload['ultimo_estudio'] ?? new stdClass);
            $item->otros_estudios_list_json = json_encode($payload['otros_estudios_list'] ?? []);
            $item->contacto_emergencia_json = json_encode($payload['contacto'] ?? new stdClass);
            $item->hijos_json = json_encode($payload['hijos'] ?? []);

            // Meta datos
            $item->created_at = date('Y-m-d H:i:s');
            $item->status = 'pending_review';

            // ------------------------------
            // 3. GUARDAR REGISTRO PRINCIPAL
            // ------------------------------
            $update_id = $this->model->save('personal_data_updates', $item);
            if ($update_id === false) {
                http_response_code(500);
                echo json_encode([
                    'type' => 'error',
                    'message' => 'Error: No se pudo guardar el registro principal en la base de datos.',
                ]);

                return;
            }

            // ------------------------------
            // 4. FOTO (Opcional) - CORRECCIÓN CLAVE AQUÍ
            // ------------------------------
            $photo_path = null;

            // El campo en el HTML tiene name="fotografia"
            if (! empty($_FILES['fotografia']['name']) && $_FILES['fotografia']['error'] === UPLOAD_ERR_OK) {

                $tmp = $_FILES['fotografia']['tmp_name'];
                $ext = strtolower(pathinfo($_FILES['fotografia']['name'], PATHINFO_EXTENSION));

                // Usar la cédula como parte del nombre del archivo

                // ⚠️ Ajusta esta ruta a tu configuración real de subida ⚠️
                $carpeta = "uploads/personal_data/$user_id";
                if (! file_exists($carpeta)) {
                    mkdir($carpeta, 0775, true); // Usar 0775 para buena práctica en lugar de 0777
                }

                $photo_file_name = "{$user_id}_".time().".$ext"; // Nombre más seguro
                $photo_path = "$carpeta/$photo_file_name";

                if (move_uploaded_file($tmp, $photo_path)) {
                    // Guardar la ruta en el registro principal
                    $this->model->update(
                        'personal_data_updates',
                        (object) ['photo_path' => $photo_path],
                        $update_id
                    );
                } else {
                    error_log("Fallo al mover archivo para $user_id. Verifique permisos de carpeta: ".$carpeta);
                }
            }

            // ------------------------------
            // 5. RESPUESTA HTMX PARA RECARGAR LA PÁGINA
            // ------------------------------

            // 1. Envía un mensaje de éxito para que el usuario lo vea antes de recargar
            // Usamos HX-Trigger para mostrar un mensaje temporal
            $message_success = json_encode([
                'type' => 'success',
                'message' => '✅ ¡Actualización guardada con éxito! Recargando la página...',
            ]);

            // Dispara un evento con el mensaje (opcional, pero ayuda a la UX)
            header('HX-Trigger: '.json_encode(['showMessage' => $message_success]));

            // 2. Encabezado CLAVE: Le dice a HTMX que debe recargar la página.
            header('HX-Refresh: true');

            // 3. Envía una respuesta HTTP 204 (No Content) para evitar que HTMX reemplace el contenido
            http_response_code(204);

        } catch (Exception $e) {
            http_response_code(500);
            error_log('Excepción en Save(): '.$e->getMessage());
            echo json_encode([
                'type' => 'error',
                'message' => '❌ Error interno del servidor: '.$e->getMessage(),
            ]);
        }
    }

    public function Detail()
    {
        $user = $this->auth->authorize(123);
        if (! empty($_REQUEST['id'])) {
            $filters = 'and id = '.$_REQUEST['id'];
            $id = $this->model->get(
                '*',
                'employees',
                $filters,
            );
        }
        require_once 'app/views/employees/detail.php';
    }

    public function Info()
    {
        $user = $this->auth->authorize(123);
        if (! empty($_REQUEST['id'])) {
            $filters = 'and a.id = '.$_REQUEST['id'];
            $id = $this->model->get(
                '*, a.id as id, c.name as profile, a.name as name',
                'employees a',
                $filters,
                'LEFT JOIN personal_data_updates b ON a.id = b.employee_id LEFT JOIN job_profiles c on a.profile = c.id'
            );
        }
        require_once 'app/views/employees/detail/tabs/info.php';
    }

    public function Documents()
    {
        $user = $this->auth->authorize(123);
        $id = $this->model->get('*', 'employees a', 'and a.id = '.$_REQUEST['id']);
        require_once 'app/views/employees/detail/tabs/documents.php';
    }

    public function SaveEmployee()
    {
        $user = $this->auth->authorize(123);
        header('Content-Type: application/json');

        $id = $_POST['id'] ?? null;

        // 1. VALIDACIÓN: Si la cédula ya existe, abortamos de inmediato
        if ($this->model->get('id', 'employees', " AND id = '$id'")) {
            $message = '{"type": "error", "message": "Error: La cédula '.$id.' ya está registrada", "close" : ""}';
            header('HX-Trigger: '.json_encode(['showMessage' => $message]));
            http_response_code(400);
            exit;
        }

        // 2. Si no existe, preparamos el objeto para insertar
        $item = new stdClass;
        foreach ($_POST as $k => $val) {
            if (! empty($val)) {
                $item->{$k} = $val;
            }
        }

        $item->status = 1;

        // 3. Guardamos (Save siempre insertará porque ya validamos que no existe)
        $result = $this->model->save('employees', $item);

        if ($result !== false) {
            $message = '{"type": "success", "message": "Saved", "close" : "closeNewModal"}';
            header('HX-Trigger: '.json_encode([
                'eventChanged' => true,
                'showMessage' => $message,
            ]));
            http_response_code(204);
        } else {
            http_response_code(500);
            echo json_encode(['type' => 'error', 'message' => 'Error interno al guardar']);
        }
    }

    public function EmployeeData()
    {
        $user = $this->auth->authorize(123);
        if (! empty($_REQUEST['id'])) {
            $filters = 'and a.id = '.$_REQUEST['id'];
            $id = $this->model->get(
                '*, a.id as id, c.name as profile, a.name as name',
                'employees a',
                $filters,
                'LEFT JOIN personal_data_updates b ON a.id = b.employee_id LEFT JOIN job_profiles c on a.profile = c.id'
            );

            $rel = json_decode($id->relacion_tecnoglass_detalle_json ?? '{}');
            $contacto = json_decode($id->contacto_emergencia_json ?? '{}');
            $estudio = json_decode($id->ultimo_estudio_json ?? '{}');

            $ultimo_estudio = $id->ultimo_estudio_json ?? [];
            if (! is_array($ultimo_estudio)) {
                $ultimo_estudio = json_decode($ultimo_estudio, true) ?: [];
            }
            $ultimo_estudio = array_merge([
                'nivel' => '',
                'enfasis' => '',
                'institucion' => '',
            ], $ultimo_estudio);
        }
        require_once 'app/views/employees/detail/tabs/data.php';
    }

    public function Modal()
    {
        $user = $this->auth->authorize([123]);
        $modal = $_REQUEST['modal'];
        $id = $this->model->get('*', 'employees a', 'and a.id = '.$_REQUEST['id']);
        require_once "app/views/employees/detail/modals/$modal.php";
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
            $item = new stdClass;

            foreach ($_POST as $k => $val) {
                if (! empty($val) && $k !== 'id') {
                    $item->{$k} = htmlspecialchars(trim($val));
                }
            }

            // Datos fijos
            $item->employee_id = $_REQUEST['id'] ?? null;
            $item->user_id = $_SESSION['id-SIGMA'] ?? null;

            $id = $this->model->save('employee_documents', $item);

            if (! $id) {
                throw new Exception('Error saving maintenance item');
            }

            /* ==========================
            MANEJO DE ARCHIVOS
            ========================== */
            if (! empty($_FILES['files']['name'][0])) {

                $carpeta = "uploads/employees/documents/$id/";
                if (! is_dir($carpeta)) {
                    mkdir($carpeta, 0755, true);
                }

                // Tomamos solo el primer archivo (ya que el campo 'url' es único)
                $tmpFilePath = $_FILES['files']['tmp_name'][0];

                if ($tmpFilePath != '') {
                    $fileName = basename($_FILES['files']['name'][0]);
                    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf', 'jfif'];

                    if (! in_array($fileExt, $allowedTypes)) {
                        throw new Exception("Tipo de archivo no permitido: $fileExt");
                    }

                    // Generamos un nombre único para evitar conflictos
                    $nombreArchivo = uniqid().'.'.$fileExt;
                    $destino = $carpeta.$nombreArchivo;

                    if (move_uploaded_file($tmpFilePath, $destino)) {

                        // --- ACTUALIZACIÓN EN SQL ---
                        $updateData = new stdClass;
                        $updateData->url = $destino; // Guardamos la ruta relativa

                        // Usamos tu función update pasándole: tabla, objeto con datos, e ID
                        $this->model->update('employee_documents', $updateData, $id);

                    } else {
                        throw new Exception('Error al mover el archivo al servidor.');
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

    public function DocumentsData()
    {
        // Autorización
        $user = $this->auth->authorize(157); // Ajustado al permiso de empleados
        header('Content-Type: application/json');

        $employeeId = (int) ($_GET['id'] ?? 0);

        // Traemos los campos necesarios, incluyendo la 'url' que guardamos en SaveDocument
        $rows = $this->model->list(
            'a.id, a.name, a.code, a.expiry, a.url',
            'employee_documents a',
            "and a.employee_id = $employeeId"
        );

        $data = [];

        foreach ($rows as $r) {
            $fileLink = '';

            // Si el campo url no está vacío y el archivo existe, creamos el enlace
            if (! empty($r->url) && file_exists($r->url)) {
                $fileLink = "<a href='{$r->url}' target='_blank' class='flex items-center space-x-1 text-blue-600 hover:underline font-medium'>
                                <i class='ri-external-link-line text-sm'></i>
                                <span>Ver Documento</span>
                            </a>";
            } else {
                $fileLink = "<span class='text-gray-400 italic'>Sin archivo</span>";
            }

            $data[] = [
                'name' => $r->name,
                'code' => $r->code,
                'expiry' => $r->expiry,
                'file' => $fileLink, // Este campo lo procesa el formatter: "html" de Tabulator
            ];
        }

        echo json_encode([
            'data' => $data,
        ]);
    }
}
