<?php

declare(strict_types=1);

namespace App\Domain\Performance\Models;

use Illuminate\Database\Eloquent\Model;

final class PerformanceAnswer extends Model
{
    protected $table = 'test_answers';

    protected $guarded = [];

    public $timestamps = false;
}
