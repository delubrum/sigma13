<?php

declare(strict_types=1);

namespace App\Domain\Preoperational\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PreoperationalItem extends Model
{
    #[\Override]
    protected $table = 'preoperational_items';

    #[\Override]
    public $timestamps = false; // Based on columns checked, no created_at/updated_at

    #[\Override]
    protected $guarded = [];

    #[\Override]
    protected $casts = [
        'ticket_ids' => 'array',
    ];

    /** @return BelongsTo<Preoperational, $this> */
    public function preoperational(): BelongsTo
    {
        return $this->belongsTo(Preoperational::class, 'preop_id');
    }

    /** @return BelongsTo<PreoperationalQuestion, $this> */
    public function question(): BelongsTo
    {
        return $this->belongsTo(PreoperationalQuestion::class, 'question_id');
    }
}
