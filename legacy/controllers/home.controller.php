<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

class HomeController
{
    public function __construct(public Model $model, private AuthService $auth) {}

    public function Index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (! empty($_SESSION['id-SIGMA'])) {
            $user = $this->model->get('*', 'users', 'and id = '.$_SESSION['id-SIGMA']);
            if ($user) {
                $content = 'app/components/page.php';
                $title = '';
                require_once 'app/views/index.php';
                exit;
            }
        }

        // Ahora el intento de cookie se delega al AuthService que maneja la renovación
        $user = $this->auth->attemptCookieLogin();
        if ($user) {
            $_SESSION['id-SIGMA'] = $user->id;
            $this->saveSession((int) $user->id);
            header('Location: ?c=Home&a=Index');
            exit;
        }

        if (! empty($_REQUEST['email']) && ! empty($_REQUEST['pass'])) {
            $this->attemptFormLogin();

            return;
        }

        require_once 'app/views/login/index.php';
    }

    private function attemptFormLogin(): void
    {
        header('Content-Type: application/json');
        $email = strip_tags($_REQUEST['email']);
        $pass = strip_tags($_REQUEST['pass']);
        $now = new DateTime;

        $user = $this->model->get('id,email,password,failed_attempts,locked_until,last_password_change', 'users', "AND email = '$email' AND active = true");

        if (! $user) {
            echo json_encode(['status' => 'error', 'message' => 'Credenciales inválidas.']);

            return;
        }

        if ($user->locked_until && $now < new DateTime($user->locked_until)) {
            echo json_encode(['status' => 'locked', 'message' => 'Cuenta bloqueada hasta '.(new DateTime($user->locked_until))->format('H:i')]);

            return;
        }

        if (! password_verify($pass, $user->password)) {
            $this->incrementLoginAttempts($user);
            echo json_encode(['status' => 'error', 'message' => 'Credenciales inválidas.']);

            return;
        }

        $this->resetLoginAttempts((int) $user->id);

        if ((new DateTime($user->last_password_change))->diff($now)->days >= 90) {
            $_SESSION['pending_password_change'] = $user->email;
            echo json_encode(['status' => 'force_change', 'url' => '?c=Home&a=ChangePasswordView']);
            exit;
        }

        if (! $this->verifyRecaptchaToken($_REQUEST['g-recaptcha-response'] ?? '')) {
            echo json_encode(['status' => 'error', 'message' => 'Error de seguridad reCAPTCHA.']);

            return;
        }

        $_SESSION['id-SIGMA'] = $user->id;
        $this->saveSession((int) $user->id);
        $this->generateLoginCookies($email);

        $url = $_SESSION['redirect_url'] ?? '/';
        unset($_SESSION['redirect_url']);
        echo json_encode(['status' => 'ok', 'url' => $url]);
        exit;
    }

    private function incrementLoginAttempts($user): void
    {
        $attempts = (int) $user->failed_attempts + 1;
        $data = ['failed_attempts' => $attempts];
        if ($attempts >= 5) {
            $data['locked_until'] = (new DateTime)->modify('+15 minutes')->format('Y-m-d H:i:s');
        }
        $this->model->update('users', (object) $data, (int) $user->id);
    }

    private function resetLoginAttempts(int $userId): void
    {
        $this->model->update('users', (object) ['failed_attempts' => 0, 'locked_until' => null], $userId);
    }

    private function generateLoginCookies(string $email): void
    {
        $exp = time() + 30 * 24 * 60 * 60;
        $rp = $this->model->getToken(16);
        $rs = $this->model->getToken(32);

        setcookie('user_login', $email, $exp, '/', '', true, true);
        setcookie('random_password', $rp, $exp, '/', '', true, true);
        setcookie('random_selector', $rs, $exp, '/', '', true, true);

        $this->model->delete('token_auth', "email = '$email'");
        $this->model->save('token_auth', (object) [
            'email' => $email,
            'password_hash' => password_hash($rp, PASSWORD_DEFAULT),
            'selector_hash' => password_hash($rs, PASSWORD_DEFAULT),
            'expiry_date' => date('Y-m-d H:i:s', $exp),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'ip_address' => $this->model->getUserIP(),
        ]);
    }

    protected function verifyRecaptchaToken(string $token): bool
    {
        if (($_ENV['APP_ENV'] ?? 'production') === 'local') {
            return true;
        }
        $re = $_ENV['RECAPTCHA'] ?? null;
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = ['secret' => $re, 'response' => $token];
        $options = ['http' => ['header' => "Content-type: application/x-www-form-urlencoded\r\n", 'method' => 'POST', 'content' => http_build_query($data)]];
        $response = @file_get_contents($url, false, stream_context_create($options));
        $result = json_decode($response, true);

        return ($result['success'] ?? false) && ($result['score'] ?? 0) >= 0.5;
    }

    public function ChangePasswordView(): void
    {
        require_once 'app/views/login/change-password.php';
    }

    public function ChangePassword(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['pending_password_change'], $_POST['new_password'])) {
            $newPass = strip_tags($_POST['new_password']);
            if ($newPass !== strip_tags($_POST['confirm_password'])) {
                $error = 'No coinciden';
                require 'app/views/login/change-password.php';

                return;
            }
            if (! $this->validatePassword($newPass)) {
                $error = 'Contraseña débil';
                require 'app/views/login/change-password.php';

                return;
            }

            $user = $this->model->get('id', 'users', "AND email = '".$_SESSION['pending_password_change']."'");
            $this->model->update('users', (object) ['password' => password_hash($newPass, PASSWORD_DEFAULT), 'last_password_change' => date('Y-m-d H:i:s')], (int) $user->id);
            unset($_SESSION['pending_password_change']);
            $_SESSION['id-SIGMA'] = $user->id;
            header('Location: /');
            exit;
        }
    }

    private function validatePassword(string $password): bool
    {
        return strlen($password) >= 10 && preg_match('/[A-Z]/', $password) && preg_match('/\d/', $password) && preg_match('/[\W_]/', $password);
    }

    public function Notifications(): void
    {
        $notifications = $this->model->list('id,title', 'notifications', 'AND status = 1');
        if (isset($_REQUEST['list']) && $_REQUEST['list'] == 0) {
            echo count($notifications);
        } else {
            require_once 'app/components/notifications-list.php';
        }
    }

    public function Logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (! empty($_SESSION['id-SIGMA'])) {
            $this->model->update('users', (object) ['session_id' => null], (int) $_SESSION['id-SIGMA']);
        }
        session_destroy();
        $this->model->clearAuthCookie();
        header('Location: /');
        exit;
    }

    private function saveSession(int $userId): void
    {
        $this->model->update('users', (object) ['session_id' => session_id()], $userId);
    }
}
