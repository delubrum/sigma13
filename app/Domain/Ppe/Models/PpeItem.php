<?php

declare(strict_types=1);

namespace App\Domain\Ppe\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int         $id
 * @property string      $name
 * @property string|null $code
 * @property float|null  $price
 * @property int|null    $min_stock
 */
#[Fillable(['name', 'code', 'price', 'min_stock'])]
final class PpeItem extends Model
{
    #[\Override]
    protected $table = 'epp_db';

    #[\Override]
    public $timestamps = false;
}
