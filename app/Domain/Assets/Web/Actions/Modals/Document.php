<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Actions\Modals;

use App\Domain\Assets\Models\Asset;
use App\Domain\Assets\Data\Modals\AssetDocumentData;
use App\Domain\Assets\Models\AssetDocument;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\Field;
use App\Support\HtmxOrchestrator;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Honeypot\ProtectAgainstSpam;

final class Document
{
    use AsAction;

    /** @return list<class-string> */
    /** @return list<string> */
    public function getControllerMiddleware(): array
    {
        return [ProtectAgainstSpam::class];
    }

    use HtmxOrchestrator;

    public function config(?Asset $asset = null): Config
    {
        return new Config(
            title: $asset ? "Subir Documento: <span class='opacity-50'>{$asset->serial}</span>" : 'Subir Documento',
            subtitle: $asset?->name ?? 'Añadir un archivo anexo al activo',
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
                    widget: 'flatpickr',
                    required: false,
                    placeholder: 'YYYY-MM-DD',
                ),
                new Field(
                    name: 'file',
                    label: 'Archivo Principal (.pdf .jpg .zip)',
                    type: 'file',
                    widget: 'sigma-file',
                    required: true,
                    placeholder: '',
                    accept: '.pdf,.jpg,.png,.jpeg',
                ),
            ],
            multipart: true,
        );
    }

    public function handle(Asset $asset): Response
    {
        $config = $this->config($asset);

        return $this->hxView('components::new-modal', [
            'route' => "assets/{$asset->id}/documents",
            'customPostRoute' => route('assets.documents.store', $asset->id),
            'config' => $config,
            'target' => '#modal-body-2',
            'closeEvent' => 'close-modal-2',
            'suffix' => '-2',
        ]);
    }

    public function asController(Asset $asset): Response
    {
        return $this->handle($asset);
    }

    public function asStore(Request $request, Asset $asset): JsonResponse
    {
        $data = AssetDocumentData::from($request->all());

        $doc = AssetDocument::create([
            'asset_id' => $asset->id,
            'name' => $data->name,
            'code' => $data->code,
            'expiry' => $data->expiry,
            'user_id' => Auth::id(),
        ]);

        if ($request->hasFile('file')) {
            $media = $doc->addMediaFromRequest('file')->toMediaCollection('documents');
            $doc->update(['url' => route('shared.media.download', $media->id)]);
        }

        $this->hxNotify('Documento anexado correctamente');
        $this->hxRefreshTables(['dt_assets', 'dt_tabTableDocuments']);
        $this->hxCloseModals(['modal-body-2']);

        return $this->hxResponse();
    }
}
