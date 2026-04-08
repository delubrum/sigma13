<?php

declare(strict_types=1);

namespace App\Actions\Assets\Modals;

use App\Data\Assets\Modals\AssetDocumentData;
use App\Data\Shared\Config;
use App\Data\Shared\Field;
use App\Models\AssetDocument;
use App\Support\HtmxOrchestrator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;

final class Document
{
    use AsAction;
    use HtmxOrchestrator;

    public function config(int $id): Config
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
                    required: true,
                    placeholder: 'Factura, Garantía, Acta...',
                    type: 'text',
                ),
                new Field(
                    name: 'code',
                    label: 'Código o Referencia',
                    required: false,
                    placeholder: 'Código interno, id factura, etc',
                    type: 'text',
                ),
                new Field(
                    name: 'expiry',
                    label: 'Vencimiento',
                    required: false,
                    placeholder: 'YYYY-MM-DD',
                    type: 'date',
                ),
                new Field(
                    name: 'file',
                    label: 'Archivo Principal (.pdf .jpg .zip)',
                    required: true,
                    placeholder: '',
                    type: 'file',
                ),
            ],
            multipart: true,
        );
    }

    public function handle(int $id): View
    {
        $config = $this->config($id);

        $this->hxModalHeader([
            'icon' => $config->icon,
            'title' => $config->title,
            'subtitle' => $config->subtitle,
        ], '-2');

        $this->hxModalWidth($config->modalWidth, '-2');
        $this->hxTriggers['open-modal-2'] = true;

        return view('components.new-modal', [
            'route' => "assets/{$id}/documents",
            'config' => $config,
            'target' => '#modal-body-2',
            'closeEvent' => 'close-modal-2',
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
        if ($data->file !== null) {
            $path = $data->file->store("assets/{$id}/documents", 'public');
            if ($path) {
                /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
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
