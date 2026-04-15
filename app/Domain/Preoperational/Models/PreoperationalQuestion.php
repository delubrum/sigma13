<?php

declare(strict_types=1);

namespace App\Domain\Preoperational\Models;

use Illuminate\Database\Eloquent\Model;

final class PreoperationalQuestion extends Model
{
    #[\Override]
    protected $table = 'preoperational_questions';

    #[\Override]
    public $timestamps = false; // No created_at/updated_at fields based on schema

    #[\Override]
    protected $guarded = [];
}
