<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Actions;

use App\Domain\Maintenance\Data\TaskUpsertData;
use App\Domain\Maintenance\Models\MaintenanceItem;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class CreateMaintenanceTaskAction
{
    use AsAction;

    public function handle(TaskUpsertData $data, int $userId): MaintenanceItem
    {
        $item = MaintenanceItem::create([
            'mnt_id' => $data->maintenanceId,
            'user_id' => $userId,
            'created_at' => now(),
            'duration' => $data->duration,
            'complexity' => $data->complexity,
            'attends' => $data->attends,
            'notes' => $data->notes,
        ]);

        if (!empty($data->files)) {
            $this->handleFiles($item->id, $data->files);
        }

        return $item;
    }

    private function handleFiles(int $itemId, array $files): void
    {
        $path = public_path("uploads/mnt/pics/{$itemId}");
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        foreach ($files as $file) {
            /** @var \Illuminate\Http\UploadedFile $file */
            $name = $file->getClientOriginalName();
            $file->move($path, $name);
        }
    }
}
