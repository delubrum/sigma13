<?php

declare(strict_types=1);

namespace App\Domain\Ppe\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property int    $item_id
 * @property int    $user_id
 * @property int    $qty
 * @property \Illuminate\Support\Carbon|null $created_at
 */
#[Fillable(['item_id', 'user_id', 'qty'])]
final class PpeEntry extends Model
{
    #[\Override]
    protected $table = 'epp_register';

    #[\Override]
    public $timestamps = false;

    #[\Override]
    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    #[\Override]
    protected static function booted(): void
    {
        self::creating(static function (PpeEntry $m): void {
            $m->created_at ??= now();
        });
    }
}
