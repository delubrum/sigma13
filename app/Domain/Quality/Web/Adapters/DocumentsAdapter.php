<?php

declare(strict_types=1);

namespace App\Domain\Quality\Web\Adapters;

use App\Contracts\HasModule;
use App\Domain\Quality\Actions\GetDocumentsData;
use App\Domain\Quality\Data\DocumentTableData;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class DocumentsAdapter implements HasModule
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        return $this->hxView('components::index', [
            'route'  => 'quality/documents',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title:          'Documentos SGC',
            subtitle:       'Repositorio del sistema de gestión de calidad',
            icon:           'ri-file-search-line',
            newButtonLabel: 'Nuevo Documento',
            modalWidth:     '60',
            columns:        SchemaGenerator::toColumns(DocumentTableData::class),
        );
    }

    public function asController(): Response
    {
        return $this->handle();
    }

    public function asData(Request $request): JsonResponse
    {
        $filters = $request->collect('filter')->pluck('value', 'field')->toArray();
        $sorts   = $request->collect('sort')->pluck('dir', 'field')->toArray();

        $result = GetDocumentsData::run(
            filters: $filters,
            sorts:   $sorts,
            page:    $request->integer('page', 1),
            size:    $request->integer('size', 15),
        );

        return response()->json([
            'data'      => $result['data'],
            'last_page' => $result['last_page'],
            'last_row'  => $result['total'],
        ]);
    }
}
