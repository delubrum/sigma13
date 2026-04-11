<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

class FastenersController
{
    public function __construct(public Model $model, private AuthService $auth) {}

    public function Index()
    {
        $user = $this->auth->authorize(116);
        $tabulator = true;
        $title = 'Technical Library / Fasteners';
        $canEdit = ! empty(array_intersect(['115'], json_decode($user->permissions ?? '[]', true)));
        $button = $canEdit ? 'New' : null;
        $excludeLastColumn = true;
        $content = 'app/views/fasteners/index.php';
        $columns = '[
             { "title": "Img", "field": "img", "headerHozAlign": "center", "hozAlign":"center", "headerSort":false, "formatter": function(cell) {
                let value = cell.getValue();
                if(!value) return "";
                return `<img src="${value}" class="w-60 h-60 object-contain mx-auto" />`;
            }},
            { "title": "Code", "field": "code", "headerHozAlign": "center", "headerFilter": "input", "hozAlign": "left" },
            { "title": "Description", "field": "description", "headerHozAlign": "center", "headerFilter": "input", "hozAlign": "left" },
            { "title": "Category", "field": "category", "headerHozAlign": "center", "headerFilter": "input", "hozAlign": "left" },
            { "title": "Head", "field": "head", "headerHozAlign": "center", "headerFilter": "input", "hozAlign": "left" },
            { "title": "Screwdriver", "field": "screwdriver", "headerHozAlign": "center", "headerFilter": "input", "hozAlign": "left" },
            { "title": "Diameter", "field": "diameter", "headerHozAlign": "center", "headerFilter": "input", "hozAlign": "left" },
            { "title": "Length", "field": "length", "headerHozAlign": "center", "headerFilter": "input", "hozAlign": "left" },
            { "title": "Observation", "field": "observation", "headerHozAlign": "center", "headerFilter": "input", "hozAlign": "left" },
            { "title": "Files", "field": "files", "headerHozAlign": "center", "hozAlign": "left", "headerSort": false, "formatter": "html" }
        ]';
        require_once 'app/views/index.php';
    }

    public function Data()
    {
        $user = $this->auth->authorize(116);
        header('Content-Type: application/json');

        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $size = isset($_GET['size']) ? (int) $_GET['size'] : 15;
        $offset = ($page - 1) * $size;

        $fieldMap = [
            'code' => 'a.code',
            'description' => 'a.description',
            'category' => 'a.category',
            'head' => 'a.head',
            'screwdriver' => 'a.screwdriver',
            'diameter' => 'a.diameter',
            'length' => 'a.item_length',
            'observation' => 'a.observation',
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
                    $where .= ' AND '.$fieldMap[$field]." LIKE '%$value%'";
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

        $total = $this->model->get('COUNT(id) AS total', 'screws a', $where)->total;
        $rows = $this->model->list('a.*', 'screws a', "$where ORDER BY $orderBy LIMIT $offset, $size");

        $data = [];
        foreach ($rows as $r) {
            $link = '';
            $directorio = "uploads/screws/$r->code/";
            if (is_dir($directorio)) {
                $archivos = array_diff(scandir($directorio), ['.', '..']);
                sort($archivos);
                foreach ($archivos as $fileName) {
                    // Cache Busting para archivos
                    $v = filemtime($directorio.$fileName);
                    $link .= "<a href='$directorio$fileName?v=$v' target='_blank' class='block text-blue-600 underline'>$fileName</a>";
                }
            }

            // Cache Busting para la imagen principal
            $img_path = "uploads/screws/$r->code/$r->code.png";
            $img_v = file_exists($img_path) ? filemtime($img_path) : time();

            $data[] = [
                'id' => $r->id,
                'img' => "$img_path?v=$img_v",
                'code' => $r->code,
                'description' => $r->description,
                'category' => $r->category,
                'head' => $r->head,
                'screwdriver' => $r->screwdriver,
                'diameter' => $r->diameter,
                'length' => $r->item_length,
                'observation' => $r->observation,
                'files' => $link,
            ];
        }

        echo json_encode([
            'last_page' => ceil($total / $size),
            'last_row' => $total,
            'data' => $data,
        ]);
    }

    public function Stats() {}

    public function New()
    {
        $this->auth->authorize(116);
        require_once 'app/views/fasteners/new.php';
    }

    public function Save()
    {
        $user = $this->auth->authorize(116);
        header('Content-Type: application/json');

        try {
            $table = 'screws';
            $code = trim($_POST['code'] ?? '');

            if (empty($code)) {
                throw new Exception('The Code is required.');
            }

            if ($this->model->get('code', $table, " AND code = '$code'")) {
                $message = json_encode(['type' => 'error', 'message' => "The Code '$code' already exists."]);
                header('HX-Trigger: '.json_encode(['showMessage' => $message]));
                http_response_code(400);
                exit;
            }

            $item = new stdClass;
            $fields = ['code', 'category', 'description', 'head', 'screwdriver', 'diameter', 'item_length', 'observation'];
            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    $item->{$field} = $_POST[$field];
                }
            }

            $res = $this->model->save($table, $item);

            if (! empty($_FILES['files']['name'][0])) {
                $carpeta = "uploads/screws/$code";
                if (! file_exists($carpeta)) {
                    mkdir($carpeta, 0777, true);
                }

                foreach ($_FILES['files']['tmp_name'] as $i => $tmpFilePath) {
                    if ($tmpFilePath != '') {
                        $fileName = basename($_FILES['files']['name'][$i]);
                        $newFilePath = $carpeta.'/'.$fileName;

                        // SOBRESCRITURA FORZADA
                        if (file_exists($newFilePath)) {
                            @chmod($newFilePath, 0777);
                            @unlink($newFilePath);
                        }
                        move_uploaded_file($tmpFilePath, $newFilePath);
                    }
                }
            }

            $message = json_encode(['type' => 'success', 'message' => 'Saved', 'close' => 'closeNewModal']);
            header('HX-Trigger: '.json_encode(['listChanged' => true, 'showMessage' => $message]));
            http_response_code(204);

        } catch (Exception $e) {
            $message = json_encode(['type' => 'error', 'message' => $e->getMessage()]);
            header('HX-Trigger: '.json_encode(['showMessage' => $message]));
            http_response_code(500);
        }
    }

    public function Detail()
    {
        $id = $this->model->get('*', 'screws', 'and id = '.$_REQUEST['id']);
        require_once 'app/views/fasteners/edit.php';
    }

    public function DeleteFile()
    {
        $this->auth->authorize(116);
        $filePath = 'uploads/screws/'.basename($_POST['code']).'/'.basename($_POST['filename']);
        if (file_exists($filePath)) {
            @chmod($filePath, 0777);
            unlink($filePath);
            header('HX-Trigger: '.json_encode(['listChanged' => true, 'showMessage' => json_encode(['type' => 'success', 'message' => 'Deleted'])]));
        } else {
            header('HTTP/1.1 400 Bad Request');
        }
    }

    public function UpdateField()
    {
        $this->auth->authorize(116);
        $id = $_POST['id'];
        $data = $_POST;
        unset($data['id']);
        foreach ($data as $key => $value) {
            if ($value === '') {
                $data[$key] = null;
            }
        }
        $this->model->update('screws', (object) $data, $id);
        header('HX-Trigger: '.json_encode(['listChanged' => true, 'showMessage' => json_encode(['type' => 'success', 'message' => 'Updated'])]));
        http_response_code(200);
    }

    public function UploadSingleFile()
    {
        $this->auth->authorize(116);
        $code = $_POST['code'];
        $folder = "uploads/screws/$code/";
        if (! is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        if (isset($_FILES['file'])) {
            $destPath = $folder.basename($_FILES['file']['name']);

            // ELIMINACIÓN PREVIA PARA ASEGURAR SOBRESCRITURA
            if (file_exists($destPath)) {
                @chmod($destPath, 0777);
                @unlink($destPath);
            }

            if (move_uploaded_file($_FILES['file']['tmp_name'], $destPath)) {
                clearstatcache();
                header('HX-Trigger: '.json_encode(['listChanged' => true, 'showMessage' => json_encode(['type' => 'success', 'message' => 'Uploaded/Overwritten'])]));
                http_response_code(200);
            }
        }
        exit;
    }

    public function RefreshFileList()
    {
        $id = $this->model->get('*', 'screws', 'and id = '.$_REQUEST['id']);
        $dir = "uploads/screws/{$id->code}/";
        if (is_dir($dir)) {
            $files = array_diff(scandir($dir), ['.', '..']);
            foreach ($files as $f) { ?>
                <div class="flex items-center justify-between bg-white p-2 rounded-lg border border-gray-200" id="file-<?php echo md5($f); ?>">
                    <span class="text-xs text-gray-700"><?php echo $f; ?></span>
                    <button type="button" hx-post="?c=Fasteners&a=DeleteFile" hx-vals='{"code": "<?php echo $id->code; ?>", "filename": "<?php echo $f; ?>"}' hx-target="#file-<?php echo md5($f); ?>" hx-swap="delete" class="text-red-500"><i class="ri-delete-bin-line"></i></button>
                </div>
            <?php }
            }
    }

    public function Delete()
    {
        $this->model->delete('screws', " id = '".$_REQUEST['id']."'");
        header('HX-Trigger: '.json_encode(['eventChanged' => true, 'showMessage' => json_encode(['type' => 'success', 'message' => 'Delete', 'close' => 'closeNewModal'])]));
        http_response_code(204);
    }
}
