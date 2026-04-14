<?php

declare(strict_types=1);

namespace App\Domain\Performance\Models;

use Illuminate\Database\Eloquent\Model;

final class PerformancePlan extends Model
{
    protected $table = 'test_plans';

    protected $guarded = [];

    public $timestamps = false;
}
