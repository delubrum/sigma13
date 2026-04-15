<?php

declare(strict_types=1);

namespace App\Domain\Cbm\Actions;

use App\Domain\Cbm\Data\UpsertData;
use App\Domain\Cbm\Models\Cbm;
use App\Domain\Cbm\Models\CbmItem;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\SimpleExcel\SimpleExcelReader;
use Illuminate\Http\UploadedFile;

final class SaveCbmAction
{
    use AsAction;

    public function handle(UpsertData $data, int $userId): Cbm
    {
        /** @var UploadedFile $file */
        $file = $data->excel_file;
        
        // Use SimpleExcelReader instead of PhpSpreadsheet to avoid ext-gd dependency
        $reader = SimpleExcelReader::create($file->getRealPath())->noHeaderRow();
        $rows = $reader->getRows();

        $itemsToSave = [];
        $totalQty = 0;

        foreach ($rows as $index => $rowArray) {
            // Skip the first row (header) manually
            if ($index === 0) {
                continue;
            }

            // Convert row to indexed array if it's not
            $values = array_values($rowArray);

            if (empty($values[0])) {
                continue;
            }

            $width = (float) ($values[0] ?? 0);
            $height = (float) ($values[1] ?? 0);
            $length = (float) ($values[2] ?? 0);
            $qty = (int) ($values[3] ?? 0);
            $weight = (float) ($values[4] ?? 0);

            for ($j = 0; $j < $qty; $j++) {
                $itemsToSave[] = [
                    'width' => $width,
                    'height' => $height,
                    'item_length' => $length,
                    'weight' => $weight,
                    'qty' => 1,
                ];
                $totalQty++;
            }
        }

        $cbm = Cbm::create([
            'project' => $data->project,
            'user_id' => $userId,
            'total_items' => $totalQty,
        ]);

        foreach ($itemsToSave as $itemData) {
            $itemData['cbm_id'] = $cbm->id;
            CbmItem::create($itemData);
        }

        return $cbm;
    }
}
