<?php

declare(strict_types=1);

namespace App\Domain\Operations\Actions;

use Lorisleiva\Actions\Concerns\AsAction;

final class GetDocumentData
{
    use AsAction;

    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): array
    {
        $dir = base_path('public/uploads/docs');
        $files = [];

        if (is_dir($dir)) {
            $rii = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($rii as $file) {
                if ($file->isDir()) continue;

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
                $fileDisplay = ucfirst(str_replace(['-', '_'], ' ', pathinfo($fileName, \PATHINFO_FILENAME)));
                $ext = pathinfo($fileName, \PATHINFO_EXTENSION);
                if ($ext) $fileDisplay .= '.' . $ext;

                $files[] = [
                    'category' => $category,
                    'type' => $type,
                    'name' => "<a style='color:blue' href='/uploads/docs/$relativePath' target='_blank'>$fileDisplay</a>",
                    'date' => date('Y-m-d H:i:s', $file->getMTime()),
                    'size' => $this->formatBytes($file->getSize()),
                    'raw_category' => $category,
                    'raw_type' => $type,
                    'raw_name' => $fileDisplay,
                    'raw_date' => $file->getMTime(),
                    'raw_size' => $file->getSize(),
                ];
            }
        }

        // Filtering
        foreach ($filters as $field => $value) {
            $value = strtolower((string)$value);
            $files = array_filter($files, function ($item) use ($field, $value) {
                $target = strtolower($item["raw_$field"] ?? $item[$field] ?? '');
                return strpos($target, $value) !== false;
            });
        }

        // Sorting
        if (!empty($sorts)) {
            foreach ($sorts as $field => $dir) {
                $multiplier = $dir === 'asc' ? 1 : -1;
                usort($files, function ($a, $b) use ($field, $multiplier) {
                    $valA = $a["raw_$field"] ?? $a[$field];
                    $valB = $b["raw_$field"] ?? $b[$field];
                    return $multiplier * ($valA <=> $valB);
                });
            }
        }

        $total = count($files);
        $offset = ($page - 1) * $size;
        $paginatedFiles = array_slice($files, $offset, $size);

        return [
            'data' => array_values($paginatedFiles),
            'total' => $total,
            'last_page' => (int)ceil($total / $size),
        ];
    }

    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
