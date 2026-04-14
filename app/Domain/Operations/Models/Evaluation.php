<?php

declare(strict_types=1);

namespace App\Domain\Operations\Models;

use Illuminate\Database\Eloquent\Model;

final class Evaluation extends Model
{
    protected $table = 'suppliers_evaluation';

    protected $guarded = [];
}
