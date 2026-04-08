<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Actions\Password\SendResetLink;
use App\Data\Shared\Config;
use App\Data\Shared\Field;
use App\Data\Users\Form;
use App\Models\User;
use App\Support\HtmxOrchestrator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

final class Create
{
    use AsAction;
    use HtmxOrchestrator;

    public function config(): Config
    {
        return new Config(
            title: 'Nuevo Usuario',
            subtitle: 'Registro de personal',
            icon: 'ri-user-add-line',
            modalWidth: '35%',
            formFields: [
                new Field(name: 'name', label: 'Nombre Completo', required: true, placeholder: 'Nombre y apellidos'),
                new Field(name: 'email', label: 'Email', type: 'email', required: true, placeholder: 'ejemplo@correo.com'),
                new Field(name: 'document', label: 'Cédula / Documento', required: true, placeholder: 'Número de identidad'),
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
            'route'  => 'users',
            'config' => $config,
        ]);
    }

    public function asController(Request $request): Response
    {
        return $this->hxView($this->handle());
    }

    public function asStore(Request $request): JsonResponse
    {
        $data = Form::from($request->all());

        $user = User::create([
            'name'      => $data->name,
            'email'     => $data->email,
            'document'  => $data->document,
            'password'  => Hash::make(Str::random(32)),
            'is_active' => true,
        ]);

        // Enviar link de reset inmediatamente
        SendResetLink::run($user->email);

        $this->hxNotify('Usuario creado. Se ha enviado un correo para establecer su contraseña.');
        $this->hxCloseModals(['modal-body']);

        return $this->hxResponse();
    }
}
