<?php

declare(strict_types=1);

namespace App\Domain\Identity\Listeners;

use App\Domain\Identity\Actions\Password\SendResetLink;
use App\Domain\Shared\Events\PasswordResetRequested;
use App\Domain\Shared\Events\UserCreated;

final class SendUserInvitation
{
    public function handle(UserCreated|PasswordResetRequested $event): void
    {
        SendResetLink::run($event->user->email);
    }
}
