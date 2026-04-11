<?php

declare(strict_types=1);

namespace App\Domain\Ppe\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name', 'code', 'price', 'min_stock'
])]
final class PpeItem extends Model
{
    /** @var string */
    protected $table = 'epp_db';

    #[\Override]
    public $timestamps = false;

    #[\Override]
    protected function casts(): array
    {
        return [
            'price' => 'float',
            'min_stock' => 'integer',
        ];
    }
}
