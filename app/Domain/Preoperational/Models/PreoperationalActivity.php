<?php

declare(strict_types=1);

namespace App\Domain\Preoperational\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PreoperationalActivity extends Model
{
    #[\Override]
    protected $table = 'preoperational_activities';

    #[\Override]
    public $timestamps = false; // Based on schema check

    #[\Override]
    protected $guarded = [];

    /** @return BelongsTo<PreoperationalQuestion, $this> */
    public function question(): BelongsTo
    {
        return $this->belongsTo(PreoperationalQuestion::class, 'q_id');
    }
}
