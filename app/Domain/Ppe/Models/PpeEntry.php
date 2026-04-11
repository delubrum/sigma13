<?php

declare(strict_types=1);

namespace App\Domain\Ppe\Models;

use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'item_id', 'user_id', 'qty', 'created_at'
])]
final class PpeEntry extends Model
{
    /** @var string */
    protected $table = 'epp_register';

    #[\Override]
    public $timestamps = false;

    #[\Override]
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'qty' => 'integer',
        ];
    }

    /** @return BelongsTo<PpeItem, $this> */
    public function item(): BelongsTo
    {
        return $this->belongsTo(PpeItem::class, 'item_id');
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
