<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Actions\Tabs;

use App\Domain\Assets\Data\Tabs\DocumentTable;
use App\Domain\Assets\Models\Asset;
use App\Domain\Assets\Models\AssetDocument;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\Field;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class Documents
{
    use AsAction;
    use HtmxOrchestrator;

    public function config(): Config
    {
        return new Config(
            title: 'Documentos del Activo',
            subtitle: '',
            icon: 'ri-file-line',
            newButtonLabel: 'Subir Documento',
            columns: DocumentTable::columns(),
            formFields: [
                new Field(name: 'name', label: 'Nombre Documento', required: true),
                new Field(name: 'code', label: 'Código', required: false),
                new Field(name: 'expiry', label: 'Vencimiento', required: false),
                new Field(name: 'file', label: 'Archivo', required: true),
            ],
        );
    }

    public function handle(Asset $asset): Response
    {
        return $this->hxView('components::tab-index', [
            'config' => $this->config(),
            'parentId' => $asset->id,
            'route' => 'assets.documents',
            'tableId' => 'tabTableDocuments',
        ]);
    }

    public function asController(Asset $asset): Response
    {
        return $this->handle($asset);
    }

    public function asData(Request $request, Asset $asset): JsonResponse
    {
        $page = max(1, $request->integer('page', 1));
        $size = max(1, $request->integer('size', 10));
        $filters = $request->input('filters', []);

        $query = AssetDocument::query()->where('asset_id', $asset->id);

        // Aplicar filtros de Tabulator si existen
        foreach ($filters as $filter) {
            $field = $filter['field'];
            $value = $filter['value'];
            if ($value !== null && $value !== '') {
                $query->where($field, 'like', '%' . $value . '%');
            }
        }

        $paginator = $query->orderByDesc('id')
            ->paginate($size, ['*'], 'page', $page);

        return response()->json([
            'data' => $paginator->map(fn (AssetDocument $doc): DocumentTable => DocumentTable::fromModel($doc))->values(),
            'last_page' => $paginator->lastPage(),
        ]);
    }
}
