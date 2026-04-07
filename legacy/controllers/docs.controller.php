<?php

class docsController
{
    public function __construct(public Model $model, private AuthService $auth) {}

    public function Index()
    {
        $user = $this->auth->authorize(142);

        $tabulator = true;
        $title = 'Infrastructure / Documents';
        $button = 'New Document';
        $content = 'app/components/list.php';
        $columns = '[
            { title: "Category", field: "category", headerHozAlign: "center", headerFilter: "input" },
            { title: "Type", field: "type", headerHozAlign: "center", headerFilter: "input" },
            { title: "Name", field: "name", headerHozAlign: "center", headerFilter: "input", formatter:"html" },
            { title: "Date", field: "date", headerHozAlign: "center", headerFilter: "input" },
            { title: "Size", field: "size", headerHozAlign: "center", headerFilter: "input" }
        ]';
        require_once 'app/views/index.php';
    }

    public function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision).' '.$units[$pow];
    }

    public function Data()
    {
        $user = $this->auth->authorize(142);

        header('Content-Type: application/json');

        $dir = 'uploads/docs';
        $files = [];

        if (is_dir($dir)) {
            $rii = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
            );

            foreach ($rii as $file) {
                if ($file->isDir()) {
                    continue;
                }

                $path = $file->getPathname();
                $relativePath = str_replace('\\', '/', substr($path, strlen($dir) + 1));

                $category = '';
                $type = '';
                $dirName = dirname($relativePath);
                if ($dirName !== '.') {
                    $parts = explode('/', $dirName);
                    $category = $parts[0] ?? '';
                    $type = $parts[1] ?? '';
                }

                $fileName = $file->getFilename();
                $fileDisplay = ucfirst(str_replace(['-', '_'], ' ', pathinfo($fileName, PATHINFO_FILENAME)));
                $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                if ($ext) {
                    $fileDisplay .= '.'.$ext;
                }

                $files[] = [
                    'category' => $category,
                    'type' => $type,
                    'name' => '<a style="color:blue" href="/sigma/'.str_replace('\\', '/', $path).'" target="_blank">'.$fileDisplay.'</a>',
                    'date' => date('Y-m-d H:i:s', $file->getMTime()),
                    'size' => $this->formatBytes($file->getSize()),

                    // Raw para ordenar / filtrar
                    'raw_category' => $category,
                    'raw_type' => $type,
                    'raw_name' => $fileDisplay,
                    'raw_date' => $file->getMTime(),
                    'raw_size' => $file->getSize(),
                ];
            }
        }

        // === Paginación ===
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $size = isset($_GET['size']) ? (int) $_GET['size'] : 15;
        $offset = ($page - 1) * $size;

        // === Filtros ===
        if (isset($_GET['filter']) && is_array($_GET['filter'])) {
            foreach ($_GET['filter'] as $f) {
                if (! isset($f['field'], $f['value'])) {
                    continue;
                }
                $field = $f['field'];
                $value = strtolower($f['value']);

                $files = array_filter($files, function ($item) use ($field, $value) {
                    $target = strtolower($item["raw_$field"] ?? $item[$field] ?? '');

                    return strpos($target, $value) !== false;
                });
            }
        }

        // === Ordenamiento ===
        if (isset($_GET['sort'][0]['field'], $_GET['sort'][0]['dir'])) {
            $sortField = $_GET['sort'][0]['field'];
            $dir = strtoupper($_GET['sort'][0]['dir']) === 'ASC' ? 1 : -1;

            usort($files, function ($a, $b) use ($sortField, $dir) {
                $valA = $a["raw_$sortField"] ?? $a[$sortField];
                $valB = $b["raw_$sortField"] ?? $b[$sortField];

                return $dir * ($valA <=> $valB);
            });
        }

        $total = count($files);
        $files = array_slice($files, $offset, $size);

        echo json_encode([
            'data' => array_values($files),
            'last_page' => ceil($total / $size),
            'last_row' => $total,
        ]);
    }

    public function Stats()
    {
        $user = $this->auth->authorize(143);

        $dir = 'uploads/docs';
        $files = [];

        if (is_dir($dir)) {
            $rii = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
            );

            foreach ($rii as $file) {
                if ($file->isDir()) {
                    continue;
                }

                $path = $file->getPathname();
                $relativePath = str_replace('\\', '/', substr($path, strlen($dir) + 1));

                $category = '';
                $type = '';
                $dirName = dirname($relativePath);
                if ($dirName !== '.') {
                    $parts = explode('/', $dirName);
                    $category = $parts[0] ?? '';
                    $type = $parts[1] ?? '';
                }

                $files[] = [
                    'category' => $category,
                    'type' => $type,
                ];
            }
        }

        // === Filtros ===
        if (isset($_GET['filter']) && is_array($_GET['filter'])) {
            foreach ($_GET['filter'] as $f) {
                if (! isset($f['field'], $f['value'])) {
                    continue;
                }
                $field = $f['field'];
                $value = strtolower($f['value']);

                $files = array_filter($files, function ($item) use ($field, $value) {
                    $target = strtolower($item[$field] ?? '');

                    return strpos($target, $value) !== false;
                });
            }
        }

        // === Contar por type ===
        $counts = [];
        foreach ($files as $f) {
            $type = $f['type'] ?: 'unknown';
            if (! isset($counts[$type])) {
                $counts[$type] = 0;
            }
            $counts[$type]++;
        }

        // Variables individuales como en tu versión SQL
        $a = $counts['Registros'] ?? 0;
        $b = $counts['Procedimientos'] ?? 0;
        $c = $counts['Documentos'] ?? 0;

        // Renderizar vista con las variables
        require_once 'app/views/docs/stats.php';
    }

    public function New()
    {
        $user = $this->auth->authorize(143);

        if (! empty($_REQUEST['id'])) {
            $filters = 'and id = '.$_REQUEST['id'];
            $id = $this->model->get('*', 'infraimprovement', $filters);
        }
        $status = (! empty($_REQUEST['status'])) ? $_REQUEST['status'] : false;
        require_once 'app/views/docs/new.php';
    }

    public function Save()
    {
        $user = $this->auth->authorize(143);

        header('Content-Type: application/json');

        $item = new stdClass;
        foreach ($_POST as $k => $val) {
            if (! empty($val)) {
                if ($k != 'id' && $k != 'files') {
                    $item->{$k} = $val;
                }
            }
        }

        $item->user_id = $_SESSION['id-SIGMA'];
        $item->status = 'open';
        $item->status_at = date('Y-m-d H:i:s');

        $id = $this->model->save('infraimprovement', $item);

        if ($id !== false) {

            $message = empty($_POST['id'])
                ? '{"type": "success", "message": "Improvement Save", "close" : "closeNewModal"}'
                : '{"type": "success", "message": "Improvement Updated", "close" : "closeNewModal"}';

            $hxTriggerData = json_encode([
                'listChanged' => true,
                'showMessage' => $message,
            ]);
            header('HX-Trigger: '.$hxTriggerData);
            http_response_code(204);
        }
    }
}
