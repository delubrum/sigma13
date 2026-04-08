<?php

declare(strict_types=1);

namespace App\Actions\IT;

use App\Data\IT\CreateTicket;
use App\Data\Shared\Config;
use App\Data\Shared\Field;
use App\Data\Shared\FieldWidth;
use App\Models\Asset;
use App\Models\It;
use App\Support\HtmxOrchestrator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

final class Create
{
    use AsAction;
    use HtmxOrchestrator;

    public function config(): Config
    {
        $assets = Asset::whereIn('area', ['IT'])
            ->orderBy('hostname')
            ->get()
            ->mapWithKeys(fn ($a) => [
                (string) $a->id => mb_convert_case($a->hostname ?? '', MB_CASE_TITLE, 'UTF-8') . ' | ' . $a->serial,
            ])
            ->all();

        return new Config(
            title: 'New Service Desk',
            subtitle: 'IT / Infrastructure',
            icon: 'ri-computer-line',
            modalWidth: '40%',
            multipart: true,
            formFields: [
                new Field(
                    name: 'facility',
                    label: 'Sede',
                    required: true,
                    type: 'select',
                    options: ['ESM1' => 'ESM1', 'ESM2' => 'ESM2', 'ESM3' => 'ESM3', 'Medellín' => 'Medellín'],
                    widget: 'slimselect',
                    width: FieldWidth::Half,
                ),
                new Field(
                    name: 'kind',
                    label: 'Tipo',
                    required: true,
                    type: 'select',
                    options: [
                        'Equipment/Accessories' => 'Equipment / Accessories',
                        'Licenses'              => 'Licenses',
                        'Permissions'           => 'Permissions',
                    ],
                    widget: 'slimselect',
                    width: FieldWidth::Half,
                ),
                new Field(
                    name: 'priority',
                    label: 'Prioridad',
                    required: true,
                    type: 'select',
                    options: [
                        'High'   => 'Right Now. Locked',
                        'Medium' => 'Today. Need Attention',
                        'Low'    => 'Tomorrow. I Can Wait',
                    ],
                    widget: 'slimselect',
                    width: FieldWidth::Half,
                ),
                new Field(
                    name: 'asset_id',
                    label: 'Equipo (opcional)',
                    type: 'select',
                    options: ['' => '—'] + $assets,
                    widget: 'slimselect',
                    width: FieldWidth::Half,
                ),
                new Field(
                    name: 'description',
                    label: 'Descripción',
                    required: true,
                    type: 'textarea',
                    placeholder: 'Describe el problema...',
                ),
                new Field(
                    name: 'files',
                    label: 'Evidencia (Foto)',
                    type: 'file',
                    widget: 'filepond',
                ),
            ],
        );
    }

    public function handle(): View
    {
        $config = $this->config();

        $this->hxModalHeader([
            'icon'     => $config->icon,
            'title'    => $config->title,
            'subtitle' => $config->subtitle,
        ]);

        $this->hxModalWidth($config->modalWidth);

        return view('components.new-modal', [
            'route'  => 'it',
            'config' => $config,
        ]);
    }

    public function asController(Request $request): Response
    {
        return $this->hxView($this->handle());
    }

    public function asStore(Request $request): JsonResponse
    {
        $data = CreateTicket::from($request->all());

        It::create([
            'user_id'     => Auth::id(),
            'facility'    => $data->facility,
            'kind'        => $data->kind,
            'priority'    => $data->priority,
            'asset_id'    => $data->asset_id,
            'description' => $data->description,
            'status'      => 'Open',
        ]);

        $this->hxNotify('Ticket creado');
        $this->hxCloseModals(['modal-body']);

        return $this->hxResponse();
    }
}
