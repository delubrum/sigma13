<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

class ExtrusionController
{
    public function __construct(public Model $model, private AuthService $auth) {}

    public function Index()
    {
        $user = $this->auth->authorize(97);

        $selectFields = [
            'company' => 'company_id',
            'category' => 'category_id',
            'b' => 'b',
            'h' => 'h',
            'e1' => 'e1',
            'e2' => 'e2',
        ];

        $filtersData = [];
        foreach ($selectFields as $key => $dbColumn) {
            // Traemos los datos, limpiando espacios raros desde el SQL
            $rows = $this->model->list("DISTINCT TRIM(REPLACE($dbColumn, '\u00a0', ' ')) as val", 'matrices', "AND $dbColumn IS NOT NULL AND $dbColumn != '' ORDER BY $dbColumn ASC");
            $temp = [];
            if (! empty($rows)) {
                foreach ($rows as $r) {
                    $v = trim((string) $r->val);
                    if ($v !== '') {
                        $temp[] = $v;
                    }
                }
            }
            $filtersData[$key] = array_values(array_unique($temp));
        }

        $tabulator = true;
        $filterReset = true;
        $title = 'Technical Library / Extrusion Dies';
        $canEdit = ! empty(array_intersect(['105'], json_decode($user->permissions ?? '[]', true)));
        $button = $canEdit ? 'New' : null;
        $excludeLastColumn = true;
        $content = 'app/views/extrusion/index.php';

        // CONFIGURACIÓN DE COLUMNAS SIN AUTOCOMPLETE PARA EVITAR CONFLICTOS
        $columns = '[
            { "title": "Img", "field": "img", "headerHozAlign": "center", "hozAlign":"center", "headerSort":false, "formatter": function(cell) {
                let v = cell.getValue(); return v ? `<img src="${v}" class="w-96 h-96 object-contain mx-auto" />` : "";
            }},
            { "title": "Shape", "field": "geometry_shape", "headerHozAlign": "center", "headerFilter":"input"},
            
            { "title": "Company", "field": "company", "headerHozAlign": "center",
                "headerFilter": customSelectFilter,
                "headerFilterFunc": function(hv, rv) { return !hv || String(rv) === String(hv); },
                "headerFilterParams":{"values":'.json_encode($filtersData['company']).'}},
            
            { "title": "Category", "field": "category", "headerHozAlign": "center",
                "headerFilter": customSelectFilter,
                "headerFilterFunc": function(hv, rv) { return !hv || String(rv) === String(hv); },
                "headerFilterParams":{"values":'.json_encode($filtersData['category']).'}},
            
            { "title": "B", "field": "b", "headerHozAlign": "center",
                "headerFilter": customSelectFilter,
                "headerFilterFunc": function(hv, rv) { return !hv || String(rv) === String(hv); },
                "headerFilterParams":{"values":'.json_encode($filtersData['b']).'}},
            
            { "title": "H", "field": "h", "headerHozAlign": "center",
                "headerFilter": customSelectFilter,
                "headerFilterFunc": function(hv, rv) { return !hv || String(rv) === String(hv); },
                "headerFilterParams":{"values":'.json_encode($filtersData['h']).'}},
            
            { "title": "E1", "field": "e1", "headerHozAlign": "center",
                "headerFilter": customSelectFilter,
                "headerFilterFunc": function(hv, rv) { return !hv || String(rv) === String(hv); },
                "headerFilterParams":{"values":'.json_encode($filtersData['e1']).'}},
            
            { "title": "E2", "field": "e2", "headerHozAlign": "center",
                "headerFilter": customSelectFilter,
                "headerFilterFunc": function(hv, rv) { return !hv || String(rv) === String(hv); },
                "headerFilterParams":{"values":'.json_encode($filtersData['e2']).'}},
            
            { "title": "Clicks", "field": "clicks", "headerHozAlign": "center", "headerFilter":"input", "formatter": "html"},
            { "title": "System", "field": "system", "headerHozAlign": "center", "headerFilter":"input", "formatter": "html"},
            { "title": "Files", "field": "files", "headerHozAlign": "center", "headerSort":false, "formatter": "html"}
        ]';

