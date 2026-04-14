<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Identity\Listeners\SendUserInvitation;
use App\Domain\Shared\Events\PasswordResetRequested;
use App\Domain\Shared\Events\UserCreated;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(
            UserCreated::class,
            SendUserInvitation::class
        );

        Event::listen(
            PasswordResetRequested::class,
            SendUserInvitation::class
        );

        Blade::anonymousComponentPath(app_path('Domain/Shared/Web/Views/components'));

        View::addNamespace('components', app_path('Domain/Shared/Web/Views/components'));
        View::addNamespace('layouts', app_path('Domain/Shared/Web/Views/components/layouts'));

        View::addNamespace('assets', app_path('Domain/Assets/Web/Views'));
        View::addNamespace('users', app_path('Domain/Users/Web/Views'));
        View::addNamespace('dashboard', app_path('Domain/Dashboard/Web/Views'));
        View::addNamespace('identity', app_path('Domain/Identity/Web/Views'));
        View::addNamespace('auth', app_path('Domain/Identity/Web/Views/auth'));
        View::addNamespace('operations', app_path('Domain/Operations/Web/Views'));
        View::addNamespace('maintenance', app_path('Domain/Maintenance/Web/Views'));
        View::addNamespace('recruitment', app_path('Domain/Recruitment/Web/Views'));
        View::addNamespace('it', app_path('Domain/IT/Web/Views'));
        View::addNamespace('engineering', app_path('Domain/Engineering/Web/Views'));
        View::addNamespace('printing',     app_path('Domain/Printing/Web/Views'));
        View::addNamespace('improvement', app_path('Domain/Improvement/Web/Views'));
    }
}
