<?php

declare(strict_types=1);

namespace App\Actions\Maintenance;

use App\Data\Maintenance\CreateTicket;
use App\Data\Shared\Config;
use App\Data\Shared\Field;
use App\Data\Shared\FieldWidth;
use App\Models\Mnt;
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
        return new Config(
            title: 'Nuevo Correctivo',
            subtitle: 'Machinery / Service Desk',
            icon: 'ri-tools-line',
            modalWidth: '45%',
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
                    name: 'description',
                    label: 'Descripción de la falla',
                    required: true,
                    type: 'textarea',
                    placeholder: 'Describe la falla o avería en detalle...',
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
            'route'  => 'maintenance',
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

        Mnt::create([
            'user_id'     => Auth::id(),
            'facility'    => $data->facility,
            'priority'    => $data->priority,
            'description' => $data->description,
            'status'      => 'Open',
            'kind'        => 'Machinery',
        ]);

        $this->hxNotify('Ticket creado correctamente');
        $this->hxCloseModals(['modal-body']);

        return $this->hxResponse();
    }
}
