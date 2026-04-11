<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RecruitmentController
{
    private const SEDES = [
        'ESM1' => 'Carrera 72A # 107 - 33 Detrás de la Bomba Terpel diagonal a la ventana al mundo.',
        'ESM2' => 'Ingreso por la Portería de ES WINDOWS 4 (al final de las bodegas de Tecnoglass).',
    ];

    public function __construct(public Model $model, private AuthService $auth) {}

    public function Index()
    {
        $user = $this->auth->authorize(85);
        $tabulator = true;
        $jspreadsheet = true;
        $kpis = true;
        $button = 'New Recruitment';
        $content = 'app/components/list.php';
        $title = 'HR / Recruitment';
        $columns = '[
            { "title": "ID", "field": "id", "headerHozAlign": "center", "headerFilter": "input" },
            { "title": "Date", "field": "date", "headerHozAlign": "center", "headerFilter": customDateRangeFilter, "headerFilterFunc": customDateFilterFunc, "headerFilterLiveFilter": false },
            { "title": "Creator", "field": "user", "headerHozAlign": "center", "headerFilter": "input" },
            { "title": "Approver", "field": "approver", "headerHozAlign": "center", "headerFilter": "input" },
            { "title": "Assignee", "field": "assignee", "headerHozAlign": "center", "headerFilter": "input" },
            { "title": "Profile", "field": "profile", "headerHozAlign": "center", "headerFilter": "input" },
            { "title": "Division", "field": "division", "headerHozAlign": "center", "headerFilter": "input" },
            { "title": "Area", "field": "area", "headerHozAlign": "center", "headerFilter": "input" },
            { "title": "Quantity", "field": "qty", "headerHozAlign": "center", "headerFilter": "input" },
            {
                "title": "Conversion",
                "field": "conversion",
                "headerHozAlign": "center",
                "headerFilter": "input",
                "formatter": function(cell, formatterParams) {
                    let v = Number(cell.getValue()) || 0;
                    return `<div class="progress-outer" style="position:relative; height:15px; background:#e5e7eb; border-radius:4px;">
                        <div class="progress-inner" style="width:${v}%; height:100%; background:gray; border-radius:4px;"></div>
                        <div class="progress-label" style="position:absolute; top:0; left:0; right:0; bottom:0; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:bold;">${v}%</div>
                    </div>`;
                }
            },
            { "title": "Days", "field": "days", "headerHozAlign": "center", "headerFilter": "input" },
            { "title": "Status", "field": "status", "headerHozAlign": "center", "hozAlign": "center", "headerFilter": "list", "headerFilterParams": { "values": { "approved": "Approved", "approval": "Approval", "closed": "Closed"}, "clearable": true } }
        ]';
        require_once 'app/views/index.php';
    }

    public function Data()
    {
        // 1. Seguridad y Modo Exportación
        $user = $this->auth->authorize(85);
        $isExport = isset($_GET['export']);

        if (! $isExport) {
            header('Content-Type: application/json');
        }

        // 2. Parámetros
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $size = isset($_GET['size']) ? (int) $_GET['size'] : 15;
        $offset = ($page - 1) * $size;

        $conversionSql = "((SELECT COUNT(id) FROM recruitment_candidates WHERE recruitment_id = a.id AND status = 'hired') / a.qty * 100)";

        $fieldMap = [
            'id' => 'a.id',
            'date' => 'a.created_at',
            'user' => 'b.username',
            'approver' => 'a.approver',
            'assignee' => 'd.username',
            'profile' => 'c.name',
            'division' => 'e.name',
            'area' => 'e.area',
            'qty' => 'a.qty',
            'days' => 'DATEDIFF(CURDATE(), a.created_at)',
            'status' => 'a.status',
            'conversion' => $conversionSql,
        ];

        // 3. Permisos y Filtros
        $permissions = json_decode($user->permissions ?? '[]', true);
        $isAdmin = in_array(86, $permissions);

        $where = '';
        if ($user->id == 505) {
            $where = ' AND a.assignee_id = '.(int) $user->id;
        } elseif (! $isAdmin) {
            $where = ' AND a.user_id = '.(int) $user->id;
        }

        if (isset($_GET['filter']) && is_array($_GET['filter'])) {
            foreach ($_GET['filter'] as $f) {
                if (! isset($f['field'], $f['value']) || $f['value'] === '') {
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

        // 4. Ordenamiento
        $orderBy = 'a.id DESC';
        if (isset($_GET['sort'][0]['field'], $_GET['sort'][0]['dir'])) {
            $sortField = $_GET['sort'][0]['field'];
            $sortDir = strtoupper($_GET['sort'][0]['dir']) === 'ASC' ? 'ASC' : 'DESC';
            if (isset($fieldMap[$sortField])) {
                $orderBy = $fieldMap[$sortField]." $sortDir";
            }
        }

        // 5. Query Base
        $selectFields = "a.id, a.created_at, b.username as user, c.name as profile, e.name as division, e.area as area, 
                        d.username as assignee, a.approver, a.status, a.qty, $conversionSql as calculated_conv,
                        (a.complexity - DATEDIFF(CURDATE(), a.created_at)) as days_remaining";

        $joins = 'LEFT JOIN users b ON a.user_id = b.id
                LEFT JOIN job_profiles c ON a.profile_id = c.id
                LEFT JOIN users d ON a.assignee_id = d.id
                LEFT JOIN hr_db e ON c.division_id = e.id';

        // --- LÓGICA DE EXPORTACIÓN ---
        if ($isExport) {
            setcookie('download_complete', 'true', time() + 30, '/');
            $rows = $this->model->list($selectFields, 'recruitment a', "$where ORDER BY $orderBy", $joins);

            $spreadsheet = new Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();

            $headers = ['ID', 'Fecha', 'Usuario', 'Aprobador', 'Asignado', 'Perfil', 'División', 'Área', 'Contratados/Total', 'Conversión %', 'Días Restantes', 'Estado'];
            $sheet->fromArray($headers, null, 'A1');

            $exportData = [];
            foreach ($rows as $r) {
                // Obtener conteo de contratados para la columna Qty (formato x/y)
                $hired = $this->model->get('count(id) as total', 'recruitment_candidates', " AND recruitment_id = $r->id AND status = 'hired'")->total;
                // Obtener nombre del aprobador por email
                $approverName = $this->model->get('username', 'users', " AND email = '$r->approver'")->username ?? $r->approver;

                $exportData[] = [
                    $r->id,
                    $r->created_at,
                    $r->user,
                    $approverName,
                    $r->assignee,
                    $r->profile,
                    $r->division,
                    $r->area,
                    "$hired / $r->qty",
                    round($r->calculated_conv, 2).'%',
                    $r->days_remaining,
                    $r->status,
                ];
            }

            $sheet->fromArray($exportData, null, 'A2');
            $lastR = count($exportData) + 1;

            // Estética básica
            foreach (range('A', 'L') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            $sheet->getStyle('A1:L1')->getFont()->setBold(true);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="Reporte_Reclutamiento_'.date('dmY').'.xlsx"');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        }

        // 6. Respuesta para Tabulator (Vista normal)
        $total = $this->model->get('COUNT(a.id) AS total', 'recruitment a', $where, $joins)->total;
        $rows = $this->model->list($selectFields, 'recruitment a', "$where ORDER BY $orderBy LIMIT $offset, $size", $joins);

        $data = [];
        foreach ($rows as $r) {
            $hiredCount = $this->model->get('count(id) as total', 'recruitment_candidates', " AND recruitment_id = $r->id AND status = 'hired'")->total;
            $data[] = [
                'id' => $r->id,
                'date' => $r->created_at,
                'user' => $r->user,
                'approver' => $this->model->get('username', 'users', " AND email = '$r->approver'")->username ?? $r->approver,
                'assignee' => $r->assignee,
                'profile' => $r->profile,
                'division' => $r->division,
                'area' => $r->area,
                'qty' => ($hiredCount.'/'.$r->qty),
                'conversion' => round($r->calculated_conv, 2),
                'days' => $r->days_remaining,
                'status' => $r->status,
            ];
        }

        echo json_encode(['data' => $data, 'last_page' => ceil($total / $size), 'last_row' => (int) $total]);
    }

    public function Stats()
    {
        // require_once 'app/views/recruitment/stats.php';
    }

    public function New()
    {
        $user = $this->auth->authorize(85);
        require_once 'app/views/recruitment/new.php';
    }

    public function getJobDetails()
    {
        // dame todo el codigo para evitar errores
        $user = $this->auth->authorize(85);
        $profile_id = isset($_REQUEST['profile_id']) ? intval($_REQUEST['profile_id']) : 0;

        if ($profile_id > 0) {
            $id = $this->model->get('*', 'job_profiles', "and id = $profile_id");

            if ($id) {
                // Obtenemos los RECURSOS guardados en el perfil (JSON)
                $resourceRow = $this->model->get('content', 'job_profile_items', "and jp_id = $id->id AND kind = 'Recursos'");
                $data = (! empty($resourceRow->content)) ? $resourceRow->content : '[]';

                // Llamamos a tu función list original.
                // El tercer parámetro es $filters, donde inyectamos el ORDER BY.
                $resourcesList = $this->model->list('*', 'recruitment_resources', 'ORDER BY category ASC, name ASC');

                require_once 'app/views/recruitment/jp.php';
            }
        } else {
            echo 'Job Profile Info not found';
        }
    }

    public function Save()
    {
        try {
            $user = $this->auth->authorize(85);
            header('Content-Type: application/json');

            // Crear objeto con datos del formulario
            $item = new stdClass;
            foreach ($_POST as $k => $val) {
                // Manejo de recursos seleccionados
                if ($k === 'resources' && is_array($val)) {
                    $item->resources = json_encode($val, JSON_UNESCAPED_UNICODE);

                    continue;
                }

                if (! empty($val) && $k !== 'id') {
                    $item->{$k} = htmlspecialchars(trim($val));
                }
            }

            // Datos fijos del registro
            $item->user_id = $_SESSION['id-SIGMA'];
            $item->complexity = 15;
            $item->status = 'approval';

            // Guardar registro principal
            $id = $this->model->save('recruitment', $item);

            if ($id === false) {
                http_response_code(500);
                echo json_encode(['type' => 'error', 'message' => 'Error saving recruitment']);

                return;
            }

            // Manejar archivo ZIP único
            if (! empty($_FILES['file']['name'])) {
                $tmpFilePath = $_FILES['file']['tmp_name'];

                // Validar extensión
                $fileType = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
                if ($fileType !== 'zip') {
                    http_response_code(400);
                    echo json_encode(['type' => 'error', 'message' => 'Only ZIP files are allowed']);

                    return;
                }

                // Crear carpeta del reclutamiento si no existe
                $carpeta = 'uploads/recruitment/candidates';
                if (! file_exists($carpeta)) {
                    mkdir($carpeta, 0777, true);
                }

                // Guardar el archivo siempre como cv.zip
                $destino = "$carpeta/$id.zip";
                move_uploaded_file($tmpFilePath, $destino);
            }

            // Lógica de correo original
            $email = $_POST['approver'] ?? '';
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $itemc = new stdClass;
                $itemc->to = [$email];
                $itemc->kind = 'recruitmen_review';
                $itemc->email = 'sigma@es-metals.com'; // Evita error en Model.php linea 238
                $itemc->id = $id;
                $itemc->subject = 'SIGMA - NEW Recruitment Review';

                // Estos datos se obtienen para el cuerpo del correo si los necesitas
                $profile = $this->model->get('name', 'job_profiles', ' and id = '.($_POST['profile_id'] ?? 0))->name;

                // URL de aprobación
                $approvalLink = "https://sigma.es-metals.com/sigma/?c=Recruitment&a=Review&id=$id";

                $itemc->body = "
                To review, please click on this link: <a href='$approvalLink'>Review Recruitment</a><br><br>

                Thanks!
                ";

                $this->model->sendEmail($itemc);
            }

            // Respuesta HTMX
            $message = '{"type": "success", "message": "Saved", "close": "closeNewModal"}';
            $hxTriggerData = json_encode([
                'eventChanged' => true,
                'showMessage' => $message,
            ]);
            header('HX-Trigger: '.$hxTriggerData);
            http_response_code(204);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'type' => 'error',
                'message' => 'Internal Server Error: '.$e->getMessage(),
            ]);
        }
    }

    public function ResendApproval()
    {
        try {
            $user = $this->auth->authorize(85);
            header('Content-Type: application/json');
            $email = $this->model->get('approver', 'recruitment', 'and id = '.$_REQUEST['id'])->approver;
            $id = $_REQUEST['id'];
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $itemc = new stdClass;
                $itemc->to = [$email];
                $itemc->kind = 'recruitmen_review';
                $itemc->email = 'sigma@es-metals.com';
                $itemc->id = $id;
                $itemc->subject = 'SIGMA - NEW Recruitmen Review';

                // URL de aprobación (puedes cambiarla según tu aplicación)
                $approvalLink = "https://sigma.es-metals.com/sigma/?c=Recruitment&a=Review&id=$id";

                $itemc->body = "
                To review, please click on this link: <a href='$approvalLink'>Review Recruitment</a><br><br>

                Thanks!
                ";

                $this->model->sendEmail($itemc);
            }

            // Respuesta HTMX
            $message = '{"type": "success", "message": "Sent", "close": ""}';
            $hxTriggerData = json_encode([
                'eventChanged' => true,
                'showMessage' => $message,
            ]);
            header('HX-Trigger: '.$hxTriggerData);
            http_response_code(204);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'type' => 'error',
                'message' => 'Internal Server Error: '.$e->getMessage(),
            ]);
        }
    }

    public function Update()
    {
        try {
            // 1. AUTORIZACIÓN Y CONFIGURACIÓN
            $user = $this->auth->authorize(85); // Asumiendo el mismo permiso
            header('Content-Type: application/json');

            // Obtener el ID del registro a actualizar
            $id = $_GET['id'] ?? null;
            if (! $id) {
                http_response_code(400);
                echo json_encode(['type' => 'error', 'message' => 'Recruitment ID is missing']);

                return;
            }

            // 2. PREPARACIÓN DE DATOS A ACTUALIZAR
            $item = new stdClass;
            $allowed_keys = [
                'profile_id', 'approver', 'city', 'qty', 'contract',
                'srange', 'start_date', 'cause', 'replaces', 'others',
            ];

            // Recorrer y sanitizar
            foreach ($allowed_keys as $k) {
                $value = $_POST[$k] ?? null;

                if ($value !== null) {
                    $item->{$k} = htmlspecialchars(trim($value));
                }
            }

            $item->status = 'approved';
            $item->rejection = null;

            // Manejar archivo ZIP único
            if (! empty($_FILES['file']['name'])) {
                $tmpFilePath = $_FILES['file']['tmp_name'];

                // Validar extensión
                $fileType = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
                if ($fileType !== 'zip') {
                    http_response_code(400);
                    echo json_encode(['type' => 'error', 'message' => 'Only ZIP files are allowed']);

                    return;
                }

                // Crear carpeta del reclutamiento si no existe
                $carpeta = 'uploads/recruitment/candidates';
                if (! file_exists($carpeta)) {
                    mkdir($carpeta, 0777, true);
                }

                // Guardar el archivo siempre como cv.zip
                $destino = "$carpeta/$id.zip";
                move_uploaded_file($tmpFilePath, $destino);
            }

            // 4. ACTUALIZAR REGISTRO PRINCIPAL
            $update_success = $this->model->update('recruitment', $item, $id);

            if ($update_success === false) {
                http_response_code(500);
                echo json_encode(['type' => 'error', 'message' => 'Error updating recruitment record']);

                return;
            }

            // 6. RESPUESTA HTMX (Success)
            $message = '{"type": "success", "message": "Updated", "close": "closeNewModal"}';
            $hxTriggerData = json_encode([
                'eventChanged' => true,
                'showMessage' => $message,
            ]);
            header('HX-Trigger: '.$hxTriggerData);
            http_response_code(204);

        } catch (Exception $e) {
            // 7. MANEJO DE ERRORES (Catch)
            http_response_code(500);
            echo json_encode([
                'type' => 'error',
                'message' => 'Internal Server Error: '.$e->getMessage(),
            ]);
        }
    }

    public function Review()
    {
        $id = $_REQUEST['id'] ?? null;
        if (! $id) {
            echo '
            <div style="padding: 20px; max-width: 500px; margin: 50px auto; text-align: center; font-family: sans-serif; border: 1px solid #f5c6cb; background-color: #f8d7da; color: #721c24; border-radius: 8px;">
                <h2 style="margin-top: 0;">⚠️ Candidato no encontrado</h2>
                <p>El registro que buscas no existe o fue eliminado.</p>
            </div>';

            return;
        }

        if (! empty($this->model->get('approved_at', 'recruitment', "and id = $id")->approved_at)) {
            echo "<div style='text-align:center;margin-top:40px'>
                <h2>Decisión registrada correctamente ✅</h2>
            </div>";

            return;
        }

        if (! empty($_REQUEST['id'])) {
            $filters = 'and a.id = '.$_REQUEST['id'];
            $id = $this->model->get(
                'a.*, b.name as profile, b.schedule, b.experience, c.username',
                'recruitment a',
                $filters,
                'LEFT JOIN job_profiles b on a.profile_id = b.id
                LEFT JOIN users c on a.user_id = c.id
                '
            );
        }

        include 'app/views/recruitment/review.php';
    }

    public function ReviewList()
    {
        $id = $_REQUEST['id'] ?? null;
        if (! $id) {
            echo '
            <div style="padding: 20px; max-width: 500px; margin: 50px auto; text-align: center; font-family: sans-serif; border: 1px solid #f5c6cb; background-color: #f8d7da; color: #721c24; border-radius: 8px;">
                <h2 style="margin-top: 0;">⚠️ Candidato no encontrado</h2>
                <p>El registro que buscas no existe o fue eliminado.</p>
            </div>';

            return;
        }

        if (! empty($this->model->get('candidate_list', 'recruitment_candidates', "and id = $id")->candidate_list)) {
            echo "<div style='text-align:center;margin-top:40px'>
                <h2>Decisión registrada correctamente ✅</h2>
            </div>";

            return;
        }

        $id = $this->model->get('*', 'recruitment_candidates', "and id = $id");

        include 'app/views/recruitment/review-list.php';
    }

    public function DISC()
    {
        $id = $_REQUEST['id'] ?? null;
        $id = $this->model->get('*', 'recruitment_candidates', "and id = $id");
        if (! $id) {
            echo '
            <div style="padding: 20px; max-width: 500px; margin: 50px auto; text-align: center; font-family: sans-serif; border: 1px solid #f5c6cb; background-color: #f8d7da; color: #721c24; border-radius: 8px;">
                <h2 style="margin-top: 0;">⚠️ Candidato no encontrado</h2>
                <p>El registro que buscas no existe o fue eliminado.</p>
            </div>';

            return;
        }

        if (! empty($this->model->get('disc_date', 'recruitment_candidates', "and id = $id->id")->disc_date)) {
            echo "<div style='text-align:center;margin-top:40px'>
                <h2>DISC registrada correctamente ✅</h2>
            </div>";

            return;
        }

        $mapping = [
            1 => ['D', 1], 2 => ['D', 1], 3 => ['D', 1], 4 => ['D', 1],
            5 => ['D', 1], 6 => ['D', 1], 7 => ['D', 1], 8 => ['I', 1],
            9 => ['I', 1], 10 => ['I', 1], 11 => ['I', 1], 12 => ['I', 1],
            13 => ['S', 1], 14 => ['S', 1], 15 => ['S', 1], 16 => ['S', 1],
            17 => ['S', 1], 18 => ['S', 1], 19 => ['C', 1], 20 => ['C', 1],
            21 => ['C', 1], 22 => ['C', 1], 23 => ['C', 1], 24 => ['C', 1],
            25 => ['D', 1], 26 => ['I', 1], 27 => ['S', 1], 28 => ['C', 1],
        ];

        $questions = [
            1 => 'Me jacto de mis virtudes',
            2 => 'Intento superar a los demás',
            3 => 'Siempre busco formas de ganar dinero',
            4 => 'Llamo la atención cuando alguien cuenta historias falsas o exageradas',
            5 => 'Acelero para evitar que me adelanten',
            6 => 'Exijo el reconocimiento que merezco',
            7 => 'Pongo presión a la gente',
            8 => 'Disfruto de formar parte de una multitud ruidosa',
            9 => 'Tengo un círculo muy amplio de amigos',
            10 => 'Prefiero participar plenamente en lugar de observar la vida desde la barrera',
            11 => 'Soy paciente ante los problemas de los demás',
            12 => 'Prefiero evitar los conflictos cuando es posible',
            13 => 'Me siento cómodo con la rutina',
            14 => 'Prefiero relaciones estables a cambios constantes',
            15 => 'Valoro la armonía y la cooperación',
            16 => 'Soy fiable y consistente',
            17 => 'Soy detallista y me preocupo por la precisión',
            18 => 'Me gusta seguir procesos y normas',
            19 => 'Analizo antes de actuar',
            20 => 'Me preocupo por la calidad y exactitud',
            21 => 'Prefiero pruebas y evidencias antes que suposiciones',
            22 => 'Me enfoco en hacer las cosas bien la primera vez',
            23 => 'Acepto críticas constructivas para mejorar',
            24 => 'Me esfuerzo por ser metódico en mi trabajo',
            25 => 'Me gusta liderar cuando es necesario',
            26 => 'Me comunico con facilidad y hago amigos con rapidez',
            27 => 'Valoro la paciencia y la estabilidad',
            28 => 'Me esfuerzo por ser preciso y correcto',
        ];

        function e($s)
        {
            return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }

        include 'app/views/recruitment/disc.php';
    }

    public function PF()
    {
        $id = $_REQUEST['id'] ?? null;
        $id = $this->model->get('*', 'recruitment_candidates', " and id = $id");
        if (! $id) {
            echo '
            <div style="padding: 20px; max-width: 500px; margin: 50px auto; text-align: center; font-family: sans-serif; border: 1px solid #f5c6cb; background-color: #f8d7da; color: #721c24; border-radius: 8px;">
                <h2 style="margin-top: 0;">⚠️ Candidato no encontrado</h2>
                <p>El registro que buscas no existe o fue eliminado.</p>
            </div>';

            return;
        }

        if (! empty($this->model->get('pf_date', 'recruitment_candidates', "and id = $id->id")->pf_date)) {
            echo "<div style='text-align:center;margin-top:40px'>
                <h2>PF registrada correctamente ✅</h2>
            </div>";

            return;
        }

        $preguntas = [
            // Factor A: Calidez (Afabilidad) - Afectuoso vs. Reservado
            ['id' => 'q1', 'texto' => 'Me siento cómodo hablando con extraños y haciendo nuevos amigos', 'factor' => 'A'],
            ['id' => 'q2', 'texto' => 'Me resulta fácil expresar afecto y simpatía a otras personas', 'factor' => 'A'],
            ['id' => 'q3', 'texto' => 'Generalmente prefiero estar con gente que trabajar o estudiar solo', 'factor' => 'A'],
            ['id' => 'q4', 'texto' => 'La gente me describe como una persona muy colaboradora y atenta', 'factor' => 'A'],
            ['id' => 'q5', 'texto' => 'Busco activamente la compañía y las interacciones sociales', 'factor' => 'A'],

            // Factor B: Razonamiento - Inteligencia general (El factor B es el único que NO es un rasgo de personalidad pura, sino de habilidad)
            ['id' => 'q6', 'texto' => 'Encuentro satisfacción en resolver problemas lógicos o matemáticos complejos', 'factor' => 'B'],
            ['id' => 'q7', 'texto' => 'Me resulta fácil entender ideas complejas o abstractas de forma rápida', 'factor' => 'B'],
            ['id' => 'q8', 'texto' => 'Cuando leo, suelo reflexionar sobre las implicaciones más profundas del texto', 'factor' => 'B'],
            ['id' => 'q9', 'texto' => 'Tengo buena memoria para los detalles y la información objetiva', 'factor' => 'B'],
            ['id' => 'q10', 'texto' => 'Mi mente trabaja de forma analítica y le gusta desglosar la información', 'factor' => 'B'],

            // Factor C: Estabilidad Emocional - Maduro vs. Reactivo
            ['id' => 'q11', 'texto' => 'Generalmente mantengo la calma ante situaciones de gran presión o crisis', 'factor' => 'C'],
            ['id' => 'q12', 'texto' => 'Soy capaz de recuperarme rápidamente de los contratiempos emocionales', 'factor' => 'C'],
            ['id' => 'q13', 'texto' => 'Me considero una persona emocionalmente estable y realista', 'factor' => 'C'],
            ['id' => 'q14', 'texto' => 'Rara vez me siento frustrado o alterado por eventos menores', 'factor' => 'C'],
            ['id' => 'q15', 'texto' => 'Mis cambios de humor son poco frecuentes y no son extremos', 'factor' => 'C'],

            // Factor E: Dominancia - Asertivo vs. Deferente
            ['id' => 'q16', 'texto' => 'Me gusta asumir el liderazgo en un grupo o tomar la iniciativa', 'factor' => 'E'],
            ['id' => 'q17', 'texto' => 'Suelo expresar mis opiniones firmemente, incluso si generan debate', 'factor' => 'E'],
            ['id' => 'q18', 'texto' => 'Me considero competitivo y me esfuerzo por ganar o tener éxito', 'factor' => 'E'],
            ['id' => 'q19', 'texto' => 'No me siento incómodo al tener que dar órdenes o dirigir a otros', 'factor' => 'E'],
            ['id' => 'q20', 'texto' => 'Tiendo a tomar decisiones sin depender del consenso del grupo', 'factor' => 'E'],

            // Factor F: Animación (Impulsividad/Vivacidad) - Entusiasta vs. Serio
            ['id' => 'q21', 'texto' => 'Me considero una persona muy alegre, entusiasta y optimista', 'factor' => 'F'],
            ['id' => 'q22', 'texto' => 'Suelo ser espontáneo e improvisar en mis planes sin pensarlo mucho', 'factor' => 'F'],
            ['id' => 'q23', 'texto' => 'Me gusta que mis actividades sean rápidas, dinámicas y emocionantes', 'factor' => 'F'],
            ['id' => 'q24', 'texto' => 'Raramente me siento aburrido o con ganas de estar haciendo algo más', 'factor' => 'F'],
            ['id' => 'q25', 'texto' => 'En reuniones sociales, mi presencia se nota y soy bastante conversador', 'factor' => 'F'],

            // Factor G: Atención a las Normas - Concienzudo vs. Inconformista
            ['id' => 'q26', 'texto' => 'Me preocupa mucho seguir las reglas, la ética y los estándares establecidos', 'factor' => 'G'],
            ['id' => 'q27', 'texto' => 'Cumplo rigurosamente mis obligaciones y responsabilidades con formalidad', 'factor' => 'G'],
            ['id' => 'q28', 'texto' => 'Me molesta mucho la falta de disciplina y la irresponsabilidad en otros', 'factor' => 'G'],
            ['id' => 'q29', 'texto' => 'Considero que es importante mantener el orden y la puntualidad', 'factor' => 'G'],
            ['id' => 'q30', 'texto' => 'Tiendo a ser perfeccionista en mi trabajo y detesto los errores', 'factor' => 'G'],

            // Factor H: Atrevimiento Social - Audaz vs. Tímido
            ['id' => 'q31', 'texto' => 'Me siento seguro y desinhibido al hablar en público o ante desconocidos', 'factor' => 'H'],
            ['id' => 'q32', 'texto' => 'Acepto riesgos o desafíos sociales sin sentirme muy ansioso', 'factor' => 'H'],
            ['id' => 'q33', 'texto' => 'Me gusta participar en eventos sociales grandes y ruidosos', 'factor' => 'H'],
            ['id' => 'q34', 'texto' => 'Tengo un alto nivel de tolerancia a la crítica o el rechazo social', 'factor' => 'H'],
            ['id' => 'q35', 'texto' => 'Raramente experimento vergüenza o timidez en situaciones nuevas', 'factor' => 'H'],

            // Factor I: Sensibilidad - Tierno vs. Objetivo
            ['id' => 'q36', 'texto' => 'Me afectan profundamente los sentimientos o problemas de otras personas', 'factor' => 'I'],
            ['id' => 'q37', 'texto' => 'Disfruto mucho de las actividades artísticas, poéticas o estéticas', 'factor' => 'I'],
            ['id' => 'q38', 'texto' => 'Me conmueven fácilmente las historias o películas sentimentales', 'factor' => 'I'],
            ['id' => 'q39', 'texto' => 'Suelo tomar decisiones basándome en mis sentimientos e intuición', 'factor' => 'I'],
            ['id' => 'q40', 'texto' => 'Tengo una gran necesidad de armonía y belleza en mi entorno', 'factor' => 'I'],

            // Factor L: Vigilancia (Suspicacia) - Desconfiado vs. Confiado
            ['id' => 'q41', 'texto' => 'Suelo sospechar de las motivaciones ocultas o intenciones de los demás', 'factor' => 'L'],
            ['id' => 'q42', 'texto' => 'Me cuesta confiar completamente en la gente a la primera, soy precavido', 'factor' => 'L'],
            ['id' => 'q43', 'texto' => 'Encuentro defectos en muchas personas que otros admiran', 'factor' => 'L'],
            ['id' => 'q44', 'texto' => 'Creo que la gente suele aprovecharse de los demás si se les da la oportunidad', 'factor' => 'L'],
            ['id' => 'q45', 'texto' => 'Raramente acepto un favor sin preguntarme qué esperan a cambio', 'factor' => 'L'],

            // Factor M: Abstracción (Imaginación) - Imaginativo vs. Práctico
            ['id' => 'q46', 'texto' => 'Me pierdo fácilmente en mis pensamientos, ensoñaciones e ideas abstractas', 'factor' => 'M'],
            ['id' => 'q47', 'texto' => 'Prefiero la teoría, la fantasía o la imaginación sobre lo práctico y concreto', 'factor' => 'M'],
            ['id' => 'q48', 'texto' => 'A menudo olvido pequeños detalles porque estoy pensando en otra cosa', 'factor' => 'M'],
            ['id' => 'q49', 'texto' => 'Disfruto pensando en el futuro o en escenarios hipotéticos y creativos', 'factor' => 'M'],
            ['id' => 'q50', 'texto' => 'Soy más un idealista que una persona de acción inmediata', 'factor' => 'M'],

            // Factor N: Privacidad (Astucia) - Discreto vs. Genuino
            ['id' => 'q51', 'texto' => 'Soy muy reservado y guardo mis pensamientos y planes para mí mismo', 'factor' => 'N'],
            ['id' => 'q52', 'texto' => 'Puedo ser diplomático y estratégico para conseguir mis metas, sin mostrar todas mis cartas', 'factor' => 'N'],
            ['id' => 'q53', 'texto' => 'La gente suele sorprenderse al descubrir lo que realmente pienso o siento', 'factor' => 'N'],
            ['id' => 'q54', 'texto' => 'No me gusta la espontaneidad o la apertura excesiva con los demás', 'factor' => 'N'],
            ['id' => 'q55', 'texto' => 'Tengo cuidado en cómo me presento y lo que revelo sobre mí', 'factor' => 'N'],

            // Factor O: Aprensión (Culpabilidad) - Inseguro vs. Sereno
            ['id' => 'q56', 'texto' => 'Me preocupan mucho mis errores pasados y tiendo a sentirme culpable', 'factor' => 'O'],
            ['id' => 'q57', 'texto' => 'Tengo sentimientos frecuentes de ansiedad, inseguridad o miedo', 'factor' => 'O'],
            ['id' => 'q58', 'texto' => 'A menudo me despierto sintiéndome angustiado o preocupado por algo', 'factor' => 'O'],
            ['id' => 'q59', 'texto' => 'Soy sensible a la crítica y tiendo a tomármela muy a pecho', 'factor' => 'O'],
            ['id' => 'q60', 'texto' => 'Siento que debería haber hecho las cosas de otra manera en el pasado', 'factor' => 'O'],

            // Factor Q1: Apertura al Cambio - Experimentador vs. Conservador
            ['id' => 'q61', 'texto' => 'Estoy siempre dispuesto a probar cosas nuevas y diferentes en mi vida', 'factor' => 'Q1'],
            ['id' => 'q62', 'texto' => "Considero que es importante cuestionar la autoridad, la tradición y el 'status quo'", 'factor' => 'Q1'],
            ['id' => 'q63', 'texto' => 'Busco activamente nuevas maneras de hacer las cosas, incluso si son arriesgadas', 'factor' => 'Q1'],
            ['id' => 'q64', 'texto' => 'Disfruto leyendo o discutiendo sobre temas intelectuales y filosóficos', 'factor' => 'Q1'],
            ['id' => 'q65', 'texto' => 'Me gusta estar al día con las nuevas tecnologías y tendencias', 'factor' => 'Q1'],

            // Factor Q2: Autosuficiencia - Independiente vs. Dependiente
            ['id' => 'q66', 'texto' => 'Prefiero tomar mis propias decisiones sin la ayuda o el consejo de nadie', 'factor' => 'Q2'],
            ['id' => 'q67', 'texto' => 'Me siento cómodo pasando largos periodos de tiempo en soledad', 'factor' => 'Q2'],
            ['id' => 'q68', 'texto' => 'Raramente necesito la aprobación o el apoyo emocional de otros', 'factor' => 'Q2'],
            ['id' => 'q69', 'texto' => 'En un grupo, me gusta tener mi propio camino y no seguir a la mayoría', 'factor' => 'Q2'],
            ['id' => 'q70', 'texto' => 'No me importa ser diferente a mis amigos o colegas', 'factor' => 'Q2'],

            // Factor Q3: Perfeccionismo (Controlado) - Controlado vs. Informal
            ['id' => 'q71', 'texto' => 'Soy muy organizado, disciplinado y busco la perfección en lo que hago', 'factor' => 'Q3'],
            ['id' => 'q72', 'texto' => 'Me esfuerzo por mantener un alto grado de autocontrol sobre mis impulsos', 'factor' => 'Q3'],
            ['id' => 'q73', 'texto' => 'Mi vida diaria sigue un horario o un sistema bien estructurado', 'factor' => 'Q3'],
            ['id' => 'q74', 'texto' => 'Dedico tiempo a asegurarme de que mi apariencia o trabajo está impecable', 'factor' => 'Q3'],
            ['id' => 'q75', 'texto' => 'Es importante para mí mantener una imagen pulcra y de buen comportamiento', 'factor' => 'Q3'],

            // Factor Q4: Tensión (Impaciencia) - Tenso vs. Relajado
            ['id' => 'q76', 'texto' => 'Me siento a menudo tenso, inquieto o frustrado por no avanzar lo suficiente', 'factor' => 'Q4'],
            ['id' => 'q77', 'texto' => 'Me resulta difícil relajarme completamente, incluso cuando no hay nada que hacer', 'factor' => 'Q4'],
            ['id' => 'q78', 'texto' => 'Siento una necesidad constante de estar ocupado o en movimiento', 'factor' => 'Q4'],
            ['id' => 'q79', 'texto' => 'Soy fácilmente irritable o impaciente con la lentitud o la ineficacia', 'factor' => 'Q4'],
            ['id' => 'q80', 'texto' => 'A menudo siento mi energía y mi pulso acelerados por el estrés', 'factor' => 'Q4'],
        ];

        include 'app/views/recruitment/pf.php';
    }

    public function CISDSave()
    {
        $id = $_POST['id'] ?? null;
        $item = new stdClass;

        $answers = [];

        // Capturar las 28 respuestas
        for ($i = 1; $i <= 28; $i++) {
            $answers["q$i"] = $_POST["q$i"] ?? null;
        }

        // Convertir a JSON
        $json = json_encode($answers, JSON_UNESCAPED_UNICODE);

        $item->disc_answers = $json;
        $item->disc_date = date('Y-m-d H:i:s');

        $this->model->update('recruitment_candidates', $item, $id);

        echo "<div style='text-align:center;margin-top:40px'>
            <h2>Disc registrada correctamente ✅</h2>
        </div>";
    }

    public function PFSave()
    {
        $id = $_POST['id'] ?? null;
        if (! $id) {
            exit('ID del candidato no recibido.');
        }

        $item = new stdClass;
        $answers = [];

        // Recorrer todos los campos POST (excepto 'id')
        foreach ($_POST as $key => $value) {
            if ($key !== 'id') {
                // Guarda usando el ID de la pregunta como clave
                $answers[$key] = $value;
            }
        }

        // Convertir respuestas a JSON
        $json = json_encode($answers, JSON_UNESCAPED_UNICODE);

        // Guardar en la base de datos
        $item->pf_answers = $json; // puedes llamar este campo como gustes
        $item->pf_date = date('Y-m-d H:i:s');

        $this->model->update('recruitment_candidates', $item, $id);

        echo "
        <div style='text-align:center;margin-top:40px;font-family:sans-serif'>
            <h2>✅ Test PF registrado correctamente</h2>
        </div>";
    }

    public function DiscResult()
    {

        $id = $this->model->get('*', 'recruitment_candidates', 'and id = '.$_REQUEST['id']);
        $answersJson = $id->disc_answers;

        $errors = empty($answersJson) ? true : false;

        // Mapeo de las preguntas hacia las dimensiones y dirección (+ o -)
        // Debes tener este array definido según tu test DISC
        $mapping = [
            1 => ['D', 1], 2 => ['I', 1], 3 => ['S', 1], 4 => ['C', 1],
            5 => ['D', -1], 6 => ['I', -1], 7 => ['S', -1], 8 => ['C', -1],
            9 => ['D', 1], 10 => ['I', 1], 11 => ['S', 1], 12 => ['C', 1],
            13 => ['D', -1], 14 => ['I', -1], 15 => ['S', -1], 16 => ['C', -1],
            17 => ['D', 1], 18 => ['I', 1], 19 => ['S', 1], 20 => ['C', 1],
            21 => ['D', -1], 22 => ['I', -1], 23 => ['S', -1], 24 => ['C', -1],
            25 => ['D', 1], 26 => ['I', 1], 27 => ['S', 1], 28 => ['C', 1],
        ];

        // Si viene en formato JSON (por ejemplo, desde la base de datos)
        if ($answersJson) {
            $decoded = json_decode($answersJson, true);
            if (is_array($decoded)) {
                $answers = [];
                for ($i = 1; $i <= 28; $i++) {
                    $val = isset($decoded["q$i"]) ? intval($decoded["q$i"]) : 0;
                    if ($val < 1 || $val > 5) {
                        $errors[] = "Falta responder la pregunta $i.";
                    }
                    $answers[$i] = $val;
                }
            } else {
                $errors[] = 'Error al decodificar las respuestas JSON.';
            }
        }

        // Calcular si no hay errores
        if (! $errors) {
            $sums = ['D' => 0, 'I' => 0, 'S' => 0, 'C' => 0];
            $counts = ['D' => 0, 'I' => 0, 'S' => 0, 'C' => 0];

            foreach ($answers as $q => $val) {
                if (! isset($mapping[$q])) {
                    continue;
                }
                [$dim, $dir] = $mapping[$q];
                $score = $dir === -1 ? (6 - $val) : $val;
                $sums[$dim] += $score;
                $counts[$dim]++;
            }

            $percent = [];
            foreach ($sums as $dim => $sum) {
                $max = $counts[$dim] * 5;
                $min = $counts[$dim] * 1;
                $percent[$dim] = $max == 0 ? 0 : round(($sum - $min) / ($max - $min) * 100);
            }

            $results = ['percent' => $percent];
        }
        require_once 'app/views/recruitment/detail/tabs/disc_panel.php';
    }

    public function PFResult()
    {
        $preguntas = [
            // Factor A: Calidez (Afabilidad) - Afectuoso vs. Reservado
            ['id' => 'q1', 'texto' => 'Me siento cómodo hablando con extraños y haciendo nuevos amigos', 'factor' => 'A'],
            ['id' => 'q2', 'texto' => 'Me resulta fácil expresar afecto y simpatía a otras personas', 'factor' => 'A'],
            ['id' => 'q3', 'texto' => 'Generalmente prefiero estar con gente que trabajar o estudiar solo', 'factor' => 'A'],
            ['id' => 'q4', 'texto' => 'La gente me describe como una persona muy colaboradora y atenta', 'factor' => 'A'],
            ['id' => 'q5', 'texto' => 'Busco activamente la compañía y las interacciones sociales', 'factor' => 'A'],

            // Factor B: Razonamiento - Inteligencia general (El factor B es el único que NO es un rasgo de personalidad pura, sino de habilidad)
            ['id' => 'q6', 'texto' => 'Encuentro satisfacción en resolver problemas lógicos o matemáticos complejos', 'factor' => 'B'],
            ['id' => 'q7', 'texto' => 'Me resulta fácil entender ideas complejas o abstractas de forma rápida', 'factor' => 'B'],
            ['id' => 'q8', 'texto' => 'Cuando leo, suelo reflexionar sobre las implicaciones más profundas del texto', 'factor' => 'B'],
            ['id' => 'q9', 'texto' => 'Tengo buena memoria para los detalles y la información objetiva', 'factor' => 'B'],
            ['id' => 'q10', 'texto' => 'Mi mente trabaja de forma analítica y le gusta desglosar la información', 'factor' => 'B'],

            // Factor C: Estabilidad Emocional - Maduro vs. Reactivo
            ['id' => 'q11', 'texto' => 'Generalmente mantengo la calma ante situaciones de gran presión o crisis', 'factor' => 'C'],
            ['id' => 'q12', 'texto' => 'Soy capaz de recuperarme rápidamente de los contratiempos emocionales', 'factor' => 'C'],
            ['id' => 'q13', 'texto' => 'Me considero una persona emocionalmente estable y realista', 'factor' => 'C'],
            ['id' => 'q14', 'texto' => 'Rara vez me siento frustrado o alterado por eventos menores', 'factor' => 'C'],
            ['id' => 'q15', 'texto' => 'Mis cambios de humor son poco frecuentes y no son extremos', 'factor' => 'C'],

            // Factor E: Dominancia - Asertivo vs. Deferente
            ['id' => 'q16', 'texto' => 'Me gusta asumir el liderazgo en un grupo o tomar la iniciativa', 'factor' => 'E'],
            ['id' => 'q17', 'texto' => 'Suelo expresar mis opiniones firmemente, incluso si generan debate', 'factor' => 'E'],
            ['id' => 'q18', 'texto' => 'Me considero competitivo y me esfuerzo por ganar o tener éxito', 'factor' => 'E'],
            ['id' => 'q19', 'texto' => 'No me siento incómodo al tener que dar órdenes o dirigir a otros', 'factor' => 'E'],
            ['id' => 'q20', 'texto' => 'Tiendo a tomar decisiones sin depender del consenso del grupo', 'factor' => 'E'],

            // Factor F: Animación (Impulsividad/Vivacidad) - Entusiasta vs. Serio
            ['id' => 'q21', 'texto' => 'Me considero una persona muy alegre, entusiasta y optimista', 'factor' => 'F'],
            ['id' => 'q22', 'texto' => 'Suelo ser espontáneo e improvisar en mis planes sin pensarlo mucho', 'factor' => 'F'],
            ['id' => 'q23', 'texto' => 'Me gusta que mis actividades sean rápidas, dinámicas y emocionantes', 'factor' => 'F'],
            ['id' => 'q24', 'texto' => 'Raramente me siento aburrido o con ganas de estar haciendo algo más', 'factor' => 'F'],
            ['id' => 'q25', 'texto' => 'En reuniones sociales, mi presencia se nota y soy bastante conversador', 'factor' => 'F'],

            // Factor G: Atención a las Normas - Concienzudo vs. Inconformista
            ['id' => 'q26', 'texto' => 'Me preocupa mucho seguir las reglas, la ética y los estándares establecidos', 'factor' => 'G'],
            ['id' => 'q27', 'texto' => 'Cumplo rigurosamente mis obligaciones y responsabilidades con formalidad', 'factor' => 'G'],
            ['id' => 'q28', 'texto' => 'Me molesta mucho la falta de disciplina y la irresponsabilidad en otros', 'factor' => 'G'],
            ['id' => 'q29', 'texto' => 'Considero que es importante mantener el orden y la puntualidad', 'factor' => 'G'],
            ['id' => 'q30', 'texto' => 'Tiendo a ser perfeccionista en mi trabajo y detesto los errores', 'factor' => 'G'],

            // Factor H: Atrevimiento Social - Audaz vs. Tímido
            ['id' => 'q31', 'texto' => 'Me siento seguro y desinhibido al hablar en público o ante desconocidos', 'factor' => 'H'],
            ['id' => 'q32', 'texto' => 'Acepto riesgos o desafíos sociales sin sentirme muy ansioso', 'factor' => 'H'],
            ['id' => 'q33', 'texto' => 'Me gusta participar en eventos sociales grandes y ruidosos', 'factor' => 'H'],
            ['id' => 'q34', 'texto' => 'Tengo un alto nivel de tolerancia a la crítica o el rechazo social', 'factor' => 'H'],
            ['id' => 'q35', 'texto' => 'Raramente experimento vergüenza o timidez en situaciones nuevas', 'factor' => 'H'],

            // Factor I: Sensibilidad - Tierno vs. Objetivo
            ['id' => 'q36', 'texto' => 'Me afectan profundamente los sentimientos o problemas de otras personas', 'factor' => 'I'],
            ['id' => 'q37', 'texto' => 'Disfruto mucho de las actividades artísticas, poéticas o estéticas', 'factor' => 'I'],
            ['id' => 'q38', 'texto' => 'Me conmueven fácilmente las historias o películas sentimentales', 'factor' => 'I'],
            ['id' => 'q39', 'texto' => 'Suelo tomar decisiones basándome en mis sentimientos e intuición', 'factor' => 'I'],
            ['id' => 'q40', 'texto' => 'Tengo una gran necesidad de armonía y belleza en mi entorno', 'factor' => 'I'],

            // Factor L: Vigilancia (Suspicacia) - Desconfiado vs. Confiado
            ['id' => 'q41', 'texto' => 'Suelo sospechar de las motivaciones ocultas o intenciones de los demás', 'factor' => 'L'],
            ['id' => 'q42', 'texto' => 'Me cuesta confiar completamente en la gente a la primera, soy precavido', 'factor' => 'L'],
            ['id' => 'q43', 'texto' => 'Encuentro defectos en muchas personas que otros admiran', 'factor' => 'L'],
            ['id' => 'q44', 'texto' => 'Creo que la gente suele aprovecharse de los demás si se les da la oportunidad', 'factor' => 'L'],
            ['id' => 'q45', 'texto' => 'Raramente acepto un favor sin preguntarme qué esperan a cambio', 'factor' => 'L'],

            // Factor M: Abstracción (Imaginación) - Imaginativo vs. Práctico
            ['id' => 'q46', 'texto' => 'Me pierdo fácilmente en mis pensamientos, ensoñaciones e ideas abstractas', 'factor' => 'M'],
            ['id' => 'q47', 'texto' => 'Prefiero la teoría, la fantasía o la imaginación sobre lo práctico y concreto', 'factor' => 'M'],
            ['id' => 'q48', 'texto' => 'A menudo olvido pequeños detalles porque estoy pensando en otra cosa', 'factor' => 'M'],
            ['id' => 'q49', 'texto' => 'Disfruto pensando en el futuro o en escenarios hipotéticos y creativos', 'factor' => 'M'],
            ['id' => 'q50', 'texto' => 'Soy más un idealista que una persona de acción inmediata', 'factor' => 'M'],

            // Factor N: Privacidad (Astucia) - Discreto vs. Genuino
            ['id' => 'q51', 'texto' => 'Soy muy reservado y guardo mis pensamientos y planes para mí mismo', 'factor' => 'N'],
            ['id' => 'q52', 'texto' => 'Puedo ser diplomático y estratégico para conseguir mis metas, sin mostrar todas mis cartas', 'factor' => 'N'],
            ['id' => 'q53', 'texto' => 'La gente suele sorprenderse al descubrir lo que realmente pienso o siento', 'factor' => 'N'],
            ['id' => 'q54', 'texto' => 'No me gusta la espontaneidad o la apertura excesiva con los demás', 'factor' => 'N'],
            ['id' => 'q55', 'texto' => 'Tengo cuidado en cómo me presento y lo que revelo sobre mí', 'factor' => 'N'],

            // Factor O: Aprensión (Culpabilidad) - Inseguro vs. Sereno
            ['id' => 'q56', 'texto' => 'Me preocupan mucho mis errores pasados y tiendo a sentirme culpable', 'factor' => 'O'],
            ['id' => 'q57', 'texto' => 'Tengo sentimientos frecuentes de ansiedad, inseguridad o miedo', 'factor' => 'O'],
            ['id' => 'q58', 'texto' => 'A menudo me despierto sintiéndome angustiado o preocupado por algo', 'factor' => 'O'],
            ['id' => 'q59', 'texto' => 'Soy sensible a la crítica y tiendo a tomármela muy a pecho', 'factor' => 'O'],
            ['id' => 'q60', 'texto' => 'Siento que debería haber hecho las cosas de otra manera en el pasado', 'factor' => 'O'],

            // Factor Q1: Apertura al Cambio - Experimentador vs. Conservador
            ['id' => 'q61', 'texto' => 'Estoy siempre dispuesto a probar cosas nuevas y diferentes en mi vida', 'factor' => 'Q1'],
            ['id' => 'q62', 'texto' => "Considero que es importante cuestionar la autoridad, la tradición y el 'status quo'", 'factor' => 'Q1'],
            ['id' => 'q63', 'texto' => 'Busco activamente nuevas maneras de hacer las cosas, incluso si son arriesgadas', 'factor' => 'Q1'],
            ['id' => 'q64', 'texto' => 'Disfruto leyendo o discutiendo sobre temas intelectuales y filosóficos', 'factor' => 'Q1'],
            ['id' => 'q65', 'texto' => 'Me gusta estar al día con las nuevas tecnologías y tendencias', 'factor' => 'Q1'],

            // Factor Q2: Autosuficiencia - Independiente vs. Dependiente
            ['id' => 'q66', 'texto' => 'Prefiero tomar mis propias decisiones sin la ayuda o el consejo de nadie', 'factor' => 'Q2'],
            ['id' => 'q67', 'texto' => 'Me siento cómodo pasando largos periodos de tiempo en soledad', 'factor' => 'Q2'],
            ['id' => 'q68', 'texto' => 'Raramente necesito la aprobación o el apoyo emocional de otros', 'factor' => 'Q2'],
            ['id' => 'q69', 'texto' => 'En un grupo, me gusta tener mi propio camino y no seguir a la mayoría', 'factor' => 'Q2'],
            ['id' => 'q70', 'texto' => 'No me importa ser diferente a mis amigos o colegas', 'factor' => 'Q2'],

            // Factor Q3: Perfeccionismo (Controlado) - Controlado vs. Informal
            ['id' => 'q71', 'texto' => 'Soy muy organizado, disciplinado y busco la perfección en lo que hago', 'factor' => 'Q3'],
            ['id' => 'q72', 'texto' => 'Me esfuerzo por mantener un alto grado de autocontrol sobre mis impulsos', 'factor' => 'Q3'],
            ['id' => 'q73', 'texto' => 'Mi vida diaria sigue un horario o un sistema bien estructurado', 'factor' => 'Q3'],
            ['id' => 'q74', 'texto' => 'Dedico tiempo a asegurarme de que mi apariencia o trabajo está impecable', 'factor' => 'Q3'],
            ['id' => 'q75', 'texto' => 'Es importante para mí mantener una imagen pulcra y de buen comportamiento', 'factor' => 'Q3'],

            // Factor Q4: Tensión (Impaciencia) - Tenso vs. Relajado
            ['id' => 'q76', 'texto' => 'Me siento a menudo tenso, inquieto o frustrado por no avanzar lo suficiente', 'factor' => 'Q4'],
            ['id' => 'q77', 'texto' => 'Me resulta difícil relajarme completamente, incluso cuando no hay nada que hacer', 'factor' => 'Q4'],
            ['id' => 'q78', 'texto' => 'Siento una necesidad constante de estar ocupado o en movimiento', 'factor' => 'Q4'],
            ['id' => 'q79', 'texto' => 'Soy fácilmente irritable o impaciente con la lentitud o la ineficacia', 'factor' => 'Q4'],
            ['id' => 'q80', 'texto' => 'A menudo siento mi energía y mi pulso acelerados por el estrés', 'factor' => 'Q4'],
        ];

        $factores_info = [
            'A' => ['nombre' => 'Calidez (Afabilidad)', 'descripcion' => 'Refleja cómo te relacionas: afectuoso y cercano (Alto) o más reservado (Bajo).'],
            'B' => ['nombre' => 'Razonamiento', 'descripcion' => 'Capacidad de pensamiento lógico, abstracto y de resolución de problemas.'],
            'C' => ['nombre' => 'Estabilidad Emocional', 'descripcion' => 'Cómo manejas el estrés: sereno y maduro (Alto) o reactivo y cambiante (Bajo).'],
            'E' => ['nombre' => 'Dominancia', 'descripcion' => 'Tendencia a liderar: asertivo e independiente (Alto) o deferente y sumiso (Bajo).'],
            'F' => ['nombre' => 'Animación (Impulsividad)', 'descripcion' => 'Grado de espontaneidad: entusiasta y sociable (Alto) o serio y prudente (Bajo).'],
            'G' => ['nombre' => 'Atención a las Normas', 'descripcion' => 'Adherencia a reglas: concienzudo y formal (Alto) o inconformista e indulgente (Bajo).'],
            'H' => ['nombre' => 'Atrevimiento Social', 'descripcion' => 'Confianza en interacciones: audaz y emprendedor (Alto) o tímido y cohibido (Bajo).'],
            'I' => ['nombre' => 'Sensibilidad', 'descripcion' => 'Orientación hacia las emociones: tierno y empático (Alto) o realista y objetivo (Bajo).'],
            'L' => ['nombre' => 'Vigilancia (Suspicacia)', 'descripcion' => 'Grado de confianza: suspicaz y escéptico (Alto) o confiado y adaptable (Bajo).'],
            'M' => ['nombre' => 'Abstracción (Imaginación)', 'descripcion' => 'Foco: orientado a ideas y la fantasía (Alto) o práctico y con los pies en la tierra (Bajo).'],
            'N' => ['nombre' => 'Privacidad (Astucia)', 'descripcion' => 'Grado de apertura: discreto y astuto (Alto) o abierto y genuino (Bajo).'],
            'O' => ['nombre' => 'Aprensión (Culpabilidad)', 'descripcion' => 'Grado de preocupación: inseguro y ansioso (Alto) o sereno y seguro de sí mismo (Bajo).'],
            'Q1' => ['nombre' => 'Apertura al Cambio', 'descripcion' => 'Flexibilidad y actitud innovadora: experimentador (Alto) o conservador (Bajo).'],
            'Q2' => ['nombre' => 'Autosuficiencia', 'descripcion' => 'Necesidad de apoyo: independiente y solitario (Alto) o gregario y dependiente (Bajo).'],
            'Q3' => ['nombre' => 'Perfeccionismo (Controlado)', 'descripcion' => 'Organización y autocontrol: disciplinado y meticuloso (Alto) o flexible e informal (Bajo).'],
            'Q4' => ['nombre' => 'Tensión (Impaciencia)', 'descripcion' => 'Nivel de energía: tenso e irritable (Alto) o relajado y paciente (Bajo).'],
        ];

        $id = $this->model->get('*', 'recruitment_candidates', 'and id = '.$_REQUEST['id']);

        $respuestas = json_decode($id->pf_answers ?? '[]', true);

        $factores = [];
        foreach ($preguntas as $p) {
            $f = $p['factor'];
            $qid = $p['id'];
            $valor = isset($respuestas[$qid]) ? (float) $respuestas[$qid] : null;
            if ($valor !== null) {
                if (! isset($factores[$f])) {
                    $factores[$f] = [];
                }
                $factores[$f][] = $valor;
            }
        }

        $resultados = [];
        foreach ($factores as $f => $vals) {
            if (count($vals) > 0) {
                $promedio = array_sum($vals) / count($vals);
                if ($promedio < 1.7) {
                    $nivel = 'Bajo';
                } elseif ($promedio < 2.3) {
                    $nivel = 'Medio';
                } else {
                    $nivel = 'Alto';
                }

                $resultados[$f] = [
                    'prom' => round($promedio, 2),
                    'nivel' => $nivel,
                    'nombre' => $factores_info[$f]['nombre'],
                    'desc' => $factores_info[$f]['descripcion'],
                ];
            }
        }

        require_once 'app/views/recruitment/detail/tabs/pf_panel.php';
    }

    public function Contract()
    {
        $id = $this->model->get('*', 'recruitment_candidates', 'and id = '.$_REQUEST['id']);
        require_once 'app/views/recruitment/detail/tabs/contract.php';
    }

    public function ProcessDecision()
    {
        $id = $_POST['recruitmentId'] ?? null;
        $decision = $_POST['decision'] ?? null;
        $note = $_POST['note'] ?? null;

        if (! $id || ! $decision) {
            echo 'Datos incompletos.';

            return;
        }

        $item = new stdClass;

        if ($decision === 'approved') {

            $item->status = 'approved';
            $item->approved_at = date('Y-m-d H:i:s');

            // 1. Ejecutar la actualización de estado básica
            $this->model->update('recruitment', $item, $id);

            /* ========================================================
            LÓGICA DE TICKETS - STAGE 1 (SOLO AL APROBAR)
            ======================================================== */
            $currentData = $this->model->get('resources, city, qty', 'recruitment', "and id = $id");

            // Decodificamos lo que haya en resources (puede ser array de strings o de objetos)
            $selectedResources = json_decode($currentData->resources ?? '[]', true);

            if (! empty($selectedResources) && is_array($selectedResources)) {
                $groupedByTicketType = [];
                $finalTracking = [];

                foreach ($selectedResources as $resData) {
                    // NORMALIZACIÓN: Extraer el nombre sin importar si viene como string o array
                    $nameOnly = is_array($resData) ? ($resData['name'] ?? null) : $resData;
                    if (! $nameOnly) {
                        continue;
                    }

                    // Consultamos el recurso en la tabla maestra para saber su Stage
                    $resourceInfo = $this->model->get('kind, stage', 'recruitment_resources', "and name = '$nameOnly'");

                    if ($resourceInfo && $resourceInfo->stage == 1) {
                        // Agrupamos Stage 1 para crear tickets masivos por tipo (IT, Mantenimiento, etc.)
                        $groupedByTicketType[$resourceInfo->kind][] = $nameOnly;
                    } else {
                        // Es Stage 2 o no tiene stage: Se guarda como objeto pero con ticket_id NULL
                        // Esto permite que info.php lo reconozca y Stage 2 (hired) lo procese luego.
                        $finalTracking[] = [
                            'name' => $nameOnly,
                            'ticket_id' => null,
                            'kind' => $resourceInfo->kind ?? 'N/A',
                            'stage' => ($resourceInfo->stage ?? 2),
                        ];
                    }
                }

                // CREACIÓN DE TICKETS STAGE 1
                foreach ($groupedByTicketType as $type => $resourcesList) {
                    $ticket = new stdClass;
                    $ticket->facility = $currentData->city ?? 'N/A';
                    $ticket->kind = $type;
                    $ticket->priority = 'Medium';
                    $ticket->status = 'Open';
                    $ticket->user_id = $_SESSION['id-SIGMA'] ?? 0;
                    $ticket->description = "STAGE 1 (Approval) - Reclutamiento #{$id}.\n".
                                        "Cantidad Vacantes: {$currentData->qty}\n".
                                        'Recursos: '.implode(', ', $resourcesList);
                    $tablaDestino = (strtoupper($type) === 'IT') ? 'it' : 'tickets';
                    $ticketId = $this->model->save($tablaDestino, $ticket);

                    // Añadimos cada recurso de Stage 1 al tracking final con su ticket_id
                    foreach ($resourcesList as $resName) {
                        $finalTracking[] = [
                            'name' => $resName,
                            'ticket_id' => $ticketId,
                            'kind' => $type,
                            'table' => $tablaDestino,
                            'stage' => 1,
                        ];
                    }
                }

                // 2. Guardar el JSON final estructurado en la tabla recruitment
                if (! empty($finalTracking)) {
                    $updateTrack = new stdClass;
                    $updateTrack->resources = json_encode($finalTracking, JSON_UNESCAPED_UNICODE);
                    $this->model->update('recruitment', $updateTrack, $id);
                }
            }

        } elseif ($decision === 'rejected') {
            $item->rejection = $note;
            $item->status = 'rejected';
            $item->approved_at = date('Y-m-d H:i:s');
            $this->model->update('recruitment', $item, $id);
        }

        echo "<div style='text-align:center;margin-top:40px; font-family: sans-serif;'>
            <h2 style='color: #2d3748;'>Decisión registrada correctamente ✅</h2>
            <p style='color: #4a5568;'>Estado: <strong>".ucfirst($decision)."</strong></p>
            <p style='font-size: 0.9rem; color: #718096;'>Ya puedes cerrar esta ventana.</p>
        </div>";
    }

    public function ProcessDecisionList()
    {
        $id = $_POST['id'] ?? null;
        $item = new stdClass;
        $item->candidate_list = $_POST['candidate_list'];
        $item->status = ($_POST['candidate_list'] === '1') ? 'qualified' : 'list';
        $this->model->update('recruitment_candidates', $item, $id);

        echo "<div style='text-align:center;margin-top:40px'>
            <h2>Decisión registrada correctamente ✅</h2>
        </div>";
    }

    public function Detail()
    {
        $user = $this->auth->authorize(85);
        if (! empty($_REQUEST['id'])) {
            $filters = 'and id = '.$_REQUEST['id'];
            $id = $this->model->get(
                '*',
                'recruitment',
                $filters
            );
        }
        if ($id->status != 'review') {
            require_once 'app/views/recruitment/detail.php';
        } else {
            $recruitment = $id;
            require_once 'app/views/recruitment/edit.php';
        }
    }

    public function Info()
    {
        $user = $this->auth->authorize(85);
        $canEdit = ! empty(array_intersect(['152'], json_decode($user->permissions ?? '[]', true)));
        if (! empty($_REQUEST['id'])) {
            $filters = 'and a.id = '.$_REQUEST['id'];
            $id = $this->model->get(
                'a.*, b.name as profile, b.schedule, b.experience, c.username as assignee',
                'recruitment a',
                $filters,
                'LEFT JOIN job_profiles b on a.profile_id = b.id
                LEFT JOIN users c on a.assignee_id = c.id'
            );
        }
        require_once 'app/views/recruitment/detail/tabs/info.php';
    }

    public function Candidate()
    {
        $user = $this->auth->authorize(85);
        if (! empty($_REQUEST['id'])) {
            $filters = 'and id = '.$_REQUEST['id'];
            $id = $this->model->get(
                '*',
                'recruitment_candidates',
                $filters,
            );
        }
        require_once 'app/views/recruitment/candidate.php';
    }

    public function CandidateInfo()
    {
        $user = $this->auth->authorize(85);
        if (! empty($_REQUEST['id'])) {
            $filters = 'and a.id = '.$_REQUEST['id'];
            $id = $this->model->get(
                'a.*',
                'recruitment_candidates a',
                $filters,
            );
        }
        require_once 'app/views/recruitment/detail/tabs/cinfo.php';
    }

    public function Tabs()
    {
        $user = $this->auth->authorize(85);
        if (! empty($_REQUEST['id'])) {
            $filters = 'and a.id = '.$_REQUEST['id'];
            $id = $this->model->get(
                'a.*',
                'recruitment_candidates a',
                $filters,
            );
        }
        require_once 'app/views/recruitment/tabs.php';
    }

    public function CV()
    {
        // 1. Validamos que llegue el ID
        if (! empty($_REQUEST['id'])) {
            $candidate_id = intval($_REQUEST['id']);
            $filters = 'and a.id = '.$candidate_id;

            // 2. IMPORTANTE: La variable DEBE llamarse $id
            // para que la vista la reconozca (líneas 66, 112, 582, etc.)
            $id = $this->model->get(
                'a.*',
                'recruitment_candidates a',
                $filters
            );

            // 3. Verificamos que el query trajo algo
            if ($id) {
                require_once 'app/views/recruitment/detail/tabs/candidate_cv.php';

                return;
            }
        }

        echo '
    <div style="padding: 20px; max-width: 500px; margin: 50px auto; text-align: center; font-family: sans-serif; border: 1px solid #f5c6cb; background-color: #f8d7da; color: #721c24; border-radius: 8px;">
        <h2 style="margin-top: 0;">⚠️ Candidato no encontrado</h2>
        <p>El registro que buscas no existe o fue eliminado.</p>
    </div>';
    }

    public function DetailCandidate()
    {
        $user = $this->auth->authorize(85);
        $tab = $_REQUEST['tab'];
        $type = $_REQUEST['kind'] ?? 0;
        $filters = 'and a.id = '.$_REQUEST['id'];
        $id = $this->model->get('a.*,b.profile_id', 'recruitment_candidates a', $filters, 'LEFT JOIN recruitment b on a.recruitment_id = b.id');
        $data = (! empty($this->model->get('content', 'recruitment_items', " and recruitment_id = $id->id AND kind = '$type'")->content))
        ? $this->model->get('content', 'recruitment_items', " and recruitment_id = $id->id AND kind = '$type'")->content
        : '[]';
        require_once "app/views/recruitment/detail/tabs/$tab.php";
    }

    public function DetailTab()
    {
        $user = $this->auth->authorize(85);
        $tab = $_REQUEST['tab'];
        $filters = 'and id = '.$_REQUEST['id'];
        $id = $this->model->get('*', 'recruitment', $filters);
        require_once "app/views/recruitment/detail/tabs/$tab.php";
    }

    public function DetailModal()
    {
        $user = $this->auth->authorize(85);
        $modal = $_REQUEST['modal'];
        $filters = 'and id = '.$_REQUEST['id'];
        $id = $this->model->get('*', 'recruitment', $filters);
        require_once "app/views/recruitment/detail/modals/$modal.php";
    }

    public function DataCandidates()
    {
        $user = $this->auth->authorize(85);
        header('Content-Type: application/json');

        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $size = isset($_GET['size']) ? (int) $_GET['size'] : 15;
        $offset = ($page - 1) * $size;

        $fieldMap = [
            'id' => 'a.id',
            'type' => 'a.kind',
            'user' => 'c.username',
            'user_id' => 'a.user_id',
            'status' => 'a.status',
            'name' => 'a.name',
            'email' => 'a.email',
            'phone' => 'a.phone',
            'cc' => 'a.cc',
            'appointment' => 'a.appointment',
        ];

        // Filtro básico por requisitionId
        $where = 'AND a.recruitment_id = '.intval($_REQUEST['id'] ?? 0);

        // Filtros dinámicos
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
                $where .= ' AND '.$fieldMap[$field]." LIKE '%$value%'";
            }
        }

        // Ordenamiento
        $orderBy = 'a.id DESC';
        if (isset($_GET['sort'][0]['field'], $_GET['sort'][0]['dir'])) {
            $sortField = $_GET['sort'][0]['field'];
            $sortDir = strtoupper($_GET['sort'][0]['dir']) === 'ASC' ? 'ASC' : 'DESC';
            if (isset($fieldMap[$sortField])) {
                $orderBy = $fieldMap[$sortField]." $sortDir";
            }
        }

        $joins = 'LEFT JOIN users c ON a.user_id = c.id';

        // Total de registros
        $total = $this->model->get('COUNT(a.id) AS total', 'recruitment_candidates a', $where, $joins)->total;

        // Traer los registros
        $rows = $this->model->list(
            'a.*, c.username',
            'recruitment_candidates a',
            "$where ORDER BY $orderBy LIMIT $offset, $size",
            $joins
        );

        $data = [];
        foreach ($rows as $r) {
            $data[] = [
                'id' => $r->id,
                'type' => $r->kind,
                'user' => $r->username,
                'user_id' => $r->user_id,
                'status' => $r->status,
                'name' => $r->name,
                'cc' => $r->cc,
                'email' => $r->email,
                'phone' => $r->phone,
                'appointment' => $r->appointment,
            ];
        }

        echo json_encode([
            'data' => $data,
            'last_page' => ceil($total / $size),
            'last_row' => $total,
        ]);
    }

    public function sendCandidateNotification($id_candidate)
    {
        // 1. Buscamos al candidato
        $candidate = $this->model->get('*', 'recruitment_candidates', "AND id = $id_candidate");
        if (! $candidate) {
            return false;
        }

        // 2. Buscamos los datos del reclutador asignado (si existe)
        $recruiter = null;
        if (! empty($candidate->recruiter_id)) {
            $recruiter = $this->model->get('username, email', 'users', "AND id = $candidate->recruiter_id");
        }

        // 3. Enviamos ambos correos pasando el objeto recruiter
        $this->sendEmailToCandidate($candidate, $recruiter);
        $res = $this->sendEmailToRecruiter($candidate, $recruiter);

        return $res;
    }

    private function emailHeader(): string
    {
        return "<div style='font-family:Arial,sans-serif;color:#333;max-width:600px;border:1px solid #ddd;border-radius:10px;overflow:hidden;'>
            <div style='background:#003366;padding:20px;text-align:center;'>
                <img src='https://sigma.es-metals.com/sigma/app/assets/img/logoES.png' width='150'>
            </div>
            <div style='padding:20px;'>";
    }

    // AHORA DINÁMICO
    private function emailFooter($recruiter = null): string
    {
        $name = $recruiter->username ?? 'Ana Cepeda';
        $email = $recruiter->email ?? 'ana.cepeda@es-metals.com';

        return "<hr style='border:none;border-top:1px solid #eee;margin-top:25px;'>
            <p style='color:#555;font-size:13px;'>
                Si tienes alguna duda, comunícate con <b>$name</b>:
                <a href='mailto:$email'>$email</a>.
            </p>
            <p style='color:#555;font-size:13px;'>Saludos,<br><b>Talento Humano - ES METALS</b></p>
            </div></div>";
    }

    private function notesBlock(string $extraNotes): string
    {
        return $extraNotes
            ? "<div style='background:#fff8e1;padding:10px;border-radius:5px;border-left:4px solid #f0a500;margin-top:12px;'>
                <b>📌 Instrucciones adicionales:</b><br>$extraNotes
            </div>"
            : '';
    }

    private function locationBlock(string $mode, string $locationID, string $teamsLink): string
    {
        if ($mode === 'Virtual') {
            return "<b>💻 Link Teams:</b> <a href='$teamsLink' style='color:#003366;font-weight:bold;'>Unirse ahora</a>";
        }
        $dir = self::SEDES[$locationID] ?? $locationID;

        return "<b>📍 Sede:</b> $locationID<br><b>🏠 Dirección:</b> $dir<br>
                <img src='cid:mapa_sede' style='width:100%;max-width:500px;margin-top:10px;border-radius:5px;'>";
    }

    private function sendEmailToCandidate($candidate, $recruiter = null)
    {
        $id = $candidate->id;
        $name = htmlspecialchars($candidate->name ?? '');
        $mode = $candidate->appointment_mode ?? '';
        $locationID = $candidate->appointment_location ?? '';
        $teamsLink = $candidate->teams_link ?? '';

        $testLinks = "<li><b>Hoja De Vida:</b> <a href='https://sigma.es-metals.com/sigma/?c=Recruitment&a=CV&id=$id'>Completar Aquí</a></li>";
        if (in_array($candidate->psychometrics, ['CISD', 'Both'])) {
            $testLinks .= "<li><b>Prueba CISD:</b> <a href='https://sigma.es-metals.com/sigma/?c=Recruitment&a=DISC&id=$id'>Completar Aquí</a></li>";
        }
        if (in_array($candidate->psychometrics, ['PF', 'Both'])) {
            $testLinks .= "<li><b>Prueba PF:</b> <a href='https://sigma.es-metals.com/sigma/?c=Recruitment&a=PF&id=$id'>Completar Aquí</a></li>";
        }

        $body = $this->emailHeader()."
            <h2 style='color:#003366;'>Hola, $name</h2>

            <div style='background:#e8f5e9;padding:15px;border-radius:5px;border-left:4px solid #2e7d32;margin-bottom:20px;'>
                <h3 style='color:#2e7d32;margin:0 0 8px;'>⚠️ Antes de la entrevista — por favor completa:</h3>
                <ul style='margin:0;padding-left:20px;'>$testLinks</ul>
            </div>

            <div style='background:#f4f4f4;padding:15px;border-radius:5px;border-left:4px solid #003366;'>
                <b>📅 Fecha/Hora:</b> {$candidate->appointment}<br>
                <b>📝 Modalidad:</b> $mode<br>
                {$this->locationBlock($mode, $locationID, $teamsLink)}
            </div>

            {$this->notesBlock($candidate->additional_instructions ?? '')}
        ".$this->emailFooter($recruiter);

        $mail = new stdClass;
        $mail->to = [$candidate->email, $recruiter->email, 'lfelipecorreah@gmail.com'];
        $mail->email = $recruiter->email ?? 'ana.cepeda@es-metals.com';
        $mail->subject = 'ES METALS - Tu Entrevista';
        $mail->body = $body;
        if ($mode === 'Presencial') {
            $mail->maps = ['mapa_sede' => "app/assets/img/{$locationID}.webp"];
        }

        return $this->model->sendEmail($mail);
    }

    private function sendEmailToRecruiter($candidate, $recruiter = null)
    {
        $id = $candidate->id;
        $name = htmlspecialchars($candidate->name ?? '');
        $mode = $candidate->appointment_mode ?? '';
        $locationID = $candidate->appointment_location ?? '';
        $teamsLink = $candidate->teams_link ?? '';
        $dir = self::SEDES[$locationID] ?? $locationID;

        // Generamos el ICS incluyendo el reclutador
        $icsPath = $this->generateICS($id, $name, $candidate->appointment ?? '', $mode, $dir, $teamsLink, $recruiter, $candidate->email);

        $body = $this->emailHeader()."
            <h2 style='color:#003366;'>📅 Nueva Entrevista Programada</h2>

            <div style='background:#f4f4f4;padding:15px;border-radius:5px;border-left:4px solid #003366;margin-bottom:12px;'>
                <b>👤 Reclutador:</b> ".($recruiter->username ?? 'No asignado')."<br>
                <b>🔢 Recruitment ID:</b> {$candidate->recruitment_id}<br>
                <b>👤 Candidato:</b> $name<br>
                <b>🪪 Cédula:</b> {$candidate->cc}<br>
                <b>📧 Email:</b> {$candidate->email}<br>
                <b>📞 Teléfono:</b> {$candidate->phone}<br>
                <b>🏷️ Tipo:</b> {$candidate->kind}<br>
                <b>🌐 Fuente:</b> {$candidate->cv_source}<br>
                <b>🧠 Psicométricas:</b> {$candidate->psychometrics}
            </div>

            <div style='background:#f4f4f4;padding:15px;border-radius:5px;border-left:4px solid #003366;'>
                <b>📅 Fecha/Hora:</b> {$candidate->appointment}<br>
                <b>📝 Modalidad:</b> $mode<br>
                {$this->locationBlock($mode, $locationID, $teamsLink)}
            </div>

            {$this->notesBlock($candidate->additional_instructions ?? '')}
        ".$this->emailFooter($recruiter);

        $mail = new stdClass;
        $mail->to = [$recruiter->email, 'lfelipecorreah@gmail.com'];
        $mail->email = $recruiter->email ?? 'ana.cepeda@es-metals.com';
        $mail->subject = "AGENDA: $name";
        $mail->body = $body;
        $mail->icsFile = $icsPath;

        $res = $this->model->sendEmail($mail);
        if ($icsPath && file_exists($icsPath)) {
            unlink($icsPath);
        }

        return $res;
    }

    private function generateICS($id, $candidateName, $appointment, $mode, $location, $teamsLink, $recruiter = null, $candidateEmail = '')
    {
        $dt = DateTime::createFromFormat('Y-m-d H:i:s', $appointment)
        ?: DateTime::createFromFormat('d/m/Y H:i', $appointment)
        ?: new DateTime;

        $dt->setTimezone(new DateTimeZone('America/Bogota'));
        $dtStart = $dt->format('Ymd\THis');
        $dt->modify('+1 hour');
        $dtEnd = $dt->format('Ymd\THis');

        $orgName = $recruiter->username ?? 'ES Metals Selección';
        $orgMail = $recruiter->email ?? 'ana.cepeda@es-metals.com';

        // El asistente principal es el candidato
        $attendeeEmail = ! empty($candidateEmail) ? $candidateEmail : 'seleccion@es-metals.com';

        $CRLF = "\r\n";
        $ics = implode($CRLF, [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//ES Metals//Sigma//ES',
            'METHOD:REQUEST',
            'BEGIN:VEVENT',
            "UID:entrevista-$id-".time().'@es-metals.com',
            'SEQUENCE:0',
            'STATUS:CONFIRMED',
            "DTSTART;TZID=America/Bogota:$dtStart",
            "DTEND;TZID=America/Bogota:$dtEnd",
            "SUMMARY:Entrevista: $candidateName",
            "DESCRIPTION:Entrevista programada por $orgName",
            'LOCATION:'.($mode === 'Virtual' ? $teamsLink : $location),
            'ORGANIZER;CN=ES Metals:MAILTO:sigmareport@es-metals.com',
            'ATTENDEE;CUTYPE=INDIVIDUAL;ROLE=REQ-PARTICIPANT;PARTSTAT=NEEDS-ACTION',
            ' ;RSVP=TRUE;CN=Sigma:MAILTO:sigma@es-metals,com',
            'END:VEVENT',
            'END:VCALENDAR',
        ]).$CRLF;

        $path = sys_get_temp_dir()."/entrevista_$id.ics";
        file_put_contents($path, $ics);

        return $path;
    }

    public function SaveCandidate()
    {
        try {
            $user = $this->auth->authorize(85);
            header('Content-Type: application/json');

            $item = new stdClass;
            foreach ($_POST as $k => $val) {
                if (! empty($val) && $k !== 'id') {
                    $item->{$k} = htmlspecialchars(trim($val));
                }
            }

            if ($this->model->get('cc', 'recruitment_candidates', "AND cc = '$item->cc'")) {
                $message = '{"type": "error", "message": "Candidate already exists", "close" : ""}';
                header('HX-Trigger: '.json_encode(['showMessage' => $message]));
                http_response_code(400);
                exit;
            }

            $item->user_id = $_SESSION['id-SIGMA'];
            $item->status = 'appointment';

            $id_candidate = $this->model->save('recruitment_candidates', $item);

            if ($id_candidate === false) {
                throw new Exception('Error saving candidate');
            }

            // Dispara ambos correos al crear
            if (! empty($_POST['appointment']) || (! empty($_POST['psychometrics']) && $_POST['psychometrics'] !== 'Other')) {
                $this->sendCandidateNotification($id_candidate);
            }

            $message = '{"type": "success", "message": "Candidate saved and agendas notified", "close": "closeNestedModal"}';
            header('HX-Trigger: '.json_encode(['eventChanged' => true, 'showMessage' => $message]));
            http_response_code(204);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function ResendInitialEmail()
    {
        try {
            $this->auth->authorize(85);
            $id = $_GET['id'] ?? null;
            if (! $id) {
                throw new Exception('ID missing');
            }

            // Reenvía ambos correos con la lógica actualizada
            $sent = $this->sendCandidateNotification($id);

            if ($sent) {
                $message = json_encode(['type' => 'success', 'message' => 'All notifications resent successfully']);
                header('HX-Trigger: '.json_encode(['showMessage' => $message]));
                http_response_code(204);
            } else {
                throw new Exception('Error resending email');
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function SaveFile()
    {
        try {
            $user = $this->auth->authorize(85);
            header('Content-Type: application/json');

            $id = intval($_REQUEST['id'] ?? 0);
            $folder = "uploads/recruitment/candidates/$id";
            $filePath = "$folder/psychometrics.pdf";

            // Check if a file already exists
            if (file_exists($filePath)) {
                http_response_code(400);
                echo json_encode(['type' => 'error', 'message' => 'A file has already been uploaded']);

                return;
            }

            // Validate file
            if (empty($_FILES['file']['name'])) {
                http_response_code(400);
                echo json_encode(['type' => 'error', 'message' => 'No file was uploaded']);

                return;
            }

            $fileType = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if ($fileType !== 'pdf') {
                http_response_code(400);
                echo json_encode(['type' => 'error', 'message' => 'Only PDF files are allowed']);

                return;
            }

            // Create folder if it doesn't exist
            if (! file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            // Save the file as psychometrics.pdf
            move_uploaded_file($_FILES['file']['tmp_name'], $filePath);

            // Save only the upload date in the database
            $this->model->update('recruitment_candidates', (object) [
                'psychometrics' => date('Y-m-d H:i:s'),
            ],
                $id);

            $message = '{"type": "success", "message": "Saved", "close": ""}';
            $hxTriggerData = json_encode([
                'eventChanged' => true,
                'showMessage' => $message,
            ]);
            header('HX-Trigger: '.$hxTriggerData);
            http_response_code(204);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'type' => 'error',
                'message' => 'Internal Server Error: '.$e->getMessage(),
            ]);
        }
    }

    public function ContractEmail()
    {
        try {
            header('Content-Type: application/json');

            $candidateId = $_POST['candidate_id'] ?? null;
            if (! $candidateId) {
                http_response_code(400);
                echo json_encode(['type' => 'error', 'message' => 'ID del candidato faltante']);

                return;
            }

            // Recuperar correo del candidato
            $candidate = $this->model->get('email, name', 'recruitment_candidates', "AND id = $candidateId");
            if (! $candidate) {
                http_response_code(404);
                echo json_encode(['type' => 'error', 'message' => 'Candidato no encontrado']);

                return;
            }

            $item = new stdClass;
            $item->to = [$candidate->email];
            $item->kind = 'contract';
            $item->email = 'sigmareport@es-metals.com';
            $item->subject = 'ES METALS - Documentación para proceso de contratación';
            $item->id = $candidateId;

            $item->body = '
            <div style="font-family: Arial, sans-serif; color: #333; font-size: 14px; line-height: 1.6;">
                <p><b>Buen Día,</b></p>

                <p>
                    Con el fin de agilizar el proceso en el que te encuentras con la empresa <b>ES-Metals</b>, agradecemos:
                </p>

                <ol>
                    <li>
                        Ingresar al siguiente link, realizar el curso y enviar el certificado que se genera al final del proceso:<br>
                        <a href="https://forms.gle/1jYbvGheFVi3DBPGA" style="color:#2563eb;">https://forms.gle/1jYbvGheFVi3DBPGA</a>
                    </li>
                    <li>
                        Revisar y diligenciar los formatos adjuntos para posterior envío por este medio 
                        (la Política de Tratamiento de Datos Personales, Autorización Historia Clínica, 
                        Formato Actualización de Datos Personales y Encuesta Pre-Ingreso)
                    </li>
                    <li>
                        Enviar la siguiente documentación escaneada y firmada a mano lo antes posible, 
                        <b>es urgente el envío de estos documentos.</b>
                    </li>
                </ol>

                <h3 style="margin-top:25px; color:#111;">LISTADO DE DOCUMENTOS PARA INGRESO, CONTRATACIÓN Y AFILIACIONES</h3>

                <table style="width:100%; border-collapse:collapse; font-size:13px; margin-top:10px;">
                    <thead>
                        <tr style="background:#f3f4f6;">
                            <th style="border:1px solid #ddd; padding:8px; text-align:left;">Documento</th>
                            <th style="border:1px solid #ddd; padding:8px; text-align:left;">Observación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td style="border:1px solid #ddd; padding:8px;">Hoja de vida actualizada</td><td style="border:1px solid #ddd; padding:8px;">Realizada en computador, poner referencias laborales con números de contacto</td></tr>
                        <tr><td style="border:1px solid #ddd; padding:8px;">Fotocopia del documento de identidad</td><td style="border:1px solid #ddd; padding:8px;">Ampliado al 150%</td></tr>
                        <tr><td style="border:1px solid #ddd; padding:8px;">Copia de los certificados de estudios</td><td style="border:1px solid #ddd; padding:8px;">Diplomas o cursos (OBLIGATORIO RELACIONAR TODOS LOS ESTUDIOS REALIZADOS)</td></tr>
                        <tr><td style="border:1px solid #ddd; padding:8px;">Certificación Bancaria</td><td style="border:1px solid #ddd; padding:8px;">No superior a 1 mes - En caso de no tener, informar a RRHH para realizar la respectiva carta de apertura de cuenta</td></tr>
                        <tr><td style="border:1px solid #ddd; padding:8px;">Certificado afiliación EPS</td><td style="border:1px solid #ddd; padding:8px;">No superior a 1 mes - En caso de ser la primera afiliación a seguridad social presentar carta de solicitud de afiliación a la EPS elegida</td></tr>
                        <tr><td style="border:1px solid #ddd; padding:8px;">Certificado afiliación Fondo de pensión</td><td style="border:1px solid #ddd; padding:8px;">No superior a 1 mes - En caso de ser la primera afiliación a seguridad social presentar carta de solicitud de afiliación al fondo elegido</td></tr>
                        <tr><td style="border:1px solid #ddd; padding:8px;">Certificado afiliación cesantías</td><td style="border:1px solid #ddd; padding:8px;">No superior a 1 mes - En caso de no tener cesantías consignadas en ningún fondo, presentar carta de solicitud de afiliación al fondo elegido por el empleado</td></tr>
                        <tr><td style="border:1px solid #ddd; padding:8px;">Tarjeta Profesional</td><td style="border:1px solid #ddd; padding:8px;">Hacer envío de su tarjeta profesional por medio del correo electrónico (Si no la tiene, iniciar proceso - OBLIGATORIO)</td></tr>
                        <tr><td style="border:1px solid #ddd; padding:8px;">Certificados Laborales</td><td style="border:1px solid #ddd; padding:8px;">Hacer envío del certificado laboral (OBLIGATORIO)</td></tr>
                    </tbody>
                </table>

                <h3 style="margin-top:25px; color:#111;">Grupo familiar y/o beneficiarios</h3>

                <p><b>Conyugue o compañero(a) permanente</b><br>En caso de tenerlo afiliada como beneficiario(a)</p>
                <ul>
                    <li>Fotocopia del documento de identidad (Ampliado al 150%)</li>
                </ul>

                <p><b>Padres</b><br>En caso de tenerlo afiliada como beneficiario(a)</p>
                <ul>
                    <li>Registro civil del empleado</li>
                    <li>Fotocopia del documento de identidad del padre o madre (Ampliada al 150%)</li>
                    <li>Certificado de EPS de sus padres</li>
                    <li>Formato de declaración de subsidio (diligenciar para afiliación de sus padres)</li>
                </ul>

                <p><b>Hijos</b><br>En caso de tenerlos afiliados como beneficiario(a)</p>
                <ul>
                    <li><b>De 0 a 7 años:</b> Registro civil</li>
                    <li><b>De 7 a 18 años:</b> Tarjeta de identidad y certificado de estudio</li>
                    <li><b>Mayores de 18 años:</b> Cédula y certificado de estudio (técnica laboral, tecnología o carreras universitarias)</li>
                </ul>

                <h3 style="margin-top:25px; color:#111;">Documentos firmados</h3>
                <ul>
                    <li>Autorización de manejo de historia clínica</li>
                    <li>Encuesta Pre ingreso</li>
                    <li>Autorización Tratamiento de Datos Personales Empleados ES METALS - Completo</li>
                    <li>F04-PRRH-01 Formato Actualización Tratamiento de Datos Personales Empleados ES METALS - Completo</li>
                    <li>Certificado de ética y cumplimiento - Link en el cuerpo del correo</li>
                </ul>

                <p style="color:#b91c1c;"><b>*Si no entrega los documentos de los beneficiarios no podremos afiliarlos.*</b></p>

                <p>Cualquier duda será con gusto atendida.</p>
            </div>
            ';

            $folderPath = '/var/www/html/sigma/uploads/recruitment/docs/Documentos.zip';
            if (file_exists($folderPath)) {
                $item->attachments = [$folderPath];
            }

            // Enviar correo
            $this->model->sendEmail($item);

            // Actualizar fecha de envío
            $update = new stdClass;
            $update->contract_email = date('Y-m-d H:i:s');
            $this->model->update('recruitment_candidates', $update, $candidateId);

            echo '<div class="text-green-600 font-semibold"><i class="ri-check-line mr-1"></i> Correo enviado</div>';
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'type' => 'error',
                'message' => 'Error interno: '.$e->getMessage(),
            ]);
        }
    }

    public function UpdateField()
    {
        try {
            header('Content-Type: application/json');

            $id = $_REQUEST['id']; // ID del candidato
            $field = $_REQUEST['field'];

            $item = new stdClass;

            // --- CASO 1: EXPERIENCIA LABORAL (JSON) ---
            if ($field === 'work_experience') {
                $index = $_REQUEST['index'];
                $subfield = $_REQUEST['subfield'];
                $value = $_REQUEST[$subfield] ?? '';

                $current = $this->model->get('work_experience', 'recruitment_candidates', "AND id = $id");

                $data = json_decode($current->work_experience ?? '', true);
                if (! is_array($data)) {
                    $data = [];
                }
                if (! isset($data[$index]) || ! is_array($data[$index])) {
                    $data[$index] = [];
                }

                $data[$index][$subfield] = htmlspecialchars(trim((string) $value));
                $item->work_experience = json_encode($data, JSON_UNESCAPED_UNICODE);
            }
            // --- CASO 2: FAMILIARES ---
            elseif ($field === 'relatives_data') {
                $value = $_REQUEST[$field] ?? null;
                $item->{$field} = trim((string) $value);
            }
            // --- CASO 3: CAMPOS NORMALES ---
            else {
                $value = $_REQUEST[$field] ?? null;
                $item->{$field} = htmlspecialchars(trim((string) $value));
            }

            $item->status_at = date('Y-m-d H:i:s');

            // --- LÓGICA DE STATUS Y OTROS CAMPOS ---
            switch ($field) {
                case 'hired':
                    // VALIDACIÓN ANTES DE PROCEDER
                    if (strtolower($value) === 'screening' || strtolower($value) === 'hired') {
                        $check = $this->model->get(
                            'id, name, cc, email, phone, age, city, neighborhood, maritalstatus, liveswith, 
                            relativework, relatives_data, educationlevel, degree, school, work_experience, 
                            wage, has_knowledge, shortgoals, longgoals, reasons, talla_pantalon, 
                            talla_camisa, talla_zapatos, psychometrics, disc_answers, pf_answers, recruitment_id',
                            'recruitment_candidates',
                            'AND id = '.(int) $id
                        );

                        $required = [
                            'data_consent' => 'Autorización de Datos',
                            'name' => 'Nombre Completo',
                            'cc' => 'Cédula (CC)',
                            'email' => 'Correo Electrónico',
                            'phone' => 'Celular',
                            'age' => 'Edad',
                            'city' => 'Ciudad de Residencia',
                            'neighborhood' => 'Barrio de Residencia',
                            'maritalstatus' => 'Estado Civil',
                            'liveswith' => 'Con quién convive',
                            'relativework' => 'Parentesco con empleados',
                            'educationlevel' => 'Nivel de Estudio',
                            'degree' => 'Título Académico',
                            'school' => 'Institución Educativa',
                            'work_experience' => 'Experiencia Laboral',
                            'wage' => 'Aspiración Salarial',
                            'has_knowledge' => 'Conocimientos Específicos',
                            'shortgoals' => 'Metas a Corto Plazo',
                            'longgoals' => 'Metas a Largo Plazo',
                            'reasons' => 'Razones de Idoneidad',
                            'talla_pantalon' => 'Talla Pantalón',
                            'talla_camisa' => 'Talla Camisa',
                            'talla_zapatos' => 'Talla Zapatos',
                        ];

                        $assigned = $check->psychometrics ?? '';
                        if ($assigned === 'Both' || $assigned === 'CISD' || $assigned === 'DISC') {
                            $required['disc_answers'] = 'Prueba DISC';
                        }
                        if ($assigned === 'Both' || $assigned === 'PF') {
                            $required['pf_answers'] = 'Prueba 16PF';
                        }

                        $missing = [];
                        foreach ($required as $col => $label) {
                            if (empty($check->$col) || $check->$col === '[]' || $check->$col === 'null') {
                                $missing[] = $label;
                            }
                        }

                        if (! empty($missing)) {
                            // 1. Separamos las pruebas psicotécnicas del resto
                            $testKeys = ['disc_answers', 'pf_answers'];
                            $testsMissing = [];
                            $dataMissingCount = 0;

                            foreach ($missing as $label) {
                                // Si el label corresponde a una prueba, lo movemos a su grupo
                                if (strpos($label, 'Prueba') !== false) {
                                    $testsMissing[] = $label;
                                } else {
                                    $dataMissingCount++;
                                }
                            }

                            // 2. Construimos un mensaje elegante y corto
                            $summary = [];
                            if ($dataMissingCount > 0) {
                                $summary[] = "$dataMissingCount datos de la Hoja de Vida / Tallas";
                            }
                            if (! empty($testsMissing)) {
                                $summary[] = 'Pendiente: '.implode(' y ', $testsMissing);
                            }

                            $finalMessage = 'No se puede proceder. Faltan: '.implode(' | ', $summary);

                            // 3. Enviamos al Notyf de tu Index
                            $errorMsg = json_encode([
                                'type' => 'error',
                                'message' => $finalMessage,
                                'close' => '',
                            ]);

                            header('HX-Trigger: '.json_encode(['showMessage' => $errorMsg]));
                            http_response_code(400);
                            exit;
                        }
                    }
                    $item->hired = (strtolower($value) === 'screening') ? 1 : 0;
                    $item->status = strtolower($value);
                    break;

                case 'polygraph_result':
                    $item->status = ($value === '0') ? 'polygraph' : 'active';
                    break;

                case 'security_result':
                    $item->status = ($value === '0') ? 'security' : 'active';
                    break;

                case 'medical_result':
                    $item->status = ($value === '0') ? 'medical' : 'active';
                    break;

                case 'home_result':
                    if ($value === '0') {
                        $item->status = 'home';
                    } else {
                        $item->status = 'active';
                        $itemd = new stdClass;
                        $itemd->to = ['lfelipecorreah@gmail.com', 'mario.gonzalez@es-metals.com'];
                        $itemd->kind = 'recruitment_list';
                        $itemd->email = 'sigma@es-metals.com';
                        $itemd->subject = 'ES METALS - List Review';
                        $itemd->id = $id;
                        $approvalLink = sprintf('https://sigma.es-metals.com/sigma/?c=Recruitment&a=ReviewList&id=%s', urlencode($id));
                        $itemd->body = "Hello,<br><br>The candidate has passed the Home Visit...<br><a href='$approvalLink'>Review List</a><br><br>Thanks!";
                        $this->model->sendEmail($itemd);
                    }
                    break;
            }

            // Guardar cambios
            $update_res = $this->model->update('recruitment_candidates', $item, $id);

            if ($update_res === false) {
                http_response_code(500);
                echo json_encode(['type' => 'error', 'message' => 'Error saving data']);

                return;
            }

            /* ========================================================
            LÓGICA DE TICKETS
            ======================================================== */
            if ($field === 'status' && $value === 'hired') {

                $candidate = $this->model->get('*', 'recruitment_candidates', 'AND id = '.(int) $id);

                if ($candidate && ! empty($candidate->recruitment_id)) {
                    $recruitmentId = $candidate->recruitment_id;
                    $recruitment = $this->model->get('resources, city, profile_id, qty', 'recruitment', "AND id = $recruitmentId");
                    $profileData = $this->model->get('name', 'job_profiles', "AND id = $recruitment->profile_id");
                    $profile = ($profileData) ? $profileData->name : 'N/A';

                    if ($recruitment && ! empty($recruitment->resources)) {
                        $masterResources = json_decode($recruitment->resources, true);
                        $groupedTickets = [];
                        $marketingItems = [];
                        $candidateResources = [];

                        if (is_array($masterResources)) {
                            foreach ($masterResources as $res) {
                                $resName = is_array($res) ? $res['name'] : $res;
                                if (! empty($res['ticket_id'])) {
                                    continue;
                                }

                                $info = $this->model->get('kind', 'recruitment_resources', "AND name = '$resName'");
                                $type = ($info && ! empty($info->kind)) ? $info->kind : 'General';

                                if (strcasecmp($type, 'Marketing') === 0) {
                                    $marketingItems[] = $resName;
                                    $candidateResources[] = ['name' => $resName, 'ticket_id' => 'EMAIL_SENT', 'kind' => 'Marketing'];
                                } else {
                                    $groupedTickets[$type][] = $resName;
                                }
                            }

                            foreach ($groupedTickets as $type => $names) {
                                $ticket = new stdClass;
                                $ticket->facility = $recruitment->city ?? 'N/A';
                                $ticket->kind = $type;
                                $ticket->priority = 'High';
                                $ticket->status = 'Open';
                                $ticket->user_id = $_SESSION['id-SIGMA'];
                                $ticket->description = 'CONTRATACIÓN - Candidato: '.strtoupper($candidate->name)."\nRecursos: ".implode(', ', $names);

                                $tablaDestino = (strtoupper($type) === 'IT') ? 'it' : 'tickets';
                                $ticketId = $this->model->save($tablaDestino, $ticket);

                                foreach ($names as $name) {
                                    $candidateResources[] = ['name' => $name, 'ticket_id' => $ticketId, 'kind' => $type, 'table' => $tablaDestino];
                                }
                            }

                            if (! empty($marketingItems)) {
                                $itemc = new stdClass;
                                $itemc->to = ['lfelipecorreah@gmail.com', 'marketing@es-metals.com'];
                                $itemc->kind = 'recruitment_marketing_alert';
                                $itemc->id = $id;
                                $resourcesList = implode(', ', $marketingItems);
                                $itemc->subject = 'SIGMA - New Hire Broadcast Listing';
                                $itemc->body = 'New hire: <b>'.strtoupper($candidate->name).'</b><br>Broadcast: <b>'.strtoupper($profile)."</b><br>Resources: $resourcesList";
                                $this->model->sendEmail($itemc);
                            }

                            $profileData = $this->model->get('name, reports_to', 'job_profiles', "AND id = $recruitment->profile_id");
                            $bossUsername = 'No asignado';
                            if ($profileData && ! empty($profileData->reports_to)) {
                                $boss = $this->model->get('username', 'users', "AND id = $profileData->reports_to");
                                $bossUsername = $boss->username ?? 'ID: '.$profileData->reports_to;
                            }

                            $fullName = strtoupper(trim($candidate->name));
                            $candidateEmail = (! empty($candidate->email)) ? $candidate->email : 'Actualizar correo';

                            $email = new stdClass;
                            $email->to = ['lfelipecorreah@gmail.com', 'marketing@es-metals.com'];
                            $email->subject = '🚀 ¡NUEVO INGRESO! - '.$fullName;
                            $email->body = "<div style='background:#003366; color:white; padding:40px; text-align:center;'><h1>$fullName</h1><p>".strtoupper($profileData->name ?? 'N/A').'</p></div>';

                            $this->model->sendEmail($email);

                            if (! empty($candidateResources)) {
                                $upCan = new stdClass;
                                $upCan->resources = json_encode($candidateResources, JSON_UNESCAPED_UNICODE);
                                $this->model->update('recruitment_candidates', $upCan, $id);
                            }
                        }
                    }

                    $exists = $this->model->get('id', 'employees', "AND id = '{$candidate->cc}'");
                    if (! $exists) {
                        $employee = new stdClass;
                        $employee->id = $candidate->cc;
                        $employee->name = strtoupper($candidate->name);
                        $employee->kind = $candidate->kind;
                        $employee->city = $recruitment->city;
                        $employee->talla_pantalon = $candidate->talla_pantalon;
                        $employee->talla_camisa = $candidate->talla_camisa;
                        $employee->talla_zapatos = $candidate->talla_zapatos;
                        $employee->profile = $recruitment->profile_id;
                        $employee->status = 1;
                        $this->model->save('employees', $employee);
                    }

                    $hiredCount = $this->model->get('count(id) as total', 'recruitment_candidates', "and recruitment_id = $recruitmentId and status = 'hired'")->total;
                    if ($recruitment->qty == $hiredCount) {
                        $close = new stdClass;
                        $close->status = 'closed';
                        $close->closed_at = date('Y-m-d H:i:s');
                        $this->model->update('recruitment', $close, $recruitmentId);
                    }
                }
            }

            $msg = 'Updated';
            $message = '{"type": "success", "message": "'.$msg.'", "close": ""}';
            $hxTriggerData = ['cvChanged' => true, 'showMessage' => $message];

            if (isset($_REQUEST['hired']) || isset($_REQUEST['hired_pf']) || isset($_REQUEST['hired_disc'])) {
                $hxTriggerData['eventChanged'] = true;
            }

            if (isset($_REQUEST['appointment']) || ($field === 'status' && ($value === 'discarded' || $value === 'hired'))) {
                $hxTriggerData['eventChanged'] = true;
                $hxTriggerData['showMessage'] = '{"type": "success", "message": "Updated", "close": "closeNestedModal"}';
            }

            header('HX-Trigger: '.json_encode($hxTriggerData));
            http_response_code(200);
            exit;

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function Move()
    {
        try {
            $user = $this->auth->authorize(85);
            header('Content-Type: application/json');

            $id = $_REQUEST['id']; // ID del candidato
            $field = $_REQUEST['field'];

            $item = new stdClass;

            $value = $_REQUEST[$field] ?? null;
            $item->{$field} = htmlspecialchars(trim((string) $value));

            $update_res = $this->model->update('recruitment_candidates', $item, $id);

            if ($update_res === false) {
                http_response_code(500);
                echo json_encode(['type' => 'error', 'message' => 'Error saving data']);

                return;
            }

            $hxTriggerData['eventChanged'] = true;
            $hxTriggerData['showMessage'] = '{"type": "success", "message": "User Moved", "close": "closeNestedModal"}';
            header('HX-Trigger: '.json_encode($hxTriggerData));
            http_response_code(204);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function Close()
    {
        try {
            $user = $this->auth->authorize(85);
            header('Content-Type: application/json');
            $id = $_REQUEST['id'];
            $item = new stdClass;
            $item->status = 'closed';
            $item->closed_at = date('Y-m-d H:i:s');

            $id = $this->model->update('recruitment', $item, $id);

            if ($id === false) {
                http_response_code(500);
                echo json_encode([
                    'type' => 'error',
                    'message' => 'Error saving appointment',
                ]);

                return;
            }

            $message = '{"type": "success", "message": "Updated", "close": "closeNewModal"}';
            $hxTriggerData = json_encode([
                'eventChanged' => true,
                'showMessage' => $message,
            ]);
            header('HX-Trigger: '.$hxTriggerData);
            http_response_code(204);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'type' => 'error',
                'message' => 'Internal Server Error: '.$e->getMessage(),
            ]);
        }
    }

    public function Reject()
    {
        try {
            $user = $this->auth->authorize(85);
            header('Content-Type: application/json');
            $id = $_REQUEST['id'];
            $item = new stdClass;
            $item->status = 'review';
            $item->rejected_at = date('Y-m-d H:i:s');
            $item->rejection = $_REQUEST['reason'];

            $id = $this->model->update('recruitment', $item, $id);

            if ($id === false) {
                http_response_code(500);
                echo json_encode([
                    'type' => 'error',
                    'message' => 'Error saving appointment',
                ]);

                return;
            }

            $message = '{"type": "success", "message": "Updated", "close": "closeNewModal"}';
            $hxTriggerData = json_encode([
                'eventChanged' => true,
                'showMessage' => $message,
            ]);
            header('HX-Trigger: '.$hxTriggerData);
            http_response_code(204);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'type' => 'error',
                'message' => 'Internal Server Error: '.$e->getMessage(),
            ]);
        }
    }

    public function Assign()
    {
        try {
            $user = $this->auth->authorize(85);
            header('Content-Type: application/json');
            $id = $_REQUEST['id'];
            $value = $_REQUEST['assignee_id'];
            $item = new stdClass;
            $item->assignee_id = htmlspecialchars(trim($value));
            $id = $this->model->update('recruitment', $item, $id);
            if ($id === false) {
                http_response_code(500);
                echo json_encode([
                    'type' => 'error',
                    'message' => 'Error saving appointment',
                ]);

                return;
            }

            // ------------------- Respuesta HTMX -------------------
            $message = '{"type": "success", "message": "Updated", "close": ""}';
            $hxTriggerData = json_encode([
                'eventChanged' => true,
                'showMessage' => $message,
            ]);
            header('HX-Trigger: '.$hxTriggerData);
            http_response_code(204);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'type' => 'error',
                'message' => 'Internal Server Error: '.$e->getMessage(),
            ]);
        }
    }

    public function AddExperienceRow()
    {
        $id = $_GET['id'];
        $current = $this->model->get('work_experience', 'recruitment_candidates', "AND id = $id");
        $data = json_decode($current->work_experience ?? '[]', true);
        if (! is_array($data)) {
            $data = [];
        }

        $newIndex = count($data);
        $data[$newIndex] = ['company' => '', 'job_position' => '', 'duration' => '', 'salary' => '', 'reason' => '', 'functions' => ''];

        $item = new stdClass;
        $item->work_experience = json_encode($data, JSON_UNESCAPED_UNICODE);
        $this->model->update('recruitment_candidates', $item, $id);

        $base_class = 'w-full p-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm';

        echo '
        <div class="experience-block border-b border-gray-100 pb-6 mb-6 last:border-0 relative">
            <button type="button" hx-post="?c=Recruitment&a=RemoveExperienceRow" hx-vals=\'{"id":'.$id.', "index":'.$newIndex.'}\' hx-target="closest .experience-block" hx-swap="delete" class="absolute top-0 right-0 text-red-500 font-bold text-xs uppercase flex items-center"><i class="ri-delete-bin-line mr-1"></i> Quitar</button>
            <div class="grid md:grid-cols-2 gap-4 mt-6">
                <div><label class="block mb-1 font-medium text-gray-600">Empresa donde laboró</label><input type="text" name="company" hx-post="?c=Recruitment&a=UpdateField" hx-vals=\'{"id":'.$id.',"field":"work_experience","index":'.$newIndex.',"subfield":"company"}\' class="'.$base_class.'"></div>
                <div><label class="block mb-1 font-medium text-gray-600">Cargo</label><input type="text" name="job_position" hx-post="?c=Recruitment&a=UpdateField" hx-vals=\'{"id":'.$id.',"field":"work_experience","index":'.$newIndex.',"subfield":"job_position"}\' class="'.$base_class.'"></div>
                <div><label class="block mb-1 font-medium text-gray-600">Motivo de terminación</label><input type="text" name="reason" hx-post="?c=Recruitment&a=UpdateField" hx-vals=\'{"id":'.$id.',"field":"work_experience","index":'.$newIndex.',"subfield":"reason"}\' class="'.$base_class.'"></div>
                <div><label class="block mb-1 font-medium text-gray-600">Salario</label><input type="number" name="salary" hx-post="?c=Recruitment&a=UpdateField" hx-vals=\'{"id":'.$id.',"field":"work_experience","index":'.$newIndex.',"subfield":"salary"}\' class="'.$base_class.'"></div>
                <div><label class="block mb-1 font-medium text-gray-600">Tiempo en el cargo</label><input type="text" name="duration" hx-post="?c=Recruitment&a=UpdateField" hx-vals=\'{"id":'.$id.',"field":"work_experience","index":'.$newIndex.',"subfield":"duration"}\' class="'.$base_class.'"></div>
                <div class="md:col-span-2"><label class="block mb-1 font-medium text-gray-600">Funciones Realizadas</label><textarea rows="2" name="functions" hx-post="?c=Recruitment&a=UpdateField" hx-vals=\'{"id":'.$id.',"field":"work_experience","index":'.$newIndex.',"subfield":"functions"}\' class="'.$base_class.' w-full"></textarea></div>
            </div>
        </div>';
        exit;
    }

    public function RemoveExperienceRow()
    {
        $id = $_POST['id'];
        $index = $_POST['index'];

        $current = $this->model->get('work_experience', 'recruitment_candidates', "AND id = $id");
        $data = json_decode($current->work_experience ?? '[]', true);

        if (isset($data[$index])) {
            unset($data[$index]);
            $data = array_values($data); // Reorganiza los índices
        }

        $item = new stdClass;
        $item->work_experience = json_encode($data, JSON_UNESCAPED_UNICODE);
        $this->model->update('recruitment_candidates', $item, $id);

        http_response_code(200);
        exit;
    }

    public function GenerateAIConcept()
    {
        try {
            $candidateId = (int) $_REQUEST['id'];

            // Versión enviada desde el botón (1 = sucinto | 2 = detallado | 3 = psicológico)
            $promptVersion = (int) ($_REQUEST['promptVersion'] ?? 1);

            // 1. CANDIDATO
            $candidate = $this->model->get('*', 'recruitment_candidates', "AND id = $candidateId");
            if (! $candidate) {
                throw new Exception('Candidato no encontrado');
            }

            // 2. VACANTE Y PERFIL
            $vacante = $this->model->get('*', 'recruitment', "AND id = $candidate->recruitment_id");
            $perfil_puesto = $this->model->get('name', 'job_profiles', 'AND id = '.($vacante->profile_id ?? 0));

            // 3. FORMACIÓN REQUERIDA
            $formation_req = '';
            if (! empty($vacante->profile_id)) {
                $item_data = $this->model->get('content', 'job_profile_items', "AND jp_id = $vacante->profile_id AND kind = 'Formación'");
                if ($item_data && ! empty($item_data->content)) {
                    foreach (json_decode($item_data->content, true) as $row) {
                        if (! empty($row[0])) {
                            $formation_req .= "- {$row[0]}\n";
                        }
                    }
                }
            }

            // 4. DISC — calcular desde disc_answers
            $discSummary = 'No realizado';
            if (! empty($candidate->disc_answers)) {
                $mapping = [
                    1 => ['D', 1],  2 => ['I', 1],  3 => ['S', 1],  4 => ['C', 1],
                    5 => ['D', -1], 6 => ['I', -1], 7 => ['S', -1], 8 => ['C', -1],
                    9 => ['D', 1],  10 => ['I', 1], 11 => ['S', 1], 12 => ['C', 1],
                    13 => ['D', -1], 14 => ['I', -1], 15 => ['S', -1], 16 => ['C', -1],
                    17 => ['D', 1], 18 => ['I', 1], 19 => ['S', 1], 20 => ['C', 1],
                    21 => ['D', -1], 22 => ['I', -1], 23 => ['S', -1], 24 => ['C', -1],
                    25 => ['D', 1], 26 => ['I', 1], 27 => ['S', 1], 28 => ['C', 1],
                ];
                $decoded = json_decode($candidate->disc_answers, true);
                if (is_array($decoded)) {
                    $sums = $counts = ['D' => 0, 'I' => 0, 'S' => 0, 'C' => 0];
                    for ($i = 1; $i <= 28; $i++) {
                        $val = isset($decoded["q$i"]) ? intval($decoded["q$i"]) : 0;
                        if ($val < 1 || $val > 5 || ! isset($mapping[$i])) {
                            continue;
                        }
                        [$dim, $dir] = $mapping[$i];
                        $sums[$dim] += $dir === -1 ? (6 - $val) : $val;
                        $counts[$dim] += 1;
                    }
                    $parts = [];
                    foreach ($sums as $dim => $sum) {
                        $max = $counts[$dim] * 5;
                        $min = $counts[$dim] * 1;
                        $pct = $max > 0 ? round(($sum - $min) / ($max - $min) * 100) : 0;
                        $parts[] = "$dim: {$pct}%";
                    }
                    $discSummary = implode(', ', $parts);
                }
            }

            // 5. PF — calcular desde pf_answers
            $pfSummary = 'No realizado';
            if (! empty($candidate->pf_answers)) {
                $pf_map = [
                    'q1' => 'A', 'q2' => 'A', 'q3' => 'A', 'q4' => 'A', 'q5' => 'A',
                    'q6' => 'B', 'q7' => 'B', 'q8' => 'B', 'q9' => 'B', 'q10' => 'B',
                    'q11' => 'C', 'q12' => 'C', 'q13' => 'C', 'q14' => 'C', 'q15' => 'C',
                    'q16' => 'E', 'q17' => 'E', 'q18' => 'E', 'q19' => 'E', 'q20' => 'E',
                    'q21' => 'F', 'q22' => 'F', 'q23' => 'F', 'q24' => 'F', 'q25' => 'F',
                    'q26' => 'G', 'q27' => 'G', 'q28' => 'G', 'q29' => 'G', 'q30' => 'G',
                    'q31' => 'H', 'q32' => 'H', 'q33' => 'H', 'q34' => 'H', 'q35' => 'H',
                    'q36' => 'I', 'q37' => 'I', 'q38' => 'I', 'q39' => 'I', 'q40' => 'I',
                    'q41' => 'L', 'q42' => 'L', 'q43' => 'L', 'q44' => 'L', 'q45' => 'L',
                    'q46' => 'M', 'q47' => 'M', 'q48' => 'M', 'q49' => 'M', 'q50' => 'M',
                    'q51' => 'N', 'q52' => 'N', 'q53' => 'N', 'q54' => 'N', 'q55' => 'N',
                    'q56' => 'O', 'q57' => 'O', 'q58' => 'O', 'q59' => 'O', 'q60' => 'O',
                    'q61' => 'Q1', 'q62' => 'Q1', 'q63' => 'Q1', 'q64' => 'Q1', 'q65' => 'Q1',
                    'q66' => 'Q2', 'q67' => 'Q2', 'q68' => 'Q2', 'q69' => 'Q2', 'q70' => 'Q2',
                    'q71' => 'Q3', 'q72' => 'Q3', 'q73' => 'Q3', 'q74' => 'Q3', 'q75' => 'Q3',
                    'q76' => 'Q4', 'q77' => 'Q4', 'q78' => 'Q4', 'q79' => 'Q4', 'q80' => 'Q4',
                ];
                $nombres = [
                    'A' => 'Calidez', 'B' => 'Razonamiento', 'C' => 'Estabilidad Emocional',
                    'E' => 'Dominancia', 'F' => 'Animación', 'G' => 'Atención a Normas',
                    'H' => 'Atrevimiento Social', 'I' => 'Sensibilidad', 'L' => 'Vigilancia',
                    'M' => 'Abstracción', 'N' => 'Privacidad', 'O' => 'Aprensión',
                    'Q1' => 'Apertura al Cambio', 'Q2' => 'Autosuficiencia',
                    'Q3' => 'Perfeccionismo', 'Q4' => 'Tensión',
                ];
                $respuestas = json_decode($candidate->pf_answers, true);
                $factores = [];
                foreach ($pf_map as $qid => $factor) {
                    if (isset($respuestas[$qid])) {
                        $factores[$factor][] = (float) $respuestas[$qid];
                    }
                }
                $pfLines = [];
                foreach ($factores as $f => $vals) {
                    $prom = array_sum($vals) / count($vals);
                    $nivel = $prom < 1.7 ? 'Bajo' : ($prom < 2.3 ? 'Medio' : 'Alto');
                    $pfLines[] = "- {$nombres[$f]} ($f): $nivel (".number_format($prom, 2).')';
                }
                $pfSummary = implode("\n", $pfLines);
            }

            // 6. DATOS BASE reutilizables en los prompts
            $cargoNombre = $perfil_puesto->name ?? 'No especificado';
            $formato = "Responde ÚNICAMENTE en este formato, sin texto adicional fuera de él:\n\n"
                    ."CONCEPTO\n[descripción]\n\nCONCLUSIONES\n[puntos]\n\nVEREDICTO\n[RECOMENDADO / RECOMENDADO CON OBSERVACIONES / NO APTO]";

            // ── PROMPT 1: Sucinto ─────────────────────────────────────────────────
            // Rápido, directo. Ideal para decisiones ágiles.
            $prompts[1] = "Eres un psicólogo organizacional experto en selección. Genera un concepto breve y profesional.\n\n"
                ."VACANTE: {$cargoNombre} | Salario: ".($vacante->srange ?? 'N/D').' | Ciudad: '.($vacante->city ?? 'N/D')."\n"
                .'CANDIDATO: '.($candidate->name ?? '').' | Aspiración: '.($candidate->wage ?? 'N/D').' | Ciudad: '.($candidate->city ?? 'N/D')."\n"
                ."DISC: {$discSummary}\n"
                ."16PF: {$pfSummary}\n"
                .'EXPERIENCIA: '.($candidate->work_experience ?? 'No registrada')."\n\n"
                ."CONCEPTO: 2 oraciones del perfil vs cargo.\n"
                ."CONCLUSIONES: 3 puntos concisos (fit técnico, psicológico, logístico).\n"
                ."VEREDICTO: una línea.\n\n"
                .$formato;

            // ── PROMPT 2: Detallado ───────────────────────────────────────────────
            // Análisis cruzado completo, el más exhaustivo.
            $prompts[2] = 'Actúa como un Psicólogo Organizacional Senior y experto en Reclutamiento. '
                ."Analiza la compatibilidad del candidato con la vacante específica.\n\n"
                ."--- DATOS DE LA VACANTE (REQUISITOS) ---\n"
                ."Cargo: {$cargoNombre}\n"
                .'Ciudad/Sede: '.($vacante->city ?? '')."\n"
                .'Rango Salarial: '.($vacante->srange ?? '')."\n"
                ."Conocimientos Técnicos Requeridos:\n{$formation_req}"
                .'Detalles adicionales: '.($vacante->others ?? '')."\n\n"
                ."--- DATOS DEL CANDIDATO (PERFIL) ---\n"
                .'Nombre: '.($candidate->name ?? '')."\n"
                .'Ubicación: '.($candidate->city ?? '').' (Barrio: '.($candidate->neighborhood ?? '').")\n"
                .'Aspiración Salarial: '.($candidate->wage ?? '')."\n"
                .'Experiencia Laboral (JSON): '.($candidate->work_experience ?? '')."\n"
                .'Autoevaluación Técnica: '.($candidate->has_knowledge == '1' ? 'Afirma conocer todo' : 'No conoce todo')."\n"
                .'Metas y Razones: '.($candidate->shortgoals ?? '').' | '.($candidate->reasons ?? '')."\n\n"
                ."--- RESULTADOS PSICOMÉTRICOS ---\n"
                ."DISC (Conductual): {$discSummary}\n"
                ."16PF (Personalidad):\n{$pfSummary}\n\n"
                ."INSTRUCCIONES DE ANÁLISIS:\n"
                ."1. CRUCE TÉCNICO: ¿Su experiencia real respalda los conocimientos requeridos?\n"
                ."2. CRUCE PSICOLÓGICO: ¿Su personalidad (16PF) y conducta (DISC) encajan con el cargo de {$cargoNombre}?\n"
                ."3. FIT LOGÍSTICO/ECONÓMICO: ¿Hay coherencia entre ubicación/salario vs oferta?\n"
                ."4. REDACTA un concepto profesional (2-3 párrafos) sin muletillas.\n"
                ."5. VEREDICTO final.\n\n"
                .$formato;

            // ── PROMPT 3: Enfoque psicológico ─────────────────────────────────────
            // Profundiza en personalidad y conducta, menos en lo técnico.
            $prompts[3] = "Eres especialista en psicología organizacional. Enfócate en el análisis de personalidad y comportamiento del candidato para el cargo.\n\n"
                ."CARGO: {$cargoNombre}\n"
                .'CANDIDATO: '.($candidate->name ?? '')."\n"
                ."DISC: {$discSummary}\n"
                ."16PF:\n{$pfSummary}\n\n"
                ."Interpreta el perfil conductual (DISC) y de personalidad (16PF) en relación al cargo de {$cargoNombre}. "
                ."Identifica fortalezas, riesgos y compatibilidad cultural.\n"
                ."CONCEPTO: 2 oraciones sobre su perfil psicológico aplicado al cargo.\n"
                ."CONCLUSIONES: 3 puntos sobre fortalezas, riesgos y compatibilidad.\n"
                ."VEREDICTO: una línea basada únicamente en el perfil psicológico.\n\n"
                .$formato;

            $prompt = $prompts[$promptVersion] ?? $prompts[1];

            // 7. LLAMADA A GROQ
            $apiKey = $_ENV['GROQ_API_KEY'] ?? '';
            $apiUrl = 'https://api.groq.com/openai/v1/chat/completions';

            $payload = json_encode([
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [['role' => 'user', 'content' => $prompt]],
                'temperature' => 0.4,
                'max_tokens' => 1024,
            ]);

            $ch = curl_init($apiUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer '.$apiKey,
                ],
                CURLOPT_TIMEOUT => 30,
            ]);
            $raw = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($raw === false || $httpCode !== 200) {
                throw new Exception("Error al contactar Groq (HTTP $httpCode): ".$raw);
            }

            $response = json_decode($raw, true);
            $aiText = trim($response['choices'][0]['message']['content'] ?? 'Sin respuesta de la IA.');

            // 8. PARSEAR SECCIONES
            $concepto = $conclusiones = $veredicto = '';
            if (preg_match('/CONCEPTO\s*\n(.*?)(?=\nCONCLUSIONES|\nVEREDICTO|$)/si', $aiText, $m)) {
                $concepto = trim($m[1]);
            }
            if (preg_match('/CONCLUSIONES\s*\n(.*?)(?=\nVEREDICTO|$)/si', $aiText, $m)) {
                $conclusiones = trim($m[1]);
            }
            if (preg_match('/VEREDICTO\s*\n(.*?)$/si', $aiText, $m)) {
                $veredicto = trim(strtoupper($m[1]));
            }

            if (str_contains($veredicto, 'NO APTO')) {
                $color = ['bg-red-100',    'text-red-700'];
            } elseif (str_contains($veredicto, 'OBSERVACIONES')) {
                $color = ['bg-yellow-100', 'text-yellow-700'];
            } elseif (str_contains($veredicto, 'RECOMENDADO')) {
                $color = ['bg-green-100',  'text-green-700'];
            } else {
                $color = ['bg-gray-100',   'text-gray-600'];
            }

            $versionLabels = [1 => 'Análisis sucinto', 2 => 'Análisis detallado', 3 => 'Enfoque psicológico'];

            // 9. RENDER
            echo '
            <div class="p-5 bg-white border border-gray-200 rounded-xl shadow-sm mb-6 group hover:border-black transition-all">
                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] font-bold uppercase text-gray-400 mb-1">Concepto</p>
                        <p class="text-sm text-gray-700 leading-relaxed">'.nl2br(htmlspecialchars($concepto)).'</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase text-gray-400 mb-1">Conclusiones</p>
                        <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">'.htmlspecialchars($conclusiones).'</p>
                    </div>
                    <div class="flex items-center justify-between pt-3 border-t">
                        <p class="text-[10px] font-bold uppercase text-gray-400">Veredicto</p>
                        <span class="text-xs font-bold uppercase px-3 py-1 rounded-full '.$color[0].' '.$color[1].'">
                            '.htmlspecialchars($veredicto ?: 'Sin veredicto').'
                        </span>
                    </div>
                </div>
            </div>';

        } catch (Exception $e) {
            echo '<div class="p-4 bg-red-50 text-red-600 rounded-lg text-xs font-bold uppercase">Error: '.htmlspecialchars($e->getMessage()).'</div>';
        }
    }

    public function DeleteCandidate()
    {
        try {
            $user = $this->auth->authorize(85); // Mantienes el mismo nivel de permiso
            $id = $_GET['id'] ?? null;

            if (! $id) {
                throw new Exception('ID missing');
            }

            // Obtenemos el candidato para verificar propiedad
            $candidate = $this->model->get('*', 'recruitment_candidates', "AND id = '$id'");

            if (! $candidate) {
                throw new Exception('Candidate not found');
            }

            // VALIDACIÓN DE SEGURIDAD: Comparar user_id logueado con el del registro
            if ($candidate->user_id != $_SESSION['id-SIGMA']) {
                $message = '{"type": "error", "message": "No tienes permiso para eliminar este candidato"}';
                header('HX-Trigger: '.json_encode(['showMessage' => json_decode($message)]));
                http_response_code(403);
                exit;
            }

            // Borrado físico o lógico (asumiendo que tu model tiene delete)
            $deleted = $this->model->delete('recruitment_candidates', "id = '$id'");

            if (! $deleted) {
                throw new Exception('Error deleting record');
            }

            $message = '{"type": "success", "message": "Candidate deleted successfully"}';
            // Disparamos eventChanged para que la tabla se refresque sola
            header('HX-Trigger: '.json_encode(['eventChanged' => true, 'showMessage' => json_decode($message)]));
            http_response_code(204);

        } catch (Exception $e) {
            http_response_code(500);
            $message = json_encode(['type' => 'error', 'message' => $e->getMessage()]);
            header('HX-Trigger: '.json_encode(['showMessage' => json_decode($message)]));
        }
    }

    public function Kpis()
    {
        $user = $this->auth->authorize(85);
        $year = (! empty($_REQUEST['year'])) ? $_REQUEST['year'] : date('Y');

        $result1 = [];
        $dates = [];
        $data_opened = [];
        $data_closed = [];

        for ($m = 1; $m <= 12; $m++) {
            $mes_fmt = str_pad($m, 2, '0', STR_PAD_LEFT);
            $firstDay = "$year-$mes_fmt-01";
            $lastDay = date('Y-m-t', strtotime($firstDay));
            $dateStr = date('M', strtotime($firstDay));

            // OPENED: Requests created within this month
            $resA = $this->model->get('COUNT(id) as n', 'recruitment', "AND DATE(created_at) >= '$firstDay' AND DATE(created_at) <= '$lastDay'");
            $opened = (int) $resA->n;

            // CLOSED: Requests finalized within this month
            $resC = $this->model->get('COUNT(id) as n', 'recruitment', "AND DATE(closed_at) >= '$firstDay' AND DATE(closed_at) <= '$lastDay'");
            $closed = (int) $resC->n;

            // EFFICIENCY: Ratio between output and input
            $efficiency = ($opened > 0) ? round(($closed / $opened) * 100) : ($closed > 0 ? 100 : 0);

            $result1[] = [
                'dateStr' => $dateStr,
                'opened' => $opened,
                'closed' => $closed,
                'result1' => $efficiency,
            ];

            $dates[] = $dateStr;
            $data_opened[] = $opened;
            $data_closed[] = $closed;
        }

        $result2 = $this->model->list(
            'p.name as job_title, COUNT(r.id) as total',
            'recruitment r INNER JOIN job_profiles p ON r.profile_id = p.id',
            "AND YEAR(r.created_at) = '$year' GROUP BY p.id ORDER BY total DESC"
        );

        $result3 = $this->model->list(
            'a.area as area_name, COUNT(r.id) as total',
            'recruitment r 
            INNER JOIN job_profiles p ON r.profile_id = p.id 
            INNER JOIN hr_db a ON p.division_id = a.id',
            "AND YEAR(r.created_at) = '$year' 
            GROUP BY a.area 
            ORDER BY total DESC"
        );

        // --- INDICATOR 4: AVERAGE TIME TO HIRE (DAYS) ---
        $result4 = [];
        $data_days = [];
        $total_days = 0;
        $total_closed_count = 0;

        for ($m = 1; $m <= 12; $m++) {
            $mes_fmt = str_pad($m, 2, '0', STR_PAD_LEFT);
            $firstDay = "$year-$mes_fmt-01";
            $lastDay = date('Y-m-t', strtotime($firstDay));
            $dateStr = date('M', strtotime($firstDay));

            // Get AVG, MIN and MAX days for vacancies closed in this specific month
            $resT = $this->model->get(
                'AVG(DATEDIFF(closed_at, created_at)) as avg_d, 
                MIN(DATEDIFF(closed_at, created_at)) as min_d, 
                MAX(DATEDIFF(closed_at, created_at)) as max_d,
                COUNT(id) as count_closed',
                'recruitment',
                "AND status = 'Closed' AND DATE(closed_at) >= '$firstDay' AND DATE(closed_at) <= '$lastDay'"
            );

            $avg = ($resT->avg_d > 0) ? round((float) $resT->avg_d, 1) : 0;

            $result4[] = [
                'dateStr' => $dateStr,
                'avg' => $avg,
                'min' => (int) $resT->min_d,
                'max' => (int) $resT->max_d,
            ];

            $data_days[] = $avg;

            if ($resT->count_closed > 0) {
                $total_days += ($resT->avg_d * $resT->count_closed);
                $total_closed_count += $resT->count_closed;
            }
        }

        // Global Annual Average
        $annual_avg_time = ($total_closed_count > 0) ? round($total_days / $total_closed_count, 1) : 0;

        require_once 'app/views/recruitment/kpis/index.php';
    }
}
