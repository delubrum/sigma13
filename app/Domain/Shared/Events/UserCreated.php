<?php

declare(strict_types=1);

namespace App\Domain\Shared\Events;

use App\Domain\Users\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

final class UserCreated
{
    use Dispatchable;

    public function __construct(public User $user) {}
}
