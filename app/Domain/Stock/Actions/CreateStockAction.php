<?php

declare(strict_types=1);

namespace App\Domain\Stock\Actions;

use App\Domain\Stock\Data\UpsertData;
use App\Domain\Stock\Models\Stock;
use Lorisleiva\Actions\Concerns\AsAction;

final class CreateStockAction
{
    use AsAction;

    public function handle(UpsertData $data): Stock
    {
        return Stock::create([
            'name'      => $data->name,
            'kind'      => $data->kind,
            'code'      => $data->code,
            'price'     => $data->price,
            'min_stock' => $data->min_stock,
            'area'      => $data->area,
        ]);
    }
}
