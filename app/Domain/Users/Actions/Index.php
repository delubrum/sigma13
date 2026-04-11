<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Contracts\HasDetail;
use App\Contracts\HasModule;
use App\Domain\Shared\Data\ActionOption;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\Field;
use App\Domain\Shared\Data\Tabs;
use App\Domain\Users\Data\Sidebar;
use App\Domain\Users\Data\Table;
use App\Domain\Users\Data\UpsertData;
use App\Domain\Users\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

final class Index implements HasDetail, HasModule
{
    use AsAction;

    public function handle(): Config
    {
        return $this->config();
    }

    public function config(): Config
    {
        return new Config(
            title: 'Usuarios',
            subtitle: 'Registro de acceso al sistema',
            icon: 'ri-user-settings-line',
            newButtonLabel: 'Nuevo Usuario',
            showKpi: true,
            modalWidth: 'md',
            columns: Table::columns(),
            formFields: UpsertData::fields(),
            tabs: [
                new Tabs(key: 'info', label: 'Permisos', icon: 'ri-shield-keyhole-line', route: 'users.info', default: true),
            ],
            options: [
                new ActionOption(
                    label: 'Editar Usuario',
                    icon: 'ri-edit-line',
                    route: 'users/create',
                    target: '#modal-body',
                    level: 1
                ),
            ]
        );
    }

    public function sidebarData(int $id): Sidebar
    {
        return Sidebar::from(User::findOrFail($id));
    }
}
