<?php

declare(strict_types=1);

namespace App\Domain\Performance\Models;

use Illuminate\Database\Eloquent\Model;

final class PerformanceEvaluation extends Model
{
    #[\Override]
    protected $table = 'test';

    #[\Override]
    protected $guarded = [];

    #[\Override]
    public $timestamps = false;
}
