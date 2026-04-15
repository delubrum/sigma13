<?php

declare(strict_types=1);

namespace App\Domain\Docs\Web\Adapters;

use App\Contracts\HasModule;
use App\Domain\Docs\Actions\GetDocsAction;
use App\Domain\Docs\Data\DocsTableData;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class DocsIndexAdapter implements HasModule
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        return $this->hxView('shared::components.index', [
            'route' => 'docs',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title: 'Infrastructure / Documents',
            icon: 'ri-folder-info-fill',
            subtitle: 'Manage system documents and infrastructure improvements',
            newButtonLabel: '', // Read-only as requested
            columns: SchemaGenerator::toColumns(DocsTableData::class),
        );
    }

    public function asController(): Response
    {
        return $this->handle();
    }

    public function asData(Request $request): JsonResponse
    {
        $result = GetDocsAction::run();
        $items = collect($result->items);

        // Sorting
        if ($sort = $request->get('sort')) {
            $field = $sort[0]['field'];
            $direction = $sort[0]['dir'];

            $items = $items->sortBy(fn ($doc) => $doc->{"raw_$field"} ?? $doc->{$field}, SORT_REGULAR, $direction === 'desc');
        }

        // Filtering
        if ($filters = $request->get('filter')) {
            foreach ($filters as $f) {
                $field = $f['field'];
                $value = strtolower((string) $f['value']);

                $items = $items->filter(function ($doc) use ($field, $value): bool {
                    $target = strtolower((string) ($doc->{"raw_$field"} ?? $doc->{$field} ?? ''));

                    return str_contains($target, $value);
                });
            }
        }

        $totalCount = $items->count();
        $size = (int) $request->get('size', 15);
        $page = (int) $request->get('page', 1);
        $offset = ($page - 1) * $size;

        return response()->json([
            'data' => $items->slice($offset, $size)->values(),
            'last_page' => (int) ceil($totalCount / $size),
            'last_row' => $totalCount,
        ]);
    }
}
