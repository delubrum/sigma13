<?php

declare(strict_types=1);

namespace App\Domain\Assets\Actions\Modals;

use App\Domain\Assets\Data\Modals\AssetDocumentData;
use App\Domain\Assets\Models\AssetDocument;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\Field;
use App\Support\HtmxOrchestrator;
use Illuminate\Contracts\View\View;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;

final class Document
{
    use AsAction;
    use HtmxOrchestrator;

    public function config(): Config
    {
        return new Config(
            title: 'Subir Documento',
            subtitle: 'Añadir un archivo anexo al activo',
            icon: 'ri-upload-cloud-2-line',
            newButtonLabel: 'Guardar',
            modalWidth: '40%',
            columns: [],
            formFields: [
                new Field(
                    name: 'name',
                    label: 'Nombre del Documento',
                    type: 'text',
                    required: true,
                    placeholder: 'Factura, Garantía, Acta...',
                ),
                new Field(
                    name: 'code',
                    label: 'Código o Referencia',
                    type: 'text',
                    required: false,
                    placeholder: 'Código interno, id factura, etc',
                ),
                new Field(
                    name: 'expiry',
                    label: 'Vencimiento',
                    type: 'date',
                    required: false,
                    placeholder: 'YYYY-MM-DD',
                ),
                new Field(
                    name: 'file',
                    label: 'Archivo Principal (.pdf .jpg .zip)',
                    type: 'file',
                    required: true,
                    placeholder: '',
                ),
            ],
            multipart: true,
        );
    }

    public function handle(int $id): View
    {
        $config = $this->config();

        $this->hxModalWidth($config->modalWidth, '-2');
        $this->hxTriggers['open-modal-2'] = true;

        return view('components.new-modal', [
            'route' => "assets/{$id}/documents",
            'config' => $config,
            'target' => '#modal-body-2',
            'closeEvent' => 'close-modal-2',
            'suffix' => '-2',
        ]);
    }

    public function asController(Request $request, int $id): Response
    {
        return $this->hxView($this->handle($id));
    }

    public function asStore(Request $request, int $id): JsonResponse
    {
        $data = AssetDocumentData::from($request->all());

        $url = null;
        if ($data->file instanceof UploadedFile) {
            $path = $data->file->store("assets/{$id}/documents", 'public');
            if ($path) {
                /** @var FilesystemAdapter $disk */
                $disk = Storage::disk('public');
                $url = $disk->url($path);
            }
        }

        AssetDocument::create([
            'asset_id' => $id,
            'name' => $data->name,
            'code' => $data->code,
            'expiry' => $data->expiry,
            'url' => $url,
            'user_id' => Auth::id(),
        ]);

        $this->hxNotify('Documento anexado correctamente');
        $this->hxRefreshTables(['tabTableDocuments']);
        $this->hxCloseModals(['modal-body-2']);

        return $this->hxResponse();
    }
}
