<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Domain\Shared\Services\SchemaGenerator;
use App\Domain\Users\Data\SidebarData;
use App\Domain\Users\Data\UpsertData;
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
            fields: SchemaGenerator::toFields(UpsertData::class)
        );
    }
}
