<?php

declare(strict_types=1);

namespace App\Domain\Improvement\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $improvement_id
 * @property int $user_id
 * @property int|null $responsible_id
 * @property string|null $action
 * @property string|null $how_to
 * @property Carbon|null $whenn
 * @property Carbon|null $done
 * @property array<int,mixed>|null $results
 * @property bool $fulfill
 * @property Carbon|null $created_at
 */
#[Fillable([
    'improvement_id', 'user_id', 'responsible_id',
    'action', 'how_to', 'whenn', 'done', 'results', 'fulfill',
])]
final class ImprovementActivity extends Model
{
    #[\Override]
    protected $table = 'improvement_activities';

    #[\Override]
    public $timestamps = false;

    #[\Override]
    protected function casts(): array
    {
        return [
            'results'    => 'array',
            'whenn'      => 'datetime',
            'done'       => 'datetime',
            'created_at' => 'datetime',
            'fulfill'    => 'boolean',
        ];
    }

    /** @return BelongsTo<Improvement, $this> */
    public function improvement(): BelongsTo
    {
        return $this->belongsTo(Improvement::class, 'improvement_id');
    }
}
