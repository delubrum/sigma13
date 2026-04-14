<?php

declare(strict_types=1);

namespace App\Domain\Printing\Actions;

use Illuminate\Http\UploadedFile;
use InvalidArgumentException;
use Lorisleiva\Actions\Concerns\AsAction;
use PhpOffice\PhpSpreadsheet\IOFactory;

final class ParseWoExcelAction
{
    use AsAction;

    /**
     * @return array{code: string, project: string, items: list<array{code: string, description: string, fuc: string, qty: int}>}
     */
    public function handle(UploadedFile $file): array
    {
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $reader->setReadEmptyCells(false);

        $spreadsheet = $reader->load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet()->toArray();

        $code = isset($sheet[1][0]) ? trim((string) $sheet[1][0]) : '';
        if ($code === '') {
            throw new InvalidArgumentException('WO code not found in Excel row 2, column A.');
        }

        $project = isset($sheet[1][4]) ? trim((string) $sheet[1][4]) : '';

        $items = [];
        $total = count($sheet);
        for ($i = 1; $i < $total; $i++) {
            $itemCode = isset($sheet[$i][1]) ? trim((string) $sheet[$i][1]) : '';
            if ($itemCode === '') {
                continue;
            }

            $items[] = [
                'code'        => $itemCode,
                'description' => isset($sheet[$i][5]) ? trim((string) $sheet[$i][5]) : '',
                'fuc'         => isset($sheet[$i][8]) ? trim((string) $sheet[$i][8]) : '',
                'qty'         => isset($sheet[$i][9]) ? (int) $sheet[$i][9] : 0,
            ];
        }

        return compact('code', 'project', 'items');
    }
}
