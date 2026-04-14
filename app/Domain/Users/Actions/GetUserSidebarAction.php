<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Domain\Users\Data\SidebarData;
use App\Domain\Users\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetUserSidebarAction
{
    use AsAction;

    public function handle(int $id): SidebarData
    {
        $user = User::findOrFail($id);

        return new SidebarData(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            document: $user->document,
            isActive: $user->is_active,
            fields: \App\Domain\Shared\Services\SchemaGenerator::toFields(\App\Domain\Users\Data\UpsertData::class)
        );
    }
}
