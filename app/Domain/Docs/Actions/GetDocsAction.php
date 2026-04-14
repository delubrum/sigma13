<?php

declare(strict_types=1);

namespace App\Domain\Docs\Actions;

use App\Domain\Docs\Data\DocsTableData;
use App\Domain\Shared\Data\PaginatedResult;
use FilesystemIterator;
use Lorisleiva\Actions\Concerns\AsAction;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class GetDocsAction
{
    use AsAction;

    public function handle(string $directory = 'uploads/docs'): PaginatedResult
    {
        $basePath = base_path($directory);
        $files = [];

        if (! is_dir($basePath)) {
            return new PaginatedResult(items: [], lastPage: 0, total: 0);
        }

        $rii = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basePath, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($rii as $file) {
            if ($file->isDir()) {
                continue;
            }

            $path = $file->getPathname();
            $relativeToDir = str_replace('\\', '/', substr($path, strlen($basePath) + 1));

            $category = '';
            $type = '';
            $dirName = dirname($relativeToDir);
            
            if ($dirName !== '.') {
                $parts = explode('/', $dirName);
                $category = $parts[0] ?? '';
                $type = $parts[1] ?? '';
            }

            $fileName = $file->getFilename();
            $fileDisplay = ucfirst(str_replace(['-', '_'], ' ', pathinfo($fileName, PATHINFO_FILENAME)));
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            if ($ext) {
                $fileDisplay .= '.' . $ext;
            }

            $files[] = DocsTableData::from([
                'category' => $category,
                'type' => $type,
                'name' => $fileDisplay,
                'date' => date('Y-m-d H:i:s', $file->getMTime()),
                'size' => $this->formatBytes($file->getSize()),
                'url' => asset($directory . '/' . $relativeToDir),
                'raw_name' => $fileDisplay,
                'raw_size' => $file->getSize(),
                'raw_date' => $file->getMTime(),
            ]);
        }

        $totalCount = count($files);
        $size = 15; // Default size
        
        return new PaginatedResult(
            items: $files,
            lastPage: (int) ceil($totalCount / $size),
            total: $totalCount
        );
    }

    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
