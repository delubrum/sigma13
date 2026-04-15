<?php

declare(strict_types=1);

namespace App\Domain\Ppe\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int         $id
 * @property int         $user_id
 * @property int         $employee_id
 * @property string      $name
 * @property string      $kind
 * @property string|null $notes
 * @property mixed       $img
 * @property int         $is_optimized
 * @property \Illuminate\Support\Carbon|null $created_at
 */
#[Fillable(['user_id', 'employee_id', 'name', 'kind', 'notes', 'img', 'is_optimized'])]
final class PpeDelivery extends Model
{
    #[\Override]
    protected $table = 'epp';

    #[\Override]
    public $timestamps = false;

    #[\Override]
    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }
}
