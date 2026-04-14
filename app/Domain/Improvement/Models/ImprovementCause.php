<?php

declare(strict_types=1);

namespace App\Domain\Improvement\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $improvement_id
 * @property string|null $reason
 * @property int $method
 * @property array<int,string>|null $whys
 * @property string|null $probable
 * @property string|null $file
 */
#[Fillable(['improvement_id', 'reason', 'method', 'whys', 'probable', 'file'])]
final class ImprovementCause extends Model
{
    #[\Override]
    protected $table = 'improvement_causes';

    #[\Override]
    public $timestamps = false;

    #[\Override]
    protected function casts(): array
    {
        return [
            'whys'   => 'array',
            'method' => 'integer',
        ];
    }

    /** @return BelongsTo<Improvement, $this> */
    public function improvement(): BelongsTo
    {
        return $this->belongsTo(Improvement::class, 'improvement_id');
    }
}
