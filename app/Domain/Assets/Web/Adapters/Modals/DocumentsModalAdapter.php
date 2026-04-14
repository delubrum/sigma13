<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Adapters\Modals;

use App\Domain\Assets\Actions\RegisterDocumentAction;
use App\Domain\Assets\Data\Modals\DocumentModalData;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class DocumentsModalAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function config(): Config
    {
        return new Config(
            title: 'Agregar Documento',
            icon: 'ri-file-add-line',
            subtitle: 'Certificados, manuales o facturas del activo',
            modalWidth: '50',
            formFields: SchemaGenerator::toFields(DocumentModalData::class),
        );
    }

    public function handle(int $id): View
    {
        $config = $this->config();
        $this->hxModalWidth($config->modalWidth, '-2');
        $this->hxTriggers['open-modal-2'] = true;

        return view('components::new-modal', [
            'route' => "assets/{$id}/documents",
            'config' => $config,
            'target' => '#modal-body-2',
            'closeEvent' => 'close-modal-2',
            'suffix' => '-2',
        ]);
    }

    public function asController(int $id): Response
    {
        return $this->hxView($this->handle($id));
    }

    public function asStore(Request $request, int $id): JsonResponse
    {
        $data = DocumentModalData::from($request->all());

        RegisterDocumentAction::run($id, $data, $request->file('file'));

        $this->hxNotify('Documento cargado correctamente');
        $this->hxRefreshTables(['tabTableDocuments']);
        $this->hxCloseModals(['modal-body-2']);

        return $this->hxResponse();
    }
}
