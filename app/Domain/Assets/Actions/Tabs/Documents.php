<?php

declare(strict_types=1);

namespace App\Domain\Assets\Actions\Tabs;

use App\Contracts\HasModule;
use App\Domain\Assets\Data\Tabs\DocumentTable;
use App\Domain\Assets\Models\AssetDocument;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\Field;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class Documents implements HasModule
{
    use AsAction;

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

    public function handle(int $id): View
    {
        return view('components.tab-index', [
            'config' => $this->config(),
            'parentId' => $id,
            'route' => 'assets.documents',
        ]);
    }

    public function asController(Request $request, int $id): View
    {
        return $this->handle($id);
    }

    public function asData(Request $request, int $id): JsonResponse
    {
        $page = max(1, $request->integer('page', 1));
        $size = max(1, $request->integer('size', 10));

        $paginator = AssetDocument::query()
            ->where('asset_id', $id)
            ->orderByDesc('id')
            ->paginate($size, ['*'], 'page', $page);

        return response()->json([
            'data' => $paginator->map(fn (AssetDocument $doc): DocumentTable => DocumentTable::fromModel($doc))->values(),
            'last_page' => $paginator->lastPage(),
        ]);
    }
}
