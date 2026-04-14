<?php

declare(strict_types=1);

namespace App\Domain\Performance\Models;

use Illuminate\Database\Eloquent\Model;

final class PerformanceEvaluation extends Model
{
    protected $table = 'test';

    protected $guarded = [];

    public $timestamps = false;
}
