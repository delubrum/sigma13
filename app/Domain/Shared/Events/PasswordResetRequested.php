<?php

declare(strict_types=1);

namespace App\Domain\Shared\Events;

use App\Domain\Users\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class PasswordResetRequested
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public User $user) {}
}
