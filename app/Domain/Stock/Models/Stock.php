<?php

declare(strict_types=1);

namespace App\Domain\Stock\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int|null $old_id
 * @property string $kind
 * @property string $name
 * @property int|null $code
 * @property float $initial_stock
 * @property int|null $min_stock
 * @property string|null $area
 * @property \Illuminate\Support\Carbon $created_at
 * @property string|null $temp_source
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection<int, StockItem> $items
 */
#[Fillable([
    'old_id',
    'kind',
    'name',
    'code',
    'initial_stock',
    'min_stock',
    'area',
    'temp_source',
])]
class Stock extends Model
{
    protected $table = 'stock';

    public const UPDATED_AT = null;

    #[\Override]
    protected function casts(): array
    {
        return [
            'old_id'        => 'integer',
            'code'          => 'integer',
            'initial_stock' => 'float',
            'min_stock'     => 'integer',
            'created_at'    => 'datetime',
        ];
    }

    /** @return HasMany<StockItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(StockItem::class, 'stock_id');
    }
}
