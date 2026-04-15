<?php

declare(strict_types=1);

namespace App\Domain\Docs\Web\Controllers;

use App\Domain\Docs\Actions\GetDocsAction;
use App\Domain\Shared\Data\Column;
use App\Domain\Shared\Data\Config;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class DocsController
{
    public function index(): View
    {
        $config = new Config(
            title: 'Infrastructure / Documents',
            icon: 'ri-folder-info-fill',
            subtitle: 'Manage system documents and infrastructure improvements',
            columns: [
                Column::make('Category', 'category', 150),
                Column::make('Type', 'type', 150),
                Column::make('Name', 'name', null, 'left', 'link', ['urlField' => 'url', 'target' => '_blank']),
                Column::make('Date', 'date', 180, 'center'),
                Column::make('Size', 'size', 100, 'center'),
            ]
        );

        return view('docs::index', [
            'config' => $config,
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $docs = GetDocsAction::run();

        // Sorting
        if ($sort = $request->get('sort')) {
            $field = $sort[0]['field'];
            $direction = $sort[0]['dir'];

            $docs = $docs->sortBy(fn ($doc) => $doc->{"raw_$field"} ?? $doc->{$field}, SORT_REGULAR, $direction === 'desc');
        }

        // Filtering
        if ($filters = $request->get('filter')) {
            foreach ($filters as $f) {
                $field = $f['field'];
                $value = strtolower((string) $f['value']);

                $docs = $docs->filter(function ($doc) use ($field, $value): bool {
                    $target = strtolower((string) ($doc->{"raw_$field"} ?? $doc->{$field} ?? ''));

                    return str_contains($target, $value);
                });
            }
        }

        $total = $docs->count();
        $size = (int) $request->get('size', 25);
        $page = (int) $request->get('page', 1);
        $offset = ($page - 1) * $size;

        return response()->json([
            'data' => $docs->slice($offset, $size)->values(),
            'last_page' => (int) ceil($total / $size),
            'last_row' => $total,
        ]);
    }
}