        require_once 'app/views/index.php';
    }

    public function Data()
    {
        $user = $this->auth->authorize(97);

        header('Content-Type: application/json');

        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $size = isset($_GET['size']) ? (int) $_GET['size'] : 15;
        $offset = ($page - 1) * $size;

        $fieldMap = [
            'geometry_shape' => 'a.geometry_shape',
            'company' => 'a.company_id',
            'category' => 'a.category_id',
            'b' => 'a.b',
            'h' => 'a.h',
            'e1' => 'a.e1',
            'e2' => 'a.e2',
            'clicks' => 'a.clicks',
            'system' => 'a.systema',
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
                    // LISTA DE CAMPOS QUE DEBEN SER EXACTOS (LITERALES)
                    $exactFields = ['company', 'category', 'b', 'h', 'e1', 'e2'];

                    if (in_array($field, $exactFields)) {
                        // Búsqueda exacta para evitar que "ES" traiga "ESM"
                        $where .= ' AND '.$fieldMap[$field]." = '$value'";
                    } else {
                        // Búsqueda parcial para "shape", "clicks" o "system"
                        $where .= ' AND '.$fieldMap[$field]." LIKE '%$value%'";
                    }
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

        $totalResult = $this->model->get('COUNT(id) AS total', 'matrices a', $where);
        $total = $totalResult->total ?? 0;

        $rows = $this->model->list(
            'a.*',
            'matrices a',
            "$where ORDER BY $orderBy LIMIT $offset, $size"
        );

        $data = [];
        foreach ($rows as $r) {
            $clicksArr = json_decode($r->clicks, true);
            $clicks = ! empty($clicksArr) ? implode('<br>', $clicksArr) : '';

            $systemArr = json_decode($r->systema, true);
            $system = ! empty($systemArr) ? implode('<br>', $systemArr) : '';

            $link = '';
            $directorio = "uploads/matrices/$r->geometry_shape/";
            if (is_dir($directorio)) {
                $archivos = array_diff(scandir($directorio), ['.', '..']);
                sort($archivos);
                foreach ($archivos as $fileName) {
                    $v = filemtime($directorio.$fileName);
                    $link .= "<a target='_blank' href='$directorio$fileName?v=$v' class='block text-blue-600 underline'>$fileName</a>";
                }
            }

            $img_path = "uploads/matrices/$r->geometry_shape/$r->geometry_shape.png";
            $img_v = file_exists($img_path) ? filemtime($img_path) : time();

            $data[] = [
                'id' => $r->id,
                'img' => "$img_path?v=$img_v",
                'geometry_shape' => $r->geometry_shape,
                'company' => $r->company_id,
                'category' => $r->category_id,
                'b' => $r->b,
                'h' => $r->h,
                'e1' => $r->e1,
                'e2' => $r->e2,
                'clicks' => $clicks,
                'system' => $system,
                'files' => $link,
            ];
        }

        echo json_encode([
            'last_page' => ceil($total / $size),
            'last_row' => $total,
            'data' => $data,
        ]);
    }

    public function FilterOptions()
    {
        header('Content-Type: application/json');

        $fieldMap = [
            'company' => 'company_id',
            'category' => 'category_id',
            'b' => 'b',
            'h' => 'h',
            'e1' => 'e1',
            'e2' => 'e2',
        ];

        $activeFilters = [];
        if (isset($_GET['filter']) && is_array($_GET['filter'])) {
            foreach ($_GET['filter'] as $field => $value) {
                if (isset($fieldMap[$field]) && $value !== '') {
                    $activeFilters[$field] = addslashes($value);
                }
            }
        }

        $result = [];
        foreach ($fieldMap as $key => $dbColumn) {
            $where = "AND $dbColumn IS NOT NULL AND $dbColumn != ''";
            foreach ($activeFilters as $f => $v) {
                if ($f === $key) {
                    continue;
                }
                $col = $fieldMap[$f];
                $where .= " AND TRIM(REPLACE($col, '\u00a0', ' ')) = '$v'";
            }

            $rows = $this->model->list(
                "DISTINCT TRIM(REPLACE($dbColumn, '\u00a0', ' ')) as val",
                'matrices',
                "$where ORDER BY $dbColumn ASC"
            );

            $vals = [];
            foreach ($rows as $r) {
                $v = trim((string) $r->val);
                if ($v !== '') {
                    $vals[] = $v;
                }
            }
            $result[$key] = array_values(array_unique($vals));
        }

        echo json_encode($result);
    }

    public function Stats() {}

    public function New()
    {
        $this->auth->authorize(97);
        require_once 'app/views/extrusion/new.php';
    }

    public function Save()
    {
        $user = $this->auth->authorize(97);
        header('Content-Type: application/json');

        try {
            $item = new stdClass;
            $table = 'matrices';
            $id_folder = $_REQUEST['shape'];

            if ($this->model->get('geometry_shape', 'matrices', " and geometry_shape = '$id_folder'")) {
                $message = '{"type": "error", "message": "Shape already exist", "close" : ""}';
                header('HX-Trigger: '.json_encode(['showMessage' => $message]));
                http_response_code(400);
                exit;
            }

            foreach ($_POST as $k => $val) {
                if (! empty($val)) {
                    if ($k != 'id') {
                        $item->{$k} = $val;
                    }
                }
            }

            $item->products = '[""]';
            $item->clicks = (! empty($_REQUEST['clicks'])) ? json_encode($_REQUEST['clicks']) : '[""]';
            $item->systema = (! empty($_REQUEST['systema'])) ? json_encode($_REQUEST['systema']) : '[""]';

            $res = $this->model->save($table, $item);

            if ($res === false) {
                throw new Exception('Error al procesar la solicitud en la base de datos.');
            }

            if (! empty($_FILES['files']['name'][0])) {
                $carpeta = "uploads/matrices/$id_folder";

                if (! file_exists($carpeta)) {
                    mkdir($carpeta, 0777, true);
                }

                $total = count($_FILES['files']['name']);
                for ($i = 0; $i < $total; $i++) {
                    $tmpFilePath = $_FILES['files']['tmp_name'][$i];
                    if ($tmpFilePath != '') {
                        $newFilePath = $carpeta.'/'.$_FILES['files']['name'][$i];

                        // Si el archivo ya existe, lo borramos para asegurar limpieza
                        if (file_exists($newFilePath)) {
                            @chmod($newFilePath, 0777);
                            @unlink($newFilePath);
                        }

                        move_uploaded_file($tmpFilePath, $newFilePath);
                    }
                }
            }

            $message = json_encode(['type' => 'success', 'message' => 'Saved', 'close' => 'closeNewModal']);
            $hxTriggerData = json_encode([
                'listChanged' => true,
                'showMessage' => $message,
            ]);

            header('HX-Trigger: '.$hxTriggerData);
            http_response_code(204);

        } catch (Exception $e) {
            $message = json_encode(['type' => 'error', 'message' => $e->getMessage()]);
            $hxTriggerData = json_encode([
                'showMessage' => $message,
            ]);

            header('HX-Trigger: '.$hxTriggerData);
            http_response_code(500);
        }
    }

    public function Detail()
    {
        $id = $this->model->get('*', 'matrices', 'and id = '.$_REQUEST['id']);
        require_once 'app/views/extrusion/edit.php';
    }

    public function DeleteFile()
    {
        $this->auth->authorize(97);
        $shape = $_POST['shape'];
        $filename = $_POST['filename'];
        $filePath = 'uploads/matrices/'.basename($shape).'/'.basename($filename);

        if (file_exists($filePath)) {
            @chmod($filePath, 0777);
            unlink($filePath);
            $message = json_encode(['type' => 'success', 'message' => 'Deleted', 'close' => '']);
            header('HX-Trigger: '.json_encode([
                'listChanged' => true,
                'showMessage' => $message,
            ]));
            echo '';
        } else {
            header('HTTP/1.1 400 Bad Request');
            echo 'File not found';
        }
    }

    public function UpdateField()
    {
        $this->auth->authorize(97);
        if (! isset($_POST['id'])) {
            http_response_code(400);
            exit;
        }

        $id = $_POST['id'];
        $data = $_POST;
        unset($data['id']);

        $multipleFields = ['clicks', 'systema'];

        foreach ($data as $key => $value) {
            if (in_array($key, $multipleFields)) {
                $data[$key] = empty($value) ? json_encode([]) : json_encode((array) $value);
            } else {
                if ($value === '') {
                    $data[$key] = null;
                }
            }
        }

        $this->model->update('matrices', (object) $data, $id);

        $message = json_encode(['type' => 'success', 'message' => 'Updated', 'close' => '']);
        header('HX-Trigger: '.json_encode([
            'listChanged' => true,
            'showMessage' => $message,
        ]));

        http_response_code(200);
        exit;
    }

    public function UploadSingleFile()
    {
        $this->auth->authorize(97);
        $shape = $_POST['shape'];
        $folder = "uploads/matrices/$shape/";

        if (! is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        if (isset($_FILES['file'])) {
            $file = $_FILES['file'];
            $path = $folder.basename($file['name']);

            // ELIMINACIÓN PREVIA PARA ASEGURAR SOBRESCRITURA
            if (file_exists($path)) {
                @chmod($path, 0777);
                @unlink($path);
            }

            if (move_uploaded_file($file['tmp_name'], $path)) {
                // Forzamos a PHP a olvidar el estado anterior del archivo
                clearstatcache();

                $message = json_encode(['type' => 'success', 'message' => 'Uploaded', 'close' => '']);
                header('HX-Trigger: '.json_encode([
                    'listChanged' => true,
                    'showMessage' => $message,
                ]));
                http_response_code(200);
            }
        }
        exit;
    }

    public function RefreshFileList()
    {
        $id = $this->model->get('*', 'matrices', 'and id = '.$_REQUEST['id']);
        $dir = "uploads/matrices/{$id->geometry_shape}/";

        if (is_dir($dir)) {
            $files = array_diff(scandir($dir), ['.', '..']);
            foreach ($files as $f) { ?>
                <div class="flex items-center justify-between bg-white p-2 rounded-lg border border-gray-200" id="file-<?php echo md5($f); ?>">
                    <span class="text-xs text-gray-700"><?php echo $f; ?></span>
                    <button type="button" 
                        hx-post="?c=Extrusion&a=DeleteFile" 
                        hx-vals='{"shape": "<?php echo $id->geometry_shape; ?>", "filename": "<?php echo $f; ?>"}'
                        hx-target="#file-<?php echo md5($f); ?>"
                        hx-swap="delete"
                        class="text-red-500"><i class="ri-delete-bin-line"></i></button>
                </div>
            <?php }
            }
    }

    public function Delete()
    {
        $id = $_REQUEST['id'];
        $this->model->delete('matrices', " id = '$id'");

        $msg_content = '{"type": "success", "message": "Delete", "close" : "closeNewModal"}';
        $hxTriggerData = json_encode([
            'eventChanged' => true,
            'showMessage' => $msg_content,
        ]);

        header('HX-Trigger: '.$hxTriggerData);
        http_response_code(204);

    }

    public function Admin()
    {
        $user = $this->auth->authorize(165);
        $tabulator = true;
        $title = 'Technical Library / Admin';
        $button = 'New';
        $content = 'app/views/extrusion/admin.php';
        require_once 'app/views/index.php';
    }

    public function NewItem()
    {
        $this->auth->authorize(165);

        $r = new stdClass; // Objeto vacío por defecto (para creación)

        // Si recibimos un ID por GET, cargamos los datos del registro
        if (isset($_REQUEST['id']) && ! empty($_REQUEST['id'])) {
            $r = $this->model->get('*', 'matrices_db', 'AND id ='.$_REQUEST['id']);
        }

        // Cargamos la vista (asegúrate que el nombre del archivo coincida)
        require_once 'app/views/extrusion/new-item.php';
    }

    public function SaveDB()
    {
        $user = $this->auth->authorize(165);
        header('Content-Type: application/json');

        try {
            $table = 'matrices_db';
            $id = ! empty($_POST['id']) ? (int) $_POST['id'] : null; // Capturamos el ID
            $item = new stdClass;

            // Limpiamos los datos del POST para el objeto $item
            foreach ($_POST as $k => $val) {
                if ($k !== 'id' && ! empty($val)) {
                    $item->{$k} = $val;
                }
            }

            // DECISIÓN: ¿Update o Save?
            if ($id) {
                // Si hay ID, usamos tu método UPDATE
                $res = $this->model->update($table, $item, $id);
                $message = 'Registro actualizado correctamente';
            } else {
                // Si NO hay ID, usamos tu método SAVE (Insert)
                $res = $this->model->save($table, $item);
                $message = 'Nuevo registro guardado';
            }

            if ($res === false) {
                throw new Exception('Error en la operación de base de datos.');
            }

            // Triggers para HTMX
            $hxTriggerData = json_encode([
                'listChanged' => true,
                'showMessage' => ['type' => 'success', 'message' => $message],
            ]);

            header('HX-Trigger: '.$hxTriggerData);
            http_response_code(204);

        } catch (Exception $e) {
            $errorMsg = json_encode(['type' => 'error', 'message' => $e->getMessage()]);
            header('HX-Trigger: '.json_encode(['showMessage' => $errorMsg]));
            http_response_code(500);
        }
    }

    private function groqChat(string $prompt, int $maxTokens = 200): string
    {
        $ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode([
                'model' => 'llama-3.1-8b-instant',
                'temperature' => 0.1,
                'max_tokens' => $maxTokens,
                'messages' => [['role' => 'user', 'content' => $prompt]],
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer '.($_ENV['GROQ_API_KEY'] ?? ''),
            ],
            CURLOPT_TIMEOUT => 30,
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);

        return $data['choices'][0]['message']['content'] ?? '';
    }

    public function AiSearch()
    {
        header('Content-Type: application/json');
        $q = trim($_GET['q'] ?? '');
        if (! $q) {
            echo json_encode([]);

            return;
        }

        $companies = $this->model->list('DISTINCT company_id as val', 'matrices', "AND company_id IS NOT NULL AND company_id != ''");
        $categories = $this->model->list('DISTINCT category_id as val', 'matrices', "AND category_id IS NOT NULL AND category_id != ''");

        $companyList = implode(', ', array_column((array) $companies, 'val'));
        $categoryList = implode(', ', array_column((array) $categories, 'val'));

        $prompt = 'Extrae parámetros para buscar matrices de extrusión de aluminio.
        Empresas reales: '.$companyList.'
        Categorías reales: '.$categoryList.'

        MAPEO ESTRICTO de operadores:
        - "mayor a X", "más de X", "> X", "mayor que X", ">X" → campo_gt
        - "menor a X", "menos de X", "< X", "menor que X", "<X" → campo_lt
        - "entre X y Y", "de X a Y", "X - Y" → campo_min + campo_max
        - "alrededor de X", "aprox X", "como X", "~X" → campo_min: X-0.5, campo_max: X+0.5
        - "X" sin calificador → campo + tolerance:0.05

        Ejemplos EXACTOS:
        "b mayor a 2" → {"b_gt":2}
        "b > 2" → {"b_gt":2}
        "b>2" → {"b_gt":2}
        "b menor a 1.5" → {"b_lt":1.5}
        "b < 1.5" → {"b_lt":1.5}
        "b<1.5" → {"b_lt":1.5}
        "b entre 1 y 3" → {"b_min":1,"b_max":3}
        "b 1-3" → {"b_min":1,"b_max":3}
        "b alrededor de 2.6" → {"b_min":2.1,"b_max":3.1}
        "b ~1.5" → {"b":1.5,"tolerance":0.1}
        "ES Cover b>2 h entre 1 y 3 e1<0.5" → {"company":"ES","category":"Cover","b_gt":2,"h_min":1,"h_max":3,"e1_lt":0.5}
        "ES Cover b mayor a 2 h entre 1 y 3 e1 menor a 0.5" → {"company":"ES","category":"Cover","b_gt":2,"h_min":1,"h_max":3,"e1_lt":0.5}

        REGLAS: solo campos mencionados, nunca inventar, respetar el mapeo estrictamente.
        Texto: "'.addslashes($q).'"
        Responde SOLO JSON. Sin markdown.';

        $raw = trim(preg_replace('/```json|```/', '', $this->groqChat($prompt, 200)));
        $decoded = json_decode($raw, true);

        echo json_encode($decoded ?? []);
    }

    public function AiData()
    {
        header('Content-Type: application/json');

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $size = (int) ($_GET['size'] ?? 15);
        $offset = ($page - 1) * $size;

        $params = json_decode($_GET['params'] ?? '{}', true) ?? [];
        $tolerance = (float) ($params['tolerance'] ?? 0.05);

        // Dimensiones — rango explícito o valor+tolerance
        $whereBase = '';
        foreach (['b', 'h', 'e1', 'e2'] as $d) {
            if (isset($params["{$d}_min"]) && isset($params["{$d}_max"])) {
                $whereBase .= " AND $d BETWEEN ".(float) $params["{$d}_min"].' AND '.(float) $params["{$d}_max"];
            } elseif (isset($params[$d])) {
                $val = (float) $params[$d];
                $whereBase .= " AND $d BETWEEN ".($val - $tolerance).' AND '.($val + $tolerance);
            }
        }

        // Category — exacto primero, LIKE si no hay resultados
        if (! empty($params['category'])) {
            $cat = addslashes($params['category']);
            $whereExact = " AND category_id = '$cat'".$whereBase;
            $count = $this->model->get('COUNT(id) AS total', 'matrices', $whereExact)->total ?? 0;
            $whereBase = $count > 0
                ? $whereExact
                : " AND category_id LIKE '%$cat%'".$whereBase;
        }

        // Company — exacto primero, LIKE si no hay resultados
        $where = $whereBase;
        if (! empty($params['company'])) {
            $company = addslashes($params['company']);
            $whereExact = " AND company_id = '$company'".$whereBase;
            $count = $this->model->get('COUNT(id) AS total', 'matrices', $whereExact)->total ?? 0;
            $where = $count > 0
                ? $whereExact
                : " AND company_id LIKE '%$company%'".$whereBase;
        }

        $total = $this->model->get('COUNT(id) AS total', 'matrices', $where)->total ?? 0;
        $rows = $this->model->list('*', 'matrices', "$where ORDER BY id DESC LIMIT $offset, $size");

        $data = [];
        foreach ($rows as $r) {
            $clicksArr = json_decode($r->clicks, true);
            $systemArr = json_decode($r->systema, true);
            $link = '';
            $dir = "uploads/matrices/$r->geometry_shape/";
            if (is_dir($dir)) {
                foreach (array_diff(scandir($dir), ['.', '..']) as $f) {
                    $v = filemtime($dir.$f);
                    $link .= "<a target='_blank' href='$dir$f?v=$v' class='block text-blue-600 underline'>$f</a>";
                }
            }
            $img_path = "uploads/matrices/$r->geometry_shape/$r->geometry_shape.png";
            $data[] = [
                'id' => $r->id,
                'img' => "$img_path?v=".(file_exists($img_path) ? filemtime($img_path) : time()),
                'shape' => $r->geometry_shape,
                'company' => $r->company_id,
                'category' => $r->category_id,
                'b' => $r->b,
                'h' => $r->h,
                'e1' => $r->e1,
                'e2' => $r->e2,
                'clicks' => ! empty($clicksArr) ? implode('<br>', $clicksArr) : '',
                'system' => ! empty($systemArr) ? implode('<br>', $systemArr) : '',
                'files' => $link,
            ];
        }

        echo json_encode([
            'last_page' => ceil($total / $size),
            'last_row' => $total,
            'data' => $data,
        ]);
    }
}
