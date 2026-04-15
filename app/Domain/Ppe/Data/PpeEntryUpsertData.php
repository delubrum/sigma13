<?php

declare(strict_types=1);

namespace App\Domain\Ppe\Data;

use App\Domain\Shared\Data\Field;
use App\Domain\Shared\Data\FieldWidth;
use Spatie\LaravelData\Data;

final class PpeEntryUpsertData extends Data
{
    public function __construct(
        public readonly ?int $id,

        #[Field(label: 'Item EPP', widget: 'slimselect', route: 'ppe.options.items', width: FieldWidth::Full)]
        public readonly int $item_id,

        #[Field(label: 'Cantidad', width: FieldWidth::Half, placeholder: '0')]
        public readonly int $qty,
    ) {}
}
