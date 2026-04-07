<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

class NotificationsController
{
    public function __construct(public Model $model, private AuthService $auth) {}

    /**
     * Helper para obtener el usuario sin disparar el exit/403 de authorize()
     */
    private function getSessionUser()
    {
        // Usamos el método user() que ya tienes, pero controlamos el flujo
        // para que las notificaciones no maten la ejecución.
        if (empty($_SESSION['id-SIGMA'])) {
            return null;
        }
        $userId = (int) $_SESSION['id-SIGMA'];

        return $this->model->get('*', 'users', "AND active = true AND id = $userId");
    }

    public function Index()
    {
        $count = 0;
        $user = $this->getSessionUser();

        if ($user) {
            $userPermissions = json_decode($user->permissions ?? '[]', true) ?: [];

            if (in_array(159, $userPermissions)) {
                $count += $this->model->get('count(id) as total', 'tickets', "and kind = 'HR' and status <> 'Closed'")->total;
            }
            if (in_array(162, $userPermissions)) {
                $count += $this->model->get('count(id) as total', 'tickets', "and kind = 'OHS' and status <> 'Closed'")->total;
            }
            if (in_array(163, $userPermissions)) {
                $count += $this->model->get('count(id) as total', 'tickets', "and kind = 'Marketing' and status <> 'Closed'")->total;
            }
        }

        // Usamos require en lugar de require_once para componentes de UI
        require 'app/components/notifications.php';
    }

    public function Data()
    {
        $count = 0;
        $user = $this->getSessionUser();

        if ($user) {
            $userPermissions = json_decode($user->permissions ?? '[]', true) ?: [];

            if (in_array(159, $userPermissions)) {
                $count += $this->model->get('count(id) as total', 'tickets', "and kind = 'HR' and status <> 'Closed'")->total;
            }
            if (in_array(162, $userPermissions)) {
                $count += $this->model->get('count(id) as total', 'tickets', "and kind = 'OHS' and status <> 'Closed'")->total;
            }
            if (in_array(163, $userPermissions)) {
                $count += $this->model->get('count(id) as total', 'tickets', "and kind = 'Marketing' and status <> 'Closed'")->total;
            }
        }

        require 'app/components/notifications-list.php';
    }
}
