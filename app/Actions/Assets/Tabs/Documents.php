<?php

declare(strict_types=1);

namespace App\Actions\Assets\Tabs;

use App\Models\Asset;
use App\Models\AssetDocument;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;

final class Documents implements \App\Contracts\HasModule
{
    use AsAction;

    public function config(): \App\Data\Shared\Config
    {
        return new \App\Data\Shared\Config(
            title: 'Documentos del Activo',
            subtitle: '',
            icon: 'ri-file-line',
            newButtonLabel: 'Subir Documento',
            columns: [
                ['title' => 'Nombre', 'field' => 'name', 'width' => 300],
                ['title' => 'Código de Ref.', 'field' => 'code'],
                ['title' => 'Vencimiento', 'field' => 'expiry'],
                ['title' => 'Archivo', 'field' => 'file', 'formatter' => 'html', 'hozAlign' => 'center', 'width' => 150],
            ],
            formFields: [
                new \App\Data\Shared\Field(name: 'name', label: 'Nombre Documento', required: true),
                new \App\Data\Shared\Field(name: 'code', label: 'Código', required: false),
                new \App\Data\Shared\Field(name: 'expiry', label: 'Vencimiento', required: false),
                new \App\Data\Shared\Field(name: 'file', label: 'Archivo', required: true),
            ]
        );
    }

    public function handle(int $id): \Illuminate\Contracts\View\View
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
        $page = max(1, (int) $request->integer('page', 1));
        $size = max(1, (int) $request->integer('size', 10));
        $offset = ($page - 1) * $size;

        $query = AssetDocument::query()
            ->where('asset_id', $id);

        $total = $query->count();
        
        $documents = $query->orderByDesc('id')
            ->offset($offset)
            ->limit($size)
            ->get()
            ->map(fn (AssetDocument $doc): \App\Data\Assets\Tabs\DocumentTable => \App\Data\Assets\Tabs\DocumentTable::fromModel($doc));

        return response()->json([
            'data' => $documents->all(),
            'last_page' => (int) ceil($total / $size),
        ]);
    }
}
